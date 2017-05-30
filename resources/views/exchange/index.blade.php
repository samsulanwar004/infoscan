@extends('app')

@section('content')
    @include('partials.content_header', ['pageTitle' => 'Exchange Rates', 'pageDescription' => 'List of exchange rates', 'breadcrumbs' => ['Exchange rates' => false]])

    <!-- Main content -->
    <section class="content">

        <!-- Default box -->
        <div class="box">
            <div class="box-header with-border">
                <div id="create-rate">
                    <div class="box-tools pull-right">
                        @cando('Exchange.Create')
                        <a href="{{ admin_route_url('exchange.create') }}" class="btn btn-box-tool" data-toggle="tooltip"
                           title="Create New">
                            <i class="fa fa-plus-circle fa-btn"></i> Create New</a>
                        @endcando
                    </div>
                </div>

                <div id="create-city" style="display: none;">
                    <div class="box-tools pull-right">
                        @cando('Exchange.Create')
                        <a href="{{ admin_route_url('city.rate.create') }}" class="btn btn-box-tool" data-toggle="tooltip"
                           title="Create New">
                            <i class="fa fa-plus-circle fa-btn"></i> Create New</a>
                        @endcando
                    </div>
                </div>
            </div>
            <div class="box-header with-border form-inline" style="overflow: hidden; height: 45px;">
                <ul class="nav nav-tabs">
                    <li class="active"><a data-toggle="tab" href="#entry-rate" id="entry">Entry Rate</a></li>
                    <li><a data-toggle="tab" href="#city-rate" id="city">City Rate</a></li>
                </ul>              
            </div>
            <div class="box-body">
                <div class="tab-content">
                    <div id="entry-rate" class="tab-pane fade in active">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th width="100">Date</th>
                                <!-- <th width="100">Cash</th>
                                <th width="100">Point</th>  -->                          
                                <th width="100">Entry Rate</th>                           
                                <th width="250"></th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($rates as $item)
                                <tr>
                                    <td>
                                        {{ date_format(date_create($item->created_at), 'M, d Y') }}
                                    </td>
                                    <td>
                                        Rp {{ $item->cash_per_unit }}
                                    </td>
                                    <!-- <td>
                                        {{ number_format($item->point_unit_count) }} Pts
                                    </td>   -->                                
                                    <td class="text-right vertical-middle">
                                        <div class="btn-group">
                                            @cando('Exchange.Update')
                                            <a href="{{ admin_route_url('exchange.edit', ['id' => $item->id]) }}"
                                               class="btn btn-info">
                                                <i class="fa fa-pencil"> </i>
                                            </a>
                                            @endcando
                                            @cando('Exchange.Delete')
                                            <a class="btn btn-danger"
                                               href="{{ admin_route_url('exchange.destroy', ['id' => $item->id]) }}"
                                               data-toggle="modal"
                                               data-target="#"
                                               title="Delete this data"
                                               for-delete="true"
                                               data-message="Are you sure you want to delete this Exchange ?"
                                            > <i class="fa fa-trash"></i> </a>
                                            @endcando
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <td colspan="6"> There is no record for exchange rates data!</td>
                            @endforelse
                            </tbody>
                        </table>
                        {{ $rates->links() }}
                    </div>
                    <div id="city-rate" class="tab-pane fade">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th width="100">Date</th>
                                <th width="100">City</th>                      
                                <th width="100">Rate</th>                           
                                <th width="100">Active</th>                           
                                <th width="250"></th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($citys as $item)
                                <tr>
                                    <td>
                                        {{ date_format(date_create($item->created_at), 'M, d Y') }}
                                    </td>
                                    <td>
                                        {{ $item->city_name }}
                                    </td>
                                    <td>
                                        Rp {{ $item->cash_per_unit }}
                                    </td>
                                    <td>
                                       <i class="fa fa-check-circle {{ $item->is_active == 1 ? 'text-green' : 'text-default' }}"></i>
                                    </td>                                  
                                    <td class="text-right vertical-middle">
                                        <div class="btn-group">
                                            @cando('Exchange.Update')
                                            <a href="{{ admin_route_url('city.rate.edit', ['id' => $item->id]) }}"
                                               class="btn btn-info">
                                                <i class="fa fa-pencil"> </i>
                                            </a>
                                            @endcando
                                            @cando('Exchange.Delete')
                                            <a class="btn btn-danger"
                                               href="{{ admin_route_url('city.rate.destroy', ['id' => $item->id]) }}"
                                               data-toggle="modal"
                                               data-target="#"
                                               title="Delete this data"
                                               for-delete="true"
                                               data-message="Are you sure you want to delete this City Rate ?"
                                            > <i class="fa fa-trash"></i> </a>
                                            @endcando
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <td colspan="6"> There is no record for city rates data!</td>
                            @endforelse
                            </tbody>
                        </table>
                        {{ $citys->links() }}
                    </div>
                </div>
            </div>
        </div>
        <!-- /.box -->

    </section>
    <!-- /.content -->
@endsection

@section('footer_scripts')
    <script type="text/javascript">
        $('#entry').on('click', function() {
            $('#create-rate').show();
            $('#create-city').hide();
        });

        $('#city').on('click', function() {
            $('#create-rate').hide();
            $('#create-city').show();
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
            }
        });
    </script>
@stop
