<div class="col-md-4">
	<form action="{{ admin_route_url('snaps.update', ['id' => $snap->id]) }}" method="POST">
		{{ csrf_field() }}
    	{{ method_field('PUT') }}
    	@if($snap->approved_by == null)
			<button type="submit" class="btn btn-success btn-block btn-lg"><i class="fa fa-check-circle-o fa-btn"></i> Approve This Content</button>
		@else
			<button class="btn btn-danger btn-block btn-lg" disabled=""><i class="fa fa-check-circle-o fa-btn"></i> This Content Approved</button>
		@endif
	</form>
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