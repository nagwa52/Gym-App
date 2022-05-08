@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <h1 class="text-black-50 text-center"> Inside Packages tab </h1>
    </div>
    @can('CRUD_packages')
        <div class="d-flex justify-content-end mb-3">
            <a class="btn btn-success" href="javascript:void(0)" id="createNewPackage">Add New Package</a>
        </div>
    @endcan
    <table id="PackagesTable" class="table table-bordered mt-4">
        <thead>
            <tr>
                <th scope="col">Name</th>
                <th scope="col">Price</th>
                <th scope="col">Session amount</th>
                <th scope="col">Gym</th>
                <th scope="col">City</th>
                <th scope="col" style="width: 150px">Actions</th>
                <th scope="col" style="width: 100px">Purchase</th>
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
                    <form method="post" id="packageForm" name="packageForm" class="form-horizontal"
                        enctype="multipart/form-data">
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
                            <label for="name" class="col-sm-3 control-label">Price</label>
                            <div class="col-sm-12">
                                <input type="text" class="form-control" id="Price" name="Price" placeholder="Enter Price"
                                    required="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="name" class="col-sm-3 control-label">Session_amount</label>
                            <div class="col-sm-12">
                                <input type="text" class="form-control" id="Sessions_amount" name="Sessions_amount"
                                    value="" required="">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="name" class="col-sm-12 control-label">Select GYM</label>
                            <select id="gymSelect" class="col-sm-12">
                                @foreach ($gyms as $gym)
                                    <option value="{{ $gym->id }}">{{ $gym->name }}</option>
                                @endforeach
                            </select>
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
                        <div>Are you sure you want to delete this Package?</div>
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
        var package_id = "";

        $(document).ready(function() {
            $.noConflict();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            var table = $('#PackagesTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('packages.index') }}',
                columns: [{
                        data: 'name',
                        name: 'name',
                    },
                    {
                        data: 'price',
                        name: 'price'
                    },
                    {
                        data: 'sessions_amount',
                        name: 'Sessions_amount'
                    },
                    {
                        data: 'gym',
                        name: 'gym'
                    },
                    {
                        data: 'city',
                        name: 'city',
                    },
                    {
                        data: 'action',
                        name: 'action',
                    },
                    {
                        data: 'purchase',
                        name: 'purchase',
                    },
                ]
            });

            // Hide column depending on current user role.
            var userIsAdmin = "{{ Auth::user()->hasRole('admin') }}"
            if (!userIsAdmin) {
                table.column('5').visible(false);
            }

            var userIsGymManager = "{{ Auth::user()->hasRole('gym_manager') }}"
            if (!userIsGymManager) {
                table.column('6').visible(false);
            }
            // Create button action.
            $('#createNewPackage').click(function() {
                $(".print-error-msg").css('display', 'none');

                $('#saveBtn').val("create-package");
                $('#packageForm').trigger("reset");
                $('#modelHeading').html("Add New Package");
                $('#ajaxModel').modal('show');
            });

            // Edit package button action.
            $('body').on('click', '.editPackage', function() {
                $(".print-error-msg").css('display', 'none');

                package_id = $(this).data('id');
                var name = $(this).parent().siblings()[0].innerHTML;
                var price = $(this).parent().siblings()[1].innerHTML;
                var sessions_amount = $(this).parent().siblings()[2].innerHTML;
                $.get("{{ route('packages.index') }}" + '/' + package_id + '/edit', function(data) {
                    $('#modelHeading').html("Edit Package");
                    $('#saveBtn').val("edit-package");
                    $('#ajaxModel').modal('show');
                    $('#name').val(name);
                    $('#price').val(price);
                    $('#sessions_amount').val(sessions_amount);
                })
            });



            // Handling both Create and edit ajax requests.
            $('#saveBtn').click(function(e) {
                e.preventDefault();
                $(this).html('Sending..');
                var request_is_create = $('#modelHeading').html() == "Add New Package";
                var url = request_is_create ? "/packages" : "/packages/" + package_id;
                var method = request_is_create ? "POST" : "PUT";

                var myFormData = new FormData();
                myFormData.append('_method', method);
                myFormData.append('package_id', package_id);
                myFormData.append('name', $('#name').val());
                myFormData.append('price', parseFloat($('#Price').val()));
                myFormData.append('sessions_amount', parseFloat($('#Sessions_amount').val()));
                myFormData.append('has_packages_id', $('#gymSelect').find(":selected").val());


                $.ajax({
                    url: url,
                    type: "POST",
                    // dataType: 'json',
                    processData: false,
                    contentType: false,
                    cache: false,
                    // enctype: 'multipart/form-data',
                    data: myFormData,
                    success: function(data) {
                        if ($.isEmptyObject(data.error)) {
                            $('#packageForm').trigger("reset");
                            $('#ajaxModel').modal('hide');
                            $('#PackagesTable').DataTable().ajax.reload();
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
            $('body').on('click', '.deletePackage', function() {
                package_id = $(this).data('id');
                $('#deleteBtn').val("Delete");
                $('#deleteForm').trigger("reset");
                $('#deleteHeading').html("Delete confirmation");
                $('#deleteModel').modal('show');
            });

            // Handling delete ajax request.
            $('#deleteBtn').click(function(e) {
                e.preventDefault();
                $(this).html('Deleting..');
                var url = "/packages/" + package_id;
                $.ajax({
                    url: url,
                    data: {
                        "_token": "{{ csrf_token() }}",
                        "package_id": package_id,
                    },
                    type: "DELETE",
                    dataType: 'json',
                    success: function(data) {
                        $('#PackagesTable').DataTable().ajax.reload();
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
