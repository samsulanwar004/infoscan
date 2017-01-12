@extends('app', ['mini_sidebar' => true])

@section('content')
    @include('partials.content_header', [
        'pageTitle' => 'Snaps',
        'pageDescription' => 'Detail of Snap',
        'breadcrumbs' => ['Snap' => admin_route_url('snaps.index'), 'detail' => false]
    ])

    <!-- Main content -->
    <section class="content">

        <!-- Default box -->
        <div class="box">
            <div class="box-header with-border form-inline" style="overflow: hidden; height: 0px; padding: 0px; margin-bottom: 0px;">
                <!-- <i class="fa fa-camera fa-btn"></i> Snap Code: {-- strtoupper($snap->request_code) --} -->
            </div>
            <div class="box-body">
                <div class="row">

                    <div class="col-md-8">
                        <p class="text-muted well well-sm no-shadow">
                            Terimakasih Misbach
                        </p>
                        <div class="timeline-item ">
                            <ul class="timeline timeline-inverse">
                                <li class="no-margin-right">
                                    <i class="fa fa-camera bg-purple"></i>

                                    <div class="timeline-item no-margin-right">
                                        <span class="time"><i class="fa fa-clock-o"></i> {{ $snap->created_at->diffForHumans() }}</span>
                                        <h3 class="timeline-header">
                                            <a href="{{ admin_route_url('members.show', ['id' => $snap->member->id]) }}">{{ $snap->member->name }}</a> uploaded new photos
                                        </h3>
                                        <div class="timeline-body">
                                            @foreach($snap->files as $file)
                                                @if ($file->mode_type == 'audio')
                                                    <img src="{{ URL::to('img/snaps/window-player.png') }}" alt="{{ $file->file_code }}" class="margin img-thumbnail img-responsive img-tag"  id="{{$file->id}}" style="width:150px;height:150px;">
                                                @else
                                                    <img src="{{ config('filesystems.s3url') . $file->file_path }}" alt="{{ $file->file_code }}" class="margin img-thumbnail img-responsive img-tag"  id="{{$file->id}}" style="width:150px;height:150px;">
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                </li>
                            </ul>

                        </div>

                    </div>
                        <div class="col-md-4">
                            <a href="{{ admin_route_url('snaps.edit', ['id' => $snap->id]) }}" class="btn btn-success btn-block btn-lg"
                                data-toggle="modal"
                                data-target="#"
                                modal-size="modal-lg" 
                                title="Edit">
                                <i class="fa fa-check-circle-o fa-btn"></i>Approve This Content</a>  
                            <div class="snaps-detail">
                                @include('snaps.show_detail', ['snap' => $snap])
                            </div>   
                        </div>                     
                </div>
            </div>
        </div>
        <!-- /.box -->
        <a href="#"  style="display: none;"
        data-toggle="modal"
        data-target="#"
        modal-size="modal-lg"
        id="modal-edit"
        title="Edit">
        Edit Select Image</a>
    </section>
    <!-- /.content -->
@endsection

@section('footer_scripts')
<link rel="stylesheet" href="{{ elixir('css/taggd.css') }}">
<script src="{{ elixir('js/taggd.js') }}"></script>
<script src="{{ elixir('js/elevate.js') }}"></script>
<script type="text/javascript">

    $(".img-tag").on('click', function(img) {
        //console.log(img.toElement.id);
        var link = $('#modal-edit');
        var nameLink = img.toElement.id+'/edit-snap-file';
        link.attr('href', nameLink);

        link.trigger('click');

    });

</script>
<style type="text/css">
    img.img-tag 
    {
        cursor: pointer;
    }
</style>
@stop
