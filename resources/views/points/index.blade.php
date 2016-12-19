@extends('app')

@section('content')
    @include('partials.content_header', ['pageTitle' => 'Point', 'pageDescription' => 'Point Manager', 'breadcrumbs' => ['Points' => false]])

    <!-- Main content -->
    <section class="content">

        <!-- Default box -->
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">
                </h3>

                <div class="box-tools pull-right">
                </div>
            </div>
            <div class="box-body">
                <table id="point-pivot" style="width:700px;height:300px"></table>
            </div>
        </div>
        <!-- /.box -->

    </section>
    <!-- /.content -->
@endsection

@section('footer_scripts')
<script type="text/javascript">
    $(function() {
        $('#point-pivot').pivotgrid({
            //url:'/js/pivotgrid_data1.json',
            url:'/points',
            method:'get',
            width: '100%',
            height: '450',
            pivot:{
                rows:['Task'],
                columns:['Level'],
                values:[
                    {field:'Point'},
                ]
            },
            forzenColumnTitle:'<span style="font-weight:bold; padding-left: 110px;">Snap Task</span>',
            forzenColumnWidth: 300,
            valuePrecision:0,
            valueStyler:function(value,row,index){
                if (/Point$/.test(this.field) && value<1){
                    return 'background:#dd4b39;color:#ffffff'
                }
            }
        });

    });
</script>
@stop