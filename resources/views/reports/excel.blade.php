<section class="content">
    <div class="box">
        <div class="box-body" id="form-body">
            <table class="table table-striped" border="1">
                <thead>
                    <tr align="center">
                        <th class="outlet_name" name="outlet_name">Outlet Name</th>
                        <th class="products" name="products">Products</th>
                        <th class="users_city" name="users_city">User's City</th>
                        <th class="age" name="age">Age</th>
                        <th class="outlet_area" name="outlet_area">Outlet Area</th>
                        <th class="province" name="province">Province</th>
                        <th class="gender" name="gender">Gender</th>
                        <th class="usership" name="usership">Usership</th>
                        <th class="sec" name="sec">SEC</th>
                        <th class="outlet_type" name="outlet_type">Outlet Type</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reports as $item)
                        <tr align="center">
                            <td>{{ $item->outlet_name }}</td>
                            <td>{{ $item->products }}</td>
                            <td>{{ $item->users_city }}</td>
                            <td>{{ $item->age }}</td>
                            <td>{{ $item->outlet_area }}</td>
                            <td>{{ $item->province }}</td>
                            <td>{{ $item->gender }}</td>
                            <td>{{ $item->usership }}</td>
                            <td>{{ $item->sec }}</td>
                            <td>{{ $item->outlet_type }}</td>
                        </tr>
                    @empty
                            <td colspan="10"> There Is No Record For Report Status Data !!!</td>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</section>