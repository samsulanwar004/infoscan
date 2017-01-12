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
                    <select class="form-control snap-type">
                        <option value="all">All</option>
                        @foreach($snapCategorys as $id => $name)
                            <option value="{{$id}}" id="{{$id}}">{{$name}}</option>
                        @endforeach                                                                   
                    </select>

                    Filter Mode type:
                    <select class="form-control snap-mode">
                        <option value="all">All</option>
                        @foreach($snapCategoryModes as $id => $name)
                            <option value="{{$id}}" id="{{$id}}">{{$name}}</option>
                        @endforeach 
                    </select>
                </div>
            </div>
            <div class="box-body">
                @include('snaps.table_snaps')
            </div>
        </div>
        <!-- /.box -->

    </section>
    <!-- /.content -->
@endsection

@section('footer_scripts')
<script>

    $(".snap-type").on("change", function() {

        var type = $(this).val();
        var mode = $(".snap-mode").val();

        $.get( '/snaps/'+type+'/'+mode+'/filter' , function(view){ 
            $(".box-body").html(view);
        });

    });

    $(".snap-mode").on("change", function() {

        var mode = $(this).val();
        var type = $(".snap-type").val();

        $.get( '/snaps/'+type+'/'+mode+'/filter' , function(view){ 
            $(".box-body").html(view);
        });

    });
    
</script>
@stop