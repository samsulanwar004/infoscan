@extends('app')

@section('content')
    @include('partials.content_header', ['pageTitle' => 'Point', 'pageDescription' => 'Point Manager', 'breadcrumbs' => ['Points' => false]])

    <!-- Main content -->
    <section class="content">

        <!-- Default box -->
        <div class="box">
            <div class="box-header with-border form-inline" style="overflow: hidden; height: 45px;">
                <div class="box-tools-new">
                    <a href="{{ admin_route_url('points.manager') }}" class="btn btn-box-tool"
                        data-toggle="modal"
                        data-target="#"
                        modal-size="modal-lg"
                        title="Manager Points">
                        <i class="fa fa-plus-circle fa-btn"></i>Point Manager</a>
                </div>
                <div id="create-task">
                    <div class="box-tools pull-right">
                        @cando('Points.Create')
                        <a href="{{ admin_route_url('points.create') }}" class="btn btn-box-tool"
                            data-toggle="modal"
                            data-target="#"
                            modal-size="modal-lg"
                            title="Create New">
                            <i class="fa fa-plus-circle fa-btn"></i> Create New</a>
                        @endcando
                    </div>
                </div>

                <div id="create-limit" style="display: none;">
                    <div class="box-tools pull-right">
                        @cando('Points.Create')
                        <a href="{{ admin_route_url('task.limit.create') }}" class="btn btn-box-tool"
                            data-toggle="modal"
                            data-target="#"
                            title="Create New">
                            <i class="fa fa-plus-circle fa-btn"></i> Create New</a>
                        @endcando
                    </div>
                </div>
            </div>
            <div class="box-header with-border form-inline" style="overflow: hidden; height: 45px;">
                <ul class="nav nav-tabs">
                    <li class="active"><a data-toggle="tab" href="#task-point" id="task">Task Point</a></li>
                    <li><a data-toggle="tab" href="#task-limit" id="limit">Task Limit</a></li>
                </ul>
            </div>
            <a href="#" style="display: none;"
                data-toggle="modal"
                data-target="#"
                id="modal-edit"
                title="Edit">
                Edit Select Row</a>
            <div class="box-body">
                <div class="tab-content">
                    <div id="task-point" class="tab-pane fade in active">
                        <table id="point-pivot" style="width:700px;height:300px"></table>
                    </div>
                    <div id="task-limit" class="tab-pane fade">
                        <table id="point-limit" style="width:700px;height:300px"></table>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.box -->

    </section>
    <!-- /.content -->
@endsection

@section('footer_scripts')
<style type="text/css">
    .box-tools-new {
        position: absolute;
        top: 5px;
    }
</style>
<script type="text/javascript">
    $(function() {
         loadPointData();
    });
/*
    function loadPointData() {
        $('#point-pivot').pivotgrid({
            //url:'/js/pivotgrid_data1.json',
            url:'/points/get-task-table',
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
            },
            onDblClickRow: function(row) {
                var link = $('#modal-edit');
                var mystr = row._tree_field;
                var myarr = mystr.split(" ");
                var nameLink = 'points/'+myarr[0]+'/edit';
                link.attr('href', nameLink);

                link.trigger('click');
            }
        });
    }
*/

    function loadPointData() {
        $('#point-pivot').pivotgrid({
            //url:'/js/pivotgrid_data1.json',
            url:'/points/get-task-table',
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
            },
            onDblClickRow: function(row) {
                var link = $('#modal-edit');
                var mystr = row._tree_field;
                var myarr = mystr.split(" ");
                var nameLink = 'points/'+myarr[0]+'/edit';
                link.attr('href', nameLink);

                link.trigger('click');
            }
        });
    }

    $(document).ready(function() {
        $('#limit').on('click', function() {
            $('#point-limit').pivotgrid({
                //url:'/js/pivotgrid_data1.json',
                url:'/points/get-task-limit',
                method:'get',
                width: '100%',
                height: '450',
                pivot:{
                    rows:['Task'],
                    columns:['limit_name'],
                    values:[
                        {field:'Limit'},
                    ]
                },
                forzenColumnTitle:'<span style="font-weight:bold; padding-left: 110px;">Snap Task</span>',
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
                    var nameLink = '/point/limit/'+myarr[0]+'/edit';
                    link.attr('href', nameLink);

                    link.trigger('click');
                }
            });
        });

        $('#task').on('click', function() {
            loadPointData();
        });
    });

    $('#task').on('click', function() {
        $('#create-task').show();
        $('#create-limit').hide();
    });

    $('#limit').on('click', function() {
        $('#create-task').hide();
        $('#create-limit').show();
    });

    $(function() {
        // for bootstrap 3 use 'shown.bs.tab', for bootstrap 2 use 'shown' in the next line
        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            // save the latest tab; use cookies if you like 'em better:
            localStorage.setItem('lastTab', $(this).attr('href'));
        });

        // go to the latest tab, if it exists:
        var lastTab = localStorage.getItem('lastTab');
        if (lastTab) {
            $('[href="' + lastTab + '"]').tab('show');

            if (lastTab == '#task-point') {
                $('#task').click();
            } else {
                $('#limit').click();
            }
        }
    });

</script>
@stop