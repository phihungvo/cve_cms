@extends('layouts.in')

@section('body')
    <!-- Thông báo -->
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <!-- Form tìm kiếm và nút Create -->
    <form method="GET" action="{{ route('video.index') }}">
        <div class="sm:flex sm:space-x-4">
            <!-- Ô tìm kiếm -->
            <div class="flex-grow mt-2 sm:mt-0">
                <input type="search" name="search" class="form-control form-control-lg"
                    placeholder="{{ __('video-index.search') }}" value="{{ $search ?? '' }}"
                    data-table-search="#video-list-table" />
            </div>

            <!-- Nút Create -->
            <div class="sm:ml-4 mt-2 sm:mt-0 bg-white">
                <a href="{{ route('video.create') }}" class="btn form-control-lg whitespace-nowrap">
                    {{ __('video-index.create') }}
                </a>
            </div>
        </div>
    </form>

    <!-- Bảng danh sách video -->
    <div class="overflow-auto scroll-visible header-sticky">
        <table id="video-list-table"
            class="table table-report sm:mt-2 font-medium font-semibold text-center whitespace-nowrap" data-table-sort>
            <thead>
                <tr>
                    <th>{{ __('video-index.stt') }}</th> <!-- Số thứ tự thay cho ID -->
                    <th>{{ __('video-index.name') }}</th>
                    {{-- <th>{{ __('video-index.description') }}</th> --}}
                    {{-- <th>{{ __('video-index.publish_time') }}</th> --}}
                    {{-- <th>{{ __('video-index.start_time') }}</th>
                    <th>{{ __('video-index.end_time') }}</th> --}}
                    {{-- <th>{{ __('video-index.logo_url') }}</th> --}}
                    {{-- <th>{{ __('video-index.thumbnail_url') }}</th> --}}
                    <th>{{ __('video-index.video_url') }}</th>
                    <th>{{ __('video-index.reach_target') }}</th>
                    <th>{{ __('video-index.distance_target') }}</th>
                    <th>{{ __('video-index.impression_target') }}</th>
                    <th>{{ __('video-index.device_target') }}</th>
                    <th>{{ __('video-index.cpm_target') }}</th>
                    <th>{{ __('video-index.cost') }}</th>
                    <th>{{ __('video-index.video_type') }}</th>
                    {{-- <th>{{ __('video-index.created_at') }}</th> --}}
                    {{-- <th>{{ __('video-index.updated_at') }}</th> --}}
                    {{-- <th>{{ __('video-index.enabled') }}</th>s --}}
                    <th>{{ __('video-index.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($items as $key => $video)
                    <tr>
                        <td>{{ $key + 1 }}</td> <!-- Số thứ tự -->
                        <td>{{ $video->name }}</td>
                        {{-- <td>{{ $video->description }}</td> --}}
                        {{-- <td>{{ $video->publish_time }}</td> --}}
                        {{-- <td>{{ $video->start_time }}</td> --}}
                        {{-- <td>{{ $video->end_time }}</td> --}}
                        {{-- <td><img src="{{ $video->logo_url }}" alt="Logo" width="50" height="50" /></td> --}}
                        {{-- <td><img src="{{ $video->thumbnail_url }}" alt="Thumbnail" width="50" height="50" /></td> --}}
                        <td>
                            <video width="100" height="75" controls>
                                <source src="{{ $video->video_url }}" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                        </td>
                        <td>{{ $video->reach_target }}</td>
                        <td>{{ $video->distance_target }}</td>
                        <td>{{ $video->impression_target }}</td>
                        <td>{{ $video->device_target }}</td>
                        <td>{{ $video->cpm_target }}</td>
                        <td>{{ $video->cost }}</td>
                        <td>
                            @if ($video->video_type == 1)
                                AdBike
                            @elseif ($video->video_type == 2)
                                AdCar
                            @else
                                AdOther
                            @endif
                        </td>
                        {{-- <td>{{ $video->created_at }}</td> --}}
                        {{-- <td>{{ $video->updated_at }}</td> --}}
                        {{-- <td>
                            @if ($video->enabled == 1)
                                <span style="color: green;">Playing</span>
                            @else
                                <span style="color: red;">Stop</span>
                            @endif
                        </td> --}}
                        <td>
                            <a href="{{ route('video.edit', $video->id) }}"
                                class="btn btn-primary btn-sm">{{ __('video-index.edit') }}</a>
                            <form action="{{ route('video.destroy', $video->id) }}" method="POST" style="display:inline;"
                                onsubmit="return confirm('{{ __('video-index.delete_confirm') }}');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="btn btn-danger btn-sm">{{ __('video-index.delete') }}</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="20">{{ __('video-index.no_data') }}</td> <!-- Cập nhật colspan cho đủ cột -->
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@stop
