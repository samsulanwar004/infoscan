@extends('app')

@section('content')
    @include('partials.content_header', ['pageTitle' => 'Payment Portal', 'pageDescription' => 'List of payment portal', 'breadcrumbs' => ['Payment Portal' => false]])

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
                        <th>#</th>
                        <th>Point Redeem</th>
                        <th>Cashout</th>
                        <th>Bank Account</th>
                        <th>Account Number</th>
                        <th>Date</th>
                        <th width="250"></th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($payment as $item)
                        <tr>
                            <td class="vertical-middle">
                                <i class="fa fa-check-circle {{  isset($item->approved_by) ? 'text-green' : 'text-default' }}"></i>
                            </td>
                            <td class="vertical-middle">
                                {{ number_format($item->point) }} Pts
                            </td>
                            <td class="vertical-middle">
                                Rp {{ number_format($item->cashout) }}
                            </td>
                            <td class="vertical-middle">
                                {{ $item->bank_account }}
                            </td>
                            <td class="vertical-middle">
                                {{ $item->account_number }}
                            </td>
                            <td class="vertical-middle">
                                {{ date_format(date_create($item->created_at), 'M, d Y') }}
                            </td>
                            <td class="text-right vertical-middle">
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
                                </div>
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
