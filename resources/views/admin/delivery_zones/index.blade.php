@extends('admin.pages.master')
@section('title', 'Delivery Zones')
@section('content')
    <div class="container-fluid" id="newBtnSection">
        <div class="row mb-3">
            <div class="col-auto">
                <button class="btn btn-primary" id="newBtn">Add New Zone</button>
            </div>
        </div>
    </div>

    <div class="container-fluid" id="addThisFormContainer" style="display:none;">
        <div class="row justify-content-center">
            <div class="col-xl-8">
                <div class="card">
                    <div class="card-header">
                        <h4 id="cardTitle">Add New Delivery Zone</h4>
                    </div>
                    <div class="card-body">
                        <form id="createThisForm">
                            @csrf
                            <input type="hidden" id="codeid" name="id">
                            <div class="mb-3">
                                <label class="form-label">Postcode Prefix <span class="text-danger">*</span></label>
                                <input type="text" id="postcode_prefix" name="postcode_prefix" class="form-control" placeholder="e.g., LN5, SW1">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Delivery Charge <span class="text-danger">*</span></label>
                                <input type="number" id="delivery_charge" name="delivery_charge" class="form-control" step="0.01" min="0" placeholder="0.00">
                            </div>
                            <div class="mb-3 form-check">
                                <input type="checkbox" id="is_active" name="is_active" class="form-check-input">
                                <label class="form-check-label" for="is_active">Active</label>
                            </div>
                            <div class="mb-3 text-end">
                                <button type="button" id="addBtn" class="btn btn-primary" value="Create">Create</button>
                                <button type="button" id="FormCloseBtn" class="btn btn-light">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid" id="contentContainer">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">All Delivery Zones</h4>
            </div>
            <div class="card-body">
                <table id="zoneTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Sl</th>
                            <th>Postcode Prefix</th>
                            <th>Delivery Charge</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <script>
        $(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var table = $('#zoneTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('delivery-zone.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'postcode_prefix',
                        name: 'postcode_prefix'
                    },
                    {
                        data: 'delivery_charge',
                        name: 'delivery_charge',
                        render: function(data) {
                            return '£' + parseFloat(data).toFixed(2);
                        }
                    },
                    {
                        data: 'is_active',
                        name: 'is_active'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            $('#newBtn').click(function() {
                $('#createThisForm')[0].reset();
                $('#codeid').val('');
                $('#cardTitle').text('Add New Delivery Zone');
                $('#addBtn').val('Create').text('Create');
                $('#addThisFormContainer').show(300);
                $('#newBtn').hide();
            });

            $('#FormCloseBtn').click(function() {
                $('#addThisFormContainer').hide(200);
                $('#newBtn').show(100);
                $('#createThisForm')[0].reset();
            });

            $('#addBtn').click(function() {
                var btn = this;
                var url = $(btn).val() === 'Create' ? "{{ route('delivery-zone.store') }}" :
                    "{{ route('delivery-zone.update') }}";
                var form = document.getElementById('createThisForm');
                var fd = new FormData(form);
                if ($(btn).val() !== 'Create') fd.append('id', $('#codeid').val());

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: fd,
                    contentType: false,
                    processData: false,
                    success: function(res) {
                        showSuccess(res.message);
                        $('#addThisFormContainer').hide();
                        $('#newBtn').show();
                        table.ajax.reload(null, false);
                        $('#createThisForm')[0].reset();
                    },
                    error: function(xhr) {
                        if (xhr.status === 422 && xhr.responseJSON) {
                            let first = Object.values(xhr.responseJSON.errors)[0][0];
                            showError(first);
                        } else {
                            showError(xhr.responseJSON?.message ?? 'Something went wrong');
                        }
                    }
                });
            });

            $(document).on('click', '.EditBtn', function() {
                var id = $(this).data('id');
                $.get("{{ url('/admin/delivery-zones') }}/" + id + "/edit", {}, function(res) {
                    $('#codeid').val(res.id);
                    $('#postcode_prefix').val(res.postcode_prefix);
                    $('#delivery_charge').val(res.delivery_charge);
                    $('#is_active').prop('checked', res.is_active == 1);
                    $('#cardTitle').text('Update Delivery Zone');
                    $('#addBtn').val('Update').text('Update');
                    $('#addThisFormContainer').show(300);
                    $('#newBtn').hide();
                });
            });

            $(document).on('change', '.toggle-status', function() {
                var zone_id = $(this).data('id');
                var is_active = $(this).prop('checked') ? 1 : 0;
                $.post('/admin/delivery-zones-status', {
                    _token: '{{ csrf_token() }}',
                    zone_id: zone_id,
                    is_active: is_active
                }, function(d) {
                    reloadTable('#zoneTable');
                    showSuccess(d.message);
                }).fail(() => showError('Failed to update status'));
            });
        });
    </script>
@endsection