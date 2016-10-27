@extends('app')

@section('content')
    @include('partials.content_header', ['pageTitle' => 'Roles', 'pageDescription' => 'Edit role', 'breadcrumbs' => ['Roles' => '/users/roles', 'Edit' => false]])

    <!-- Main content -->
    <section class="content">

        <form role="form" action="{{ route('roles.update', ['id' => $role->id]) }}" method="POST">
        {{ csrf_field() }}
        {{ method_field('PUT') }}

        <!-- Default box -->
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title"></h3>

                    <div class="box-tools pull-right">
                        <a href="/users/roles" class="btn btn-box-tool" data-toggle="tooltip" title="Back">
                            <i class="fa fa-times"></i>
                        </a>
                    </div>
                </div>
                <div class="box-body">
                    <div class="box-body">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Enter name"
                                   value="{{ old('name', $role->role_name) }}" required autofocus>
                        </div>


                        <div class="checkbox">
                            <label>
                                <input name="is_active"
                                       {{ 'on' == old('is_active') || true === (bool)$role->is_active ? 'checked' : '' }} type="checkbox">
                                Is Active ?
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.box -->


            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title">
                        <i class="fa fa-key"></i> Permissions (Policy)

                    </h3>

                    <div class="box-tools pull-right">
                        <span title="Create New" data-toggle="tooltip">
                            <a href="/users/permissions/create" class="btn btn-box-tool"
                               data-toggle="modal"
                               data-target="#"
                            >
                                <i class="fa fa-plus-circle fa-btn"></i> Add New Permissions</a>
                        </span>
                    </div>
                </div>
                <div class="box-body">
                    <div class="box-body">
                        <div class="form-group">
                            <label for="name">Search Permissions</label>
                            <input type="text"
                                   class="form-control search"
                                   id="search_permission"
                                   name="search_permission search"
                                   placeholder="Search by name"
                                   value="{{ old('name') }}"
                            >
                        </div>

                        @if($permissions->count() > 0)
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>
                                        <input type="checkbox"
                                               name="select_all"
                                               data-toggle="tooltip"
                                               title="Select All"
                                               id="select_all" style="cursor:pointer;">
                                    </th>
                                    <th>Name</th>
                                </tr>
                                </thead>
                                <tbody id="permission_list">
                                @foreach($permissions as $permission)
                                    <tr>
                                        <td class="group_name">
                                            <input type="checkbox"
                                                   {{ $currentPermissions->contains('id', $permission->id) ? 'checked' : '' }}
                                                   name="permissions[]"
                                                   value="{{ $permission->id }}">
                                        </td>
                                        <td>{{ $permission->permission_name }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                    <!-- /.box-body -->

                    <div class="box-footer text-right">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save fa-btn"></i> Save Roles
                        </button>
                    </div>
                </div>
            </div>

        </form>

    </section>
    <!-- /.content -->
@endsection

@section('footer_scripts')
    <script>
        $(document).ready(function () {
            $("#search_permission").keyup(function () {
                //split the current value of searchInput
                var data = this.value.split(" ");
                //create a jquery object of the rows
                var jo = $("#permission_list").find("tr");
                if (this.value == "") {
                    jo.show();
                    return;
                }
                //hide all the rows
                jo.hide();

                //Recusively filter the jquery object to get results.
                jo.filter(function (i, v) {
                    var $t = $(this);
                    for (var d = 0; d < data.length; ++d) {
                        /*if ($t.is(":contains('" + data[d] + "')")) {
                         return true;
                         }*/

                        if ($t.text().toLowerCase().indexOf(data[d].toLowerCase()) > -1) {
                            return true;
                        }
                    }
                    return false;
                }).show();
            }).focus(function () {
                this.value = "";
                $(this).css({
                    "color": "black"
                });
                $(this).unbind('focus');
            }).css({
                "color": "#C0C0C0"
            });


            $("#select_all").on('click', function (e) {
                var table = $(e.target).closest('table');
                $('td input:checkbox', table).prop('checked', this.checked);
            })
        })
    </script>
@stop