@extends('app')

@section('content')
    @include('partials.content_header', [
        'pageTitle' => 'Merchant users',
        'pageDescription' => 'Create a new merchant user',
        'breadcrumbs' => [
            'Merchant users' => admin_route_url('merchantusers.index'),
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
                    <a href="{{ admin_route_url('merchantusers.index') }}" class="btn btn-box-tool"
                       data-toggle="tooltip"
                       title="Back"> <i
                                class="fa fa-times"></i></a>
                </div>
            </div>
            <div class="box-body">
                <form role="form" action="{{ admin_route_url('merchantusers.store') }}" method="POST"
                      enctype="multipart/form-data" class="form" accept-charset="utf-8" onsubmit="myLoading()">
                    {{ csrf_field() }}
                    <div class="box-body" id="form-body">
                        <div id="users">
                            @for($i=0; $i <= session('countOfUser', 0); ++$i)
                                <div id="user">
                                    <div class="text-right">
                                        <button class="btn btn-box-tool" id="remove">
                                            <i class="fa fa-remove"></i>
                                        </button>
                                    </div>
                                    <div class="form-group has-feedback {{ $errors->has('user.name.'.$i) ? 'has-error' : false }}">
                                        <label for="name">Name</label>
                                        <input type="text" class="form-control" name="user[name][]" id="name"
                                               value="{{ old('user.name.' . $i) }}" placeholder="Enter user name" required>
                                    </div>
                                    <div class="form-group has-feedback {{ $errors->has('user.email.'.$i) ? 'has-error' : false }}">
                                        <label for="email">Email</label>
                                        <input type="email" class="form-control" name="user[email][]" id="email"
                                               value="{{ old('user.email.' . $i) }}" placeholder="Enter email" required>
                                    </div>
                                </div>
                            @endfor
                        </div>
                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer text-right">
                        <button class="btn btn-primary" id="add">
                            <i class="fa fa-plus fa-btn"></i> Add User
                        </button>
                        <button type="submit" class="btn btn-primary" id="submit">
                            <i class="fa fa-save fa-btn"></i> Save All
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
            $('div#users').append('<div id="user"><div class="text-right"><button class="btn btn-box-tool" id="remove"><i class="fa fa-remove"></i></button></div><div class="form-group has-feedback"><label for="name">Name</label><input type="text" class="form-control" name="user[name][]" id="name" placeholder="Enter user name" required></div><div class="form-group has-feedback"><label for="email">Email</label><input type="email" class="form-control"  placeholder="Enter email" required></div></div></div>');
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
    });

    function myLoading() {
        $('#loading').addClass('overlay');
        document.getElementById("loading").innerHTML = '<i class="fa fa-spinner fa-spin" style="font-size:50px; position: fixed;"></i>';
    }
</script>
@endsection