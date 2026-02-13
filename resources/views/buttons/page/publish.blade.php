<a href="javascript:void(0)" role="button" onclick="changePagePublishStatus(event, this);"
   data-route="{{ url($crud->route.'/'.$entry->getKey().'/publish/' . (($entry->is_published) ? 0 : 1))  }}"
   class="btn btn-sm btn-link"
   data-button-type="Process">
    <span>
        @if($entry->is_published == 1)
            <i class="la la-minus-circle"></i> Draft it
        @else
            <i class="la la-check"></i> Publish
        @endif
    </span>
</a>
<script>
    if (typeof changePagePublishStatus != 'function') {
        function changePagePublishStatus(event, element) {
            event.preventDefault();
            var button = $(element);
            var route = button.attr('data-route');

            $.ajax({
                url: route,
                type: 'GET',
                success: function (result) {

                    new Noty({
                        text: result.message,
                        type: result.type
                    }).show();

                    crud.table.ajax.reload();
                },
                error: function (result) {
                    new Noty({
                        text: result.message,
                        type: result.type
                    }).show();
                }
            });
        }
    }
</script>
