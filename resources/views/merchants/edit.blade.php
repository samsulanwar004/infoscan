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
                      method="POST" enctype="multipart/form-data" accept-charset="utf-8">
                    {{ csrf_field() }}
                    {{ method_field('PUT') }}
                    <div class="box-body">
                        <div class="form-group has-feedback{{ $errors->has('company_name') ? ' has-error' : '' }}">
                            <label for="company_name">Company Name</label>
                            <input type="text" class="form-control" id="company_name" name="company_name"
                                   placeholder="Enter company name"
                                   value="{{ old('company_name', $merchant->company_name) }}" required>
                        </div>

                        <div class="form-group has-feedback{{ $errors->has('company_logo') ? ' has-error' : '' }}">
                            <label for="company_logo">Company Logo</label><br>
                            <img width="200" height="200" src="{{ '/storage/merchants/'.$merchant->company_logo }}">
                            <input type="file" class="form-control" id="company_logo" name="company_logo">
                        </div>

                        <div class="form-group has-feedback{{ $errors->has('address') ? ' has-error' : '' }}">
                            <label for="address">Address</label>
                            <textarea class="form-control" name="address" id="address" placeholder="Enter address"
                                      value="">{{ old('address', $merchant->address) }}</textarea>
                        </div>
                        <div class="form-group has-feedback{{ $errors->has('company_email') ? ' has-error' : '' }}">
                            <label for="company_email">Email</label>
                            <input type="email" class="form-control" name="company_email" id="company_email"
                                   value="{{ old('company_email', $merchant->company_email) }}"
                                   placeholder="Enter email" required>
                        </div>
                        <hr>
                        <button class="btn btn-primary" id="add">Add user field</button>
                        <div id="users">
                            @foreach($merchantUsers as $mu)
                                <div id="user">
                                    <hr>
                                    <button class="btn btn-box-tool" data-id="{{ $mu->user->id }}"
                                            data-token="{{ csrf_token() }}" id="remove">x
                                    </button>
                                    <div class="form-group has-feedback{{ $errors->has('name') ? ' has-error' : '' }}">
                                        <label for="name">Name</label>
                                        <input type="hidden" name="user[id][]" id="id" value="{{ $mu->user->id }}">
                                        <input type="text" class="form-control" name="user[name][]" id="name"
                                               value="{{ old('name', $mu->user->name) }}" placeholder="Enter user name"
                                               required>
                                    </div>
                                    <div class="form-group has-feedback{{ $errors->has('email') ? ' has-error' : '' }}">
                                        <label for="email">Email</label>
                                        <input type="email" class="form-control" name="user[email][]" id="email"
                                               value="{{ old('email', $mu->user->email) }}" placeholder="Enter email"
                                               required>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer text-right">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save fa-btn"></i> Save Merchant
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <!-- /.box -->

    </section>
    <!-- /.content -->
@endsection

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
<script>
    var counterform = {{ count($merchantUsers)+1 }};
    console.log("users = " + counterform);

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

        console.log(counterform);
    }

    $(document).ready(function () {

        updateCounterForm(true);

        $(document).on('click', 'button#add', function (e) {
            e.preventDefault();
            $('div#users').append('<div id="user"><hr><button class="btn btn-box-tool" id="remove">x</button><div class="form-group has-feedback"><label for="name">Name</label><input type="hidden" name="user[id][]" id="id"><input type="text" class="form-control" name="user[name][]" id="name" placeholder="Enter user name" required></div><div class="form-group has-feedback"><label for="email">Email</label><input type="email" class="form-control" name="user[email][]" id="email" placeholder="Enter email" required></div></div>');
            updateCounterForm(false);
        });

        $(document).on('click', 'button#remove', function (e) {
            e.preventDefault();

            var id = $(this).data('id');
            var token = $(this).data('token');
            console.log('id=' + id + ' // token=' + token);

            if (id != null) {
                if (confirm('Are you sure to delete this user?')) {
                    $.ajax({
                        url: '/merchants/users/' + id,
                        type: 'post',
                        data: {
                            '_token': token,
                            '_method': 'delete'
                        },
                        success: function () {
                            $(e.target).closest('#user').remove();
                            updateCounterForm(true);
                            alert('User succesfully deleted.');
                        }
                    });
                } else {
                    $(e.target).closest('#user').remove();
                    updateCounterForm(true);
                }
            }
        });
    });
</script>