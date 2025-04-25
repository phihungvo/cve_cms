<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Domains\Campaign\Media\Action\Create as CreateMediaAction;
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
            $this->dispatch('media-upload-error', ['message' => 'Lỗi validate: ' . $e->getMessage()]);
            return;
        }

        $this->totalFiles = count($this->mediaFiles);
        if ($this->totalFiles > 10) {
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
                'status' => 'pending', // pending, uploading, success,ity error
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
        if ($totalSize > 1073741824) { // 1GB
            $this->dispatch('media-upload-error', ['message' => 'Tổng kích thước file vượt quá 1GB!']);
            return;
        }

        $this->uploadedMedia = [];
        $this->isUploading = true;
        $this->successfulUploads = 0;
        Log::info('Starting upload', ['total_files' => $this->totalFiles]);

        foreach ($this->mediaFiles as $index => $file) {
            if (!$this->isUploading) {
                $this->cleanupPartialUploads();
                $this->resetUploadState();
                return;
            }

            $this->currentFileIndex = $index;
            Log::info('Processing file', ['current_file' => $index + 1, 'total_files' => $this->totalFiles]);

            $originalFileName = $file->getClientOriginalName();
            $fileName = $this->sanitizeFileName($originalFileName);
            $mimeType = $file->getMimeType();
            $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            // Bắt đầu upload
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
                continue;
            }

            $path = auth()->user()->hasRole('root') ? $fileName : "{$this->enterpriseId}/{$fileName}";
            $mediaUrl = Storage::disk('minio')->url($path);

            if (Storage::disk('minio')->exists($path)) {
                Log::warning('File already exists', ['file' => $fileName]);
                $this->dispatch('livewire-file-error', [
                    'index' => $index,
                    'error' => 'File đã tồn tại trên server!',
                    'status' => 'error',
                ]);
                continue;
            }

            $duration = isset($fileInfo['playtime_seconds']) ? (int) $fileInfo['playtime_seconds'] : null;

            try {
                // Upload with progress tracking
                $path = $file->storeAs(
                    auth()->user()->hasRole('root') ? '' : $this->enterpriseId,
                    $fileName,
                    'minio'
                );

                // Simulate progress updates (Livewire doesn't natively support per-file progress for multiple uploads)
                for ($progress = 10; $progress <= 90; $progress += 10) {
                    $this->dispatch('livewire-file-progress', [
                        'index' => $index,
                        'progress' => $progress,
                        'status' => 'uploading',
                    ]);
                    usleep(200000); // 200ms delay to simulate progress
                }

                // Hoàn tất: 100%
                $this->dispatch('livewire-file-progress', [
                    'index' => $index,
                    'progress' => 100,
                    'status' => 'success',
                ]);
            } catch (\Exception $e) {
                Log::error('Upload failed', ['file' => $fileName, 'error' => $e->getMessage()]);
                $this->dispatch('livewire-file-error', [
                    'index' => $index,
                    'error' => 'Lỗi tải lên: ' . $e->getMessage(),
                    'status' => 'error',
                ]);
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
            } catch (\Exception $e) {
                Log::error('Database save failed', ['file' => $fileName, 'error' => $e->getMessage()]);
                Storage::disk('minio')->delete($path);
                $this->dispatch('livewire-file-error', [
                    'index' => $index,
                    'error' => 'Lỗi lưu vào database: ' . $e->getMessage(),
                    'status' => 'error',
                ]);
                continue;
            }
        }

        if ($this->isUploading && !empty($this->uploadedMedia)) {
            $this->showModal = true;
            $this->mediaFiles = [];
            $this->isUploading = false;
        } elseif (empty($this->uploadedMedia)) {
            $this->dispatch('media-upload-error', ['message' => 'Không có file nào được upload thành công!']);
            $this->resetUploadState();
        }
    }

    // Handle Livewire's native progress event
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