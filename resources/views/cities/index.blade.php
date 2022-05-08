@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <h1 class="text-black-50 text-center"> Cities </h1>
    </div>
    <div class="d-flex justify-content-end mb-3">
        <a class="btn btn-success" href="javascript:void(0)" id="createNewCity"> Add New City</a>
    </div>
    <table id="citiesTable" class="table table-bordered mt-4">
        <thead>
            <tr>

                <th scope="col">Name</th>
                <th scope="col">Gyms</th>
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
                    <form method="post" id="cityForm" name="cityForm" class="form-horizontal" enctype="multipart/form-data">
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
                        <div>Are you sure you want to delete this City?</div>
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
        var city_id = "";

        $(document).ready(function() {
            $.noConflict();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $('#citiesTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('cities.index') }}',
                columns: [{
                        data: 'name',
                    }, {
                        data: 'gyms',
                    },
                    {
                        data: 'action',
                        orderable: false,
                        searchable: false
                    },
                ]
            });

            // Create button action.
            $('#createNewCity').click(function() {
                $(".print-error-msg").css('display', 'none');

                $('#saveBtn').val("create-city");
                $('#cityForm').trigger("reset");
                $('#modelHeading').html("Add New City");
                $('#ajaxModel').modal('show');
            });


            // Edit city button action.
            $('body').on('click', '.editCity', function() {
                $(".print-error-msg").css('display', 'none');
                city_id = $(this).data('id');
                var name = $(this).parent().siblings()[0].innerHTML;
                $.get("{{ route('cities.index') }}" + '/' + city_id + '/edit', function(data) {
                    $('#modelHeading').html("Edit City");
                    $('#saveBtn').val("edit-city");
                    $('#ajaxModel').modal('show');
                    $('#name').val(name);
                })
            });

            // Handling both Create and edit ajax requests.
            $('#saveBtn').click(function(e) {
                e.preventDefault();
                $(this).html('Sending..');
                var request_is_create = $('#modelHeading').html() == "Add New City";
                var url = request_is_create ? "/cities" : "/cities/" + city_id;
                var method = request_is_create ? "POST" : "PUT";
                var myFormData = new FormData();
                myFormData.append('_method', method);
                myFormData.append('city_id', city_id);
                myFormData.append('name', $('#name').val());


                $.ajax({
                    url: url,
                    type: "POST",
                    processData: false,
                    contentType: false,
                    cache: false,
                    data: myFormData,
                    success: function(data) {
                        if ($.isEmptyObject(data.error)) {
                            $('#cityForm').trigger("reset");
                            $('#ajaxModel').modal('hide');
                            $('#citiesTable').DataTable().ajax.reload();
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
            $('body').on('click', '.deleteCity', function() {
                city_id = $(this).data('id');
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
                var url = "/cities/" + city_id;
                $.ajax({
                    url: url,
                    data: {
                        "_token": "{{ csrf_token() }}",
                        "city_id": city_id,
                    },
                    type: "DELETE",
                    dataType: 'json',
                    success: function(data) {
                        $('#citiesTable').DataTable().ajax.reload();
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
