@extends('app')
@section('content')
    @include('partials.content_header', ['pageTitle' => 'Snaps Report', 'pageDescription' => 'List of Snaps Report Table', 'breadcrumbs' => ['Report' => false]])
    <section class="content">
        <div class="box">
            <form role="form" action="#" method="post" enctype="multipart/form-data" class="form" accept-charset="utf-8">
                {{ csrf_field() }}
                <div class="box-header with-border">
                    <h3 class="box-title"></h3>
                    <div class="btn-group pull-left">
                        <button class="btn btn-default" type="button" aria-expended="false" style="height: 38px;">
                            <input class="dates form-control" id="startDate" name="startDate" style="width: 100px; height: 24px;" type="text" value="{{ $startDate }}">
                        </button>
                        <button class="btn btn-default" type="button" aria-expended="false" style="height: 38px;">
                            <input class="dates form-control" id="endDate" name="endDate"  style="width: 100px; height: 24px;" type="text" value="{{ $endDate }}">
                        </button>
                        <button class="btn btn-default" type="text" aria-expended="false" style="height: 38px;">
                            <a id="search" class="search" title="Submit Snaps Date" href="#">
                                <i class="fa fa-search"></i>
                            </a>
                        </button>                    
                    </div>
                    <div class="btn-group pull-right">
                        <button type="button" class="btn btn-default" data-toggle="modal" data-target="#filtersAttributes" title="Filters Attributes" style="height: 38px;"> 
                            <a id="filtersAttr" class="filtersAttr" title="Filters Attributes" href="#">
                                <i class="fa fa-filter"></i>                            
                            </a>
                        </button>
                        <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown" aria-expended="false" style="height: 38px; width: 36px;">
                            <a id="downloads" class="downloads" title="Downloads" href="#">
                                <i class="fa fa-download"></i>
                            </a>
                        </button>
                        <button type="button" class="btn btn-default" title="Maps" style="height: 38px;"> 
                            <a id="maps" class="maps" title="Maps" href="#">
                                <i class="fa fa-map-marker"></i>
                            </a>
                        </button>
                        <ul class="dropdown-menu" role="menu" style="width:10px; text-align:center;">
                            <li role="presentation">
                                <a role="menuitem" id="pdf" class="pdf" title="PDF" href="#">
                                    <i class="fa fa-file-pdf-o"></i><span>PDF</span>
                                </a>
                            </li>
                            <li role="presentation">
                                <a role="menuitem" id="excel" class="excel" title="Excel" href="#">
                                    <i class="fa fa-file-excel-o"></i><span>Excel</span>
                                </a>                                
                            </li>
                            <li role="presentation">
                                <a role="menuitem" id="word" class="word" title="Word" href="#">
                                    <i class="fa fa-file-word-o"></i><span>Word</span>
                                </a>                                
                            </li>
                        </ul>                                        
                    </div>
                </div>
                <div class="box-body" id="form-body">
                    <div class="box-body" id="form-body">
                        <table class="table table-striped" id="tableAttributes">
                            <thead>
                                <tr>
                                    <th class="no" name="no">No.</th>
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
                                @php 
                                    $i = 1; 
                                @endphp
                                @forelse($reports as $item)
                                    <tr>
                                        <td>{{ $i++ }}.</td>
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
            </form>
        </div>
        <div class="modal fade" id="filtersAttributes" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Customizable Atributes</h4>
                    </div>
                    <form role="form" action="#" method="post" enctype="multipart/form-data" class="form" accept-charset="utf-8">
                        {{ csrf_field() }}
                        <div class="modal-body">
                            <div class="form-group col-md-6" id="checkAttributes1">
                                <span text-align="center"><input style="cursor:pointer" type="checkbox" id="outlet_name" name="outlet_name" class="outlet_name" value="Outlet Name" /> Outlet Name</span>
                            </div>
                            <div class="col-md-6" id="selectAttributes1">
                                <select id="val1" name="val1" multiple="multiple" class"form-control">
                                    @foreach($dataReports1 as $data1) 
                                        <option style="cursor:pointer" id="{{ $data1->outlet_name }}" name="{{ $data1->outlet_name }}" class="{{ $data1->outlet_name }}" value="{{ $data1->outlet_name }}"> {{ $data1->outlet_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <br><br><br><br><br>
                        </div>
                        <div class="modal-body">
                            <div class="form-group col-md-6" id="checkAttributes2">
                                <span text-align="top"><input style="cursor:pointer" type="checkbox" id="products" name="products" class="products" value="Products" /> Products</span>
                            </div>
                            <div class="col-md-6" id="selectAttributes2">
                                <select id="val2" name="val2" multiple="multiple" class"form-control">
                                    @foreach($dataReports2 as $data2) 
                                        <option style="cursor:pointer" id="{{ $data2->products }}" name="{{ $data2->products }}" class="{{ $data2->products }}" value="{{ $data2->products }}"> {{ $data2->products }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <br><br><br><br><br>
                        </div>
                        <div class="modal-body">
                            <div class="form-group col-md-6" id="checkAttributes3">
                                <span text-align="top"><input style="cursor:pointer" type="checkbox" id="users_city" name="users_city" class="users_city" value="User's City" /> User's City</span>
                            </div>
                            <div class="col-md-6" id="selectAttributes3">
                                <select id="val3" name="val3" multiple="multiple" class"form-control">
                                    @foreach($dataReports3 as $data3) 
                                        <option style="cursor:pointer" id="{{ $data3->users_city }}" name="{{ $data3->users_city }}" class="{{ $data3->users_city }}" value="{{ $data3->users_city }}"> {{ $data3->users_city }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <br><br><br><br><br>
                        </div>
                        <div class="modal-body">
                            <div class="form-group col-md-6" id="checkAttributes4">
                                <span text-align="top"><input style="cursor:pointer" type="checkbox" id="age" name="age" class="age" value="Age" /> Age</span>
                            </div>
                            <div class="col-md-6" id="selectAttributes4">
                                <select id="val4" name="val4" multiple="multiple" class"form-control">
                                    @foreach($dataReports4 as $data4) 
                                        <option style="cursor:pointer" id="{{ $data4->age }}" name="{{ $data4->age }}" class="{{ $data4->age }}" value="{{ $data4->age }}"> {{ $data4->age }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <br><br><br><br><br>
                        </div>
                        <div class="modal-body">
                            <div class="form-group col-md-6" id="checkAttributes5">
                                <span text-align="top"><input style="cursor:pointer" type="checkbox" id="outlet_area" name="outlet_area" class="outlet_area" value="Outlet Area" /> Outlet Area</span>
                            </div>
                            <div class="col-md-6" id="selectAttributes5">
                                <select id="val5" name="val5" multiple="multiple" class"form-control">
                                    @foreach($dataReports5 as $data5) 
                                        <option style="cursor:pointer" id="{{ $data5->outlet_area }}" name="{{ $data5->outlet_area }}" class="{{ $data5->outlet_area }}" value="{{ $data5->outlet_area }}"> {{ $data5->outlet_area }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <br><br><br><br><br>
                        </div>
                        <div class="modal-body">
                            <div class="form-group col-md-6" id="checkAttributes6">
                                <span text-align="top"><input style="cursor:pointer" type="checkbox" id="province" name="province" class="province" value="Province" /> Province</span>
                            </div>
                            <div class="col-md-6" id="selectAttributes6">
                                <select id="val6" name="val6" multiple="multiple" class"form-control">
                                    @foreach($dataReports6 as $data6) 
                                        <option style="cursor:pointer" id="{{ $data6->province }}" name="{{ $data6->province }}" class="{{ $data6->province }}" value="{{ $data6->province }}"> {{ $data6->province }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <br><br><br><br><br>
                        </div>
                        <div class="modal-body">
                            <div class="form-group col-md-6" id="checkAttributes7">
                                <span text-align="top"><input style="cursor:pointer" type="checkbox" id="gender" name="gender" class="gender" value="Gender" /> Gender</span>
                            </div>
                            <div class="col-md-6" id="selectAttributes7">
                                <select id="val7" name="val7" multiple="multiple" class"form-control">
                                    @foreach($dataReports7 as $data7) 
                                        <option style="cursor:pointer" id="{{ $data7->gender }}" name="{{ $data7->gender }}" class="{{ $data7->gender }}" value="{{ $data7->gender }}"> {{ $data7->gender }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <br><br><br><br><br>
                        </div>
                        <div class="modal-body">
                            <div class="form-group col-md-6" id="checkAttributes8">
                                <span text-align="top"><input style="cursor:pointer" type="checkbox" id="usership" name="usership" class="usership" value="Usership" /> Usership</span>
                            </div>
                            <div class="col-md-6" id="selectAttributes8">
                                <select id="val8" name="val8" multiple="multiple" class"form-control">
                                    @foreach($dataReports8 as $data8) 
                                        <option style="cursor:pointer" id="{{ $data8->usership }}" name="{{ $data8->usership }}" class="{{ $data8->usership }}" value="{{ $data8->usership }}"> {{ $data8->usership }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <br><br><br><br><br>
                        </div>
                        <div class="modal-body">
                            <div class="form-group col-md-6" id="checkAttributes9">
                                <span text-align="top"><input style="cursor:pointer" type="checkbox" id="sec" name="sec" class="sec" value="SEC" /> SEC</span>
                            </div>
                            <div class="col-md-6" id="selectAttributes9">
                                <select id="val9" name="val9" multiple="multiple" class"form-control">
                                    @foreach($dataReports9 as $data9) 
                                        <option style="cursor:pointer" id="{{ $data9->sec }}" name="{{ $data9->sec }}" class="{{ $data9->sec }}" value="{{ $data9->sec }}"> {{ $data9->sec }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <br><br><br><br><br>
                        </div>
                        <div class="modal-body">
                            <div class="form-group col-md-6" id="checkAttributes10">
                                <span text-align="top"><input style="cursor:pointer" type="checkbox" id="outlet_type" name="outlet_type" class="outlet_type" value="Outlet Type" /> Outlet Type</span>
                            </div>
                            <div class="col-md-6" id="selectAttributes10">
                                <select id="val10" name="val10" multiple="multiple" class"form-control">
                                    @foreach($dataReports10 as $data10) 
                                        <option style="cursor:pointer" id="{{ $data10->outlet_type }}" name="{{ $data10->outlet_type }}" class="{{ $data10->outlet_type }}" value="{{ $data10->outlet_type }}"> {{ $data10->outlet_type }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <br><br><br><br><br>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save fa-btn"></i> Submit
                            </button>
                            <button type="button" class="btn btn-warning" data-dismiss="modal">
                                <i class="fa fa-close fa-btn"></i> Close
                            </button>
                        </div>
                    </form>
                </div>   
            </div>
        </div>        
    </section>
@endsection
@section('footer_scripts')
    <script type="text/javascript">
        $('.dates').datepicker( {  
            format: 'dd-mm-yyyy'  
        });            
        $(function () {
            $('#val1, #val2, #val3, #val4, #val5, #val6, #val7, #val8, #val9, #val10').multiselect({
                includeSelectAllOption: true,
                maxHeight: 80
            });
            $('#btnSelected').click(function () {
                var selected = $("#lstFruits option:selected");
                var message = "";
                selected.each(function () {
                    message += $(this).text() + " " + $(this).val() + "\n";
                });
                alert(message);
            });
        });
        function checkboxHide(attrs, vals) {
            var $chk = $("#" + attrs + " input:checkbox"); 
            var $tbl = $("#tableAttributes");
            var $tblhead = $("#tableAttributes th");
            $chk.prop('checked', true); 
            $chk.click(function () {
                var colToHide = $tblhead.filter("." + $(this).attr("name"));
                var index = $(colToHide).index();
                $tbl.find('tr :nth-child(' + (index + 1) + ')').toggle();
            });            
        }  
        $(function () {
            var attrs1 = 'checkAttributes1';
            var vals1 = 'val1';
            checkboxHide(attrs1, vals1);
            var attrs2 = 'checkAttributes2';
            var vals2 = 'val2';
            checkboxHide(attrs2, vals2);
            var attrs3 = 'checkAttributes3';
            var vals3 = 'val3';
            checkboxHide(attrs3, vals3);
            var attrs4 = 'checkAttributes4';
            var vals4 = 'val4';
            checkboxHide(attrs4, vals4);
            var attrs5 = 'checkAttributes5';
            var vals5 = 'val5';
            checkboxHide(attrs5, vals5);
            var attrs6 = 'checkAttributes6';
            var vals6 = 'val6';
            checkboxHide(attrs6, vals6);
            var attrs7 = 'checkAttributes7';
            var vals7 = 'val7';
            checkboxHide(attrs7, vals7);
            var attrs8 = 'checkAttributes8';
            var vals8 = 'val8';
            checkboxHide(attrs8, vals8);
            var attrs9 = 'checkAttributes9';
            var vals9 = 'val9';
            checkboxHide(attrs9, vals9);
            var attrs10 = 'checkAttributes10';
            var vals10 = 'val10';
            checkboxHide(attrs10, vals10);
        });
        function showValues() {
            var fields = $("input:checkbox").serializeArray();           
            $("#results").empty();
            jQuery.each( fields, function(i, field) {
                $("#results").append(field.value + ",");
            });
            fields = JSON.stringify(fields);
            return fields;
        }
        $(":checkbox").click(showValues);
        $('.pdf').click(function() {
            window.location.href = "http://infoscan.dev/reports/formatPdf?attributes=" + showValues() + "&startDate=<?php echo $startDate; ?>&endDate=<?php echo $endDate; ?>";
        });
        $('.word').click(function() {
            window.location.href = "http://infoscan.dev/reports/formatWord?attributes=" + showValues() + "&startDate=<?php echo $startDate; ?>&endDate=<?php echo $endDate; ?>";
        });
        $('.excel').click(function() {
            window.location.href = "http://infoscan.dev/reports/formatExcel?attributes=" + showValues() + "&startDate=<?php echo $startDate; ?>&endDate=<?php echo $endDate; ?>";
        });   
        $('.maps').click(function() {
            window.location.href = "http://infoscan.dev/reports/maps";
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