@extends('admin.app')
@section('breadcrumbs')
	<li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Dashboard</a></li>
	<li class="breadcrumb-item"><a href="{{route('admin.category.index')}}">Categories</a></li>
	<li class="breadcrumb-item active" aria-current="page">Add/Edit Category</li>
@endsection
@section('content')
{{--  two routes for add category and edit category depending upon the coming data from controller --}}
<form action="@if(isset($category)) {{route('admin.category.update', $category)}}
              @else {{route('admin.category.store')}} @endif"
        method="post" accept-charset="utf-8">
    @csrf

    {{--  if we are updating the category, we have to use PUT method  --}}
	@if(isset($category))
		@method('PUT')
	@endif
	<div class="form-group row">
		<div class="col-sm-12">
			@if ($errors->any())
			<div class="alert alert-danger">
				<ul>
					@foreach ($errors->all() as $error)
					<li>{{ $error }}</li>
					@endforeach
				</ul>
			</div>
			@endif
		</div>
		<div class="col-sm-12">
			@if (session()->has('message'))
			<div class="alert alert-success">
				{{session('message')}}
			</div>
			@endif
		</div>
		<div class="col-sm-12">
            <label class="form-control-label">Title: </label>
            {{--  display category fields if this page is used for Edit the category  --}}
            {{--  {{@$category->title}} - here @ makes this $category->title as not required  --}}
			<input type="text" id="txturl" name="title" class="form-control " value="{{@$category->title}}">
			<p class="small">{{config('app.url')}}<span id="url">{{@$category->slug}}</span></p>
			<input type="hidden" name="slug" id="slug" value="{{@$category->slug}}">
		</div>
	</div>
	<div class="form-group row">

		<div class="col-sm-12">
			<label class="form-control-label">Description: </label>
			<textarea name="description" id="editor" class="form-control " rows="10" cols="80">{!! @$category->description !!}</textarea>
		</div>
	</div>
	<div class="form-group row">
        {{--  here sort out the currently selected ids  --}}
        {{--  if this category has any parent else ids = null --}}
        {{--  isset is required, because count function can not run on null   --}}
        @php
			$ids = (isset($category->parents) && $category->parents->count() > 0 ) ? array_pluck($category->parents, 'id') : null
        @endphp
        {{--  {{dd($ids)}}  --}}
		<div class="col-sm-12">
			<label class="form-control-label">Select Category: </label>
			<select name="parent_id[]" id="parent_id" class="form-control" multiple>
				@if(isset($categories))
				<option value="0">Top Level</option>
                @foreach($categories as $cat)
                {{--  if ids is not null and it have some id in its array, then populate it as selected  --}}
				<option value="{{$cat->id}}" @if(!is_null($ids) && in_array($cat->id, $ids)) {{'selected'}} @endif>{{$cat->title}}</option>
				@endforeach
				@endif
				option
			</select>
		</div>
	</div>
	<div class="form-group row">
		<div class="col-sm-12">
			@if(isset($category))
			<input type="submit" name="submit" class="btn btn-primary" value="Update Category" />
			@else
			<input type="submit" name="submit" class="btn btn-primary" value="Add Category" />
			@endif
		</div>

	</div>

</form>
@endsection
@section('scripts')
<script type="text/javascript">
$(function(){
	ClassicEditor.create( document.querySelector( '#editor' ), {
		toolbar: [ 'Heading', 'Link', 'bold', 'italic', 'bulletedList', 'numberedList', 'blockQuote','undo', 'redo' ],
	}).then( editor => {
		console.log( editor );
	}).catch( error => {
		console.error( error );
	});
	$('#txturl').on('keyup', function(){
		var url = slugify($(this).val());
		$('#url').html(url);
		$('#slug').val(url);
    })

	$('#parent_id').select2({
		placeholder: "Select a Parent Category",
		allowClear: true,
		minimumResultsForSearch: Infinity
	});
})
</script>
@endsection
