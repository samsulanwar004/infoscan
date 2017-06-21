@extends('app')

@section('content')
    @include('partials.content_header', ['pageTitle' => 'Referral Points', 'pageDescription' => 'List of referral point', 'breadcrumbs' => ['Referrals' => false]])

    <!-- Main content -->
    <section class="content">

        <!-- Default box -->
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">
                </h3>

                <div class="box-tools pull-right">
                    @cando('Referral.Create')
                    <a href="{{ route('referral.create') }}" class="btn btn-box-tool" data-toggle="tooltip"
                       title="Create New">
                        <i class="fa fa-plus-circle fa-btn"></i> Create New</a>
                    @endcando
                </div>
            </div>
            <div class="box-body">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th width="200">Description</th>
                        <th>Referral Point</th>
                        <th>Referrer Point</th>
                        <th>Created Date</th>
                        <th width="250"></th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($referrals as $referral)
                        <tr>
                            <td class="vertical-middle">
                                {{ $referral->description }} <br>
                            </td>
                            <td class="vertical-middle">
                                {{ number_format($referral->referral_point, 0,0,'.') }} Pts
                            </td>
                            <td class="vertical-middle">
                                {{ number_format($referral->referrer_point, 0,0,'.') }} Pts
                            </td>
                            <td class="vertical-middle">
                                {{ date_format(date_create($referral->created_at), 'M, d Y') }}
                            </td>
                            <td class="text-right vertical-middle">
                                <div class="btn-group">
                                    @cando('Referral.Update')
                                    <a href="{{ route('referral.edit', ['id' => $referral->id]) }}" class="btn btn-info">
                                        <i class="fa fa-pencil"> </i>
                                    </a>
                                    @endcando

                                    @cando('Referral.Delete')
                                    <a class="btn btn-danger"
                                       href="{{ route('referral.destroy', ['id' => $referral->id]) }}"
                                       data-toggle="modal"
                                       data-target="#"
                                       title="Delete this data"
                                       for-delete="true"
                                       data-message="Are you sure you want to delete this referral ?"
                                    > <i class="fa fa-trash"></i> </a>
                                    @endcando
                                </div>
                            </td>
                        </tr>
                    @empty
                        <td colspan="8"> There is no record for referrals data!</td>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <!-- /.box -->

    </section>
    <!-- /.content -->
@endsection