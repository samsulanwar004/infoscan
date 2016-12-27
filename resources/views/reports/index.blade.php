@extends('app')

@section('content')
    @include('partials.content_header', ['pageTitle' => 'Report', 'pageDescription' => 'List of Report Table', 'breadcrumbs' => ['Report' => false]])
    <section class="content">
        <div class="box">
            <div class="box-body" id="form-body">
                <form role="form" action="" method="post" accept-charset="utf-8">
                    <div class="box-body" id="form-body">
                        <div class="text-right">
                            <div class="btn-group">
                                <a class="btn btn-primary btn-sm" id="filters" href="{{ admin_route_url('reports.filters') }}?attributes=@foreach($dataAttributes as $data){!! $data !!},@endforeach" data-toggle="modal" data-target="#" title="Filters"> 
                                    Filters 
                                </a>
                            </div>
                            <div class="btn-group">
                                <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown" aria-expended="false">
                                    Format                                   
                                </button>
                                <ul class="dropdown-menu" role="menu" style="width: 200px">
                                    <li role="presentation">
                                        <a role="menuitem" id="pdf" title="PDF" href="{{ admin_route_url('reports.formatPdf') }}?attributes=@foreach($dataAttributes as $data){!! $data !!},@endforeach">PDF</a>
                                    </li>
                                    <li role="presentation">
                                        <a role="menuitem" id="excel" title="Excel" href="{{ admin_route_url('reports.formatExcel') }}?attributes=@foreach($dataAttributes as $data){!! $data !!},@endforeach">Excel</a>
                                    </li>
                                    <li role="presentation">
                                        <a role="menuitem" id="word" title="Word" href="{{ admin_route_url('reports.formatWord') }}?attributes=@foreach($dataAttributes as $data){!! $data !!},@endforeach">Word</a>
                                    </li>
                                    <li role="presentation">
                                        <a role="menuitem" id="image" title="Image" href="{{ admin_route_url('reports.formatImage') }}?attributes=@foreach($dataAttributes as $data){!! $data !!},@endforeach">Image</a>
                                    </li>
                                <ul>                                        
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