<div id="restore-modal" class="modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body p-0">
                <form action="{{ $route ?? '' }}" method="post">
                    @csrf
                    <input type="hidden" name="_action" value="{{ $action ?? 'restore' }}" />

                    <div class="p-5 text-center">
                        @icon('check-circle', 'w-16 h-16 text-theme-10 mx-auto mt-3')

                        <div class="text-3xl mt-5">{{ $title ?? __('restore-modal.title') }}</div>
                        <div class="text-gray-600 mt-2">{!! $message ?? __('restore-modal.message') !!}</div>
                    </div>

                    <div class="px-5 pb-8 text-center">
                        <button type="button" data-dismiss="modal"
                            class="btn btn-outline-secondary w-24 mr-1">{{ __('restore-modal.cancel') }}</button>
                        <button type="submit" class="btn btn-success w-24">{{ __('restore-modal.restore') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
