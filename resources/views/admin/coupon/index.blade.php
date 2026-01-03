@extends('admin.pages.master')
@section('title', 'Coupons')
@section('content')

    <div class="container-fluid" id="newBtnSection">
        <div class="row mb-3">
            <div class="col text-start">
                <button type="button" class="btn btn-primary" id="newBtn">
                    <i class="ri-coupon-line me-1"></i> Add New Coupon
                </button>
            </div>
        </div>
    </div>

    <div class="container-fluid" id="addThisFormContainer">
        <div class="row justify-content-center">
            <div class="col-xl-8">
                <div class="card">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1" id="cardTitle">Add New Coupon</h4>
                    </div>
                    <div class="card-body">
                        <form id="createThisForm">
                            @csrf
                            <input type="hidden" id="codeid" name="codeid">

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Coupon Code <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control text-uppercase" id="code" name="code" placeholder="e.g., SAVE20">
                                    <small class="text-muted">Uppercase letters and numbers only</small>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Coupon Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name" placeholder="e.g., Summer Sale 20% Off">
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label">Description</label>
                                    <textarea class="form-control" id="description" name="description" rows="2" placeholder="Optional description"></textarea>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Discount Type <span class="text-danger">*</span></label>
                                    <select class="form-control" id="discount_type" name="discount_type">
                                        <option value="percent">Percentage (%)</option>
                                        <option value="fixed">Fixed Amount ($)</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Discount Value <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="discount_value" name="discount_value" step="0.01" min="0.01" placeholder="e.g., 20">
                                        <span class="input-group-text" id="discount_suffix">%</span>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Minimum Order Amount</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" class="form-control" id="min_order_amount" name="min_order_amount" step="0.01" min="0" placeholder="0.00">
                                    </div>
                                    <small class="text-muted">Set 0 for no minimum</small>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Maximum Uses</label>
                                    <input type="number" class="form-control" id="max_uses" name="max_uses" min="1" placeholder="Leave empty for unlimited">
                                    <small class="text-muted">Leave empty for unlimited uses</small>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Start Date</label>
                                    <input type="date" class="form-control" id="start_date" name="start_date">
                                    <small class="text-muted">Leave empty for immediate start</small>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">End Date</label>
                                    <input type="date" class="form-control" id="end_date" name="end_date">
                                    <small class="text-muted">Leave empty for no expiry</small>
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label">Coupon Image (Optional)</label>
                                    <input type="file" class="form-control" id="image" accept="image/*"
                                        onchange="previewImage(event, '#preview-image')">
                                    <img id="preview-image" src="#" alt="" class="img-thumbnail rounded mt-3"
                                        style="max-width: 300px; display: none;">
                                    <small class="text-muted">Recommended: 512x512</small>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer text-end">
                        <button type="submit" id="addBtn" class="btn btn-primary">
                            Create Coupon
                        </button>
                        <button type="button" id="FormCloseBtn" class="btn btn-light">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid" id="contentContainer">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Coupons</h4>
            </div>
            <div class="card-body">
                <table id="couponsTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Coupon Code</th>
                            <th>Image</th>
                            <th>Discount Info</th>
                            <th>Usage</th>
                            <th>Validity</th>
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
    $(document).ready(function() {
        // Set default dates
        $('#start_date').val(new Date().toISOString().split('T')[0]);
        var nextMonth = new Date();
        nextMonth.setMonth(nextMonth.getMonth() + 1);
        $('#end_date').val(nextMonth.toISOString().split('T')[0]);

        // Update discount suffix based on type
        $('#discount_type').change(function() {
            var suffix = $(this).val() === 'percent' ? '%' : '$';
            $('#discount_suffix').text(suffix);
            $('#discount_value').attr('placeholder', $(this).val() === 'percent' ? 'e.g., 20' : 'e.g., 10.00');
        });

        $('#couponsTable').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 25,
                ajax: {
        url: "{{ route('coupons.index') }}",
        type: "GET",
        error: function (xhr, status, error) {
            console.error(xhr.responseText);
        }
    },
            columns: [
                {
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'code',
                    name: 'code'
                },
                {
                    data: 'image',
                    name: 'image',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'discount_info',
                    name: 'discount_info',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'usage',
                    name: 'usage',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'dates',
                    name: 'dates',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'status',
                    name: 'status',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }
            ]
        });

        $(document).on('change', '.toggle-status', function() {
            var coupon_id = $(this).data('id');
            var status = $(this).prop('checked') ? 1 : 0;

            $.ajax({
                url: '{{ route("coupons.status") }}',
                method: "POST",
                data: {
                    coupon_id: coupon_id,
                    status: status,
                    _token: "{{ csrf_token() }}"
                },
                success: function(d) {
                    reloadTable('#couponsTable');
                    showSuccess(d.message);
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                    showError('Failed to update status');
                }
            });
        });
    });
</script>

<script>
    $(document).ready(function() {
        $("#addThisFormContainer").hide();
        $("#newBtn").click(function() {
            clearform();
            $("#newBtn").hide(100);
            $("#addThisFormContainer").show(300);
        });
        
        $("#FormCloseBtn").click(function() {
            $("#addThisFormContainer").hide(200);
            $("#newBtn").show(100);
            clearform();
        });

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        
        var url = "{{ route('coupons.store') }}";
        var upurl = "{{ route('coupons.update') }}";

        $("#addBtn").click(function() {
            //create
            if ($(this).val() == 'Create') {
                var form_data = new FormData();
                form_data.append("code", $("#code").val().toUpperCase());
                form_data.append("name", $("#name").val());
                form_data.append("description", $("#description").val());
                form_data.append("discount_type", $("#discount_type").val());
                form_data.append("discount_value", $("#discount_value").val());
                form_data.append("min_order_amount", $("#min_order_amount").val() || 0);
                form_data.append("max_uses", $("#max_uses").val());
                form_data.append("start_date", $("#start_date").val());
                form_data.append("end_date", $("#end_date").val());

                var imageInput = document.getElementById('image');
                if (imageInput.files && imageInput.files[0]) {
                    form_data.append("image", imageInput.files[0]);
                }

                $.ajax({
                    url: url,
                    method: "POST",
                    contentType: false,
                    processData: false,
                    data: form_data,
                    success: function(d) {
                        showSuccess(d.message);
                        $("#addThisFormContainer").slideUp(300);
                        setTimeout(() => {
                            $("#newBtn").show(200);
                        }, 300);
                        reloadTable('#couponsTable');
                        clearform();
                    },
                    error: function(xhr, status, error) {
                        if (xhr.status === 422) {
                            let firstError = Object.values(xhr.responseJSON.errors)[0][0];
                            showError(firstError);
                        } else {
                            showError(xhr.responseJSON?.message ?? "Something went wrong!");
                        }
                        console.error(xhr.responseText);
                    }
                });
            }
            //create  end

            //Update
            if ($(this).val() == 'Update') {
                var form_data = new FormData();
                form_data.append("code", $("#code").val().toUpperCase());
                form_data.append("name", $("#name").val());
                form_data.append("description", $("#description").val());
                form_data.append("discount_type", $("#discount_type").val());
                form_data.append("discount_value", $("#discount_value").val());
                form_data.append("min_order_amount", $("#min_order_amount").val() || 0);
                form_data.append("max_uses", $("#max_uses").val());
                form_data.append("start_date", $("#start_date").val());
                form_data.append("end_date", $("#end_date").val());
                form_data.append("codeid", $("#codeid").val());

                var imageInput = document.getElementById('image');
                if (imageInput.files && imageInput.files[0]) {
                    form_data.append("image", imageInput.files[0]);
                }

                $.ajax({
                    url: upurl,
                    type: "POST",
                    dataType: 'json',
                    contentType: false,
                    processData: false,
                    data: form_data,
                    success: function(d) {
                        showSuccess(d.message);
                        $("#addThisFormContainer").hide();
                        $("#addThisFormContainer").slideUp(300);
                        setTimeout(() => {
                            $("#newBtn").show(200);
                        }, 300);
                        reloadTable('#couponsTable');
                        clearform();
                    },
                    error: function(xhr, status, error) {
                        if (xhr.status === 422) {
                            let firstError = Object.values(xhr.responseJSON.errors)[0][0];
                            showError(firstError);
                        } else {
                            showError(xhr.responseJSON?.message ?? "Something went wrong!");
                        }
                        console.error(xhr.responseText);
                    }
                });
            }
            //Update  end
        });
        
        //Edit
        $("#contentContainer").on('click', '#EditBtn', function() {
            $("#cardTitle").text('Update Coupon');
            codeid = $(this).attr('rid');
            info_url = "{{ route('coupons.edit', ':id') }}".replace(':id', codeid);
            $.get(info_url, {}, function(d) {
                populateForm(d);
                pagetop();
            });
        });
        //Edit  end 
        
        function populateForm(data) {
            $("#code").val(data.code);
            $("#name").val(data.name);
            $("#description").val(data.description);
            $("#discount_type").val(data.discount_type);
            $("#discount_value").val(data.discount_value);
            $("#min_order_amount").val(data.min_order_amount);
            $("#max_uses").val(data.max_uses);
            $("#start_date").val(data.start_date);
            $("#end_date").val(data.end_date);
            $("#codeid").val(data.id);
            $("#addBtn").val('Update');
            $("#addBtn").html('Update');
            $("#addThisFormContainer").show(300);
            $("#newBtn").hide(100);

            // Update discount suffix
            $('#discount_type').trigger('change');

            var imagePreview = document.getElementById('preview-image');
            if (data.image) {
                imagePreview.src = data.image;
                imagePreview.style.display = 'block';
            } else {
                imagePreview.src = "#";
                imagePreview.style.display = 'none';
            }
        }

        function clearform() {
            $('#createThisForm')[0].reset();
            $("#addBtn").val('Create');
            $("#addBtn").html('Create Coupon');
            $('#preview-image').attr('src', '#').hide();
            $("#cardTitle").text('Add New Coupon');
            
            // Reset dates and update suffix
            $('#start_date').val(new Date().toISOString().split('T')[0]);
            var nextMonth = new Date();
            nextMonth.setMonth(nextMonth.getMonth() + 1);
            $('#end_date').val(nextMonth.toISOString().split('T')[0]);
            $('#discount_type').trigger('change');
        }

        // Preview image function
        function previewImage(event, selector) {
            var reader = new FileReader();
            reader.onload = function() {
                var output = document.querySelector(selector);
                output.src = reader.result;
                output.style.display = 'block';
            }
            reader.readAsDataURL(event.target.files[0]);
        }
    });
</script>
@endsection