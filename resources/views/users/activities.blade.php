<div class="modal-header">
    <a class="close" data-dismiss="modal">&times;</a>
    <h4><i class="fa fa-lock fa-btn"></i> <span class="action-title">Activities </span> List</h4>
</div>
<div class="modal-body">
    <ul class="list-group">
        @foreach($activities as $activitie)
            <li class="list-group-item"><center>{{ $activitie->action_in }} on {{ date_format(date_create($activitie->created_at), 'h:i , d-m-Y') }} | Ip Address : {{ $activitie->ip_address }}</center></li>
        @endforeach
    </ul>
</div>
<div class="modal-footer">
    <div class="button-container">
        <a class="btn btn-link" data-dismiss="modal">Close</a>
        <div class="la-ball-fall">
            <div></div>
            <div></div>
            <div></div>
        </div>
    </div>
</div>
