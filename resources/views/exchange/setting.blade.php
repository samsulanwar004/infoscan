<form id="modalForm" action="{{ admin_route_url('exchange.setting.update') }}"  method="POST">
    {{ csrf_field() }}
    {{ method_field('POST') }}
    <div class="modal-header">
        <a class="close btn-modal-close" data-dismiss="modal">&times;</a>
        <h4><i class="fa fa-tasks fa-btn"></i> <span class="action-title">Setting </span> Exchange</h4>
    </div>
    <div class="modal-body">
        <div class="form-group">
            <label for="minimum_cash">Minimum Cash</label>
            <input type="number" name="minimum_cash" class="form-control" required="required" value="{{ $minimum }}">
        </div>
    </div>
    <div class="modal-footer">
        <div class="button-container">
            <a class="btn btn-link btn-modal-close" data-dismiss="modal">Close</a>
            <button class="btn btn-primary submit-to-server">
                <i class="fa fa-save fa-btn"></i> <span class="ladda-label">Save Setting</span>
            </button>
            <div class="la-ball-fall">
                <div></div>
                <div></div>
                <div></div>
            </div>
        </div>
    </div>
</form>