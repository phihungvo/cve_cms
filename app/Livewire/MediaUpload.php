<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Domains\Campaign\Media\Action\Create as CreateMediaAction;
use App\Domains\Campaign\Media\Model\Media;
use Illuminate\Support\Str;
use getID3;

class MediaUpload extends Component
{
    use WithFileUploads;

    public $mediaFiles = [];
    public $enterpriseId;
    public $showModal = false;
    public $uploadedMedia = [];
    public $totalFiles = 0;
    public $currentFileIndex = 0;
    public $isUploading = false;
    public $successfulUploads = 0;

    protected $rules = [
        'mediaFiles.*' => 'file|max:102400|mimes:mp4', // 100MB per file
        'enterpriseId' => 'nullable|integer',
    ];

    public function mount()
    {
        $this->enterpriseId = auth()->user()->enterprise_id ?? null;
    }

    public function updatedMediaFiles()
    {
        Log::info('Media files updated', ['files_count' => count($this->mediaFiles)]);

        try {
            $this->validate();
        } catch (\Exception $e) {
            Log::error('Validation failed', ['error' => $e->getMessage()]);
            session()->flash('error', 'Lỗi validate: ' . $e->getMessage());
            $this->dispatch('media-upload-error', ['message' => 'Lỗi validate: ' . $e->getMessage()]);
            return;
        }

        $this->totalFiles = count($this->mediaFiles);
        if ($this->totalFiles > 10) {
            session()->flash('error', 'Tối đa 10 file được phép tải lên!');
            $this->dispatch('media-upload-error', ['message' => 'Tối đa 10 file được phép tải lên!']);
            return;
        }

        $this->currentFileIndex = 0;
        $this->successfulUploads = 0;

        // Initialize file list for frontend
        $files = collect($this->mediaFiles)->map(function ($file, $index) {
            return [
                'name' => $file->getClientOriginalName(),
                'progress' => 0,
                'status' => 'pending', // pending, uploading, success, error
                'error' => null,
            ];
        })->toArray();

        $this->dispatch('livewire-file-init', [
            'files' => $files,
            'totalFiles' => $this->totalFiles,
        ]);

        Log::info('Upload preparation', ['status' => 'Đang chuẩn bị upload...']);
        $this->uploadFiles();
    }

    public function uploadFiles()
    {
        $this->validate();

        $totalSize = collect($this->mediaFiles)->sum->getSize();
        Log::info('Total size of files', ['size_bytes' => $totalSize]);
        if ($totalSize > 1073741824) {
            Log::error('Total size exceeds 1GB', ['size_bytes' => $totalSize]);
            session()->flash('error', 'Tổng kích thước file vượt quá 1GB!');
            $this->dispatch('media-upload-error', ['message' => 'Tổng kích thước file vượt quá 1GB!']);
            return;
        }

        $this->uploadedMedia = [];
        $this->isUploading = true;
        $this->successfulUploads = 0;
        Log::info('Starting upload', ['total_files' => $this->totalFiles]);

        $hasError = false;

        foreach ($this->mediaFiles as $index => $file) {
            if (!$this->isUploading) {
                Log::info('Upload cancelled', ['index' => $index]);
                $this->cleanupPartialUploads();
                $this->resetUploadState();
                return;
            }

            $this->currentFileIndex = $index;
            $originalFileName = $file->getClientOriginalName();
            Log::info('Processing file', [
                'index' => $index + 1,
                'name' => $originalFileName,
                'size' => $file->getSize()
            ]);

            $fileName = $this->sanitizeFileName($originalFileName);
            $mimeType = $file->getMimeType();
            $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            $this->dispatch('livewire-file-progress', [
                'index' => $index,
                'progress' => 0,
                'status' => 'uploading',
            ]);

            if ($extension !== 'mp4') {
                Log::warning('Invalid file extension', ['file' => $originalFileName]);
                $this->dispatch('livewire-file-error', [
                    'index' => $index,
                    'error' => 'File không phải định dạng MP4!',
                    'status' => 'error',
                ]);
                Log::info('Dispatched livewire-file-error', ['error' => 'File không phải định dạng MP4!', 'index' => $index]);
                $hasError = true;
                continue;
            }

            $getID3 = new getID3();
            $fileInfo = $getID3->analyze($file->getPathname());
            if (!isset($fileInfo['fileformat']) || $fileInfo['fileformat'] !== 'mp4') {
                Log::warning('Invalid MP4 format', ['file' => $originalFileName]);
                $this->dispatch('livewire-file-error', [
                    'index' => $index,
                    'error' => 'File không phải MP4 hợp lệ!',
                    'status' => 'error',
                ]);
                Log::info('Dispatched livewire-file-error', ['error' => 'File không phải MP4 hợp lệ!', 'index' => $index]);
                $hasError = true;
                continue;
            }

            $path = auth()->user()->hasRole('root') ? $fileName : "{$this->enterpriseId}/{$fileName}";
            $mediaUrl = Storage::disk('minio')->url($path);

            // Kiểm tra sự tồn tại trong MinIO
            if (Storage::disk('minio')->exists($path)) {
                Log::warning('File already exists in MinIO', ['file' => $fileName]);
                $this->dispatch('livewire-file-error', [
                    'index' => $index,
                    'error' => 'File đã tồn tại trong MinIO!',
                    'status' => 'error',
                ]);
                Log::info('Dispatched livewire-file-error', ['error' => 'File đã tồn tại trong MinIO!', 'index' => $index]);
                session()->flash('error', 'File đã tồn tại trong MinIO: ' . $originalFileName);
                $hasError = true;
                continue;
            }

            // Kiểm tra sự tồn tại trong database
            if (Media::where('media_url', $mediaUrl)->exists()) {
                Log::warning('File already exists in database', ['file' => $fileName]);
                $this->dispatch('livewire-file-error', [
                    'index' => $index,
                    'error' => 'File đã tồn tại trong cơ sở dữ liệu!',
                    'status' => 'error',
                ]);
                Log::info('Dispatched livewire-file-error', ['error' => 'File đã tồn tại trong cơ sở dữ liệu!', 'index' => $index]);
                session()->flash('error', 'File đã tồn tại trong cơ sở dữ liệu: ' . $originalFileName);
                $hasError = true;
                continue;
            }

            $duration = isset($fileInfo['playtime_seconds']) ? (int) $fileInfo['playtime_seconds'] : null;

            try {
                $path = $file->storeAs(
                    auth()->user()->hasRole('root') ? '' : $this->enterpriseId,
                    $fileName,
                    'minio'
                );

                for ($progress = 10; $progress <= 90; $progress += 10) {
                    $this->dispatch('livewire-file-progress', [
                        'index' => $index,
                        'progress' => $progress,
                        'status' => 'uploading',
                    ]);
                    usleep(200000);
                }

                $this->dispatch('livewire-file-progress', [
                    'index' => $index,
                    'progress' => 100,
                    'status' => 'success',
                ]);
            } catch (\Exception $e) {
                Log::error('Upload failed', [
                    'file' => $fileName,
                    'error' => $e->getMessage(),
                    'index' => $index
                ]);
                $this->dispatch('livewire-file-error', [
                    'index' => $index,
                    'error' => 'Lỗi tải lên: ' . $e->getMessage(),
                    'status' => 'error',
                ]);
                Log::info('Dispatched livewire-file-error', ['error' => 'Lỗi tải lên: ' . $e->getMessage(), 'index' => $index]);
                session()->flash('error', 'Lỗi tải lên file ' . $originalFileName . ': ' . $e->getMessage());
                $hasError = true;
                continue;
            }

            $mediaUrl = Storage::disk('minio')->url($path);

            $mediaData = [
                'name' => pathinfo($fileName, PATHINFO_FILENAME),
                'file_name' => $fileName,
                'media_url' => $mediaUrl,
                'size' => $file->getSize(),
                'type' => $mimeType,
                'duration' => $duration,
                'enterprise_id' => auth()->user()->hasRole('root') ? null : $this->enterpriseId,
                'campaign_id' => null,
            ];

            try {
                $action = new CreateMediaAction();
                $media = $action->handle($mediaData);
                $this->uploadedMedia[] = $media;
                $this->successfulUploads++;
                $this->dispatch('livewire-file-success');
                Log::info('Dispatched livewire-file-success', ['file' => $fileName, 'index' => $index]);
            } catch (\Exception $e) {
                Log::error('Database save failed', [
                    'file' => $fileName,
                    'error' => $e->getMessage(),
                    'index' => $index
                ]);
                Storage::disk('minio')->delete($path);
                $this->dispatch('livewire-file-error', [
                    'index' => $index,
                    'error' => 'Lỗi lưu vào database: ' . $e->getMessage(),
                    'status' => 'error',
                ]);
                Log::info('Dispatched livewire-file-error', ['error' => 'Lỗi lưu vào database: ' . $e->getMessage(), 'index' => $index]);
                session()->flash('error', 'Lỗi lưu file ' . $originalFileName . ' vào database: ' . $e->getMessage());
                $hasError = true;
                continue;
            }
        }

        Log::info('Upload completed', [
            'successful' => $this->successfulUploads,
            'total' => $this->totalFiles
        ]);

        if ($this->isUploading && !empty($this->uploadedMedia)) {
            session()->flash('success', "Đã tải lên thành công {$this->successfulUploads}/{$this->totalFiles} file!");
            $this->showModal = true;
            $this->mediaFiles = [];
            $this->isUploading = false;
        } elseif ($hasError) {
            Log::error('Upload completed with errors');
            // Session message đã được flash trong vòng lặp, không cần lặp lại
            $this->resetUploadState();
        } else {
            Log::error('No files uploaded successfully');
            session()->flash('error', 'Không có file nào được upload thành công!');
            $this->dispatch('media-upload-error', ['message' => 'Không có file nào được upload thành công!']);
            Log::info('Dispatched media-upload-error', ['message' => 'Không có file nào được upload thành công!']);
            $this->resetUploadState();
        }
    }

    public function updatingMediaFiles($value, $name)
    {
        // This method can be used to capture progress if Livewire supports it in future updates
        // Currently, we rely on manual progress dispatching
    }

    public function cancelUpload()
    {
        $this->isUploading = false;
        $this->cleanupPartialUploads();
        $this->resetUploadState();
        session()->flash('error', 'Đã hủy quá trình tải lên!');
    }

    protected function cleanupPartialUploads()
    {
        foreach ($this->uploadedMedia as $media) {
            $path = auth()->user()->hasRole('root') ? $media->file_name : "{$this->enterpriseId}/{$media->file_name}";
            Storage::disk('minio')->delete($path);
        }
    }

    protected function resetUploadState()
    {
        $this->mediaFiles = [];
        $this->uploadedMedia = [];
        $this->totalFiles = 0;
        $this->currentFileIndex = 0;
        $this->isUploading = false;
        $this->successfulUploads = 0;
        $this->dispatch('livewire-file-init', ['files' => [], 'totalFiles' => 0]);
    }

    protected function sanitizeFileName(string $fileName): string
    {
        $extension = pathinfo($fileName, PATHINFO_EXTENSION);
        $baseName = pathinfo($fileName, PATHINFO_FILENAME);
        return Str::slug($baseName, '-') . '.' . strtolower($extension);
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetUploadState();
        $this->dispatch('modal-closed');
        $this->redirect(route('fpp.media.index'));
    }

    public function render()
    {
        return view('livewire.media-upload');
    }
}