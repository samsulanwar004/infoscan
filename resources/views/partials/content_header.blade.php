<section class="content-header">
    <h1>
        {{ $pageTitle }}
        <small>{{ $pageDescription }}</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="/"><i class="fa fa-home"></i> Home</a></li>
        @foreach($breadcrumbs as $key => $b)
            @if(! $b)
                <li class="active">{{ $key }}</li>
            @else
                <li><a href="{{ $b }}">{{ $key }}</a></li>
            @endif
        @endforeach
    </ol>
</section>