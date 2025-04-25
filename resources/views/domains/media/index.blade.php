@extends('layouts.in')

@section('title', __('media-index.title'))

@section('body')
        <div class="intro-y box p-5">
            @if(session('success'))
                <div class="alert alert-success mb-4 p-4">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger mb-4 p-4">{{ session('error') }}</div>
            @endif

            <!-- Search Form and Upload Button -->
            <div class="sm:flex sm:space-x-4">
                <form method="get" class="flex-grow mt-2 sm:mt-0">
                    <input type="search" name="search" class="form-control form-control-lg"
                        placeholder="{{ __('media-index.filter') }}" data-table-search="#media-list-table"
                        value="{{ request('search') }}" />
                </form>
                <div class="sm:ml-4 mt-2 sm:mt-0">
                    <livewire:media-upload />
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-auto scroll-visible header-sticky mt-5">
                <table id="media-list-table" class="table table-report sm:mt-2 font-medium text-center whitespace-nowrap"
                    data-table-sort data-table-pagination data-table-pagination-limit="10">
                    <thead>
                        <tr>
                            <th class="w-1">{{ __('STT') }}</th>
                            <th>{{ __('Media Name') }}</th>
                            <th>{{ __('Media File') }}</th>
                            <th>{{ __('Size') }}</th>
                            <th>{{ __('Duration') }}</th>
                            <th>{{ __('Enterprise') }}</th>
                            <th>{{ __('Created At') }}</th>
                            <th>{{ __('Updated At') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($media as $key => $item)
                            <tr>
                                <td class="w-1">{{ $key + 1 }}</td>
                                <td>{{ $item['name'] ?? '-' }}</td>
                                <td>
                                    @php
                                        $type = strtolower($item['type']);
                                        $isVideo = str_contains($type, 'video') || in_array($type, ['mp4']);
                                    @endphp
                                    @if ($isVideo)
                                        <video src="{{ $item['media_url'] }}" controls
                                            style="max-width: 100px; max-height: 100px;"></video>
                                    @else
                                        <a href="{{ $item['media_url'] }}" target="_blank">{{ __('View File') }}</a>
                                    @endif
                                </td>
                                <td>{{ number_format($item['size'] / (1024 * 1024), 2) }} MB</td>
                                <td>{{ $item['duration'] ? $item['duration'] . 's' : 'N/A' }}</td>
                                <td>
                                    @if($item['enterprise_id'])
                                        {{ \App\Domains\User\Enterprise\Model\Enterprise::find($item['enterprise_id'])->name ?? 'N/A' }}
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>{{ \Carbon\Carbon::createFromTimestamp($item['created_at'])->format('Y-m-d H:i:s') }}</td>
                                <td>{{ $item['updated_at'] ? \Carbon\Carbon::createFromTimestamp($item['updated_at'])->format('Y-m-d H:i:s') : 'N/A' }}</td>
                                <td>{{ $item['deleted_at'] ? __('Deleted') : __('Active') }}</td>
                                <td>
                                    @if(auth()->user()->hasRole('root'))
                                        @if($item['deleted_at'])
                                            <form action="{{ route('fpp.media.restore', $item['id']) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm mr-2">{{ __('Restore') }}</button>
                                            </form>
                                            <form action="{{ route('fpp.media.force-delete', $item['id']) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('{{ __('Are you sure you want to permanently delete this media?') }}')">{{ __('Force Delete') }}</button>
                                            </form>
                                        @else
                                            <a href="javascript:;" data-toggle="modal" data-target="#rename-modal"
                                                onclick="document.getElementById('rename-media-id').value = '{{ $item['id'] }}'; document.getElementById('rename-media-name').value = '{{ addslashes($item['name'] ?? '-') }}';"
                                                class="btn btn-primary btn-sm mr-2">{{ __('Rename') }}</a>
                                            <a href="javascript:;" data-toggle="modal" data-target="#delete-modal"
                                                onclick="document.getElementById('delete-media-id').value = '{{ addslashes($item['media_url']) }}'; document.getElementById('delete-media-name').innerText = '{{ addslashes($item['name'] ?? '-') }}';"
                                                class="btn btn-danger btn-sm">{{ __('Delete') }}</a>
                                        @endif
                                    @else
                                        @if(!$item['deleted_at'])
                                            <a href="javascript:;" data-toggle="modal" data-target="#rename-modal"
                                                onclick="document.getElementById('rename-media-id').value = '{{ $item['id'] }}'; document.getElementById('rename-media-name').value = '{{ addslashes($item['name'] ?? '-') }}';"
                                                class="btn btn-primary btn-sm mr-2">{{ __('Rename') }}</a>
                                            <a href="javascript:;" data-toggle="modal" data-target="#delete-modal"
                                                onclick="document.getElementById('delete-media-id').value = '{{ addslashes($item['media_url']) }}'; document.getElementById('delete-media-name').innerText = '{{ addslashes($item['name'] ?? '-') }}';"
                                                class="btn btn-danger btn-sm">{{ __('Delete') }}</a>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center">{{ __('No media found') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Delete Modal -->
        @include('molecules.delete-modal', [
        'method' => 'delete',
        'route' => route('fpp.media.delete', 0),
        'title' => __('media-delete.title'),
        'message' => '<span id="delete-media-name"></span>',
    ])

        <!-- Rename Modal -->
        @include('molecules.rename-modal', [
        'method' => 'put',
        'route' => route('fpp.media.rename'),
        'title' => __('media-rename.title'),
        'message' => __('media-rename.message'),
    ])

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Delete modal
            const deleteForm = document.querySelector('#delete-modal form');
            const deleteInput = document.createElement('input');
            deleteInput.type = 'hidden';
            deleteInput.name = 'media_url';
            deleteInput.id = 'delete-media-id';
            deleteForm.appendChild(deleteInput);

            
        });
    </script>
@endpush
