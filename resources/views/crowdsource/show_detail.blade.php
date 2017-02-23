@extends('app')

@section('content')
    @include('partials.content_header', [
        'pageTitle' => 'Crowdsource Activity',
        'pageDescription' => 'Detail of Crowdsource activity',
        'breadcrumbs' => ['Crowdsource account' => admin_route_url('crowdsource.index'),'Crowdsource activity' => admin_route_url('crowdsource.show', ['id' => $crowdsourceActivity->user_id]), 'detail' => false]
    ])

    <!-- Main content -->
    <section class="content">

        <!-- Default box -->
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">
                </h3>

                <div class="box-tools pull-right">
                    <a href="{{ admin_route_url('crowdsource.show', ['id' => $crowdsourceActivity->user_id]) }}" class="btn btn-box-tool" data-toggle="tooltip" title="Back"> <i
                            class="fa fa-times"></i></a>
                </div>
            </div>
            <div class="box-body">
                <div class="row">

                    <div class="col-md-4">
                        <p class="no-shadow">

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
                                                    <img src="{{ URL::to('img/snaps/window-player.png') }}" alt="{{ $file->file_code }}" class="margin img-thumbnail img-responsive img-tag"  id="{{$file->id}}">
                                                @else
                                                    <img src="{{ config('filesystems.s3url') . $file->file_path }}" alt="{{ $file->file_code }}" class="margin img-thumbnail img-responsive img-tag"  id="{{$file->id}}">
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                </li>
                            </ul>

                        </div>

                    </div>   
                    <div class="col-md-6">
                        <p class="no-shadow">

                        </p>
                        <div class="timeline-item ">
                            <ul class="timeline timeline-inverse">
                                <li class="no-margin-right">
                                  <i class="fa fa-list bg-blue"></i>

                                  <div class="timeline-item no-margin-right">
                                    <span class="time"><i class="fa fa-clock-o"></i> {{ $snap->created_at->diffForHumans() }}</span>

                                    <h3 class="timeline-header"><a href="{{ admin_route_url('members.show', ['id' => $snap->member->id]) }}">{{ $snap->member->name }}</a> detail product</h3>

                                    <div class="timeline-body">
                                      <div class="table-responsive table-custom">
                                        <table id="snap-table" class="table">
                                            <thead>
                                                <tr>
                                                    <th width="300">Product Item</th>
                                                    <th width="300">Brands</th>
                                                    <th width="300">Variants</th>
                                                    <th width="100">Qty</th>
                                                    <th width="200">Total Price</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                @foreach($snap->files as $file)

                                                    @if($file->tag)
                                                        @foreach($file->tag as $tag)
                                                            <tr>
                                                                <td>{{ $tag->name }}</td>
                                                                <td>{{ $tag->brands }}</td>
                                                                <td style="max-width: 100px">{{ $tag->variants }}</td>
                                                                <td>{{ $tag->quantity }}</td>
                                                                <td>{{ clean_numeric($tag->total_price,'%',false,'.') }}</td>
                                                            </tr>
                                                        @endforeach
                                                    @endif

                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    </div>
                                  </div>
                                </li>
                            </ul>

                        </div>

                    </div>   
                    <div class="col-md-2">
                        <p class="no-shadow">

                        </p>
                        <table class="table table-bordered">
                            <tr>
                                <th>Action</th>
                                <th style="width: 40px">Total</th>
                            </tr>
                            <tr>
                                <td>Add Tag</td>
                                <td><span class="badge bg-green">{{ $extract->data->add_tag }}</span></td>
                            </tr>
                            <tr>
                                <td>Edit Tag</td>
                                <td><span class="badge bg-blue">{{ $extract->data->edit_tag }}</span></td>
                            </tr>                            
                          </table>
                    </div>          
                </div>
            </div>
        </div>
        <!-- /.box -->
    </section>
    <!-- /.content -->
@endsection

@section('footer_scripts')
<style type="text/css">
    div.timeline-body img {
        width: 95%;
        background-size: 100% 100%;
    }

        div.table-custom table {
        border-collapse: separate;
        border-spacing: 0 5px;
    }

    div.table-custom thead th {
        background-color: #EA5941;
        color: white;
    }

    div.table-custom tbody td {
        background-color: rgba(14, 14, 14, 0.1);
    }

    div.table-custom tr td:first-child,
    div.table-custom tr th:first-child {
        border-top-left-radius: 6px;
        border-bottom-left-radius: 6px;
    }

    div.table-custom tr td:last-child,
    div.table-custom tr th:last-child {
        border-top-right-radius: 6px;
        border-bottom-right-radius: 6px;
    }
</style>
@endsection
