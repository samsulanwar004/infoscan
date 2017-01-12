@extends('app')

@section('content')
    @include('partials.content_header', ['pageTitle' => 'Edit', 'pageDescription' => 'my profile', 'breadcrumbs' => ['Profile' => false]])

    <!-- Main content -->
    <section class="content">

        <!-- Default box -->
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-btn fa-lock"></i> Update Credential</h3>
            </div>
            <div class="box-body">
                <form role="form" action="/users/{{ $user->id }}/credential" method="POST" id="profile">
                    {{ csrf_field() }}
                    {{ method_field('PUT') }}
                    <div class="box-body">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Enter name"
                                   value="{{ old('name', $user->name) }}" required autofocus>
                        </div>
                        <div class="form-group">
                            <label for="email">Email address</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="Enter email"
                                   value="{{ old('email', $user->email) }}" required>
                        </div>

                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" class="form-control" id="password" name="password"
                                   value="{{ old('password', '******') }}" placeholder="Password" required>
                        </div>
                        <div class="form-group">
                            <label for="confirm_password">Confirm Password</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password"
                                   value="{{ old('confirm_password', '******') }}" placeholder="Password" required>
                        </div>
                    </div>
                    <!-- /.box-body -->

                    <div class="box-footer text-right">
                        <button type="submit" class="btn btn-primary" id="save">
                            <i class="fa fa-save fa-btn"></i> Update Credential
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <!-- /.box -->
    </section>
    <!-- /.content -->
@endsection

@section('footer_scripts')
<script>

    $("#password, #confirm_password").keyup(function() {
        var pass1 = document.getElementById("password").value;
        var pass2 = document.getElementById("confirm_password").value;

        if (pass1 != pass2) {
            document.getElementById("password").style.borderColor = "#E34234";
            document.getElementById("confirm_password").style.borderColor = "#E34234";
            document.getElementById("save").disabled = true;
        } else {
            document.getElementById("password").style.borderColor = "#00ff00";
            document.getElementById("confirm_password").style.borderColor = "#00ff00";
            document.getElementById("save").disabled = false;
        }
    });

    jQuery( document ).ready( function( $ ) {

        $( '#profile' ).on( 'submit', function() {

            $.ajax({
              url: this.action,
              type: "put",
              data: {
                '_token':$('input[name=_token]').val(),
                '_method':$('input[name=_method]').val(),  
                'name':$('input[name=name]').val(), 
                'email':$('input[name=email]').val(), 
                'password':$('input[name=password]').val(),                   
            },
                success: function(msg){
                    REBEL.smallNotifTemplate(msg.message, 'body', 'success');
                    setTimeout(function () {
                        REBEL.removeAllMessageAlert();
                    }, 3000)
                },
                error: function (msg) {
                    REBEL.smallNotifTemplate(msg.responseText, 'body', 'error');
                    setTimeout(function () {
                        REBEL.removeAllMessageAlert();
                    }, 3000)
                }
            });                        
            return false;
        } );
     
    } );
</script>
@endsection
