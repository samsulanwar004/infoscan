<div class="col-md-4">
	@if($snap->approved_by == null)
	<a href="{{ admin_route_url('snaps.edit', ['id' => $snap->id]) }}" class="btn btn-success btn-block btn-lg"
        data-toggle="modal"
        data-target="#"
        modal-size="modal-lg" 
        title="Edit">
        <i class="fa fa-check-circle-o fa-btn"></i>Approve This Content</a>  
    @else
    	<a href="{{ admin_route_url('snaps.edit', ['id' => $snap->id]) }}" class="btn btn-danger btn-block btn-lg"
        data-toggle="modal"
        data-target="#"
        modal-size="modal-lg" 
        title="Edit">
        <i class="fa fa-check-circle-o fa-btn"></i>This Content Approved</a>
	@endif
	<table class="table">
		<thead>
			<tr>
				<th width="50">No</th>
				<th>Item</th>
				<th width="60">Qty</th>
				<th width="100" class="text-right">Price</th>
			</tr>
		</thead>

		<tbody>
			@foreach($snap->files as $file)

				@if($file->tag)
					@foreach($file->tag as $tag)
						<tr>
							<td>#</td>
							<td>{{ $tag->name }}</td>
							<td>{{ $tag->quantity }}</td>
							<td class="text-right">{{ $tag->total_price }}</td>
						</tr>
					@endforeach
				@endif

			@endforeach
		</tbody>
	</table>
</div>