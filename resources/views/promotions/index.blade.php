@extends('app')

@section('content')
    @include('partials.content_header', ['pageTitle' => 'Promotion', 'pageDescription' => 'List of promo', 'breadcrumbs' => ['Promotions' => false]])

    <!-- Main content -->
    <section class="content">

        <!-- Default box -->
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">
                </h3>

                <div class="box-tools pull-right">
                    @cando('Promotion.Create')
                    <a href="{{ route('promotions.create') }}" class="btn btn-box-tool" data-toggle="tooltip"
                       title="Create New">
                        <i class="fa fa-plus-circle fa-btn"></i> Create New</a>
                    @endcando
                </div>
            </div>
            <div class="box-body">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th width="50">#</th>
                        <th width="300">Title of Promo</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th width="250"></th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($promos as $promo)
                        <tr>
                            <td class="vertical-middle">
                                <i class="fa fa-check-circle {{ $promo->is_active == 1 ? 'text-green' : 'text-default' }}"></i>
                            </td>
                            <td class="vertical-middle">
                                {{ $promo->title }} <br>
                            </td>
                            <td class="vertical-middle">{{ date_format(date_create($promo->start_at), 'M, d Y') }}</td>
                            <td class="vertical-middle">{{ date_format(date_create($promo->end_at), 'M, d Y') }}</td>
                            <td class="text-right vertical-middle">
                                <div class="btn-group">
                                    @cando('Promotion.Update')
                                    <a href="{{ route('promotions.edit', ['id' => $promo->id]) }}" class="btn btn-info">
                                        <i class="fa fa-pencil"> </i>
                                    </a>
                                    @endcando

                                    @cando('Promotion.Delete')
                                    <a class="btn btn-danger"
                                       href="{{ route('promotions.destroy', ['id' => $promo->id]) }}"
                                       data-toggle="modal"
                                       data-target="#"
                                       title="Delete this data"
                                       for-delete="true"
                                       data-message="Are you sure you want to delete this promotion ?"
                                    > <i class="fa fa-trash"></i> </a>
                                    @endcando
                                </div>
                            </td>
                        </tr>
                    @empty
                        <td colspan="5"> There is no record for promotions data!</td>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <!-- /.box -->

    </section>
    <!-- /.content -->
@endsection