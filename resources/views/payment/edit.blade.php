<form id="modalForm" action="{{ admin_route_url('payment-portal.update', ['id' => $payment->id]) }}"  method="POST">
    {{ csrf_field() }}
    {{ method_field('PUT') }}
    <div class="modal-header">
        <a class="close btn-close btn-modal-close" data-dismiss="modal">&times;</a>
        <h4><i class="fa fa-file-o fa-btn"></i> <span class="action-title">Confirmation </span> Payment </h4>
    </div>
    <div class="modal-body">
        <div class="row">
            <div class="col-xs-12">
              <h2 class="page-header">
                <img src="<?php echo env('CDN_URL', ''); ?>/img/lte/logo.png" width="50"> GoJago
                <small class="pull-right">Date: {{ date_format(date_create($payment->created_at), 'M, d Y') }}</small>
              </h2>
            </div>
            <!-- /.col -->
          </div>
          <!-- info row -->
          <div class="row invoice-info">
            <div class="col-sm-4 invoice-col">
              <b>Name :</b> {{ $payment->name }}<br>
              <b>Bank Account :</b> {{ strtoupper($payment->bank_account) }}<br>
              <b>Account Number :</b> {{ strtoupper($payment->account_number) }}<br>
            </div>
            <!-- /.col -->
            <div class="col-sm-2 invoice-col">
                <div class="radio">
                    <label>
                        <input id="approve" value="approved" name="confirm" type="radio" {{ $payment->status == 'approved' ? 'checked' : '' }} >
                        Approve
                    </label>
                </div>
                <div class="radio">
                    <label>
                        <input id="reject" value="rejected" name="confirm" type="radio" {{ $payment->status == 'rejected' ? 'checked' : '' }} >
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
                <th>Redeem Point</th>
                <th>Cashout</th>
              </tr>
              </thead>
              <tbody>
                  <tr>
                    <td>{{ number_format($payment->point,0,0,'.') }} Pts</td>
                    <td>Rp. {{ number_format($payment->cashout,0,0,'.') }}</td>
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
                    window.location.href = 'payment-portal';
                }, 3000)
            });
        });

        $('#approve').on('click', function() {
            $('.submit-to-server').removeAttr('disabled');
            $('#wording').html('<label><input name="comment" type="radio" value="Selamat, penukaran uang sebesar Rp. {{ number_format($payment->cashout,0,0,".") }} telah berhasil! Kluk!" required="required">'+
              'Selamat, penukaran uang sebesar Rp. {{ number_format($payment->cashout,0,0,".") }} telah berhasil! Kluk!</label>');
            $('#reason').hide();
            $('#other').html('');
        });

        $('#reject').on('click', function() {
            $('.submit-to-server').removeAttr('disabled');
            $('#wording').html('<div class="radio"><label><input name="comment" value="Sayang sekali, penukaran uang kamu gagal. Ayo coba lagi!" type="radio" required="required">With Reason</label></div>'+
              '<div class="radio"><label><input name="comment" value="fraud" type="radio" required="required">Because Fraud</label></div>');
            $('#reason').show();
            $('#reason').prop('selectedIndex', 0);
        });

        if ('{{ $payment->status }}' == 'approved') {
            $('#approve').attr('disabled', 'disabled');
            $('#reject').attr('disabled', 'disabled');
        } else if ('{{ $payment->status }}' == 'rejected') {
            $('#approve').attr('disabled', 'disabled');
            $('#reject').attr('disabled', 'disabled');
        }
        $('#reason').on('change', function() {
            if($(this).val() == 'other') {
              $('#other').html('<input type="text" name="other" class="form-control" placeholder="Masukan Alasan Lainnya" required>');
            } else {
              $('#other').html('');
            }
        });
    });

</script>