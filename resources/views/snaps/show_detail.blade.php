<div class="table-responsive table-custom">
	@php
		$total_value = $snap->files->pluck('total')->sum();
	@endphp
	<table id="snap-table" class="table" totalValue="{{ $total_value }}">
		<thead>
			<tr>
				<th>Product Item</th>
				<th>Brands</th>
				<th>Variants</th>
				<th>Qty</th>
				<th>Total Price</th>
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
							<td>{{ $tag->total_price }}</td>
						</tr>
					@endforeach
				@endif

			@endforeach
		</tbody>
	</table>
</div>


