@extends('app')

@section('content')
    @include('partials.content_header', ['pageTitle' => 'Exchange Rates', 'pageDescription' => 'List of exchange rates', 'breadcrumbs' => ['Exchange rates' => false]])

    <!-- Main content -->
    <section class="content">

        <!-- Default box -->
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">
                </h3>

                <div class="box-tools pull-right">
                    @cando('Exchange.Create')
                    <a href="{{ admin_route_url('exchange.create') }}" class="btn btn-box-tool" data-toggle="tooltip"
                       title="Create New">
                        <i class="fa fa-plus-circle fa-btn"></i> Create New</a>
                    @endcando
                </div>
            </div>
            <div class="box-body">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th width="100">Cash</th>
                        <th width="100">Point</th>
                        <th width="100">Date</th>
                        <th width="250"></th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($rates as $item)
                        <tr>
                            <td>
                                Rp {{ number_format($item->cash_per_unit) }}
                            </td>
                            <td>
                                {{ number_format($item->point_unit_count) }} Pts
                            </td>
                            <td>
                                {{ date_format(date_create($item->created_at), 'M, d Y') }}
                            </td>
                            <td class="text-right vertical-middle">
                                <div class="btn-group">
                                    @cando('Exchange.Update')
                                    <a href="{{ admin_route_url('exchange.edit', ['id' => $item->id]) }}"
                                       class="btn btn-info">
                                        <i class="fa fa-pencil"> </i>
                                    </a>
                                    @endcando
                                    @cando('Exchange.Delete')
                                    <a class="btn btn-danger"
                                       href="{{ admin_route_url('exchange.destroy', ['id' => $item->id]) }}"
                                       data-toggle="modal"
                                       data-target="#"
                                       title="Delete this data"
                                       for-delete="true"
                                       data-message="Are you sure you want to delete this Exchange ?"
                                    > <i class="fa fa-trash"></i> </a>
                                    @endcando
                                </div>
                            </td>
                        </tr>
                    @empty
                        <td colspan="4"> There is no record for exchange rates data!</td>
                    @endforelse
                    </tbody>
                </table>
                {{ $rates->links() }}
            </div>
        </div>
        <!-- /.box -->

    </section>
    <!-- /.content -->
@endsection
