@extends('app')
@section('content')
    @include('partials.content_header', ['pageTitle' => 'Report', 'pageDescription' => 'List of Report Table', 'breadcrumbs' => ['Report' => false]])
    <section class="content">
        <div class="box">
            <form role="form" action="{{ admin_route_url('reports.store') }}" method="post" enctype="multipart/form-data" class="form" accept-charset="utf-8">
            {!! csrf_field() !!}
            <div class="box-header with-border">
                <h3 class="box-title"></h3>
                <div class="btn-group pull-right">
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal" title="Filters"> 
                        Filters 
                    </button>
                    <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown" aria-expended="false">
                        Format                                   
                    </button>
                    <ul class="dropdown-menu" role="menu" style="width: 200px">
                        <li role="presentation">
                            <a role="menuitem" id="pdf" title="PDF" href="{{ admin_route_url('reports.formatPdf') }}">PDF</a>
                            <!--<button type="submit" class="btn btn-default" id="submit">PDF</button>-->
                        </li>
                        <li role="presentation">
                            <a role="menuitem" id="excel" title="Excel" href="{{ admin_route_url('reports.formatExcel') }}">Excel</a>
                        </li>
                        <li role="presentation">
                            <a role="menuitem" id="word" title="Word" href="{{ admin_route_url('reports.formatWord') }}">Word</a>
                        </li>
                    </ul>                                        
                </div>
            </div>
            <div class="box-body" id="form-body">
                <div class="box-body" id="form-body">
                    <table class="table table-striped" id="tableAttributes">
                        <thead>
                            <tr>
                                <th class="outlet_name" name="outlet_name">Outlet Name</th>
                                <th class="products" name="products">Products</th>
                                <th class="users_city" name="users_city">User's City</th>
                                <th class="age" name="age">Age</th>
                                <th class="outlet_area" name="outlet_area">Outlet Area</th>
                                <th class="province" name="province">Province</th>
                                <th class="gender" name="gender">Gender</th>
                                <th class="usership" name="usership">Usership</th>
                                <th class="sec" name="sec">SEC</th>
                                <th class="outlet_type" name="outlet_type">Outlet Type</th>
                                <input type="hidden" id="outlet_name" name="outlet_name" value="outlet_name">                                
                                <input type="hidden" id="products" name="products" value="products">
                                <input type="hidden" id="users_city" name="users_city" value="users_city">
                                <input type="hidden" id="age" name="age" value="age">
                                <input type="hidden" id="outlet_area" name="outlet_area" value="outlet_area">
                                <input type="hidden" id="province" name="province" value="province">
                                <input type="hidden" id="gender" name="gender" value="gender">
                                <input type="hidden" id="usership" name="usership" value="usership">
                                <input type="hidden" id="sec" name="sec" value="sec">
                                <input type="hidden" id="outlet_type" name="outlet_type" value="outlet_type">
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($reports as $item)
                                <tr>
                                    <td>{{ $item->outlet_name }}</td>
                                    <td>{{ $item->products }}</td>
                                    <td>{{ $item->users_city }}</td>
                                    <td>{{ $item->age }}</td>
                                    <td>{{ $item->outlet_area }}</td>
                                    <td>{{ $item->province }}</td>
                                    <td>{{ $item->gender }}</td>
                                    <td>{{ $item->usership }}</td>
                                    <td>{{ $item->sec }}</td>
                                    <td>{{ $item->outlet_type }}</td>
                                </tr>
                            @empty
                                <td colspan="10"> There Is No Record For Report Status Data !!!</td>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            </form>
        </div>
        <div class="modal fade" id="myModal" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Customizable Atributes</h4>
                    </div>
                    <form role="form" action="{{ admin_route_url('reports.filters') }}" method="post" enctype="multipart/form-data" class="form" accept-charset="utf-8">
                        <div class="modal-body">
                            <div class="form-group" id="checkAttributes">
                                <input style="cursor:pointer" type="checkbox" id="outlet_name" name="outlet_name" class="outlet_name" value="outlet_name" /> Outlet Name<br>
                                <input style="cursor:pointer" type="checkbox" id="products" name="products" class="products" value="products" /> Products<br>
                                <input style="cursor:pointer" type="checkbox" id="users_city" name="users_city" class="users_city" value="users_city" /> Users City<br>
                                <input style="cursor:pointer" type="checkbox" id="age" name="age" class="age" value="age" /> Age<br>
                                <input style="cursor:pointer" type="checkbox" id="outlet_area" name="outlet_area" class="outlet_area" value="outlet_area" /> Outlet Area<br>
                                <input style="cursor:pointer" type="checkbox" id="province" name="province" class="province" value="province" /> Province<br>
                                <input style="cursor:pointer" type="checkbox" id="gender" name="gender" class="gender" value="gender" /> Gender<br>
                                <input style="cursor:pointer" type="checkbox" id="usership" name="usership" class="usership" value="usership" /> Usership<br>
                                <input style="cursor:pointer" type="checkbox" id="sec" name="sec" class="sec" value="sec" /> SEC<br>
                                <input style="cursor:pointer" type="checkbox" id="outlet_type" name="outlet_type" class="outlet_type" value="outlet_type" /> Outlet Type
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </form>
                </div>   
            </div>
        </div>        
    </section>
@endsection
@section('footer_scripts')
    <script type="text/javascript">
        $(function () {
            var $chk = $("#checkAttributes input:checkbox"); 
            var $tbl = $("#tableAttributes");
            var $tblhead = $("#tableAttributes th");
            $chk.prop('checked', true); 
            $chk.click(function () {
                var colToHide = $tblhead.filter("." + $(this).attr("name"));
                var index = $(colToHide).index();
                $tbl.find('tr :nth-child(' + (index + 1) + ')').toggle();
            });
        });
    </script>
@endsection