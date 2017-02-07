<div class="table-responsive table-custom">
	@php
		$total_value = $snap->files->pluck('total')->sum();
	@endphp
	<table id="snap-table" class="table" totalValue="{{ $total_value }}">
		<thead>
			<tr>
				<th width="50"></th>
				<th width="300">Product Item</th>
                <th width="300">Brands</th>
                <th width="300">Variants</th>
                <th width="100">Qty</th>
                <th width="200">Total Price</th>
			</tr>
		</thead>

		<tbody id="inputs-show">
			@foreach($snap->files as $file)

				@if($file->tag)
					@foreach($file->tag as $tag)
						<tr id="input-show">
							<td>
                                <a class="btn btn-box-tool" id="remove-show">
                                    <i class="fa fa-remove"></i>
                                </a>
                            </td>
							<td><input type="text" name="tag[name][]" class="form-control input-sm tag-name-show" value="{{ $tag->name }}" placeholder="Product Name" required="required"></td>
							<td><input type="text" name="tag[brands][]" class="form-control input-sm" value="{{ $tag->brands }}" placeholder="Brands"></td>
							<td style="max-width: 100px"><input type="text" list="variants" name="tag[variants][]" class="form-control input-sm" value="{{ $tag->variants }}" placeholder="Variants"></td>
							<td><input type="number" name="tag[qty][]" class="form-control input-sm" value="{{ $tag->quantity }}" placeholder="QTY" required="required"></td>
							<td><input type="number" name="tag[total][]" class="form-control input-sm" value="{{ clean_numeric($tag->total_price,'%',false,'.') }}" placeholder="Total Price" required="required"></td>
							<input type="hidden" name="tag[id][]" value="{{ $tag->id }}">
						</tr>
					@endforeach
				@endif

			@endforeach
		</tbody>
	</table>
	<datalist id="variants">
	  <option value="PC Packaging: Roll On| PC Weight: ">
	  <option value="PC Packaging: Spray| PC Weight: ">
	  <option value="PC Packaging: Pack| PC Weight: ">
	  <option value="PC Packaging: Bottle| PC Weight: ">
	  <option value="PC Packaging: Sachet| PC Weight: ">
	</datalist>

	@if($snap->mode_type != 'tags')
	<div class="button-container">
        <a class="btn btn-default" id="add-show">
            <i class="fa fa-plus fa-btn"></i>Create New
        </a>
    </div>
    @endif
</div>


