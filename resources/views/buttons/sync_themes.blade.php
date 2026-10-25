{{--@if ($crud->hasAccess('sync'))--}}
<button type="button" href="javascript:void(0)" onclick="syncThemes(this)" class="btn btn-success">
    <i class="la la-sync-alt"></i> {{ trans('Sync Themes') }}
</button>
{{--@endif--}}

@loadOnce('sync_theme_button_script')
@push('after_scripts') @if (request()->ajax())
    @endpush
@endif
<script>

    if (typeof syncThemes != 'function') {

        function syncThemes(button) {
            swal({
                title: "{!! trans('backpack::base.warning') !!}",
                text: "{!! trans('Syncing adds new themes, updates existing themes and options, and soft-deletes themes whose directories no longer exist.') !!}",
                icon: "warning",
                buttons: ["{!! trans('backpack::crud.cancel') !!}", "{!! trans('Sync') !!}"],
            }).then((value) => {
                if (value) {
                    $.ajax({
                        url: '{{ url($crud->route .'/sync') }}',
                        type: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            Accept: 'applicaiton/json'
                        },
                        success: function (result) {
                            if (result instanceof Object) {
                                Object.entries(result).forEach(function (entry, index) {
                                    console.log(entry);
                                    var type = entry[0];
                                    entry[1].forEach(function (message, i) {
                                        new Noty({
                                            type: type,
                                            text: message,
                                        }).show();
                                    });
                                });
                            } else {
                                swal({
                                    title: "{!! trans('backpack::base.warning') !!}",
                                    text: result,
                                    icon: "error",
                                    timer: 4000,
                                    buttons: false,
                                });
                            }
                            crud.table.draw(false);
                        },
                        error: function (result) {
                            // Show an alert with the result
                            swal({
                                title: "{!! trans('backpack::crud.delete_confirmation_not_title') !!}",
                                text: "{!! trans('backpack::crud.delete_confirmation_not_message') !!}",
                                icon: "error",
                                timer: 4000,
                                buttons: false,
                            });
                        }
                    });
                }
            });

        }
    }

    // make it so that the function above is run after each DataTable draw event
    // crud.addFunctionToDataTablesDrawEventQueue('syncThemes');
</script>
@if (!request()->ajax())
    @endpush
@endif
@endLoadOnce
