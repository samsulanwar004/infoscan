<div class="modal-header">
    <a class="close" data-dismiss="modal">&times;</a>
    <h4><i class="fa fa-lock fa-btn"></i> <span class="action-title">Permissions </span> List</h4>
</div>
<div class="modal-body">
    <ul class="list-group">
        @foreach($permissions as $permission)
            <li class="list-group-item">{{ $permission->permission_name }}</li>
        @endforeach
    </ul>
</div>
<div class="modal-footer">
    <div class="button-container">
        <a class="btn btn-link" data-dismiss="modal">Close</a>
        <a class="btn btn-warning submit-to-server" href="{{ route('roles.edit', ['id' => $role->id]) }}">
            <i class="fa fa-pencil fa-btn"></i> <span class="ladda-label">Edit This Role</span>
        </a>
        <div class="la-ball-fall">
            <div></div>
            <div></div>
            <div></div>
        </div>
    </div>
</div>
