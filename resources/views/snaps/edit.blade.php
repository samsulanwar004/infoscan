<form id="modalForm" action="{{ admin_route_url('snaps.update', ['id' => $snap->id]) }}"  method="POST">
    {{ csrf_field() }}
    {{ method_field('PUT') }}
    <div class="modal-header">
        <a class="close" data-dismiss="modal">&times;</a>
        <h4><i class="fa fa-file-o fa-btn"></i> <span class="action-title">Snap </span> Confirmation</h4>
    </div>
    <div class="modal-body">
        <div class="row">
            <div class="col-xs-12">
              <h2 class="page-header">
                <img src="/img/lte/logo.png" width="50"> GoJago
                <small class="pull-right">Date: {{ date_format(date_create($snap->created_at), 'M, d Y') }}</small>
              </h2>
            </div>
            <!-- /.col -->
          </div>
          <!-- info row -->
          <div class="row invoice-info">                
            <div class="col-sm-4 invoice-col">
              <b>Request Code :</b> {{ $snap->request_code }}<br>                      
              <b>Snap Type :</b> {{ strtoupper($snap->snap_type) }}<br>
              <b>Mode Type :</b> {{ strtoupper($snap->mode_type) }}<br>
              <b>Member Name :</b> {{ $snap->member->name }}<br>
            </div>
            <!-- /.col -->
            <div class="col-sm-4 invoice-col">
                <div class="checkbox">
                    <label>
                        <input name="approve" {{ (bool)$snap->approved_by ? 'checked' : '' }} type="checkbox">
                        Approve
                    </label>
                </div>
                <div class="checkbox">
                    <label>
                        <input name="check" {{ (bool)$snap->check_by ? 'checked' : '' }} type="checkbox">
                        Check
                    </label>
                </div>
            </div>
          </div>
          <!-- /.row -->

          <!-- Table row -->
          <div class="row">
            <div class="col-xs-12 table-responsive" style="overflow-y:scroll;max-height: 500px;">
              <table class="table table-striped">
                <thead>
                    <tr>
                      <th>File Code</th>
                      <th>Score</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($snap->files as $file)
                    <tr>
                      <td>{{ $file->file_code }}</td>
                      <td>{{ $file->recognition_score }}</td>
                    </tr>   
                @endforeach                     
                </tbody>
              </table>
            </div>
            <!-- /.col -->
          </div>
          <!-- /.row -->
    </div>
    <div class="modal-footer">
        <div class="button-container">
            <a class="btn btn-link" data-dismiss="modal">Close</a>
            <button class="btn btn-primary submit-to-server">
                <i class="fa fa-save fa-btn"></i> <span class="ladda-label">Save</span>
            </button>
            <div class="la-ball-fall">
                <div></div>
                <div></div>
                <div></div>
            </div>
        </div>
    </div>
</form>

