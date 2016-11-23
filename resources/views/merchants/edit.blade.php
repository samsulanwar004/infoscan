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

    <!-- Main content -->
    <section class="content">

        <!-- Default box -->
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <!-- <a href="/users" class="btn btn-link btn-sm">
                        <i class="fa fa-plus fa-arrow-circle-left"></i> back to user list</a> -->
                </h3>

                <div class="box-tools pull-right">
                    <a href="{{ admin_route_url('merchants.index') }}" class="btn btn-box-tool"
                       data-toggle="tooltip" title="Back">
                        <i class="fa fa-times"></i></a>
                </div>
            </div>
            <div class="box-body" id="form-body">
                <form role="form" action="{{ admin_route_url('merchants.update', ['id' => $merchant->id]) }}"
                      method="POST" enctype="multipart/form-data" accept-charset="utf-8" onsubmit="myLoading()">
                    {{ csrf_field() }}
                    {{ method_field('PUT') }}
                    <div class="box-body">
                        <div class="form-group has-feedback">
                            <label for="company_name">Company Name</label>
                            <input type="text" class="form-control" id="company_name" name="company_name"
                                   placeholder="Enter company name"
                                   value="{{ old('company_name', $merchant->company_name) }}" required>
                        </div>

                        <div class="form-group has-feedback">
                            <label for="company_logo">Company Logo</label><br>
                            <img width="200" height="200" src="{{ '/storage/merchants/'.$merchant->company_logo }}">
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
                                        <label for="email">Email</label>
                                        <input type="email" class="form-control" name="user[email][]" id="email"
                                               value="{{ old('email', $mu->user->email) }}" disabled="disabled" 
                                               required>
                                    </div>
                                    <div class="checkbox">
                                        <label>
                                            <input name="user[is_active][]" {{ (bool)$mu->user->is_active ? 'checked' : '' }} type="checkbox">
                                            Is Active ?
                                        </label>
                                    </div>
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
                                    <div class="form-group has-feedback">
                                        <label for="name">Name</label>
                                        <input type="text" class="form-control" name="newuser[name][]" id="name"
                                               value="{{ old('newuser.name.' . $i) }}" placeholder="Enter user name"
                                               required>
                                    </div>
                                    <div class="form-group has-feedback">
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
                        <button class="btn btn-primary" id="add">
                            <i class="fa fa-plus fa-btn"></i>Add User                        
                        </button>
                        <button type="submit" class="btn btn-primary">
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

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
<script>
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
            $('div#users').append('<div id="user"><hr><div class="text-right"><button class="btn btn-box-tool" id="remove"><i class="fa fa-remove"></i></button></div><div class="form-group has-feedback"><label for="name">Name</label><input type="text" class="form-control" name="newuser[name][]" id="name" placeholder="Enter user name" required></div><div class="form-group has-feedback"><label for="email">Email</label><input type="email" class="form-control" name="newuser[email][]" id="email" placeholder="Enter email" required></div></div>');
            updateCounterForm(false);
        });

        $(document).on('click', 'button#remove', function (e) {
            e.preventDefault();

            var id = $(this).data('id');
            var token = $(this).data('token');
            var name = $(this).data('name');
            var email = $(this).data('email');
            var conf = confirm('Are you sure want to delete user ' + name + ' (' + email + ') ?');

            if (id === undefined) {
                $(e.target).closest('#user').remove();
                updateCounterForm(true);
            }
            else {
                if (conf) {
                    $.ajax({
                        url: '/merchants/user/' + id,
                        type: 'post',
                        data: {
                            '_token': token,
                            '_method': 'delete'
                        },
                        success: function () {
                            $(e.target).closest('#user').remove();
                            updateCounterForm(true);
                        }
                    });
                }
            }
        });
    });

    function myLoading() {
        $('#loading').addClass('overlay');
        document.getElementById("loading").innerHTML = '<i class="fa fa-refresh fa-spin"></i>';
    }
</script>
