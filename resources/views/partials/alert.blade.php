@if($session = session('errors'))
    <div class="alert alert-danger top-alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
        <h4>Error <i class="fa fa-btn fa-exclamation-circle" style="font-size: 20px;"></i></h4>
        @if($session instanceof \Illuminate\Support\ViewErrorBag)
            @foreach($session->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        @else
            <p>{{ $session }}</p>
        @endif

    </div>
@endif

@if($session = session('success'))
    <div class="alert alert-success top-alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
        <h4>Success <i class="fa fa-btn fa-check-circle" style="font-size: 20px;"></i></h4>
        <p>{{ $session }}</p>
    </div>
@endif

@if($session = session('warning'))
    <div class="alert alert-warning top-alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
        <h4>Warning <i class="fa fa-btn fa-exclamation-triangle" style="font-size: 20px;"></i></h4>
        <p>{{ $session }}</p>
    </div>
@endif

