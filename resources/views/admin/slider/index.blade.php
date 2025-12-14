@extends('admin.pages.master')
@section('title', 'Slider')
@section('content')
    <div class="container-fluid" id="newBtnSection">
        <div class="row mb-3">
            <div class="col-auto">
                <button type="button" class="btn btn-primary" id="newBtn">
                    Add New Slider
                </button>
            </div>
        </div>
    </div>

    <div class="container-fluid" id="addThisFormContainer">
        <div class="row justify-content-center">
            <div class="col-xl-8">
                <div class="card">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1" id="cardTitle">Add New Slider</h4>
                    </div>
                    <div class="card-body">
                        <form id="createThisForm">
                            @csrf
                            <input type="hidden" id="codeid" name="codeid">

                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-label">Slider Title </label>
                                    <input type="text" class="form-control" id="title" name="title">
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label">Slider Link</label>
                                    <input type="text" class="form-control" id="link" name="link"
                                        placeholder="Enter link (optional)">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Slider Image <span class="text-danger">*</span></label>
                                    <input type="file" class="form-control" id="image" accept="image/*"
                                        onchange="previewImage(event, '#preview-image')">
                                </div>

                                <div class="col-md-6">
                                    <img id="preview-image" src="/placeholder.webp" alt="" class="img-thumbnail rounded mt-3"
                                        style="max-width: 200px; max-height: 200px;">
                                    <button type="button" class="btn btn-sm btn-danger mt-2" id="removeImageBtn" style="display:none;">
                                        Remove Image
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer text-end">
                        <button type="submit" id="addBtn" class="btn btn-primary">Create</button>
                        <button type="button" id="FormCloseBtn" class="btn btn-light">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid" id="contentContainer">
        <ul class="nav nav-tabs mb-3" id="sliderTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="list-tab" data-bs-toggle="tab" href="#list" role="tab">Slider List</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="sort-tab" data-bs-toggle="tab" href="#sort" role="tab">Sort Sliders</a>
            </li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane fade show active" id="list" role="tabpanel">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Sliders</h4>
                    </div>
                    <div class="card-body">
                        <table id="sliderTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Sl</th>
                                    <th>Title</th>
                                    <th>Image</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="sort" role="tabpanel">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Sort Sliders</h4>
                        <small class="text-muted">Drag & drop rows to change order</small>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Sl</th>
                                    <th>Title</th>
                                </tr>
                            </thead>
                            <tbody id="sortable">
                                @foreach ($sliders as $slider)
                                    <tr data-id="{{ $slider->id }}">
                                        <td>{{ $slider->serial }}</td>
                                        <td>{{ $slider->title }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

    <script>
        let currentSliderId = null;

        $(document).ready(function() {
            $("#sortable").sortable({
                placeholder: "ui-state-highlight",
                cursor: "grab",
                forcePlaceholderSize: true,
                opacity: 0.8,
                update: function(event, ui) {
                    var order = $(this).sortable('toArray', {
                        attribute: 'data-id'
                    });
                    $.ajax({
                        url: "{{ route('sliders.updateOrder') }}",
                        method: "POST",
                        data: {
                            _token: '{{ csrf_token() }}',
                            order: order
                        },
                        success: function(res) {
                            showSuccess(res.message);
                            $("#sortable tr").each(function(index) {
                                $(this).find("td:first").text(index + 1);
                            });
                            reloadTable('#sliderTable');
                        },
                        error: function(xhr) {
                            pageTop();
                            showError(xhr.responseJSON?.message ?? "Something went wrong!");
                        }
                    });
                }
            });

            $('#sliderTable').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 25,
                ajax: "{{ route('allslider') }}",
                columns: [{
                        data: 'serial',
                        name: 'serial',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'title',
                        name: 'title'
                    },
                    {
                        data: 'image',
                        name: 'image',
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
                var slider_id = $(this).data('id');
                var status = $(this).prop('checked') ? 1 : 0;
                $.post('/admin/slider-status', {
                    _token: '{{ csrf_token() }}',
                    slider_id: slider_id,
                    status: status
                }, function(d) {
                    reloadTable('#sliderTable');
                    showSuccess(d.message);
                }).fail(() => showError('Failed to update status'));
            });

            $("#addThisFormContainer").hide();
            $("#newBtn").click(function() {
                clearform();
                $("#addThisFormContainer").slideDown(300);
                $("#newBtn").hide();
                pageTop();
            });
            $("#FormCloseBtn").click(function() {
                $("#addThisFormContainer").slideUp(300);
                setTimeout(() => {
                    $("#newBtn").show();
                }, 300);
            });

            var url = "{{ URL::to('/admin/slider') }}";
            var upurl = "{{ URL::to('/admin/slider-update') }}";

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $("#addBtn").click(function() {
                var form_data = new FormData();
                form_data.append("title", $("#title").val());
                form_data.append("link", $("#link").val());
                var featureImgInput = document.getElementById('image');
                if (featureImgInput.files && featureImgInput.files[0]) {
                    form_data.append("image", featureImgInput.files[0]);
                }

                if ($(this).val() == 'Create') {
                    $.ajax({
                        url: url,
                        type: "POST",
                        data: form_data,
                        contentType: false,
                        processData: false,
                        success: function(d) {
                            showSuccess(d.message);
                            $("#addThisFormContainer").slideUp(300);
                            setTimeout(() => {
                                $("#newBtn").show();
                            }, 300);
                            reloadTable('#sliderTable');
                            clearform();
                        },
                        error: function(xhr) {
                            pageTop();
                            if (xhr.responseJSON?.errors) {
                                let errors = Object.values(xhr.responseJSON.errors).flat();
                                showError(errors[0]);
                            } else {
                                showError(xhr.responseJSON?.message ?? "Something went wrong!");
                            }
                        }
                    });
                }

                if ($(this).val() == 'Update') {
                    form_data.append("codeid", $("#codeid").val());
                    $.ajax({
                        url: upurl,
                        type: "POST",
                        data: form_data,
                        contentType: false,
                        processData: false,
                        success: function(d) {
                            showSuccess(d.message);
                            $("#addThisFormContainer").slideUp(300);
                            setTimeout(() => {
                                $("#newBtn").show();
                            }, 300);
                            reloadTable('#sliderTable');
                            clearform();
                        },
                        error: function(xhr) {
                            pageTop();
                            if (xhr.responseJSON?.errors) {
                                let errors = Object.values(xhr.responseJSON.errors).flat();
                                showError(errors[0]);
                            } else {
                                showError(xhr.responseJSON?.message ?? "Something went wrong!");
                            }
                        }
                    });
                }
            });

            $("#contentContainer").on('click', '#EditBtn', function() {
                $("#cardTitle").text('Update this data');
                codeid = $(this).attr('rid');
                currentSliderId = codeid;
                $.get(url + '/' + codeid + '/edit', {}, function(d) {
                    populateForm(d);
                });
            });

            $("#removeImageBtn").click(function(e) {
                e.preventDefault();
                if (!currentSliderId) return;
                
                $.post('/admin/slider/' + currentSliderId + '/remove-image', {
                    _token: '{{ csrf_token() }}'
                }, function(d) {
                    $("#preview-image").attr('src', '/placeholder.webp');
                    $("#removeImageBtn").hide();
                    $("#image").val('');
                    showSuccess(d.message);
                }).fail(() => showError('Failed to remove image'));
            });

            function populateForm(data) {
                $("#title").val(data.title);
                $("#link").val(data.link);
                $("#codeid").val(data.id);
                
                $("#preview-image").attr('src', data.image);
                
                if (data.image && data.image != '/placeholder.webp') {
                    $("#removeImageBtn").show();
                } else {
                    $("#removeImageBtn").hide();
                }
                
                $("#addBtn").val('Update').html('Update');
                $("#addThisFormContainer").show();
                $("#newBtn").hide();
                pageTop();
            }

            function clearform() {
                $('#createThisForm')[0].reset();
                $("#preview-image").attr('src', '/placeholder.webp');
                $("#removeImageBtn").hide();
                $("#addBtn").val('Create').html('Create');
                $("#cardTitle").text('Add new Slider');
                currentSliderId = null;
            }
        });
    </script>

@endsection