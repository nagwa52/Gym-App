@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <h1 class="text-black-50 text-center"> Revenue </h1>
    </div>
    <table id="revenueTable" class="table table-bordered mt-4">
        <br>
        <center>
            <h2>Total Revenue</h2>
            <h2>{{$total_orders}} LE</h2>
        </center>
        <thead>
            <tr>
                <th scope="col">Member Name</th>
                <th scope="col">Email</th>
                <th scope="col">Package Name</th>
                <th scope="col">Purchase Price</th>
                <th scope="col">Gym</th>
                <th scope="col">City</th>
            </tr>
        </thead>
    </table>
    <script>
        $(document).ready(function() {
            $.noConflict();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            var table = $('#revenueTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('revenue.index') }}',
                columns: [{
                    data: 'member_name',
                }, {
                    data: 'email',
                }, {
                    data: 'package_name',
                }, {
                    data: 'price',
                }, {
                    data: 'gym',
                }, {
                    data: 'city',
                }, ]
            });


            // Hide column depending on current user role.
            var userIsCityManager = "{{ Auth::user()->hasRole('city_manager') }}"
            if (userIsCityManager) {
                table.column('5').visible(false);
            }

        });
    </script>
@endsection
