<table class="table table-striped">
    <thead>
        <tr>
            <th width="25">#</th>
            <th width="100">Code</th>
            <th>Snap Details</th>
            <th width="250"></th>
        </tr>
    </thead>
    <tbody>
        @forelse($snaps as $snap)
            <tr>
                <td class="vertical-middle">
                    @if ($snap->status == 'approve')
                        <i class="fa fa-check-circle text-green"></i>
                    @elseif ($snap->status == 'reject')
                        <i class="fa fa-times-circle text-red"></i>
                    @else
                        <i class="fa fa-check-circle text-default"></i>
                    @endif
                </td>
                <td class="vertical-middle">
                    {{ strtoupper($snap->request_code) }} <br>
                </td>
                <td class="vertical-middle">
                    <span>
                        <b>{{ strtoupper($snap->snap_type) }}</b> Snap {{$snap->snap_type !== 'receipt' && (!is_null($snap->mode_type) || !empty($snap->snap_type)) ? 'with ' . strtoupper($snap->mode_type) . ' mode.' : '' }}
                    </span>
                    <br>
                    <span class="small">
                        Total image: @if ($snap->mode_type == 'audios'){{ round($snap->files->count()/2) }} @else {{ $snap->files->count() }} @endif
                    </span>

                    <span class="small ml10">Uploaded by: <a href="{{ admin_route_url('members.show', ['id' => $snap->member->id]) }}">{{ $snap->member->email }}</a></span><span class="small ml10">Point : {{ $snap->member->temporary_point }}</span><span class="small ml10">Level : {{ $snap->member->temporary_level }}</span>
                </td>
                <td class="text-right vertical-middle">
                    <div class="btn-group">
                        @cando('Snaps.Show')
                        <a href="{{ admin_route_url('snaps.show', ['id' => $snap->id]) }}" class="btn btn-primary">
                            <i class="fa fa-search"> </i>
                        </a>
                        @endcando
                    </div>
                </td>
            </tr>
        @empty
            <td colspan="4"> There is no record for snaps data!</td>
        @endforelse
    </tbody>
</table>

{{ $snaps->links() }} 