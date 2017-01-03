@extends('app')
@section('content')
    @include('partials.content_header', ['pageTitle' => 'Report', 'pageDescription' => 'List of Report Table', 'breadcrumbs' => ['Report' => false]])
    <section class="content">
        <div class="box">
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
                            <a role="menuitem" id="pdf" class="pdf" title="PDF" href="#">PDF</a>
                        </li>
                        <li role="presentation">
                            <a role="menuitem" id="excel" class="excel" title="Excel" href="#">Excel</a>
                        </li>
                        <li role="presentation">
                            <a role="menuitem" id="word" class="word" title="Word" href="#">Word</a>
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
                                <tr>
                                    <td colspan="10"> There Is No Record For Report Status Data !!!</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="modal fade" id="myModal" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Customizable Atributes</h4>
                    </div>
                    <form role="form" action="#" method="post" enctype="multipart/form-data" class="form" accept-charset="utf-8">
                        {{ csrf_field() }}
                        <div class="modal-body">
                            <div class="form-group" id="checkAttributes">
                                <input style="cursor:pointer" type="checkbox" id="outlet_name" name="outlet_name" class="outlet_name" value="Outlet Name" /> Outlet Name<br>
                                <input style="cursor:pointer" type="checkbox" id="products" name="products" class="products" value="Products" /> Products<br>
                                <input style="cursor:pointer" type="checkbox" id="users_city" name="users_city" class="users_city" value="User's City" /> User's City<br>
                                <input style="cursor:pointer" type="checkbox" id="age" name="age" class="age" value="Age" /> Age<br>
                                <input style="cursor:pointer" type="checkbox" id="outlet_area" name="outlet_area" class="outlet_area" value="Outlet Area" /> Outlet Area<br>
                                <input style="cursor:pointer" type="checkbox" id="province" name="province" class="province" value="Province" /> Province<br>
                                <input style="cursor:pointer" type="checkbox" id="gender" name="gender" class="gender" value="Gender" /> Gender<br>
                                <input style="cursor:pointer" type="checkbox" id="usership" name="usership" class="usership" value="Usership" /> Usership<br>
                                <input style="cursor:pointer" type="checkbox" id="sec" name="sec" class="sec" value="SEC" /> SEC<br>
                                <input style="cursor:pointer" type="checkbox" id="outlet_type" name="outlet_type" class="outlet_type" value="Outlet Type" /> Outlet Type
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
        function showValues() {
            //var fields = checkCookie();
            //console.log(fields);
            var fields = $("input:checkbox").serializeArray();           
            //console.log(fields);
            $("#results").empty();
            jQuery.each( fields, function(i, field) {
                $("#results").append(field.value + ",");
            });
            //localStorage.setItem("keyFields", JSON.stringify(fields));
            //fields = localStorage.getItem("keyFields");
            fields = JSON.stringify(fields);
            return fields;
        }
        $(":checkbox").click(showValues);
        $('.pdf').click(function() {
            window.location.href = "http://infoscan.dev/reports/formatPdf?attributes=" + showValues();
        });
        $('.word').click(function() {
            window.location.href = "http://infoscan.dev/reports/formatWord?attributes=" + showValues();
        });
        $('.excel').click(function() {
            window.location.href = "http://infoscan.dev/reports/formatExcel?attributes=" + showValues();
        });   
        function setCookie(cname, cvalue, exdays) {
            var d = new Date();
            d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
            var expires = "expires=" + d.toUTCString();
            document.cookie = cname + "=" + cvalue + "; " + expires + ";path=/";
        }
        function getCookie(cname) {
            var name = cname + "=";
            var ca = document.cookie.split(';');
            for (var i = 0; i < ca.length; i++) {
                var c = ca[i];
                while (c.charAt(0) == ' ') {
                    c = c.substring(1);
                }
                if (c.indexOf(name) == 0) {
                    return c.substring(name.length, c.length);
                }
            }
            return "";
        }
        function checkCookie() {
            var getCookies = getCookie(showValues());
            setCookie(showValues(), getCookies, 365);
        }    
    </script>
@endsection