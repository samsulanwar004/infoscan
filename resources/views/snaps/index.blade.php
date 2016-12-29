@extends('app')

@section('content')
    @include('partials.content_header', [
        'pageTitle' => 'Snaps',
        'pageDescription' => 'List of all Snaps',
        'breadcrumbs' => ['Snaps' => false]
    ])

    <!-- Main content -->
    <section class="content">

        <!-- Default box -->
        <div class="box">
            <div class="box-header with-border form-inline" style="overflow: hidden; height: 45px;">

                <div class="box-tools pull-right ">
                    Filter Snap type: 
                    <select class="form-control" onchange="filterBySnap(this.value)">
                        <option value="all" id="all">All</option>
                        @foreach($snapCategorys as $id => $name)
                            <option value="{{$id}}" id="{{$id}}">{{$name}}</option>
                        @endforeach                                                                   
                    </select>

                    Filter Mode type:
                    <select class="form-control" onchange="filterByMode(this.value)">
                        <option value="all">All</option>
                        @foreach($snapCategoryModes as $id => $name)
                            <option value="{{$id}}" id="{{$id}}">{{$name}}</option>
                        @endforeach 
                    </select>
                </div>
            </div>
            <div class="box-body">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th width="25">#</th>
                        <th width="100">Code</th>
                        <th>Snap Details</th>
                        <th width="250"></th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($snaps as $snap)
                        <tr>
                            <td class="vertical-middle">
                                <i class="fa fa-check-circle {{ $snap->is_active == 1 ? 'text-green' : 'text-default' }}"></i>
                            </td>
                            <td class="vertical-middle">
                                {{ strtoupper($snap->request_code) }} <br>
                            </td>
                            <td class="vertical-middle">
                                <span>
                                    <b>{{ strtoupper($snap->snap_type) }}</b> Snap {{$snap->snap_type !== 'receipt' && (!is_null($snap->mode_type) || !empty($snap->snap_type)) ? 'with ' . strtoupper($snap->mode_type) . ' mode.' : '' }}
                                </span>
                                <br>
                                <span class="small">
                                    Total image: {{ $snap->files()->count() }}
                                </span>

                                <span class="small ml10">Uploaded by: <a href="{{ admin_route_url('members.show', ['id' => $snap->member->id]) }}">{{ $snap->member->email }}</a></span>
                            </td>
                            <td class="text-right vertical-middle">
                                <div class="btn-group">
                                    @cando('Snaps.Show')
                                    <a href="{{ admin_route_url('snaps.show', ['id' => $snap->id]) }}" class="btn btn-primary">
                                        <i class="fa fa-search"> </i>
                                    </a>
                                    @endcando
                                </div>
                            </td>
                        </tr>
                    @empty
                        <td colspan="4"> There is no record for snaps data!</td>
                    @endforelse                    
                    </tbody>
                </table>
            </div>
        </div>
        <!-- /.box -->

    </section>
    <!-- /.content -->
@endsection

@section('footer_scripts')
<script>
    $(function() {
       getSelected();
    });

    function getSelected()
    {
        document.getElementById("{{$type}}").selected = true;
    }
    
    function filterBySnap(attr)
    {
        if (attr == 'all')
        {
            return window.location.href = '/snaps';
        }
        console.log(attr);
        var attr = attr;
        var link = '/snaps/'+attr+'/filter';
        window.location.href = link;
    }

    function filterByMode(attr)
    {
        if (attr == 'all')
        {
            return window.location.href = '/snaps';
        }
        console.log(attr);
        var attr = attr;
        var link = '/snaps/'+attr+'/filter';
        window.location.href = link;
    }

</script>
@stop