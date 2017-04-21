<table class="table table-striped">
    <thead>
    <tr>
        <th width="100">Id Snap</th>
        <th>Snap Code</th>
        <th>Action</th>
        <th>Point</th>
        <th>Comment</th>
        <th>Add Tag</th>
        <th>Edit Tag</th>
        <th>Date</th>
        <th>Time</th>
        <th width="250"></th>
    </tr>
    </thead>
    <tbody>
    @forelse($activities as $activity)
        @php
            $extract = json_decode($activity->description);
        @endphp
        <tr>
            <td class="vertical-middle">
                {{ $extract->data->snap_id }}
            </td>
            <td>
                {{ isset($extract->snap_code) ? $extract->snap_code : '' }}
            </td>
            <td>
                {{ $extract->action }}
            </td>
            <td>
                {{ isset($extract->point) ? $extract->point : '' }}
            </td>
            <td>
                {{ $extract->data->comment }}
            </td>
            <td>
                {{ $extract->data->add_tag }}
            </td>
            <td>
                {{ $extract->data->edit_tag }}
            </td>
            <td>
                {{ date_format(date_create($activity->created_at), 'M, d Y') }}
            </td>
            <td>
                {{ date_format(date_create($activity->created_at), 'H:i') }}
            </td>
            <td class="text-right vertical-middle">
                <div class="btn-group">
                    <a href="{{ route('crowdsource.detail-activity', ['id' => $activity->id]) }}" class="btn btn-primary"><i class="fa fa-search"> </i></a>
                </div>
            </td>
        </tr>
    @empty
        <td colspan="8"> There is no record for crowdsource activities data!</td>
    @endforelse
    </tbody>
</table>

{{ $activities->links() }}
