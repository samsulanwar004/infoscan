<table class="table">
	<thead>
		<tr>
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
						<td>{{ $tag->name }}</td>
						<td>{{ $tag->quantity }}</td>
						<td class="text-right">{{ $tag->total_price }}</td>
					</tr>
				@endforeach
			@endif

		@endforeach
	</tbody>
</table>
