<form id="modalForm" action="{{ admin_route_url('payment-portal.update', ['id' => $payment->id]) }}"  method="POST">
    {{ csrf_field() }}
    {{ method_field('PUT') }}
    <div class="modal-header">
        <a class="close btn-close btn-modal-close" data-dismiss="modal">&times;</a>
        <h4><i class="fa fa-file-o fa-btn"></i> <span class="action-title">Confirmation </span> Payment </h4>
    </div>
    <div class="modal-body">
        <span>confirmation this content ?</span>
    </div>
    <div class="modal-footer">
        <div class="button-container">
            <a class="btn btn-link btn-close" data-dismiss="modal">Close</a>
            <button class="btn btn-primary submit-to-server">
                <i class="fa fa-save fa-btn"></i> <span class="ladda-label">Save Confirmation</span>
            </button>
            <div class="la-ball-fall">
                <div></div>
                <div></div>
                <div></div>
            </div>
        </div>
    </div>
</form>

<script type="text/javascript">
    $(document).ready(function() {
        $('#modalForm').on('submit', function (e) {
            e.preventDefault();
            REBEL.onSubmit($(this), function (responseData) {
                REBEL.removeAllMessageAlert();
                if (responseData.status == "ok") {
                    REBEL.smallNotifTemplate(responseData.message, '.modal-content', 'success');
                }
                setTimeout(function () {
                    REBEL.removeAllMessageAlert();
                }, 3000)
            });
        });
    });
</script>