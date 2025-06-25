<div id="delete-modal" class="modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body p-0">
                <form action="{{ $route }}" method="delete">
                    @csrf
                    @method('DELETE')

                    <div class="p-5 text-center">
                        @icon('x-circle', 'w-16 h-16 text-theme-24 mx-auto mt-3')
                        <div class="text-3xl mt-5">{{ $title ?? 'Confirm Deletion' }}</div>
                        <div class="text-gray-600 mt-2">{!! $message ?? 'Are you sure you want to delete this item?' !!}
                        </div>
                    </div>

                    <div class="px-5 pb-8 text-center">
                        <button type="button" data-dismiss="modal"
                            class="btn btn-outline-secondary w-24 mr-1">Cancel</button>
                        <button type="submit" class="btn btn-danger w-24">Delete</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
