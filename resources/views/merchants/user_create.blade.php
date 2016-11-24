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
                        <button class="btn btn-primary" id="add">Add user field</button>
                        <button class="btn btn-danger" id="remove">Remove</button>
                        @for($i=0; $i <= session('countOfUser', 0); ++$i)
                        <div id="user">
                            <hr>
                            <div class="form-group has-feedback">
                                <label for="name">Name</label>
                                <input type="text" class="form-control" name="user[name][]" id="name"
                                       value="{{ old('user.name.' . $i) }}" placeholder="Enter user name" required>
                            </div>
                            <div class="form-group has-feedback">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" name="user[email][]" id="email"
                                       value="{{ old('user.email.' . $i) }}" placeholder="Enter email" required>
                            </div>
                            <div class="form-group">
                                <label for="merchant">Select Merchant</label>
                                <select name="user[merchant][]" id="merchant" class="form-control">
                                    <option selected>Select merchant</option>
                                    @foreach($merchants as $merchant)
                                        <option value="{{ $merchant->id }}"
                                        @if($merchant->id == $merchant->id) selected @endif>{{ $merchant->company_name }}</option>
                                    @endforeach
                                </select>
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
            <div id="loading"></div>
        </div>
        <!-- /.box -->

    </section>
    <!-- /.content -->
@endsection

@section('footer_scripts')
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
    }
    $(document).ready(function () {
        $("button#add").click(function () {
            $("#user").clone().appendTo("#form-body");
            updateCounterForm(false);
        });
        $("button#remove").click(function () {
            $("#user").last().remove();
            updateCounterForm(true);
        });
    });

    function myLoading() {
        $('#loading').addClass('overlay');
        document.getElementById("loading").innerHTML = '<i class="fa fa-refresh fa-spin"></i>';
    }
</script>
@endsection