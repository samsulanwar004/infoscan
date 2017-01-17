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

    <!-- Main content -->
    <section class="content">

        <!-- Default box -->
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">
                </h3>
                <div class="box-tools pull-right">
                    @cando('Merchant.Reports')
                        <a href="#"
                            class="">
                            Setting Reports </i>
                        </a>
                    @endcando
                    <a href="{{ admin_route_url('merchants.index') }}" class="btn btn-box-tool" data-toggle="tooltip"
                       title="Back"> <i
                                class="fa fa-times"></i></a>
                </div>
            </div>
            <div class="box-body">
                <form role="form" action="{{ admin_route_url('merchants.store') }}" method="POST"
                      enctype="multipart/form-data" class="form" accept-charset="utf-8" onsubmit="myLoading()">
                    {{ csrf_field() }}
                    <div class="box-body" id="form-body">
                        <div class="form-group has-feedback {{ $errors->has('company_name') ? 'has-error' : false }}">
                            <label for="company_name">Company Name</label>
                            <input type="text" class="form-control" id="company_name" name="company_name"
                                   value="{{ old('company_name') }}"
                                   placeholder="Enter company name" required autofocus>
                        </div>
                        <div class="form-group has-feedback">
                            <label for="company_logo">Company Logo</label>
                            <input type="file" class="form-control" id="company_logo" name="company_logo"
                                   placeholder="Enter company name">
                        </div>
                        <div class="form-group has-feedback">
                            <label for="address">Address</label>
                            <textarea class="form-control" name="address" id="address"
                                      placeholder="Enter address" required>{{ old('address') }}</textarea>
                        </div>
                        <div class="form-group has-feedback {{ $errors->has('company_email') ? 'has-error' : false }}">
                            <label for="company_email">Email</label>
                            <input type="email" class="form-control" name="company_email" id="company_email"
                                   value="{{ old('company_email') }}"
                                   placeholder="Enter company email" required>
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
                                        <input type="text" class="form-control" name="user[name][]" id="name"
                                               value="{{ old('user.name.' . $i) }}" placeholder="Enter user name"
                                               required>
                                    </div>
                                    <div class="form-group has-feedback {{ $errors->has('user.phone.'.$i) ? 'has-error' : false }}">
                                        <label for="phone">Phone</label>
                                        <input type="text" class="form-control" name="user[phone][]" id="phone"
                                               value="{{ old('user.phone.' . $i) }}" placeholder="Enter phone number"
                                               required>
                                    </div>
                                    <div class="form-group has-feedback {{ $errors->has('user.position.'.$i) ? 'has-error' : false }}">
                                        <label for="position">Position</label>
                                        <input type="text" class="form-control" name="user[position][]" id="position"
                                               value="{{ old('user.position.' . $i) }}" placeholder="Enter position"
                                               required>
                                    </div>
                                    <div class="form-group has-feedback {{ $errors->has('user.email.'.$i) ? 'has-error' : false }}">
                                        <label for="email">Email</label>
                                        <input type="email" class="form-control" name="user[email][]" id="email"
                                               value="{{ old('user.email.' . $i) }}" placeholder="Enter email"
                                               required>
                                    </div>
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
        </div>
        <!-- /.box -->

    </section>
    <!-- /.content -->
@endsection

@section('footer_scripts')
    <script>
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
    </script>
@endsection
