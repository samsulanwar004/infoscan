<form id="modalForm" action="{{ admin_route_url('snaps.update', ['id' => $snap->id]) }}"  method="POST">
    {{ csrf_field() }}
    {{ method_field('PUT') }}
    <input type="hidden" name="mode" value="confirm">
    <input type="hidden" name="point" id="point">
    <input type="hidden" name="promo" value="{{$promo}}">
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
                <div class="radio" id="wording"></div>
                <select class="form-control" name="reason" id="reason" style="display: none;">
                  <option value="">Pilih Alasan</option>
                  @foreach($reasons as $reason)
                    <option value="{{ $reason->id }}">{{ $reason->setting_value }}</option>
                  @endforeach
                  @if($admin)
                    <option value="other">Lain-lain...</option>
                  @endif
                </select>
                <div id="other"></div>
            </div>
          </div>
          <!-- /.row -->
          <!-- Table row -->
        <div class="row">
          <div class="col-xs-12 table-responsive">
            <table class="table table-striped">
              <thead>
              <tr>
                <th>Estimated Point</th>
                <th>Fixed Point</th>
                <th>Promo Point</th>
              </tr>
              </thead>
              <tbody>
                  <tr>
                    <td>{{ $snap->estimated_point }}</td>
                    <td><span id="fixed-point">{{ $fixedPoint }}</span></td>
                    <td>{{ $promo }}</td>
                  </tr>
              </tbody>
            </table>
          </div>
          <!-- /.col -->
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
<style type="text/css">
  #other {
    margin-top: 10px;
  }
</style>
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
        $('.submit-to-server').removeAttr('disabled');
        $('#wording').html('<label><input name="comment" type="radio" onclick="fixed({{ $fixedPoint }});" value="Selamat, klaim sebesar {{ $fixedPoint }} poin telah berhasil! Kluk!" required="required">'+
          'Selamat, klaim sebesar {{ number_format($fixedPoint, 0,0,".") }} poin telah berhasil! Kluk!</label>'+
          '<label><input name="comment" onclick="fixed({{ $fixedPoint }});" value="Oops, data belanja kamu belum lengkap. Kamu dapat {{ $fixedPoint }} poin! Kluk!" type="radio" required="required">'+
          'Oops, data belanja kamu belum lengkap. Kamu dapat {{ number_format($fixedPoint, 0,0,".") }} poin! Kluk!</label>');
        $('#reason').hide();
        $('#other').html('');
      });

      $('#reject').on('click', function() {
        $('.submit-to-server').removeAttr('disabled');
        $('#wording').html('<label><input name="comment" onclick="fixed(0);" value="Sayang sekali, transaksi kamu gagal. Ayo coba lagi!" type="radio" required="required">Sayang sekali, transaksi kamu gagal. Ayo coba lagi!</label>');
        $('#reason').show();
        $('#reason').prop('selectedIndex', 0);
      });

      if ('{{(bool)$snap->approved_by}}' == true) {
        $('#approve').attr('disabled', 'disabled');
        $('#reject').attr('disabled', 'disabled');
      } else if ('{{(bool)$snap->reject_by}}' == true) {
        $('#approve').attr('disabled', 'disabled');
        $('#reject').attr('disabled', 'disabled');
      }
      $('#reason').on('change', function() {
        if($(this).val() == 'other') {
          $('#other').html('<input type="text" name="other" class="form-control" placeholder="Masukan Alasan Lainya" required>');
        }
      });
      var fixed = $('#fixed-point').html();
      $('#point').val(fixed);
  });

  function fixed(point) {
    $('#fixed-point').html(point);
  }

</script>

