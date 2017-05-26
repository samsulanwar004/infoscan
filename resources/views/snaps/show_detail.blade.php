<form id="tagUpdate" action="{{ admin_route_url('approve.image', ['id' => $files->first()->id]) }}"  method="POST">
{{ csrf_field() }}
{{ method_field('PUT') }}
<div class="table-responsive table-custom">
	@php
		$total_value = $snap->files->pluck('total')->sum();
		$totalPage = $files->total();
	@endphp
	<table id="snap-table" class="table" totalValue="{{ $total_value }}">
		<thead>
			<tr>
				<th width="50"></th>
				<th width="100"></th>
				<th width="300">Product Item</th>
				<th width="100">Weight</th>
                <th width="200">Brands</th>
                <th width="200">SKU</th>                
                <th width="100">Qty</th>
                <th width="200">Total Price</th>
			</tr>
		</thead>

		<tbody id="inputs-show">
			@foreach($files->first()->tag as $tag)
				<tr id="input-show">
					<td>
                        <a class="btn btn-box-tool" id="remove-show">
                            <i class="fa fa-remove"></i>
                        </a>
                    </td>
                    <td>
                    	<i class="fa fa-spinner fa-spin fa-2x img-item-loading" id="load-{{ $tag->id }}" aria-hidden="true" style="display: none;"></i>
                        <div id="kotak-drop" class="{{ $tag->id }}" ondrop="drop(event)" ondragover="allowDrop(event)">
                        	@if($tag->crop_file_path)
                        	<img src="{{ config('filesystems.s3url') . $tag->crop_file_path }}" id="img-cropping">
                        	@endif
                        </div>
                        <input type="hidden" name="tag[crop_path][]" id="crop-{{ $tag->id }}" value="{{$tag->crop_file_path}}">
                    </td>
					<td><input type="text" name="tag[name][]" class="form-control input-sm tag-name-show" value="{{ $tag->name }}" placeholder="Product Name" required="required"></td>
					<td><input type="text" name="tag[weight][]" class="form-control input-sm" value="{{ $tag->weight }}" placeholder="Weight"></td>
					<td><input type="text" name="tag[brands][]" class="form-control input-sm" value="{{ $tag->brands }}" placeholder="Brands"></td>
					<td><input type="text" name="tag[sku][]" class="form-control input-sm" value="{{ $tag->sku }}" placeholder="SKU"></td>
					<!-- <td style="max-width: 100px"><input type="text" list="variants" name="tag[variants][]" class="form-control input-sm" value="{{ $tag->variants }}" placeholder="Variants"></td> -->
					<td><input type="number" name="tag[qty][]" class="form-control input-sm" value="{{ $tag->quantity }}" placeholder="QTY" required="required"></td>
					<td><input type="number" name="tag[total][]" class="form-control input-sm" value="{{ clean_numeric($tag->total_price,'%',false,'.') }}" placeholder="Total Price" required="required"></td>
					<input type="hidden" name="tag[id][]" value="{{ $tag->id }}">
				</tr>
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
</div>
<div class="button-container">
	@if($snap->mode_type == 'image' || $snap->mode_type == 'audios')
    <a class="btn btn-default" id="add-show">
        <i class="fa fa-plus fa-btn"></i>Create New
    </a>    
    @endif
    @if($files->first()->mode_type == 'image')
    	<input type="hidden" name="image_approve" value="a">
    @elseif($totalPage == 1)

	@else		
		<select name="image_approve" class="form-control" required="required">
		  <option value="">Select Approve</option>		  
		  @foreach($code as $key => $value)
		  	<option @if($files->first()->image_code == $value) selected @endif value="{{ $value }}">{{ $key }}</option>
		  @endforeach
		  <option @if($files->first()->is_approve == 0) selected @endif value="reject">Reject</option>
		</select>
	@endif
	<input type="hidden" name="mode_type" value="{{$files->first()->mode_type}}">
    <button type="submit" class="btn btn-default" id="submit-tag">
        <i class="fa fa-floppy-o fa-btn"></i> Save
    </button>
</div>   
</form>
@if (isset($js))
<script type="text/javascript">
	$('a#remove-show').on('click', function(e) {
        e.preventDefault();
        if(confirm('Are you sure want to delete this item ?')) {
            $(e.target).closest('#input-show').remove();
        }
    });
</script>
@endif