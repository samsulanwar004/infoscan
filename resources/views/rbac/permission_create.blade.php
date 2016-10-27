<form id="modalForm" action="{{ route('permissions.store') }}" class="form-horizontal" method="POST">
    {{ csrf_field() }}
    <div class="modal-header">
        <a class="close" data-dismiss="modal">&times;</a>
        <h4><i class="fa fa-lock fa-btn"></i> <span class="action-title">Add </span> Permission</h4>
    </div>
    <div class="modal-body">
        <div class="form-group">
            <label for="group" class="col-sm-2 control-label">Permissions Group</label>
            <div class="col-sm-10">
                <select class="form-control" name="group" id="group">
                    @foreach($groupOfPermissions as $group)
                        <option value="{{ $group->permission_group }}">{{ $group->permission_group }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="name" class="col-sm-2 control-label">Permissions Name</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="name" name="name" placeholder="Permission name">
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <div class="button-container">
            <a class="btn btn-link" data-dismiss="modal">Close</a>
            <button class="btn btn-primary submit-to-server">
                <i class="fa fa-save fa-btn"></i> <span class="ladda-label">Save Permission</span>
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
            REBEL.onSubmit($(this), function () {
                setTimeout(function() {
                    REBEL.removeAllMessageAlert();
                    $('#modalForm')[0].reset();
                }, 2000)
            });
        });
    })
</script>