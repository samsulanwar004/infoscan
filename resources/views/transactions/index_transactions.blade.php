@extends('app')

@section('content')
    @include('partials.content_header', ['pageTitle' => 'Transactions History', 'pageDescription' => 'List of transactions history', 'breadcrumbs' => ['Transactions history' => false]])

    <!-- Main content -->
    <section class="content">

        <!-- Default box -->
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">
                </h3>
            </div>
            <div class="box-body">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th width="300">Transaction Code</th>
                        <th width="300">Member Code</th>
                        <th width="300">Transaction Type</th>
                        <th width="300">Date</th>
                        <th width="250"></th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($transactions as $transaction)
                        <tr>
                            <td class="vertical-middle">
                                {{ $transaction->transaction_code }} <br>
                            </td>
                            <td class="vertical-middle">
                                {{ $transaction->member_code }} <br>
                            </td>
                            <td class="vertical-middle">
                                {{ $transaction->transaction_type }} <br>
                            </td>
                            <td class="vertical-middle">
                                {{ date_format(date_create($transaction->created_at), 'M, d Y') }} <br>
                            </td>
                            <td class="text-right vertical-middle">
                                <div class="btn-group">
                                <!--TODO : cando-->
                                    <a href="{{ route('transaction.show', ['id' => $transaction->id]) }}" class="btn btn-primary">
                                        <i class="fa fa-eye"> </i>
                                    </a>
                           
                                </div>
                            </td>
                        </tr>
                    @empty
                        <td colspan="5"> There is no record for transactions data!</td>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <!-- /.box -->

    </section>
    <!-- /.content -->
@endsection