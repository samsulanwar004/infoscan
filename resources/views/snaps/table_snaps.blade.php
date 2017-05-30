<table class="table table-striped">
    <thead>
        <tr>
            <th width="25">#</th>
            <th width="100">Code</th>
            <th>Snap Details</th>
            <th>Estimated Point</th>
            <th>Fixed Point</th>
            <th width="150">Upload Date</th>
            <th width="50"></th>
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
                    <span class="small pull-left">
                        Total image: {{ $snap->files->count() }}
                    </span>

                    <span class="small ml10 pull-left" style="min-width: 250px;">Uploaded by: <a href="{{ admin_route_url('members.show', ['id' => $snap->member->id]) }}">{{ $snap->member->email }}</a></span>
                    <span class="small ml10 pull-left" style="min-width: 100px;">Point : {{ $snap->member->temporary_point }}</span>
                    <span class="small ml10 pull-left">Level : {{ $snap->member->temporary_level }}</span>
                </td>
                <td class="vertical-middle text-center">
                    {{ $snap->estimated_point }}
                </td>
                <td class="vertical-middle text-center">
                    @if($snap->fixed_point) {{ $snap->fixed_point }} @else n\a @endif
                </td>
                <td class="vertical-middle">
                    {{ $snap->created_at->format('d M y H:i A') }} <br>
                </td>
                <td class="text-right vertical-middle">
                    <div class="btn-group">
                        @cando('Snaps.Show')
                        <a href="{{ admin_route_url('snaps.show', ['id' => $snap->id]) }}" class="btn btn-primary" @if($admin) target="_blank" @endif>
                            <i class="fa fa-search"> </i>
                        </a>
                        @endcando
                    </div>
                </td>
            </tr>
        @empty
            <td colspan="6"> There is no record for snaps data!</td>
        @endforelse
    </tbody>
</table>

{{ $snaps->links() }}