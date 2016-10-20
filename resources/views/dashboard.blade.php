@extends('app')

@section('content')
    <div class="col-xs-12">
        <form action="{{ admin_route_url('transfer') }}" enctype="multipart/form-data" method="post">
            {{ csrf_field() }}
            <div class="form-group">
            	<label for="inputID" class="col-sm-2 control-label">Images:</label>
            	<div class="col-sm-10">
            		<input type="file" name="images[]" id="inputID" class="form-control" multiple>
            	</div>
                
                <div class="form-group">
                	<div class="col-sm-10 col-sm-offset-2">
                		<button type="submit" class="btn btn-primary">Submit the Images</button>
                	</div>
                </div>
            </div>
        </form>
    </div>
@endsection