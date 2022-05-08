@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <h1 class="text-black-50 text-center"> Sessions </h1>
    </div>
    <div class="d-flex justify-content-end mb-3">
        <a class="btn btn-success" href="javascript:void(0)" id="createNewSession">Add New Session</a>
    </div>
    <table id="sessionsTable" class="table table-bordered mt-4">
        <thead>
            <tr>
                <th scope="col">Session Name</th>
                <th scope="col">Day</th>
                <th scope="col">Starts At</th>
                <th scope="col">Ends At</th>
                <th scope="col">Gym</th>
                <th scope="col">City</th>
                <th scope="col">Coaches</th>
                <th scope="col">Members</th>
                <th scope="col">Actions</th>
            </tr>
        </thead>
    </table>

    <!-- Create and update Hidden div -->
    <div class="modal fade" id="insertModel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modelHeading"></h4>
                </div>
                <div class="modal-body">
                    <form method="post" id="sessionForm" name="sessionForm" class="form-horizontal"
                        enctype="multipart/form-data">
                        <div class="alert alert-danger print-error-msg" style="display:none">
                            <ul></ul>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-12 control-label">Session Name</label>
                            <div class="col-sm-12">
                                <input type="text" class="form-control" id="name" name="name"
                                    placeholder="Enter Session Name" value="" maxlength="255" required="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-12 control-label">Session Start Date & Time</label>
                            <div class="col-sm-12">
                                <input type="datetime-local" id="starts_at" name="starts_at" value="" required="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-12 control-label">Session End Date & Time</label>
                            <div class="col-sm-12">
                                <input type="datetime-local" id="finishes_at" name="finishes_at" value="" required="">
                            </div>
                        </div>

                        <div class="form-group" @hasrole('gym_manager') style="display:none" @endhasrole>
                            <label class="col-sm-12 control-label">Select Gym</label>
                            <select id="gymSelect" class="col-sm-12">
                                @foreach ($gyms as $gym)
                                    <option value="{{ $gym->id }}">{{ $gym->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group d-flex flex-column">
                            <label class="col-sm-12 control-label">Select Coaches</label>
                            <div class="d-flex align-items-center mx-2 flex-wrap">
                                @foreach ($coaches as $coach)
                                    <label class="mx-2 d-flex">
                                        <input type="checkbox" class="coachesSelect" name="coaches"
                                            value="{{ $coach->id }}">
                                        {{ $coach->name }}
                                    </label><br>
                                @endforeach
                            </div>
                        </div>
                        
                        <div class="col-sm-offset-2 col-sm-10">
                            <button type="submit" class="btn btn-primary" id="saveBtn" value="create">Save changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete confiramtion box div -->
    <div class="modal fade" id="deleteModel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="deleteHeading"></h4>
                </div>
                <div class="modal-body">
                    <form method="post" id="deleteForm" name="deleteForm" class="form-horizontal">
                        <div class="alert alert-danger print-error-msg" style="display:none">
                            <ul></ul>
                        </div>
                        @csrf
                        @method('DELETE')
                        <div>Are you sure you want to delete this Session?</div>
                        <div>This action cannot be undone.</div>
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-danger mx-1" id="deleteBtn" value="delete">Confirm Delete
                            </button>
                            <button type="button" class="btn btn-secondary mx-1" id="deleteCancelBtn" value="cancel">Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        var session_id;
        var editor;

        $(document).ready(function() {
            $.noConflict();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var table = $('#sessionsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('sessions.index') }}',
                columns: [{
                        data: 'name',
                    },
                    {
                        data: 'day',
                    },
                    {
                        data: 'starts_at',
                    },
                    {
                        data: 'finishes_at',
                    },
                    {
                        data: 'gym',
                    },
                    {
                        data: 'city',
                    },
                    {
                        data: 'coaches',
                    },
                    {
                        data: 'members',
                    },
                    {
                        data: 'action',
                    },
                ]
            });

            // Hide column depending on current user role.
            var userIsCityManager = "{{ Auth::user()->hasRole('city_manager') }}"
            if (userIsCityManager) {
                table.column('5').visible(false);
            }

            var userIsGymManager = "{{ Auth::user()->hasRole('gym_manager') }}"
            if (userIsGymManager) {
                table.column('5').visible(false);
                table.column('4').visible(false);
            }


            // Create button action.
            $('#createNewSession').click(function() {
                $(".print-error-msg").css('display', 'none');

                $('#saveBtn').val("create-session");
                $('#sessionForm').trigger("reset");
                $('#modelHeading').html("Add New Session");
                $('#insertModel').modal('show');
            });

            // Edit user button action.
            $('body').on('click', '.editSession', function() {
                $(".print-error-msg").css('display', 'none');

                session_id = $(this).data('id');
                var name = $(this).parent().siblings()[0].innerHTML;
                $.get("{{ route('sessions.index') }}" + '/' + session_id + '/edit', function(
                    data) {
                    $('#modelHeading').html("Edit Session");
                    $('#saveBtn').val("edit-session");
                    $('#insertModel').modal('show');
                    $('#name').val(name);
                })
            });

            // Handling both Create and edit ajax requests.
            $('#saveBtn').click(function(e) {
                e.preventDefault();

                let selectedCoaches = [];
                $.each($("input[name='coaches']:checked"), function() {
                    selectedCoaches.push($(this).val());
                });

                let selectedMembers = [];
                $.each($("input[name='members']:checked"), function() {
                    selectedMembers.push($(this).val());
                });

                $(this).html('Sending..');
                var request_is_create = $('#modelHeading').html() == "Add New Session";
                var url = request_is_create ? "/sessions" : "/sessions/" + session_id;
                var method = request_is_create ? "POST" : "PUT";
                var myFormData = new FormData();
                myFormData.append('_method', method);
                myFormData.append('session_id', session_id);
                myFormData.append('name', $('#name').val());
                myFormData.append('starts_at', ($('#starts_at').val().replace("T", " ")).concat(':00'));
                myFormData.append('finishes_at', ($('#finishes_at').val().replace("T", " ")).concat(':00'));
                myFormData.append('coaches', selectedCoaches);
                myFormData.append('has_sessions_id', $('#gymSelect').find(":selected").val());

                $.ajax({
                    url: url,
                    type: "POST",
                    processData: false,
                    contentType: false,
                    cache: false,
                    data: myFormData,
                    success: function(data) {
                        if ($.isEmptyObject(data.error)) {
                            $('#sessionForm').trigger("reset");
                            $('#insertModel').modal('hide');
                            $('#sessionsTable').DataTable().ajax.reload();
                        } else {
                            printErrorMsg(data.error);
                        }
                    },
                    error: function(data) {
                        printErrorMsg(data);
                    }
                });
                $('#saveBtn').html('Save Changes');
            });


            // Delete button action.
            $('body').on('click', '.deleteSession', function() {
                session_id = $(this).data('id');
                $('#deleteBtn').val("Delete");
                $('#deleteForm').trigger("reset");
                $('#deleteHeading').html("Delete confirmation");
                $('#deleteModel').modal('show');
            });

            // Handling delete ajax request.
            $('#deleteBtn').click(function(e) {
                e.preventDefault();
                $(this).html('Deleting..');
                var url = "/sessions/" + session_id;
                $.ajax({
                    url: url,
                    data: {
                        "session_id": session_id,
                    },
                    type: "DELETE",
                    dataType: 'json',
                    success: function(data) {
                        $('#sessionsTable').DataTable().ajax.reload();
                        $('#deleteModel').modal('hide');
                    },
                    error: function(data) {
                        printErrorMsg(data);
                    }
                });
                $('#deleteBtn').html('Confirm Delete');
            });

            $('#deleteCancelBtn').click(function(e) {
                $('#deleteModel').modal('hide');
            });
        });

        function printErrorMsg(data) {
            $(".print-error-msg").find("ul").html('');
            $(".print-error-msg").css('display', 'block');

            $.each(data.responseJSON.errors, function(key, value) {
                $(".print-error-msg").find("ul").append('<li>' + value[0] + '</li>');
            });
            if (data.responseJSON.error != null) {
                $(".print-error-msg").find("ul").append('<li>' + data.responseJSON.error + '</li>');
            }
        }
    </script>
@endsection
