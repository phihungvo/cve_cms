@extends ('layouts.in')

@section ('body')

<div class="tab-content">
    <div class="tab-pane active" role="tabpanel">
        <form method="post">
            <input type="hidden" name="_action" value="create" />

            @include ('domains.user.enterprise.billing.molecules.create-update')

            <div class="box p-5 mt-5">
                <div class="flex justify-between">
                    <div>
                        <button type="button" onclick="window.history.back()" class="btn btn-outline-danger">
                            {{ __('billing.back') }}
                        </button>

                    </div>

                    <div>
                        <button type="submit" class="btn btn-primary">
                            {{ __('billing.create.create') }}
                        </button>
                    </div>
                </div>
            </div>

        </form>
    </div>
</div>

@stop