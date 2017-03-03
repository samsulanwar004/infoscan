@extends('app')
@section('content')
    @include('partials.content_header', [
        'pageTitle' => 'Merchants',
        'pageDescription' => 'Edit merchants',
        'breadcrumbs' => [
            'Merchants' => admin_route_url('merchants.index'),
            'Edit' => false]
        ]
    )
    <?php
        $configurations = config('common.reports.fields');
        $ignoredFields = config('common.reports.ignored_fields');
    ?>
    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title"></h3>
                <div class="box-tools pull-right">
                    <a href="{{ admin_route_url('merchants.index') }}" class="btn btn-box-tool"
                       data-toggle="tooltip" title="Back">
                        <i class="fa fa-times"></i></a>
                </div>
            </div>
            <div class="box-body" id="form-body">
                <form role="form" action="{{ admin_route_url('merchants.update', ['id' => $merchant->id]) }}" method="post" enctype="multipart/form-data" accept-charset="utf-8" onsubmit="myLoading()">
                    {{ csrf_field() }}
                    {{ method_field('PUT') }}
                    <div class="box-body">
                        <div class="form-group has-feedback">
                            <label for="company_name">Company Name</label>
                            <input type="text" class="form-control" id="company_name" name="company_name" placeholder="Enter company name" value="{{ old('company_name', $merchant->company_name) }}" required>
                        </div>
                        <div class="form-group has-feedback">
                            <label for="company_logo">Company Logo</label><br>
                            @if($merchant->company_logo)
                                <img height="200" src="{{ $merchant->company_logo }}">
                            @endif
                            <input type="file" class="form-control" id="company_logo" name="company_logo">
                        </div>
                        <div class="form-group has-feedback">
                            <label for="address">Address</label>
                            <textarea class="form-control" name="address" id="address" placeholder="Enter address"
                                      value="">{{ old('address', $merchant->address) }}</textarea>
                        </div>
                        <div class="form-group has-feedback">
                            <label for="company_email">Email</label>
                            <input type="email" class="form-control" name="company_email" id="company_email"
                                   value="{{ old('company_email', $merchant->company_email) }}"
                                   placeholder="Enter email" required>
                        </div>
                        <hr>
                        <h4>List of {{ $merchant->company_name }} users</h4>
                        <div id="users">
                            @foreach($merchantUsers as $mu)
                                <div id="user">
                                    <hr>
                                    <div class="text-right">
                                        <button class="btn btn-box-tool" data-id="{{ $mu->user->id }}"
                                                data-name="{{ $mu->user->name }}" data-email="{{ $mu->user->email }}"
                                                data-token="{{ csrf_token() }}" id="remove"><i class="fa fa-remove"></i>
                                        </button>
                                    </div>
                                    <div class="form-group has-feedback">
                                        <label for="name">Name</label>
                                        <input type="hidden" name="user[id][]" id="id" value="{{ $mu->user->id }}">
                                        <input type="text" class="form-control" name="user[name][]" id="name"
                                               value="{{ old('name', $mu->user->name) }}" placeholder="Enter user name"
                                               required>
                                    </div>
                                    <div class="form-group has-feedback">
                                        <label for="phone">Phone</label>
                                        <input type="text" class="form-control" name="user[phone][]" id="phone"
                                               value="{{ old('phone', $mu->user->phone) }}" placeholder="Enter phone number"
                                               required>
                                    </div>
                                    <div class="form-group has-feedback">
                                        <label for="position">Position</label>
                                        <input type="text" class="form-control" name="user[position][]" id="position"
                                               value="{{ old('position', $mu->user->position) }}" placeholder="Enter position"
                                               required>
                                    </div>
                                    <div class="form-group has-feedback">
                                        <label for="email">Email</label>
                                        <input type="email" class="form-control" name="user[email][]" id="email"
                                               value="{{ old('email', $mu->user->email) }}" disabled="disabled"
                                               required>
                                    </div>
                                    <div class="checkbox">
                                        <label>
                                            <input onclick='handleCheck(this, {{$mu->user->id}});' {{ (bool)$mu->user->is_active ? 'checked' : '' }} type="checkbox">
                                            <input type="hidden" name="user[is_active][]" id="cbx{{$mu->user->id}}" {{ (bool)$mu->user->is_active ? 'value=1' : 'value=0' }}>
                                            Is Active ?
                                        </label>
                                    </div>
                                    @cando('Merchant.Reports')
                                        <div class="form-group has-feedback">
                                            <a class="btn btn-default btn-open-modal" href="#"><i class="fa fa-btn fa-filter"></i> Setting Reports</a>
                                        </div>
                                    @endcando
                                </div>
                            @endforeach
                        </div>
                        @if(session('countOfNewUser') === 0 || session('countOfNewUser') > 0)
                            @for($i=0; $i <= session('countOfNewUser', 0); ++$i)
                                <div id="user">
                                    <hr>
                                    <div class="text-right">
                                        <button class="btn btn-box-tool" id="remove">
                                            <i class="fa fa-remove"></i>
                                        </button>
                                    </div>
                                    <div class="form-group has-feedback {{ $errors->has('newuser.name.'.$i) ? 'has-error' : false }}">
                                        <label for="name">Name</label>
                                        <input type="text" class="form-control" name="newuser[name][]" id="name"
                                               value="{{ old('newuser.name.' . $i) }}" placeholder="Enter user name"
                                               required>
                                    </div>
                                    <div class="form-group has-feedback {{ $errors->has('newuser.phone.'.$i) ? 'has-error' : false }}">
                                        <label for="phone">Phone</label>
                                        <input type="text" class="form-control" name="newuser[phone][]" id="phone"
                                               value="{{ old('newuser.phone.' . $i) }}" placeholder="Enter phone number"
                                               required>
                                    </div>
                                    <div class="form-group has-feedback {{ $errors->has('newuser.position.'.$i) ? 'has-error' : false }}">
                                        <label for="position">Position</label>
                                        <input type="text" class="form-control" name="newuser[position][]" id="position"
                                               value="{{ old('newuser.position.' . $i) }}" placeholder="Enter position"
                                               required>
                                    </div>
                                    <div class="form-group has-feedback {{ $errors->has('newuser.email.'.$i) ? 'has-error' : false }}">
                                        <label for="email">Email</label>
                                        <input type="email" class="form-control" name="newuser[email][]" id="email"
                                               value="{{ old('newuser.email.' . $i) }}" placeholder="Enter email"
                                               required>
                                    </div>
                                </div>
                            @endfor
                        @endif
                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer text-right">
                        <button class="btn btn-default" id="add">
                            <i class="fa fa-plus fa-btn"></i>Add User
                        </button>
                        <button type="submit" class="btn btn-primary" id="submit">
                            <i class="fa fa-save fa-btn"></i> Save Merchant
                        </button>
                    </div>
                </form>
            </div>
            <div id="loading"></div>
            <div id="modals" class="modal fade" tabindex="-1" role="dialog">
                <form id="settingReportsForm" role="form" action="{{ admin_route_url('merchants.settingReports.update', ['id' => 1]) }}" method="post" class="form" accept-charset="utf-8">
                    {{ csrf_field() }}
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title">Setting Reports</h4>
                            </div>
                            <div class="modal-body" style="padding-bottom: 1px;">
                                <div class="checkbox-list">
                                    @foreach($configurations as $field => $label)
                                        @if(! in_array($field, $ignoredFields))
                                            <div class="row bg-soft">
                                                <div class="col-md-6 d4-border-top" style="min-height: 45px; padding-top: 15px;">
                                                    <div class="checkbox">
                                                        <label><input checked name="check_{{ $field }}"type="checkbox" class="column-list" checkboxIndex="{{ $loop->index }}">{{ $label['label'] }}</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 d4-border-top" style="padding-top: 15px;">
                                                    <div class="form-group checkbox-input-{{ $loop->index }}">
                                                        @php
                                                            $elementType = $label['type'];
                                                            $options = [
                                                                'class' => sprintf("%s %s", 'input-sm form-control', $elementType)
                                                            ];
                                                            if('range' === $elementType) {
                                                                $options['data-min'] = $label['data']['min'];
                                                                $options['data-max'] = $label['data']['max'];
                                                            }

                                                            if('multiple' === $elementType) {
                                                                $options['style'] = 'width: 100%;';
                                                                $options['multiple'] = 'multiple';
                                                            }

                                                            if('single' === $elementType) {
                                                                $options['style'] = 'width: 100%;';
                                                            }

                                                            if('dateRange' === $elementType) {
                                                                $options['date-min'] = $label['data']['min'];
                                                                $options['date-max'] = $label['data']['max'];
                                                                $options['date-format'] = $label['data']['format'];
                                                                $elementType = 'input';
                                                            }
                                                        @endphp
                                                        {!! \RebelField::type($elementType, $field, [], [], $options) !!}
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                            <div class="modal-footer">
                                <a href="#" class="btn-link" data-dismiss="modal">Close</a>
                                <button type="submit" class="btn btn-primary">Save Setting</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- /.box -->
    </section>
    <!-- /.content -->
@endsection
@section('footer_scripts')
    <link rel="stylesheet" href="{{ elixirCDN('css/report-vendor.css') }}" />
    <script type="text/javascript" src="{{ elixirCDN('js/report-vendor.js') }}"></script>
    <script type="text/javascript">
        var counterform = {{ count($merchantUsers)+1 }};
        function updateCounterForm(isRemove) {
            if (isRemove) {
                counterform = counterform - 1;
            } else {
                counterform = counterform + 1;
            }
            if (counterform > 1) {
                $("button#remove").prop("disabled", false);
            } else {
                $("button#remove").prop("disabled", true);
            }
        }
        $(document).ready(function () {
            updateCounterForm(true);
            $(document).on('click', 'button#add', function (e) {
                e.preventDefault();
                $('div#users').append('<div id="user"><hr>' +
                '<div class="text-right"><button class="btn btn-box-tool" id="remove"><i class="fa fa-remove"></i></button></div>' +
                '<div class="form-group has-feedback"><label for="name">Name</label><input type="hidden" name="user[id][]" id="id"><input type="text" class="form-control" name="newuser[name][]" id="name" placeholder="Enter user name" required></div>' +
                '<div class="form-group has-feedback"><label for="phone">Phone</label><input type="text" class="form-control" name="newuser[phone][]" id="phone" placeholder="Enter phone number" required></div>' +
                '<div class="form-group has-feedback"><label for="position">Position</label><input type="text" class="form-control" name="newuser[position][]" id="position" placeholder="Enter position"required></div>' +
                '<div class="form-group has-feedback"><label for="email">Email</label><input type="email" class="form-control" name="newuser[email][]" id="email" placeholder="Enter email" required></div></div>' +
                '<div class="form-group has-feedback"><a class="btn btn-default btn-open-modal" href="#"><i class="fa fa-btn fa-filter"></i> Setting Reports</a></div>');
                updateCounterForm(false);
                window.location.href='#add';
            });
            $(document).on('click', 'button#remove', function (e) {
                e.preventDefault();
                var name = $(this).data('name');
                var email = $(this).data('email');
                if(confirm('Are you sure want to delete user ' + name + ' (' + email + ') ?')) {
                    $(e.target).closest('#user').remove();
                    updateCounterForm(true);
                }
            });
            $(document).on('click', '.btn-open-modal', function (e) {
                $('#modals').modal('show');
            });
        });
        function myLoading() {
            $('#loading').addClass('overlay');
            document.getElementById("loading").innerHTML = '<i class="fa fa-spinner fa-spin" style="font-size:50px;"></i>';
        }
        function handleCheck(cb, id) {
            if (cb.checked == false) {
                $("#cbx"+id).val("0");
            } else {
                $("#cbx"+id).val("1");
            }
        }
        $(document).ready(function() {
            whenLoaded();
            $('.range').each(function(i, obj) {
                buildRangeSlider($(obj));
            });
            $('.dateRange').each(function(i, obj) {
                buildDateRange($(obj));
            });
            $('.multiple, .single').select2();
            $('.btn-modal').on('click', function(e) {
                e.preventDefault();
                $('.modal').modal('show');
            })
            $('.datepicker').daterangepicker({
                timePicker: false,
                timePicker24Hour: false,
                minDate: -0,
                maxDate: "<?php echo \Carbon\Carbon::today()->toDateString(); ?>",
                locale: {
                    format: 'YYYY-MM-DD'
                }
            });
            $('.column-list').on('change', function() {
                var checkboxIndex = $(this).attr('checkboxIndex');
                var checkboxInput = $('.checkbox-input-' + checkboxIndex);
                if($(this).is(':checked')) {
                    //showHideColumn('reportTable', checkboxIndex, true);
                    checkboxInput.show();
                    $(this).parents('.row').addClass('bg-soft');
                } else {
                    //showHideColumn('reportTable', checkboxIndex, false);
                    checkboxInput.hide();
                    $(this).parents('.row').removeClass('bg-soft');
                }
            });
            $('#settingReportsForm').on('submit', function (e) {
                e.preventDefault();
                REBEL.onSubmit($(this), function (responseData) {
                    REBEL.removeAllMessageAlert();
                    if (responseData.status == "ok") {
                        REBEL.smallNotifTemplate(responseData.message, '.modal-content', 'success');
                        console.log('success');
                    }
                    setTimeout(function () {
                        REBEL.removeAllMessageAlert();
                    }, 3000)
                });
            });
        });
        function whenLoaded() {
            //console.log(Cookies.get('reports'));
            var $cookies = Cookies.get('reportFilters');
            if(!$cookies) {
                Cookies.set('reportFilters', 'getCookies', { path: '/reports' });
                return;
            } else {
                console.log($cookies);
            }
        }
        function buildRangeSlider(selector) {
            var selector = $(".range"),
            $min = selector.attr('data-min'),
            $max = selector.attr('data-max'),
            slider;
            var create = function () {
                selector.ionRangeSlider({
                    type: "double",
                    hide_min_max: true,
                    keyboard: true,
                    min: $min,
                    max: $max,
                    //from: 1000,
                    grid: true
                });
                slider = selector.data("ionRangeSlider");
            };
            return create();
        }
        function buildDateRange(selector) {
            var $min = selector.attr('date-min'),
                $max = selector.attr('date-max'),
                $format = selector.attr('date-format');
            $(selector).daterangepicker({
                timePicker: false,
                drops: "up",
                timePicker24Hour: false,
                showDropdowns: true,
                autoApply: true,
                alwaysShowCalendars: true,
                minDate: $min,
                maxDate: $max,
                cancelClass: "btn-danger",
                locale: {
                    format: 'YYYY-MM-DD'
                }
            });
        }
    </script>
@endsection