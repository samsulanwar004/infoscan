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
                                        <span class="time"><i class="fa fa-clock-o"></i> 2 days ago</span>
                                        <h3 class="timeline-header">
                                            <a href="{{ admin_route_url('members.show', ['id' => $snap->member->id]) }}">{{ $snap->member->name }}</a> uploaded new photos
                                        </h3>
                                        <div class="timeline-body">
                                            @foreach($snap->files as $file)
                                                <img src="http://placehold.it/150x100" alt="..." class="margin">
                                            @endforeach
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

    </section>
    <!-- /.content -->
@endsection
