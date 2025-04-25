<div>
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
         x-on:livewire-file-error="files[$event.detail.index].status = $event.detail.status; files[$event.detail.index].error = $event.detail.error"
         x-on:livewire-file-success="successfulUploads += 1"
         x-on:livewire-file-init="files = $event.detail.files; totalFiles = $event.detail.totalFiles; overallProgress = 0">

        <input type="file" wire:model="mediaFiles" multiple accept="video/mp4" class="form-control">
        <small class="text-muted">{{ __('Multiple MP4 files allowed, max 10 files, total 1GB') }}</small>

        <!-- Per-File Progress Bars -->
        <div x-show="uploading" class="mt-4 space-y-4">
            <template x-for="(file, index) in files" :key="index">
                <div class="relative">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-semibold text-gray-600 truncate" x-text="file.name"></p>
                        <span class="text-sm text-gray-500" x-text="file.status"></span>
                    </div>
                    <div class="w-full h-4 bg-slate-100 rounded-lg shadow-inner overflow-hidden">
                        <div class="h-4 rounded-lg transition-all duration-300 ease-in-out"
                             :class="{
                                 'bg-gray-300': file.status === 'pending',
                                 'bg-gradient-to-r from-blue-500 via-purple-500 to-pink-500 animate-pulse': file.status === 'uploading',
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
                            <span x-show="file.status === 'error'" class="text-red-600 ml-2" x-text="file.error || '{{ __('Error') }}'"></span>
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
             class="fixed bottom-4 right-4 bg-white shadow-lg rounded-lg p-4 max-w-sm z-50">
            <div class="flex items-center space-x-2">
                <div class="animate-spin rounded-full h-5 w-5 border-t-2 border-b-2 border-blue-500"></div>
                <div>
                    <p class="font-semibold text-gray-800">{{ __('Uploading files') }} (<span x-text="successfulUploads + '/' + totalFiles"></span>)</p>
                    <p class="text-sm text-gray-600">{{ __('Overall Progress') }}: <span x-text="Math.round(overallProgress) + '%'"></span></p>
                    <div class="w-full h-2 bg-slate-100 rounded-lg mt-1">
                        <div class="h-2 bg-blue-500 rounded-lg transition-all duration-300"
                             x-bind:style="{ width: overallProgress + '%' }"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Success -->
    @if($showModal)
        <div class="modal-overlay fixed inset-0 z-50 bg-black bg-opacity-50 flex items-center justify-center">
            <div class="modal-content bg-white rounded-lg p-6 p-2">
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

        // Calculate overall progress
        window.updateOverallProgress = function () {
            const files = this.files || [];
            if (files.length === 0) return 0;
            const totalProgress = files.reduce((sum, file) => sum + (file.progress || 0), 0);
            this.overallProgress = totalProgress / files.length;
        };
    </script>
@endpush