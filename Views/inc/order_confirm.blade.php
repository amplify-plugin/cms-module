<div class="modal fade" id="order-confirm" tabindex="-1" aria-labelledby="orderConfirm" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content shadow-sm">
                <div class="modal-body">
                    <h3 class="text-center">{{ __('Are you sure?') }}</h3>
                </div>
                <div class="modal-footer justify-content-around pt-0 border-top-0">
                    <button type="button" data-dismiss="modal" class="btn btn-dark">{{ __('No') }}</button>
                    <button type="button" data-dismiss="modal" onclick="createOrder()" class="btn btn-success">{{ __('Yes') }}</button>
                </div>
        </div>
    </div>
</div>

<script>
    $(document).on('click', '.create-order', function () {
        $("#order-confirm").modal('show');
    });
</script>
