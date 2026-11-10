<div class="modal fade"
     id="redirectPageParamModal"
     tabindex="-1"
     data-backdrop="static"
     data-keyboard="false"
     aria-labelledby="exampleModalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="redirectPageParamForm" action="" onsubmit="submitRouteInfo(event, this)">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">URL Required Parameters</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="redirectPageContent">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="redirectPageBtn">
                        <i class="las la-external-link-square-alt"></i> Go To
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
