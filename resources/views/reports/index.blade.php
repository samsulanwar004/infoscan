@extends('app')

@section('content')
    @include('partials.content_header', ['pageTitle' => 'Report', 'pageDescription' => 'List of Report Table', 'breadcrumbs' => ['Report' => false]])
    <section class="content">
        <div class="box">
            <div class="box-body" id="form-body">
                <form role="form" action="" method="" accept-charset="utf-8">
                    <div class="box-body" id="form-body">
                        <div class="text-right">
                            <div class="btn-group">
                                <a class="btn btn-primary btn-sm" id="filters" href="{{ admin_route_url('reports.filters') }}?attributes=@foreach($dataAttributes as $data){!! $data !!},@endforeach" data-toggle="modal" data-target="#" title="Filters"> 
                                    <i class="fa fa-pencil"></i> 
                                </a>
                            </div>
                            <div class="btn-group">
                                <a class="btn btn-warning btn-sm" id="pdf" href="{{ admin_route_url('reports.formatPdf') }}?attributes=@foreach($dataAttributes as $data){!! $data !!},@endforeach" title="PDF"> 
                                    <i class="fa fa-pencil"></i> 
                                </a>
                            </div>
                            <div class="btn-group">
                                <a class="btn btn-warning btn-sm" id="excel" href="{{ admin_route_url('reports.formatExcel') }}?attributes=@foreach($dataAttributes as $data){!! $data !!},@endforeach" title="Excel"> 
                                    <i class="fa fa-pencil"></i> 
                                </a>
                            </div>
                            <div class="btn-group">
                                <a class="btn btn-warning btn-sm" id="word" href="{{ admin_route_url('reports.formatWord') }}?attributes=@foreach($dataAttributes as $data){!! $data !!},@endforeach" title="Word"> 
                                    <i class="fa fa-pencil"></i> 
                                </a>
                            </div>
                            <div class="btn-group">
                                <a class="btn btn-warning btn-sm" id="image" href="{{ admin_route_url('reports.formatImage') }}?attributes=@foreach($dataAttributes as $data){!! $data !!},@endforeach" title="Image"> 
                                    <i class="fa fa-pencil"></i> 
                                </a>
                            </div>
                        </div>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    @foreach ($dataAttributes as $data)
                                        <th>{!! $data !!}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    @foreach ($dataAttributes as $data)
                                        <th>&nbsp;</th>
                                    @endforeach
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection