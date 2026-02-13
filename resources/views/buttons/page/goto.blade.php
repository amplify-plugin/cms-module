<button type="button"
        role="button"
        onclick="checkPageParams(event, this);"
        data-route="{{ backpack_url($crud->entity_name .'/'.$entry->getKey().'/check-url-param')  }}"
        class="btn btn-sm btn-link text-decoration-none"
        data-button-type="Preview Page">
    <i class="las la-external-link-square-alt"></i> Preview
</button>

<script>
    if (typeof checkPageParams == 'undefined') {

        function checkPageParams(event, element) {
            event.preventDefault();
            var button = $(element);
            const route = button.attr('data-route');
            const modalElement = $("#redirectPageParamModal");

            $.ajax({
                url: route,
                type: 'GET',
                success: function (response) {
                    if (response.params.length > 0) {
                        (new Noty({
                            text: response.message,
                            type: response.type
                        })).show();

                        $('#redirectPageParamForm').prop('action', response.url);

                        $("#redirectPageContent").empty();

                        $(response.params).each(function (index, item) {
                            $("#redirectPageContent").append(`
                           <div class="form-group">
                                <label for="form-${index}-${item}">
                                ${formatFieldLabel(item)}<span class="text-danger font-weight-bold">*</span>
                                </label>
                                <input type="text" class="form-control" id="form-${index}-${item}" required name="${item}" value="">
                           </div>
                           `);
                        });

                        setTimeout(() => modalElement.modal('show'), 300);

                    }
                    else {
                        redirectToPage(response.url);
                    }
                },
                error: function (error) {
                    const result = error.responseJSON;
                    (new Noty({
                        text: result.message,
                        type: result.type
                    })).show();
                }
            });

            return false;
        }

        function formatFieldLabel(item) {
            return item.toString().replace(/[\{_\}]+/gm, ' ').trim().split(' ').map(function (word) {
                return word[0].toUpperCase() + word.substring(1);
            }).join(' ');
        }

        function submitRouteInfo(event, element) {

            event.preventDefault();

            var form = $(element);

            var route = decodeURIComponent(form.prop('action'));

            $(form.serializeArray()).each(function (index, item) {
                route = route.replace(item['name'], item['value']).toString();
            });


            $("#redirectPageParamModal").modal('hide');

            redirectToPage(route);

            return true;
        }

        function redirectToPage(url) {
            new Noty({
                type: 'success',
                text: 'Redirecting to Web Page.'
            }).show();

            setTimeout(() => window.open(url, "_blank"), 1500);
        }
    }
</script>
