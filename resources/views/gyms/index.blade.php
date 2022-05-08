@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <h1 class="text-black-50 text-center"> Gyms </h1>
    </div>
    <div class="d-flex justify-content-end mb-3">
        <a class="btn btn-success" href="javascript:void(0)" id="createNewGym"> Add New Gym</a>
    </div>
    <table id="gymsTable" class="table table-bordered mt-4">
        <thead>
            <tr>

                <th scope="col">Name</th>
                <th scope="col">Created At</th>
                <th scope="col">Cover</th>
                <th scope="col">Gym Creator</th>
                <th scope="col" style="width: 200px">Actions</th>
            </tr>
        </thead>
    </table>

    <!-- Create and update Hidden div -->
    <div class="modal fade" id="ajaxModel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modelHeading"></h4>
                </div>
                <div class="modal-body">
                    <form method="post" id="gymForm" name="gymForm" class="form-horizontal" enctype="multipart/form-data">
                        <div class="alert alert-danger print-error-msg" style="display:none">
                            <ul></ul>
                        </div>
                        <div class="form-group">
                            <label for="name" class="col-sm-3 control-label">Name</label>
                            <div class="col-sm-12">
                                <input type="text" class="form-control" id="name" name="name" placeholder="Enter Name"
                                    value="" maxlength="50" required="">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-12 control-label">Select City</label>
                            <select id="citySelect" class="col-sm-12">
                                @foreach ($cities as $city)
                                    <option value="{{ $city->id }}">{{ $city->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group d-flex flex-column">
                            <label for="name" class="col-sm-12 control-label">Upload Cover photo (optional)</label>
                            <input type="file" name="cover_img" class="mb-3 mx-2" id="cover_img">
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
                        @csrf
                        @method('DELETE')
                        <div>Are you sure you want to delete this Gym?</div>
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
        var gym_id = "";

        $(document).ready(function() {
            $.noConflict();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            var table = $('#gymsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('gyms.index') }}',
                columns: [{
                        data: 'name',
                    }, {
                        data: 'created_at',
                    }, {
                        data: 'cover',
                    }, {
                        data: 'gym_creator',
                    },
                    {
                        data: 'action',
                        orderable: false,
                        searchable: false
                    },
                ]
            });

            // Hide column depending on current user role.
            var userIsAdmin = "{{ Auth::user()->hasRole('admin') }}"
            if (!userIsAdmin) {
                table.column('3').visible(false);
            }

            // Create button action.
            $('#createNewGym').click(function() {
                $(".print-error-msg").css('display', 'none');

                $('#saveBtn').val("create-gym");
                $('#gymForm').trigger("reset");
                $('#modelHeading').html("Add New Gym");
                $('#ajaxModel').modal('show');
            });


            // Edit Gym button action.
            $('body').on('click', '.editGym', function() {
                $(".print-error-msg").css('display', 'none');
                gym_id = $(this).data('id');
                var name = $(this).parent().siblings()[0].innerHTML;
                $.get("{{ route('gyms.index') }}" + '/' + gym_id + '/edit', function(data) {
                    $('#modelHeading').html("Edit Gym");
                    $('#saveBtn').val("edit-gym");
                    $('#ajaxModel').modal('show');
                    $('#name').val(name);
                })
            });

            // Handling both Create and edit ajax requests.
            $('#saveBtn').click(function(e) {
                e.preventDefault();
                $(this).html('Sending..');
                var request_is_create = $('#modelHeading').html() == "Add New Gym";
                var url = request_is_create ? "/gyms" : "/gyms/" + gym_id;
                var method = request_is_create ? "POST" : "PUT";
                var myFormData = new FormData();
                myFormData.append('_method', method);
                myFormData.append('gym_id', gym_id);
                myFormData.append('name', $('#name').val());
                myFormData.append('cover_img', $('#cover_img')[0].files[0]);
                myFormData.append('has_gyms_id', $('#citySelect').find(":selected").val());


                $.ajax({
                    url: url,
                    type: "POST",
                    processData: false,
                    contentType: false,
                    cache: false,
                    data: myFormData,
                    success: function(data) {
                        if ($.isEmptyObject(data.error)) {
                            $('#gymForm').trigger("reset");
                            $('#ajaxModel').modal('hide');
                            $('#gymsTable').DataTable().ajax.reload();
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
            $('body').on('click', '.deleteGym', function() {
                gym_id = $(this).data('id');
                $('#deleteBtn').val("Delete");
                $('#deleteForm').trigger("reset");
                $('#deleteHeading').html("Delete confirmation");
                $('#deleteModel').modal('show');
            });

            // Handling delete ajax request.
            $('#deleteBtn').click(function(e) {
                e.preventDefault();
                console.log("here");
                $(this).html('Deleting..');
                var url = "/gyms/" + gym_id;
                $.ajax({
                    url: url,
                    data: {
                        "_token": "{{ csrf_token() }}",
                        "gym_id": gym_id,
                    },
                    type: "DELETE",
                    dataType: 'json',
                    success: function(data) {
                        $('#gymsTable').DataTable().ajax.reload();
                        $('#deleteModel').modal('hide');
                    },
                    error: function(data) {
                        console.log('Error:', data);
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
        }
    </script>
@endsection
