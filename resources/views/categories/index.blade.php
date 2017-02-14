@extends('app')

@section('content')
    @include('partials.content_header', ['pageTitle' => 'Product Categories', 'pageDescription' => 'List of category', 'breadcrumbs' => ['Product Categories' => false]])

    <!-- Main content -->
    <section class="content">

        <!-- Default box -->
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">
                </h3>

                <div class="box-tools pull-right">
                    @cando('ProductCategories.Create')
                    <a href="{{ route('product-categories.create') }}" class="btn btn-box-tool" data-toggle="tooltip"
                       title="Create New">
                        <i class="fa fa-plus-circle fa-btn"></i> Create New</a>
                    @endcando
                </div>
            </div>
            <div class="box-body">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th width="300">Title of Category</th>
                        <th>Icon</th>
                        <th>Background</th>
                        <th width="250"></th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($categories as $category)
                        <tr>
                            <td class="vertical-middle">
                                {{ $category->name }}
                            </td>
                            <td class="vertical-middle">
                                @if($category->icon)
                                    <img src="{{ '/storage/product-categories/'.$category->icon }}" style="width: 50px;">
                                @endif
                            </td>
                            <td class="vertical-middle">
                                @if($category->background)
                                    <img src="{{ '/storage/product-categories/'.$category->background }}" style="width: 150px;">
                                @endif
                            </td>
                            <td class="text-right vertical-middle">
                                <div class="btn-group">
                                    @cando('ProductCategories.Update')
                                    <a href="{{ route('product-categories.edit', ['id' => $category->id]) }}" class="btn btn-info">
                                        <i class="fa fa-pencil"> </i>
                                    </a>
                                    @endcando

                                    @cando('ProductCategories.Delete')
                                    <a class="btn btn-danger"
                                       href="{{ route('product-categories.destroy', ['id' => $category->id]) }}"
                                       data-toggle="modal"
                                       data-target="#"
                                       title="Delete this data"
                                       for-delete="true"
                                       data-message="Are you sure you want to delete this category ?"
                                    > <i class="fa fa-trash"></i> </a>
                                    @endcando
                                </div>
                            </td>
                        </tr>
                    @empty
                        <td colspan="5"> There is no record for product categories data!</td>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <!-- /.box -->

    </section>
    <!-- /.content -->
@endsection