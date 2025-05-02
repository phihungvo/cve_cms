<div>
    <!-- Session Messages -->
    @if(session('success'))
        <div class="alert alert-success mb-4 p-4">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger mb-4 p-4">{{ session('error') }}</div>
    @endif

    <!-- File Input -->
    <div class="mb-4" 
         x-data="{ 
             uploading: false, 
             files: [], 
             showStack: false,
             successfulUploads: 0,
             totalFiles: 0,
             overallProgress: 0
         }"
         x-on:livewire-upload-start="uploading = true; showStack = true"
         x-on:livewire-upload-finish="uploading = false; showStack = false"
         x-on:livewire-upload-error="uploading = false; showStack = false"
         x-on:livewire-upload-progress="overallProgress = $event.detail.progress"
         x-on:livewire-file-progress="files[$event.detail.index].progress = $event.detail.progress; files[$event.detail.index].status = $event.detail.status; updateOverallProgress()"
         x-on:livewire-file-error="console.log('livewire-file-error:', $event.detail); files[$event.detail.index].status = $event.detail.status; files[$event.detail.index].error = $event.detail.error || 'Lỗi không xác định';"
         x-on:livewire-file-success="successfulUploads += 1"
         x-on:livewire-file-init="files = $event.detail.files; totalFiles = $event.detail.totalFiles; overallProgress = 0">

        <input type="file" wire:model="mediaFiles" multiple accept="video/mp4" class="form-control">
        <small class="text-muted">{{ __('Multiple MP4 files allowed, max 10 files, total 1GB') }}</small>

        <!-- Per-File Progress Bars -->
        <div x-show="uploading" class="upload-progress mt-4 space-y-4">
            <template x-for="(file, index) in files" :key="index">
                <div class="relative">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-semibold text-gray-600 truncate" x-text="file.name"></p>
                        <span class="text-sm text-gray-500" x-text="file.status"></span>
                    </div>
                    <div class="w-full h-4 bg-slate-100 rounded-lg shadow-inner overflow-hidden">
                        <div class="h-4 rounded-lg progress-bar"
                             :class="{
                                 'bg-gray-300': file.status === 'pending',
                                 'bg-gradient-to-r': file.status === 'uploading',
                                 'bg-green-600': file.status === 'success',
                                 'bg-red-500': file.status === 'error'
                             }"
                             x-bind:style="{ width: file.progress + '%' }">
                        </div>
                    </div>
                    <div class="flex justify-between items-center mt-1">
                        <p class="text-sm text-gray-600">
                            <span x-text="file.progress + '%'"></span>
                            <span x-show="file.status === 'success'" class="text-green-600 ml-2">{{ __('Uploaded') }}</span>
                            <span x-show="file.status === 'error'" class="text-red-600 ml-2 font-semibold" x-text="file.error || 'Lỗi không xác định'"></span>
                        </p>
                    </div>
                </div>
            </template>

            <!-- Success Counter -->
            <div class="mt-2 text-sm text-gray-600">
                {{ __('Uploaded successfully') }}: <span x-text="successfulUploads + '/' + totalFiles"></span>
            </div>

            <!-- Cancel Button -->
            <button type="button" class="text-sm text-blue-500" wire:click="cancelUpload">
                {{ __('Cancel Upload') }}
            </button>
        </div>

        <!-- Stacked Popup Notification -->
        <div x-show="showStack"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 translate-y-4"
             class="stacked-popup fixed bottom-4 right-4 bg-white shadow-lg rounded-lg p-4 max-w-sm z-50">
            <div class="flex items-center space-x-2">
                <div class="animate-spin rounded-full h-5 w-5 border-t-2 border-b-2 border-blue-500"></div>
                <div>
                    <p class="font-semibold text-gray-800">{{ __('Uploading files') }} (<span x-text="successfulUploads + '/' + totalFiles"></span>)</p>
                    <p class="text-sm text-gray-600">{{ __('Overall Progress') }}: <span x-text="Math.round(overallProgress) + '%'"></span></p>
                    <div class="w-full h-2 bg-slate-100 rounded-lg mt-1">
                        <div class="h-2 bg-blue-500 rounded-lg overall-progress"
                             x-bind:style="{ width: overallProgress + '%' }"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Success -->
    @if($showModal)
        <div class="modal-overlay fixed inset-0 z-50 bg-black bg-opacity-50 flex items-center justify-center">
            <div class="modal-content bg-white rounded-lg p-6">
                <h3 class="text-lg font-bold mb-4">{{ __('Upload Success') }}</h3>
                <p class="mb-4">{{ __('Successfully uploaded') }}: {{ count($uploadedMedia) }}/{{ $totalFiles }}</p>
                <div class="space-y-4">
                    @foreach($uploadedMedia as $media)
                        <div class="border border-gray-300 rounded p-4">
                            <p><strong>{{ __('Name') }}:</strong> {{ $media->name }}</p>
                            <p><strong>{{ __('Size') }}:</strong> {{ number_format($media->size / (1024 * 1024), 2) }} MB</p>
                            <p><strong>{{ __('Duration') }}:</strong> {{ $media->duration ?? 'N/A' }}s</p>
                            <p><strong>{{ __('Type') }}:</strong> {{ $media->type }}</p>
                            <p><strong>{{ __('URL') }}:</strong> 
                                <a href="{{ $media->media_url }}" target="_blank" class="text-blue-600 hover:underline">
                                    {{ __('View File') }}
                                </a>
                            </p>
                        </div>
                    @endforeach
                </div>
                <button wire:click="closeModal" class="btn btn-secondary mt-4">
                    {{ __('Close') }}
                </button>
            </div>
        </div>
    @endif
</div>

@push('styles')
    <style>
        /* Reset cơ bản */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
            background-color: #f7fafc;
            padding: 20px;
        }

        /* Alert styles */
        .alert {
            padding: 16px;
            border-radius: 6px;
            margin-bottom: 16px;
            font-size: 14px;
        }

        .alert-success {
            background-color: #d1fae5;
            color: #065f46;
            border: 1px solid #34d399;
        }

        .alert-danger {
            background-color: #fee2e2;
            color: #991b1b;
            border: 1px solid #ef4444;
        }

        /* Form input */
        .form-control {
            display: block;
            width: 100%;
            padding: 8px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 14px;
            margin-bottom: 8px;
        }

        .text-muted {
            font-size: 12px;
            color: #6b7280;
        }

        /* Margin và spacing */
        .mb-4 {
            margin-bottom: 16px;
        }

        .mt-4 {
            margin-top: 16px;
        }

        .space-y-4 > * + * {
            margin-top: 16px;
        }

        /* Progress bar cho từng file */
        .upload-progress .relative {
            position: relative;
        }

        .flex {
            display: flex;
        }

        .items-center {
            align-items: center;
        }

        .justify-between {
            justify-content: space-between;
        }

        .text-sm {
            font-size: 14px;
        }

        .font-semibold {
            font-weight: 600;
        }

        .text-gray-600 {
            color: #4b5563;
        }

        .truncate {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .text-gray-500 {
            color: #6b7280;
        }

        /* Thanh progress bar */
        .w-full {
            width: 100%;
        }

        .h-4 {
            height: 16px;
        }

        .bg-slate-100 {
            background-color: #f1f5f9;
        }

        .rounded-lg {
            border-radius: 6px;
        }

        .shadow-inner {
            box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.1);
        }

        .overflow-hidden {
            overflow: hidden;
        }

        .progress-bar {
            transition: width 0.3s ease-in-out, background 0.3s ease-in-out;
        }

        /* Trạng thái của progress bar */
        .progress-bar.bg-gray-300 {
            background: #d1d5db;
        }

        .progress-bar.bg-gradient-to-r {
            background: linear-gradient(to right, #3b82f6, #8b5cf6, #ec4899);
        }

        .progress-bar.bg-green-600 {
            background: #16a34a;
        }

        .progress-bar.bg-red-500 {
            background: #ef4444;
        }

        /* Hiệu ứng pulse cho trạng thái uploading */
        @keyframes pulse {
            0% {
                opacity: 1;
            }
            50% {
                opacity: 0.5;
            }
            100% {
                opacity: 1;
            }
        }

        .animate-pulse {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        /* Success và error text */
        .text-green-600 {
            color: #16a34a;
        }

        .text-red-600 {
            color: #dc2626;
        }

        .ml-2 {
            margin-left: 8px;
        }

        /* Success counter */
        .mt-2 {
            margin-top: 8px;
        }

        /* Cancel button */
        .text-blue-500 {
            color: #3b82f6;
            background: none;
            border: none;
            cursor: pointer;
        }

        .text-blue-500:hover {
            text-decoration: underline;
        }

        /* Popup thông báo */
        .stacked-popup {
            transition: all 0.3s ease-in-out;
        }

        .fixed {
            position: fixed;
        }

        .bottom-4 {
            bottom: 16px;
        }

        .right-4 {
            right: 16px;
        }

        .bg-white {
            background: #ffffff;
        }

        .shadow-lg {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        .p-4 {
            padding: 16px;
        }

        .max-w-sm {
            max-width: 320px;
        }

        .z-50 {
            z-index: 50;
        }

        .space-x-2 > * + * {
            margin-left: 8px;
        }

        /* Spinner loading */
        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }
            100% {
                transform: rotate(360deg);
            }
        }

        .animate-spin {
            animation: spin 1s linear infinite;
        }

        .rounded-full {
            border-radius: 9999px;
        }

        .h-5 {
            height: 20px;
        }

        .w-5 {
            width: 20px;
        }

        .border-t-2 {
            border-top-width: 2px;
        }

        .border-b-2 {
            border-bottom-width: 2px;
        }

        .border-blue-500 {
            border-color: #3b82f6;
        }

        .text-gray-800 {
            color: #1f2937;
        }

        .h-2 {
            height: 8px;
        }

        .mt-1 {
            margin-top: 4px;
        }

        .overall-progress {
            transition: width 0.3s ease-in-out;
        }

        .bg-blue-500 {
            background: #3b82f6;
        }

        /* Modal styles */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 50;
        }

        .modal-content {
            background: #ffffff;
            border-radius: 6px;
            padding: 24px;
            max-width: 500px;
            width: 100%;
        }

        .text-lg {
            font-size: 18px;
        }

        .font-bold {
            font-weight: 700;
        }

        .border-gray-300 {
            border: 1px solid #d1d5db;
        }

        .btn.btn-secondary {
            padding: 8px 16px;
            background: #6b7280;
            color: #ffffff;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        .btn.btn-secondary:hover {
            background: #4b5563;
        }

        .text-blue-600 {
            color: #2563eb;
        }

        .text-blue-600:hover {
            text-decoration: underline;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            console.log('✅ DOM Loaded');
        });

        Livewire.on('media-upload-error', (event) => {
            console.error('❌ Upload error:', event.message);
            if (event.message) {
                alert(event.message);
            } else {
                console.warn('No error message provided for media-upload-error');
            }
        });

        Livewire.on('modal-closed', () => {
            console.log('🔳 Modal closed');
        });

        // Calculate overall progress
        window.updateOverallProgress = function () {
            const files = this.files || [];
            if (files.length === 0) return 0;
            const totalProgress = files.reduce((sum, file) => sum + (file.progress || 0), 0);
            this.overallProgress = totalProgress / files.length;
        };
    </script>
@endpush