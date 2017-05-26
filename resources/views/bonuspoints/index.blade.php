@extends('app')

@section('content')
    @include('partials.content_header', ['pageTitle' => 'Bonus Point', 'pageDescription' => 'Bonus Point Manager', 'breadcrumbs' => ['Points' => false]])

    <!-- Main content -->
    <section class="content">

        <!-- Default box -->
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">
                </h3>

                <div class="box-tools pull-right">
                    @cando('Points.Create')
                    <a href="{{ admin_route_url('bonus-points.create') }}" class="btn btn-box-tool"
                        data-toggle="modal"
                        data-target="#"
                        modal-size="modal-lg"
                        title="Create New">
                        <i class="fa fa-plus-circle fa-btn"></i> Create New</a>
                    @endcando
                </div>
            </div>
            <a href="#"  style="display: none;" 
                data-toggle="modal"
                data-target="#"
                modal-size="modal-lg" 
                id="modal-edit" 
                title="Edit">
                Edit Select Row</a>
            <div class="box-body">
                <table id="point-pivot" style="width:700px;height:300px"></table>
            </div>
        </div>
        <!-- /.box -->

    </section>
    <!-- /.content -->
@endsection

@section('footer_scripts')
<link rel="stylesheet" href="{{ elixirCDN('css/report-vendor.css') }}" />
<script src="{{ elixirCDN('js/report-vendor.js') }}"></script>
<script type="text/javascript">
    $(function() {
         loadPointData();
    });

    function loadPointData() {
        $('#point-pivot').pivotgrid({
            //url:'/js/pivotgrid_data1.json',
            url:'/bonus-points',
            method:'get',
            width: '100%',
            height: '450',
            pivot:{
                rows:['Bonus'],
                columns:['Level'],
                values:[
                    {field:'Point'},
                ]
            },
            forzenColumnTitle:'<span style="font-weight:bold; padding-left: 110px;">Bonus Name</span>',
            forzenColumnWidth: 300,
            valuePrecision:0,
            valueStyler:function(value,row,index){
                if (/Point$/.test(this.field) && value<1){
                    return 'background:#dd4b39;color:#ffffff'
                }
            },
            onDblClickRow: function(row) {
                var link = $('#modal-edit');
                var mystr = row._tree_field;
                var myarr = mystr.split(" ");
                var nameLink = 'bonus-points/'+myarr[0]+'/edit';
                link.attr('href', nameLink);

                link.trigger('click');
            }
        });
    }
</script>
@stop