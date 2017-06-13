@extends('app')

@section('content')
    @include('partials.content_header', ['pageTitle' => 'Payment Portal', 'pageDescription' => 'List of payment portal', 'breadcrumbs' => ['Payment Portal' => false]])

    <!-- Main content -->
    <section class="content">
        <div class="progress" style="display: none">
          <div class="progress-bar progress-bar-striped active" role="progressbar"
            aria-valuenow="10" aria-valuemin="0" aria-valuemax="100" style="width:0%">
            <span id="persent"></span>
          </div>
        </div>

        <!-- Default box -->
        <div class="box">
            <div class="box-header with-border form-inline" style="overflow: hidden; height: 45px;">
                <h3 class="box-title">
                </h3>
                <div class="box-tools-new">
                    <div class="input-group date">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <input type="text" class="form-control datepicker" name="filter_date" id="filter-date"
                            @if(isset($date)) value="{{$date}}" @endif
                        >
                    </div>
                    <button class="btn btn-primary" id="export">Export</button>
                </div>
            </div>
            <div class="box-body">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Point Redeem</th>
                        <th>Cashout</th>
                        <th>Current Point</th>
                        <th>Current Money</th>
                        <th>Username & Email</th>
                        <th>Bank Account</th>
                        <th>Account Number</th>
                        <th>Date</th>
                        <!-- <th width="250"></th> -->
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($payment as $item)
                        <tr>
                            <td class="vertical-middle">
                                @if ($item->status == 'approved')
                                    <i class="fa fa-check-circle text-green"></i>
                                @elseif ($item->status == 'rejected')
                                    <i class="fa fa-times-circle text-red"></i>
                                @else
                                    <i class="fa fa-check-circle text-default"></i>
                                @endif
                            </td>
                            <td class="vertical-middle">
                                {{ number_format($item->point) }} Pts
                            </td>
                            <td class="vertical-middle">
                                Rp {{ number_format($item->cashout) }}
                            </td>
                            <td class="vertical-middle">
                                {{ number_format($item->member->temporary_point) }} Pts
                            </td>
                            <td class="vertical-middle">
                                Rp {{ number_format($item->member->temporary_point * 2.5) }}
                            </td>
                            <td class="vertical-middle">
                                {{ $item->member->name }} <br>
                                <small>{{ $item->member->email }}</small>
                            </td>
                            <td class="vertical-middle">
                                {{ $item->name }} <br>
                                {{ strtoupper($item->bank_account) }}
                            </td>
                            <td class="vertical-middle">
                                {{ $item->account_number }}
                            </td>
                            <td class="vertical-middle">
                                {{ date_format(date_create($item->created_at), 'M, d Y') }}
                            </td>
                            {{--<td class="text-right vertical-middle">
                                <div class="btn-group">
                                    @cando('PaymentPortal.Confirm')
                                    <a href="{{ admin_route_url('payment-portal.edit', ['id' => $item->id]) }}"
                                      data-toggle="modal"
                                      data-target="#"
                                      modal-size="modal-md"
                                      class="btn btn-success"
                                      title="Edit">
                                      <i class="fa fa-pencil"> </i></a>
                                    @endcando
                                </div> --}}
                            </td>
                        </tr>
                    @empty
                        <td colspan="8"> There is no record for payment portal data!</td>
                    @endforelse
                    </tbody>
                </table>
                {{ $payment->links() }}
            </div>
        </div>
        <!-- /.box -->

    </section>
    <!-- /.content -->
@endsection

@section('footer_scripts')

<style type="text/css">
    .box-tools-new {
        position: absolute;
        top: 5px;
    }
</style>

<script type="text/javascript">
    $(document).ready(function() {
        $('.datepicker').daterangepicker({
            ranges: {
               'Today': [moment(), moment()],
               'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
               'Last 7 Days': [moment().subtract(6, 'days'), moment()],
               'Last 30 Days': [moment().subtract(29, 'days'), moment()],
               'This Month': [moment().startOf('month'), moment().endOf('month')],
               'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            }
        });

        $('#export').on('click', function() {
            var date = $(".datepicker").val();
            var dateArr = date.split(' - ');
            var startArr = dateArr[0].split("/");
            var endArr = dateArr[1].split("/");

            var start_at = startArr[2]+"-"+startArr[0]+"-"+startArr[1];
            var end_at = endArr[2]+"-"+endArr[0]+"-"+endArr[1];

            var type_request = 'new';
            var page = 1;
            var filename = null;

            requestCsv(start_at, end_at, type_request, page, filename);

            $('.progress').show();
        });

        function requestCsv(start_at, end_at, type_request, page, filename, no = null) {
            $.get( "{{ admin_route_url('payment.export') }}", {
                date_start : start_at,
                date_end : end_at,
                type_request : type_request,
                filename : filename,
                page : page,
                no : no,
            })
                .success(function( data ) {
                    var type_request = data.message.type_request;
                    if (type_request == 'next') {
                        var type_request = data.message.type_request;
                        var filename = data.message.filename;
                        var page = data.message.page;
                        var no = data.message.no;
                        var last = data.message.last;
                        requestCsv(start_at, end_at, type_request, page, filename, no);
                        var progress = Math.round(page/last*100);
                        $('.progress-bar').css("width", function(){
                            return progress+'%';
                        });
                        $('#persent').text(progress+'% Please wait...');
                    } else {
                        $('.progress-bar').css("width", function(){
                            return 100+'%';
                        });
                        $('#persent').text('100% Please wait...');
                        setTimeout(function(){
                            $('.progress').hide();
                        }, 3000);
                        window.location.href = 'download/payment/?download='+data.message.filename;
                    }
            });
        }
    });

</script>

@endsection