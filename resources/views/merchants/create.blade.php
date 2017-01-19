@extends('app')
@section('content')
    @include('partials.content_header', [
        'pageTitle' => 'Merchants',
        'pageDescription' => 'Create a new merchant',
        'breadcrumbs' => [
            'Merchants' => admin_route_url('merchants.index'),
            'Create' => false]
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
                    <a href="{{ admin_route_url('merchants.index') }}" class="btn btn-box-tool" data-toggle="tooltip" title="Back">
                        <i class="fa fa-times"></i>
                    </a>
                </div>
            </div>
            <div class="box-body">
                <form role="form" action="{{ admin_route_url('merchants.store') }}" method="post" enctype="multipart/form-data" class="form" accept-charset="utf-8" onsubmit="myLoading()">
                    {{ csrf_field() }}
                    <div class="box-body" id="form-body">
                        <div class="form-group has-feedback {{ $errors->has('company_name') ? 'has-error' : false }}">
                            <label for="company_name">Company Name</label>
                            <input type="text" class="form-control" id="company_name" name="company_name" value="{{ old('company_name') }}" placeholder="Enter company name" required autofocus>
                        </div>
                        <div class="form-group has-feedback">
                            <label for="company_logo">Company Logo</label>
                            <input type="file" class="form-control" id="company_logo" name="company_logo" placeholder="Enter company name">
                        </div>
                        <div class="form-group has-feedback">
                            <label for="address">Address</label>
                            <textarea class="form-control" name="address" id="address" placeholder="Enter address" required>{{ old('address') }}</textarea>
                        </div>
                        <div class="form-group has-feedback {{ $errors->has('company_email') ? 'has-error' : false }}">
                            <label for="company_email">Email</label>
                            <input type="email" class="form-control" name="company_email" id="company_email" value="{{ old('company_email') }}" placeholder="Enter company email" required>
                        </div>
                        <div id="users">
                            @for($i=0; $i <= session('countOfUser', 0); ++$i)
                                <div id="user">
                                    <hr>
                                    <div class="text-right">
                                        <button class="btn btn-box-tool" id="remove">
                                            <i class="fa fa-remove"></i>
                                        </button>
                                    </div>
                                    <div class="form-group has-feedback {{ $errors->has('user.name.'.$i) ? 'has-error' : false }}">
                                        <label for="name">Name</label>
                                        <input type="text" class="form-control" name="user[name][]" id="name" value="{{ old('user.name.' . $i) }}" placeholder="Enter user name" required>
                                    </div>
                                    <div class="form-group has-feedback {{ $errors->has('user.phone.'.$i) ? 'has-error' : false }}">
                                        <label for="phone">Phone</label>
                                        <input type="text" class="form-control" name="user[phone][]" id="phone" value="{{ old('user.phone.' . $i) }}" placeholder="Enter phone number" required>
                                    </div>
                                    <div class="form-group has-feedback {{ $errors->has('user.position.'.$i) ? 'has-error' : false }}">
                                        <label for="position">Position</label>
                                        <input type="text" class="form-control" name="user[position][]" id="position" value="{{ old('user.position.' . $i) }}" placeholder="Enter position" required>
                                    </div>
                                    <div class="form-group has-feedback {{ $errors->has('user.email.'.$i) ? 'has-error' : false }}">
                                        <label for="email">Email</label>
                                        <input type="email" class="form-control" name="user[email][]" id="email" value="{{ old('user.email.' . $i) }}" placeholder="Enter email" required>
                                    </div>
                                    @cando('Merchant.Reports')
                                        <div class="form-group has-feedback">
                                            <a class="btn btn-default btn-modal" href="javascript:void(0)"><i class="fa fa-btn fa-filter"></i> Setting Reports</a>
                                        </div>
                                    @endcando
                                </div>
                            @endfor
                        </div>
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
            <div class="modal fade" tabindex="-1" role="dialog">
                <form id="settingReportsForm" role="form" action="{{ admin_route_url('merchants.settingReports.store') }}" method="post" class="form" accept-charset="utf-8">
                    {{ csrf_field() }}
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title">Setting Reports</h4>
                            </div>
                            <div class="modal-body" style="padding-bottom: 1px; padding-top:0;">
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
        var counterform = 2;
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
            $("button#add").on('click', function (e) {
                e.preventDefault();
                $('div#users').append('<div id="user"><hr>' +
                    '<div class="text-right"><button class="btn btn-box-tool" id="remove"><i class="fa fa-remove"></i></button></div>' +
                    '<div class="form-group has-feedback"><label for="name">Name</label><input type="hidden" name="user[id][]" id="id"><input type="text" class="form-control" name="user[name][]" id="name" placeholder="Enter user name" required></div>' +
                    '<div class="form-group has-feedback"><label for="phone">Phone</label><input type="text" class="form-control" name="user[phone][]" id="phone" placeholder="Enter phone number" required></div>' +
                    '<div class="form-group has-feedback"><label for="position">Position</label><input type="text" class="form-control" name="user[position][]" id="position" placeholder="Enter position"required></div>' +
                    '<div class="form-group has-feedback"><label for="email">Email</label><input type="email" class="form-control" name="user[email][]" id="email" placeholder="Enter email" required></div></div>');
                updateCounterForm(false);
                window.location.href = '#add';
            });
            $(document).on('click', 'button#remove', function (e) {
                e.preventDefault();
                var name = $(this).data('name');
                var email = $(this).data('email');
                if (confirm('Are you sure want to delete user ' + name + ' (' + email + ') ?')) {
                    $(e.target).closest('#user').remove();
                    updateCounterForm(true);
                }
            });
        });
        function myLoading() {
            $('#loading').addClass('overlay');
            document.getElementById("loading").innerHTML = '<i class="fa fa-spinner fa-spin" style="font-size:50px; position: fixed;"></i>';
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