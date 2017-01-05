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

                    <div class="{{ $snap->snap_type == 'receipt' ? 'col-md-12' : 'col-md-8' }}">
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
                                            <a href="{{ admin_route_url('members.show', ['id' => $snap->member->id]) }}">{{ $snap->member->name }}</a> uploaded new {{ $snap->mode_type == 'audios' ? 'audios' : 'photos' }}
                                        </h3>
                                        <div class="timeline-body">
                                        @if ($snap->mode_type != 'audios')
                                            @foreach($snap->files as $file)
                                                <img src="{{ $file->file_path }}" alt="{{ $file->file_code }}" class="margin img-thumbnail img-responsive @if($snap->snap_type != 'receipt') img-tag @endif"  id="{{$file->id}}" style="width:150px;height:150px;">
                                            @endforeach
                                        @else
                                            @foreach($snap->files as $file)
                                                <img src="{{ URL::to('img/window-player.png') }}" alt="{{ $file->file_code }}" class="margin img-thumbnail img-responsive @if($snap->snap_type != 'receipt') img-tag @endif"  id="{{$file->id}}" style="width:150px;height:150px;">
                                            @endforeach
                                        @endif
                                        </div>
                                    </div>
                                </li>
                            </ul>

                        </div>

                    </div>

                    @if($snap->snap_type !== 'receipt' && (!empty($snap->mode_type) || !is_null($snap->mode_type)))
                        @include('snaps.show_detail', ['snap' => $snap])
                    @endif
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
    img.img-tag {
        cursor: pointer;
    }
</style>
@stop
