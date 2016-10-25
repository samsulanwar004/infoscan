@if($session = session('errors'))
    <div class="alert alert-danger top-alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4>Error!</h4>
        <p>{{ $session }}</p>
    </div>
@endif

@if($session = session('success'))
    <div class="alert alert-success top-alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4>Success!</h4>
        <p>{{ $session }}</p>
    </div>
@endif