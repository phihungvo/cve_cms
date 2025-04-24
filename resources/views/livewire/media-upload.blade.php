<div>
    <!-- File Input -->
    <div class="mb-4" 
         x-data="{ uploading: false, progress: 0, showStack: false }"
         x-on:livewire-upload-start="uploading = true; showStack = true"
         x-on:livewire-upload-finish="uploading = false; progress = 0; showStack = false"
         x-on:livewire-upload-error="uploading = false; progress = 0; showStack = false"
         x-on:livewire-upload-progress="progress = $event.detail.progress">

        <input type="file" wire:model="mediaFiles" multiple accept="video/mp4" class="form-control">
        <small class="text-muted">{{ __('Multiple MP4 files allowed, max 10 files, total 1GB') }}</small>

        <!-- Progress Bar -->
        <div x-show="uploading" class="mt-2 relative">
            <div class="w-full h-4 bg-slate-100 rounded-lg shadow-inner overflow-hidden">
                <div class="h-4 rounded-lg transition-all duration-300 ease-in-out"
                     :class="{
                         'bg-gradient-to-r from-blue-500 via-purple-500 to-pink-500': progress < 100,
                         'bg-green-500': progress >= 100
                     }"
                     :style="{ width: `${progress}%` }">
                </div>
            </div>
            <div class="flex justify-between items-center mt-2">
                <p class="text-sm text-gray-600">
                    Đang upload... ({{ $currentFileIndex + 1 }}/{{ $totalFiles }} file) - 
                    <span x-text="progress + '%'"></span>
                </p>
                <button type="button" class="text-sm text-blue-500" wire:click="cancelUpload">
                    {{ __('Cancel Upload') }}
                </button>
            </div>
        </div>

        <!-- Stacked Popup Notification -->
        <div x-show="showStack"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 translate-y-4"
             class="fixed bottom-4 right-4 bg-white shadow-lg rounded-lg p-4 max-w-sm z-50">
            <div class="flex items-center space-x-2">
                <div class="animate-spin rounded-full h-5 w-5 border-t-2 border-b-2 border-blue-500"></div>
                <div>
                    <p class="font-semibold text-gray-800">Đang upload file {{ $currentFileIndex + 1 }}/{{ $totalFiles }}</p>
                    <p class="text-sm text-gray-600" x-text="`Tiến trình: ${progress}%`"></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Success -->
    @if($showModal)
        <div class="modal-overlay fixed inset-0 z-50 bg-black bg-opacity-50 flex items-center justify-center">
            <div class="modal-content bg-white rounded-lg p-6 w-1/2">
                <h3 class="text-lg font-bold mb-4">{{ __('Upload Success') }}</h3>
               ```html
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
        .bg-gradient-to-r {
            transition: width 0.3s ease-in-out, background 0.3s ease-in-out;
        }
        .animate-spin {
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
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
            alert(event.message);
        });

        Livewire.on('modal-closed', () => {
            console.log('🔳 Modal closed');
        });
    </script>
@endpush