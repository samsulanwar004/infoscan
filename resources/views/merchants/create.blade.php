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
                    <a href="{{ admin_route_url('merchants.index') }}" class="btn btn-box-tool" data-toggle="tooltip"
                       title="Back"> <i
                                class="fa fa-times"></i></a>
                </div>
            </div>
            <div class="box-body">
                <form role="form" action="{{ admin_route_url('merchants.store') }}" method="POST"
                      enctype="multipart/form-data" class="form" accept-charset="utf-8">
                    {{ csrf_field() }}
                    <div class="box-body" id="form-body">
                        <div class="form-group has-feedback">
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
                        <div class="form-group has-feedback">
                            <label for="company_email">Email</label>
                            <input type="email" class="form-control" name="company_email" id="company_email"
                                   value="{{ old('company_email') }}"
                                   placeholder="Enter email" required>
                        </div>

                        <hr>
                        <button class="btn btn-primary" id="add">Add user field</button>
                        <button class="btn btn-danger" id="remove">Remove</button>

                        @for($i=0; $i <= session('countOfUser', 0); ++$i)
                            <div id="user">
                                <hr>
                                <div class="form-group has-feedback">
                                    <label for="name">Name</label>
                                    <input type="text" class="form-control" name="user[name][]" id="name"
                                           value="{{ old('user.name.' . $i) }}" placeholder="Enter user name"
                                           required>
                                </div>
                                <div class="form-group has-feedback">
                                    <label for="email">Email</label>
                                    <input type="email" class="form-control" name="user[email][]" id="email"
                                           value="{{ old('user.email.' . $i) }}" placeholder="Enter email"
                                           required>
                                </div>
                            </div>
                        @endfor
                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer text-right">
                        <button type="submit" class="btn btn-primary" id="submit">
                            <i class="fa fa-save fa-btn"></i> Save All
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
    var counterform = 1;

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
        $("button#add").on('click', function (e) {
            e.preventDefault();
            $("#user").clone().appendTo("#form-body");
            updateCounterForm(false);
            console.log("add");
        });
        $("button#remove").on('click', function (e) {
            e.preventDefault();
            $("#user").last().remove();
            updateCounterForm(true);
            console.log("remove");
        });
    });
</script>