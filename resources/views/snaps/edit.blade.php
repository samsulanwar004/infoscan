<form id="modalForm" action="{{ admin_route_url('snaps.update', ['id' => $snap->id]) }}"  method="POST">
    {{ csrf_field() }}
    {{ method_field('PUT') }}
    <input type="hidden" name="member_id" value="{{ $snap->member_id }}">
    <input type="hidden" name="snap_type" value="{{ $snap->snap_type }}">
    <input type="hidden" name="mode_type" value="{{ $snap->mode_type }}">
    <input type="hidden" name="city" value="{{ $snap->outlet_city }}">
    <div class="modal-header">
        <a class="close btn-modal-close" data-dismiss="modal">&times;</a>
        <h4><i class="fa fa-file-o fa-btn"></i> <span class="action-title">Snap </span> Confirmation</h4>
    </div>
    <div class="modal-body">
        <div class="row">
            <div class="col-xs-12">
              <h2 class="page-header">
                <img src="<?php echo env('CDN_URL', ''); ?>/img/lte/logo.png" width="50"> GoJago
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
            <div class="col-sm-2 invoice-col">
                <div class="radio">
                    <label>
                        <input id="approve" value="approve" name="confirm" type="radio" {{ (bool)$snap->approved_by ? 'checked' : '' }} >
                        Approve
                    </label>
                </div>
                <div class="radio">
                    <label>
                        <input id="reject" value="reject" name="confirm" type="radio" {{ (bool)$snap->reject_by ? 'checked' : '' }} >
                        Reject
                    </label>
                </div>
            </div>
            <div class="col-sm-6 invoice-col">
              <textarea name="comment" id="comment" class="form-control" rows="4" placeholder="Reason"></textarea>
            </div>
          </div>
          <!-- /.row -->
    </div>
    <div class="modal-footer">
        <div class="button-container">
            <a class="btn btn-link btn-modal-close" data-dismiss="modal">Close</a>
            <button class="btn btn-primary submit-to-server" disabled="disabled">
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

<script type="text/javascript">
  $(document).ready(function() {
      $('#modalForm').on('submit', function (e) {
          e.preventDefault();
          REBEL.onSubmit($(this), function (responseData) {
            REBEL.removeAllMessageAlert();
            if (responseData.status == "ok") {
              REBEL.smallNotifTemplate(responseData.message, '.modal-content', 'success');
            }
          }, true);
      });

      $('#approve').on('click', function() {
        $('#comment').removeAttr('required');
        $('.submit-to-server').removeAttr('disabled');
      });

      $('#reject').on('click', function() {
        $('#comment').attr('required', 'required');
        $('.submit-to-server').removeAttr('disabled');
      });

      if ('{{(bool)$snap->approved_by}}' == true) {
        $('#approve').attr('disabled', 'disabled');
        $('#reject').attr('disabled', 'disabled');
      } else if ('{{(bool)$snap->reject_by}}' == true) {
        $('#approve').attr('disabled', 'disabled');
        $('#reject').attr('disabled', 'disabled');
      }
  });

</script>

