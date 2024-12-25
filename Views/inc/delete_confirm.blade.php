<div class="modal fade" id="delete-confirm" tabindex="-1" aria-labelledby="deleteConfirm" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content shadow-sm">
            <form action="" id="delete-form" method="POST">
                @method('delete')
                @csrf
                <div class="card-header">
                    <h3 class="text-center">{{ __('Are you sure?') }}</h3>
                </div>
                <div class="modal-body">
                    <p>Do you really want to delete this? This is can not be restore.</p>
                </div>
                <div class="d-flex justify-content-between modal-footer p-2 pt-0">
                    <button type="button" data-dismiss="modal" class="btn btn-outline-dark btn-sm m-0">{{ __('Close') }}</button>
                    <button type="submit" class="btn btn-danger btn-sm m-1">{{ __('Delete') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $(document).on('click', '.btn-delete', function() {
            $("#delete-confirm").modal('show')
            let link = $(this).attr("href")
            $("#delete-form").attr("action", link)
            return false;
        });
    });
</script>
