@extends('app')

@section('content')
    @include('partials.content_header', ['pageTitle' => 'Socio Economic Status', 'pageDescription' => 'List of socio economic status', 'breadcrumbs' => ['Socio Economic Status' => false]])

    <!-- Main content -->
    <section class="content">

        <!-- Default box -->
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">
                </h3>

                <div class="box-tools pull-right">
                    @cando('Ses.Create')
                    <a href="{{ admin_route_url('ses.create') }}" class="btn btn-box-tool" data-toggle="tooltip"
                       title="Create New">
                        <i class="fa fa-plus-circle fa-btn"></i> Create New</a>
                    @endcando
                </div>
            </div>
            <div class="box-body">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>Code</th>
                        <th>Range</th>
                        <th width="250"></th>
                    </tr>
                    </thead>
                    <tbody>
                    @php
                        setlocale(LC_MONETARY, 'en_US');
                    @endphp
                    @forelse($ses as $item)
                        <tr>
                            <td>
                                {{ $item->code }}
                            </td>
                            <td>
                                Rp {{ number_format($item->range_start) }} - Rp {{ number_format($item->range_end) }}
                            </td>
                            <td class="text-right vertical-middle">
                                <div class="btn-group">
                                    @cando('Ses.Update')
                                    <a href="{{ admin_route_url('ses.edit', ['id' => $item->id]) }}"
                                       class="btn btn-info">
                                        <i class="fa fa-pencil"> </i>
                                    </a>
                                    @endcando
                                    @cando('Ses.Delete')
                                    <a class="btn btn-danger"
                                       href="{{ admin_route_url('ses.destroy', ['id' => $item->id]) }}"
                                       data-toggle="modal"
                                       data-target="#"
                                       title="Delete this data"
                                       for-delete="true"
                                       data-message="Are you sure you want to delete this SES ?"
                                    > <i class="fa fa-trash"></i> </a>
                                    @endcando
                                </div>
                            </td>
                        </tr>
                    @empty
                        <td colspan="4"> There is no record for socio economic status data!</td>
                    @endforelse
                    </tbody>
                </table>
                {{ $ses->links() }}
            </div>
        </div>
        <!-- /.box -->

    </section>
    <!-- /.content -->
@endsection
