@extends('app')

@section('content')
    @include('partials.content_header', [
        'pageTitle' => 'Transaction history',
        'pageDescription' => 'Show Transaction history',
        'breadcrumbs' => [
            'Transactions history' => admin_route_url('transaction.index'),
            'Edit' => false]
        ]
    )
<?php 
  $type = config('common.transaction.transaction_type');
?>
    <!-- Main content -->
    <section class="content">

        <!-- Default box -->
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">
                </h3>
                <div class="box-tools pull-right">
                    <?php
                      $back = htmlspecialchars($_SERVER['HTTP_REFERER']);
                    ?>
                    <a href="{{ $back }}" class="btn btn-box-tool"><i
                            class="fa fa-times"></i></a>
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-xs-12">
                      <h2 class="page-header">
                        <i class="fa fa-globe"></i> AdminLTE, Inc.
                        <small class="pull-right">Date: {{ date_format(date_create($transaction->created_at), 'M, d Y') }}</small>
                      </h2>
                    </div>
                    <!-- /.col -->
                  </div>
                  <!-- info row -->
                  <div class="row invoice-info">                
                    <div class="col-sm-4 invoice-col">
                      <b>Transaction Code :</b> {{ $transaction->transaction_code }}<br>                      
                      <b>Transaction Type :</b> {{ array_search($transaction->transaction_type, $type) }}<br>
                      <b>Member Code :</b> {{ $transaction->member_code }}<br>
                    </div>
                    <!-- /.col -->
                  </div>
                  <!-- /.row -->

                  <!-- Table row -->
                  <div class="row">
                    <div class="col-xs-12 table-responsive">
                      <table class="table table-striped">
                        <thead>
                        <tr>
                          <th>Member Code From</th>
                          <th>Member Code To</th>
                          <th>Amount</th>
                          <th>Detail Type</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($transaction->transactionDetail as $detail)
                        <tr>
                          <td>{{ $detail->member_code_from }}</td>
                          <td>{{ $detail->member_code_to }}</td>
                          <td>{{ $detail->amount }}</td>
                          <td>{{ $detail->detail_type }}</td>
                        </tr>   
                        @endforeach                     
                        </tbody>
                      </table>
                    </div>
                    <!-- /.col -->
                  </div>
                  <!-- /.row -->
            </div>
        </div>
        <!-- /.box -->

    </section>
    <!-- /.content -->
@endsection