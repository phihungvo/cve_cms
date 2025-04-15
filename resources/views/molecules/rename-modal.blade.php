<div id="rename-modal" class="modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body p-0">
                <form action="{{ $route ?? '' }}" method="post">
                    @csrf
                    @if(isset($method))
                        <input type="hidden" name="_method" value="{{ $method }}" />
                    @else
                        <input type="hidden" name="_action" value="{{ $action ?? 'rename' }}" />
                    @endif

                    <div class="p-5 text-center">
                        @icon('edit', 'w-16 h-16 text-theme-1 mx-auto mt-3') <!-- Icon edit -->

                        <div class="text-3xl mt-5">{{ $title ?? __('rename-modal.title') }}</div>
                        <div class="text-gray-600 mt-2">{!! $message ?? __('rename-modal.message') !!}</div>
                    </div>

                    <div class="px-5 pb-5 text-left py-3">
                        <div class="form-group">
                            <label for="rename-media-name">{{ __('rename-modal.new-name') }}</label>
                            <input type="text" name="name" id="rename-media-name" class="form-control" required
                                value="{{ $currentName ?? '' }}">
                            <input type="hidden" name="media_id" id="rename-media-id">
                        </div>
                    </div>

                    <div class="px-5 pb-8 text-center">
                        <button type="button" data-dismiss="modal"
                            class="btn btn-outline-secondary w-24 mr-1">{{ __('rename-modal.cancel') }}</button>
                        <button type="submit" class="btn btn-primary w-24">{{ __('rename-modal.save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>