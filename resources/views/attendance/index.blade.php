@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <h1 class="text-black-50 text-center"> Attendance History </h1>
    </div>
    <table id="attendanceTable" class="table table-bordered mt-4">
        <thead>
            <tr>
                <th scope="col">Member Name</th>
                <th scope="col">Email</th>
                <th scope="col">Session Name</th>
                <th scope="col">Attendance Time</th>
                <th scope="col">Attendance Date</th>
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
            var table = $('#attendanceTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('attendance.index') }}',
                columns: [{
                    data: 'member_name',
                }, {
                    data: 'email',
                }, {
                    data: 'session_name',
                }, {
                    data: 'attendance_time',
                }, {
                    data: 'attendance_date',
                }, {
                    data: 'gym',
                }, {
                    data: 'city',
                }, ]
            });

            // Hide column depending on current user role.
            var userIsAdmin = "{{ Auth::user()->hasRole('admin') }}"
            if (!userIsAdmin) {
                table.column('6').visible(false);
            }

        });
    </script>
@endsection
