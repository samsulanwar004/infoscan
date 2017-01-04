<form id="modalForm" action="{{ admin_route_url('snaps.update', ['id' => $snapFile->mode_type]) }}"  method="POST">
    {{ csrf_field() }}
    {{ method_field('PUT') }}
    <div class="modal-header">
        <a class="close" data-dismiss="modal">&times;</a>
        <h4><i class="fa fa-file-o fa-btn"></i> <span class="action-title">Snap </span> File</h4>
    </div>
    <div class="modal-body">
        <div class="form-horizontal">
            <audio controls>
              <source src="{{ $snapFile->file_path }}" type="audio/mpeg">
                Your browser does not support the audio element.
            </audio>
        </div>
    </div>
    <div class="modal-footer">
        <div class="button-container">
            <a class="btn btn-link" data-dismiss="modal">Close</a>
            <button class="btn btn-primary submit-to-server">
                <i class="fa fa-save fa-btn"></i> <span class="ladda-label">Save Item</span>
            </button>
            <div class="la-ball-fall">
                <div></div>
                <div></div>
                <div></div>
            </div>
        </div>
    </div>
</form>

