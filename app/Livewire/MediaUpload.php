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
    public $uploadProgress = 0;
    public $uploadStatus = '';
    public $enterpriseId;
    public $showModal = false;
    public $uploadedMedia = [];
    public $totalFiles = 0;
    public $currentFileIndex = 0;
    public $isUploading = false;

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
            $this->uploadStatus = 'Lỗi validate: ' . $e->getMessage();
            $this->dispatch('media-upload-error', ['message' => $this->uploadStatus]);
            return;
        }

        $this->totalFiles = count($this->mediaFiles);
        $this->currentFileIndex = 0;
        $this->uploadStatus = 'Đang chuẩn bị upload...';
        Log::info('Upload status set', ['status' => $this->uploadStatus]);
        $this->uploadFiles();
    }

    public function uploadFiles()
    {
        $this->validate();

        $totalSize = collect($this->mediaFiles)->sum->getSize();
        if ($totalSize > 1073741824) { // 1GB
            $this->uploadStatus = 'Tổng kích thước file vượt quá 1GB!';
            $this->dispatch('media-upload-error', ['message' => $this->uploadStatus]);
            return;
        }

        $this->uploadedMedia = [];
        $this->isUploading = true;
        Log::info('Starting upload', ['total_files' => $this->totalFiles]);

        foreach ($this->mediaFiles as $index => $file) {
            if (!$this->isUploading) {
                $this->cleanupPartialUploads();
                $this->resetUploadState();
                return;
            }

            $this->currentFileIndex = $index;
            $this->uploadProgress = ($index / $this->totalFiles) * 100; // Base progress
            Log::info('Processing file', ['current_file' => $index + 1, 'total_files' => $this->totalFiles]);

            $originalFileName = $file->getClientOriginalName();
            $fileName = $this->sanitizeFileName($originalFileName);
            $mimeType = $file->getMimeType();
            $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            if ($extension !== 'mp4') {
                Log::warning('Invalid file extension', ['file' => $originalFileName]);
                continue; // Skip this file, continue with others
            }

            $getID3 = new getID3();
            $fileInfo = $getID3->analyze($file->getPathname());
            if (!isset($fileInfo['fileformat']) || $fileInfo['fileformat'] !== 'mp4') {
                Log::warning('Invalid MP4 format', ['file' => $originalFileName]);
                continue; // Skip this file
            }

            $path = auth()->user()->hasRole('root') ? $fileName : "{$this->enterpriseId}/{$fileName}";
            $mediaUrl = Storage::disk('minio')->url($path);

            if (Storage::disk('minio')->exists($path)) {
                Log::warning('File already exists', ['file' => $fileName]);
                continue; // Skip this file
            }

            $duration = isset($fileInfo['playtime_seconds']) ? (int) $fileInfo['playtime_seconds'] : null;

            try {
                $path = $file->storeAs(
                    auth()->user()->hasRole('root') ? '' : $this->enterpriseId,
                    $fileName,
                    'minio'
                );
            } catch (\Exception $e) {
                Log::error('Upload failed', ['file' => $fileName, 'error' => $e->getMessage()]);
                continue; // Skip this file
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
            } catch (\Exception $e) {
                Log::error('Database save failed', ['file' => $fileName, 'error' => $e->getMessage()]);
                Storage::disk('minio')->delete($path); // Clean up the uploaded file
                continue; // Skip this file
            }

            $this->uploadProgress = (($index + 1) / $this->totalFiles) * 100;
        }

        if ($this->isUploading && !empty($this->uploadedMedia)) {
            $this->uploadStatus = 'Upload hoàn tất!';
            $this->showModal = true;
            $this->mediaFiles = [];
            $this->isUploading = false;
        } elseif (empty($this->uploadedMedia)) {
            $this->uploadStatus = 'Không có file nào được upload thành công!';
            $this->dispatch('media-upload-error', ['message' => $this->uploadStatus]);
            $this->resetUploadState();
        }
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
        $this->uploadProgress = 0;
        $this->uploadStatus = '';
        $this->mediaFiles = [];
        $this->uploadedMedia = [];
        $this->totalFiles = 0;
        $this->currentFileIndex = 0;
        $this->isUploading = false;
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