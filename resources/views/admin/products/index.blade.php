@extends('admin.master')

@section('content')
<!-- Main content -->
<section class="content" id="newBtnSection">
    <div class="container-fluid">
        <div class="row">
            <div class="col-2">
                <button type="button" class="btn btn-secondary my-3" id="newBtn">Add new</button>
            </div>
        </div>
    </div>
</section>

<section class="content mt-3 d-none" id="stepsSection">
  <div class="container-fluid">
    <div class="row justify-content-md-center">
      <div class="col-md-8">
        <div class="card card-primary">
          <div class="card-header">
            <h3 class="card-title">Product Steps</h3>
          </div>

          <div class="card-body" id="stepsContainer">
            <div class="form-group">
              <label>Title</label>
              <input type="text" id="process_title" class="form-control" placeholder="Enter process title">
            </div>

            <div class="form-group">
              <label>Sub Title</label>
              <input type="text" id="process_sub_title" class="form-control" placeholder="Enter sub title">
            </div>

            <div class="form-group">
              <label>Steps</label>
              <div id="stepsWrapper">
                <div class="step-row mb-2">
                  <input type="text" class="form-control step-title mb-1" placeholder="Step title">
                  <textarea class="form-control step-desc" rows="2" placeholder="Step description"></textarea>
                  <button type="button" class="btn btn-sm btn-danger remove-step mt-1">Remove</button>
                </div>
              </div>
              <button type="button" class="btn btn-sm btn-primary mt-2" id="addStepBtn">Add Step</button>
            </div>
          </div>

          <div class="card-footer text-end">
            <button type="button" class="btn btn-secondary" id="saveStepsBtn">Save Steps</button>
            <button type="button" class="btn btn-default" id="closeStepsBtn">Close</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="content mt-3" id="addThisFormContainer">
    <div class="container-fluid">
        <div class="row justify-content-md-center">
            <div class="col-md-10">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title" id="cardTitle">Add new Service</h3>
                    </div>
                    <div class="card-body">
                        <form id="createThisForm">
                            @csrf
                            <input type="hidden" class="form-control" id="codeid" name="codeid">
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Title <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="title" name="title" placeholder="Enter title" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Price (£)</label>
                                        <input type="number" step="0.01" class="form-control" id="price" name="price" placeholder="0.00">
                                    </div>
                                </div>
                                <div class="col-md-6 d-none">
                                    <div class="form-group">
                                        <label>Sub Title</label>
                                        <input type="text" class="form-control" id="sub_title" name="sub_title" placeholder="Enter sub title">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 d-none">
                                    <div class="form-group">
                                        <label>Product URL</label>
                                        <input type="url" class="form-control" id="url" name="url" placeholder="Enter product URL">
                                    </div>
                                </div>
                                <div class="col-md-6 d-none">
                                    <div class="form-group">
                                        <label>Sort Order</label>
                                        <input type="number" class="form-control" id="sl" name="sl" value="0">
                                    </div>
                                </div>
                                 <div class="col-md-3 d-none">
                                    <div class="form-group">
                                        <label>Icon Class</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="icon" name="icon" placeholder="e.g. bi bi-lightbulb-fill">
                                            <button type="button" class="btn btn-outline-secondary ml-2" id="checkIcon">Check</button>
                                        </div>
                                        <div class="mt-2" id="iconPreview"></div>
                                        <small>Find icon reference <a href="https://icons.getbootstrap.com/" target="_blank">here</a></small>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Category</label>
                                        <select class="form-control select2" id="category_id" name="category_id" style="width: 100%;">
                                            <option value="">Select Category</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Product Tag</label>
                                        <select class="form-control select2" id="product_tag_id" name="product_tag_id" style="width: 100%;">
                                            <option value="">Select Tag</option>
                                            @foreach($tags as $tag)
                                                <option value="{{ $tag->id }}">{{ $tag->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label>Short Description</label>
                                <textarea class="form-control" id="short_description" name="short_description" rows="3" placeholder="Enter short description"></textarea>
                            </div>

                            <div class="form-group d-none">
                                <label>Feature Description</label>
                                <textarea class="form-control" id="feature_description" name="feature_description" rows="3" placeholder="Enter feature description"></textarea>
                            </div>
                            
                            <div class="form-group">
                                <label>Long Description</label>
                                <textarea class="form-control summernote" id="long_description" name="long_description" rows="5" placeholder="Enter long description"></textarea>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Image</label>
                                        <input type="file" class="form-control-file" id="image" name="image" accept="image/*">
                                        <img id="preview-image" src="#" alt="" style="max-width: 300px; width: 100%; height: auto; margin-top: 20px;">
                                    </div>
                                </div>
                                <div class="col-md-6 d-none">
                                    <div class="form-group">
                                        <label>Product Video</label>
                                        <input type="file" class="filepond" id="video" name="video" accept="video/mp4,video/webm">
                                        <div id="video-preview" class="mt-2"></div>
                                    </div>
                                </div>
                                <div class="col-md-6 d-none">
                                    <div class="form-group">
                                        <label>Banner Image (1920x600 recommended)</label>
                                        <input type="file" class="form-control-file" id="banner_image" name="banner_image" accept="image/*">
                                        <img id="preview-banner-image" src="#" alt="" style="max-width: 300px; width: 100%; height: auto; margin-top: 20px;">
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Meta Fields Section -->
                            <div class="row mt-4 d-none">
                                <div class="col-12">
                                    <h4>SEO Meta Fields</h4>
                                    <hr>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label>Meta Title</label>
                                        <input type="text" class="form-control" id="meta_title" name="meta_title" placeholder="Enter meta title">
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label>Meta Description</label>
                                        <textarea class="form-control" id="meta_description" name="meta_description" rows="3" placeholder="Enter meta description"></textarea>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label>Meta Keywords (comma separated)</label>
                                        <input type="text" class="form-control" id="meta_keywords" name="meta_keywords" placeholder="e.g. product, item, shop">
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label>Meta Image (1200x630 recommended)</label>
                                        <input type="file" class="form-control-file" id="meta_image" name="meta_image" accept="image/*">
                                        <img id="preview-meta-image" src="#" alt="" style="max-width: 300px; width: 100%; height: auto; margin-top: 20px;">
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer">
                        <button type="submit" id="addBtn" class="btn btn-secondary" value="Create">Create</button>
                        <button type="submit" id="FormCloseBtn" class="btn btn-default">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="content" id="contentContainer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">All Services</h3>
                    </div>
                    <div class="card-body">
                        <table id="example1" class="table cell-border table-striped">
                            <thead>
                                <tr>
                                    <th>Sl</th>
                                    <th>Image</th>
                                    <th>Title</th>
                                    <th>Category</th>
                                    <th>Price</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection

@section('script')

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<script>
  document.getElementById('checkIcon').addEventListener('click', function() {
      let iconClass = document.getElementById('icon').value.trim();
      let preview = document.getElementById('iconPreview');

      if(iconClass) {
          preview.innerHTML = `<i class="${iconClass}" style="font-size: 24px;"></i>`;
      } else {
          preview.innerHTML = '';
      }
  });
</script>

<!-- Include FilePond -->
<link href="https://unpkg.com/filepond/dist/filepond.css" rel="stylesheet">
<link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet">
<script src="https://unpkg.com/filepond/dist/filepond.js"></script>
<script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js"></script>
<script src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.js"></script>

<script>
    // Initialize FilePond for preview only
    FilePond.registerPlugin(
        FilePondPluginImagePreview,
        FilePondPluginFileValidateType
    );

    const pond = FilePond.create(document.querySelector('.filepond'), {
        acceptedFileTypes: ['video/mp4', 'video/webm'],
        fileValidateTypeDetectType: (source, type) => new Promise((resolve, reject) => {
            resolve(type);
        }),
        maxFileSize: '50MB',
        labelIdle: 'Drag & Drop your video or <span class="filepond--label-action">Browse</span>',
        labelFileTypeNotAllowed: 'File of invalid type',
        fileValidateTypeLabelExpectedTypes: 'Expects MP4 or WebM',
        allowProcess: false, // Disable auto upload
        allowRemove: true,
        allowRevert: false
    });

</script>

<script>
  $(document).ready(function () {
      $("#addThisFormContainer").hide();
      $("#newBtn").click(function(){
          clearform();
          $("#newBtn").hide(100);
          $("#addThisFormContainer").show(300);
      });
      $("#FormCloseBtn").click(function(){
          $("#addThisFormContainer").hide(200);
          $("#newBtn").show(100);
          clearform();
          pond.removeFiles();
      });

      $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
      
      var url = "{{URL::to('/admin/products')}}";
      var upurl = "{{URL::to('/admin/products/update')}}";

      $("#addBtn").click(function(){
          var form_data = new FormData();
          form_data.append("title", $("#title").val());
          form_data.append("sub_title", $("#sub_title").val());
          form_data.append("url", $("#url").val());
          form_data.append("short_description", $("#short_description").val());
          form_data.append("feature_description", $("#feature_description").val());
          form_data.append("long_description", $("#long_description").val());
          form_data.append("sl", $("#sl").val());
          form_data.append("icon", $("#icon").val());
          form_data.append("meta_title", $("#meta_title").val());
          form_data.append("meta_description", $("#meta_description").val());
          form_data.append("meta_keywords", $("#meta_keywords").val());
          form_data.append("category_id", $("#category_id").val());
          form_data.append("product_tag_id", $("#product_tag_id").val());
          form_data.append("price", $("#price").val());

          // Handle image upload
          var imageInput = document.getElementById('image');
          if(imageInput.files && imageInput.files[0]) {
              form_data.append("image", imageInput.files[0]);
          }

          var bannerImageInput = document.getElementById('banner_image');
          if(bannerImageInput.files && bannerImageInput.files[0]) {
              form_data.append("banner_image", bannerImageInput.files[0]);
          }

          // Handle FilePond video upload (only when files are added)
          if (pond.getFiles().length > 0) {
              form_data.append("video", pond.getFiles()[0].file);
          }

          // Handle meta image upload
          var metaImageInput = document.getElementById('meta_image');
          if(metaImageInput.files && metaImageInput.files[0]) {
              form_data.append("meta_image", metaImageInput.files[0]);
          }

          if($(this).val() == 'Create') {
              // Create
              $.ajax({
                  url: url,
                  method: "POST",
                  contentType: false,
                  processData: false,
                  data: form_data,
                  success: function(res) {
                    clearform();
                    success(res.message);
                    pageTop();
                    reloadTable();
                  },
                  error: function(xhr) {
                    console.error(xhr.responseText);
                    pageTop();
                    if (xhr.responseJSON && xhr.responseJSON.errors)
                      error(Object.values(xhr.responseJSON.errors)[0][0]);
                    else
                      error();
                  }
              });
          } else {
              // Update
              form_data.append("codeid", $("#codeid").val());
              
              $.ajax({
                  url: upurl,
                  type: "POST",
                  dataType: 'json',
                  contentType: false,
                  processData: false,
                  data: form_data,
                  success: function(res) {
                    clearform();
                    success(res.message);
                    pageTop();
                    reloadTable();
                  },
                  error: function(xhr) {
                    console.error(xhr.responseText);
                    pageTop();
                    if (xhr.responseJSON && xhr.responseJSON.errors)
                      error(Object.values(xhr.responseJSON.errors)[0][0]);
                    else
                      error();
                  }
              });
          }
      });

      //Edit
      $("#contentContainer").on('click','.edit', function(){
          $("#cardTitle").text('Update this Service');
          codeid = $(this).data('id');
          info_url = url + '/'+codeid+'/edit';
          $.get(info_url,{},function(d){
              populateForm(d);
          });
      });

      function populateForm(data){
          $("#title").val(data.title);
          $("#sub_title").val(data.sub_title);
          $("#url").val(data.url);
          $("#short_description").val(data.short_description);
          $("#feature_description").val(data.feature_description);
          $("#long_description").summernote('code', data.long_description);
          $("#sl").val(data.sl);
          $("#meta_title").val(data.meta_title);
          $("#meta_description").val(data.meta_description);
          $("#meta_keywords").val(data.meta_keywords);
          $("#codeid").val(data.id);
          $("#category_id").val(data.category_id).trigger('change');
          $("#product_tag_id").val(data.product_tag_id).trigger('change');
          $("#price").val(data.price);
          // Image preview
          if (data.image) {
              $("#preview-image").attr("src", '/images/products/' + data.image).show();
              if(!$("#preview-image").next('.remove-file').length){
                  $("<button/>", {
                      type: "button",
                      class: "btn btn-danger remove-file position-absolute top-0 end-0",
                      html: '<i class="fas fa-times"></i>',
                      "data-filename": data.image,
                      "data-path": "images/products",
                      "data-model": "Product",
                      "data-id": data.id,
                      "data-col": "image"
                  }).insertAfter("#preview-image");
                  $("#preview-image").parent().css('position', 'relative');
              }
          } else {
              $("#preview-image").attr("src", '').hide();
              $("#preview-image").next('.remove-file').remove();
          }

          // Meta Image preview
          if (data.meta_image) {
              $("#preview-meta-image").attr("src", '/images/products/meta/' + data.meta_image).show();
              if(!$("#preview-meta-image").next('.remove-file').length){
                  $("<button/>", {
                      type: "button",
                      class: "btn btn-danger remove-file position-absolute top-0 end-0",
                      html: '<i class="fas fa-times"></i>',
                      "data-filename": data.meta_image,
                      "data-path": "images/products/meta",
                      "data-model": "Product",
                      "data-id": data.id,
                      "data-col": "meta_image"
                  }).insertAfter("#preview-meta-image");
                  $("#preview-meta-image").parent().css('position', 'relative');
              }
          } else {
              $("#preview-meta-image").attr("src", '').hide();
              $("#preview-meta-image").next('.remove-file').remove();
          }

          if (data.banner_image) {
              $("#preview-banner-image").attr("src", '/images/products/banners/' + data.banner_image).show();
              if(!$("#preview-banner-image").next('.remove-file').length){
                  $("<button/>", {
                      type: "button",
                      class: "btn btn-danger remove-file position-absolute top-0 end-0",
                      html: '<i class="fas fa-times"></i>',
                      "data-filename": data.banner_image,
                      "data-path": "images/products/banners",
                      "data-model": "Product",
                      "data-id": data.id,
                      "data-col": "banner_image"
                  }).insertAfter("#preview-banner-image");
                  $("#preview-banner-image").parent().css('position', 'relative');
              }
          } else {
              $("#preview-banner-image").attr("src", '').hide();
              $("#preview-banner-image").next('.remove-file').remove();
          }

          // Video preview
          $(".video-remove-btn").remove();
          if (data.video) {
              $("<button/>", {
                  type: "button",
                  class: "btn btn-danger remove-file video-remove-btn",
                  html: '<i class="fas fa-times"></i> Remove Video',
                  "data-filename": data.video,
                  "data-path": "images/products/videos",
                  "data-model": "Product",
                  "data-id": data.id,
                  "data-col": "video"
              }).insertAfter("#video");
          }

          $("#icon").val(data.icon || '');
          if(data.icon) {
              $("#iconPreview").html('<i class="'+data.icon+'" style="font-size:24px;"></i>');
          } else {
              $("#iconPreview").html('');
          }

          $("#addBtn").val('Update').html('Update');
          $("#addThisFormContainer").show(300);
          $("#newBtn").hide(100);
      }
      
      function clearform(){
          $('.select2').val('').trigger('change');
          $('#createThisForm')[0].reset();
          $("#addBtn").val('Create');
          $("#addBtn").html('Create');
          $("#addThisFormContainer").slideUp(200);
          $("#newBtn").slideDown(200);
          $('#video-preview').html('');
          $('.summernote').summernote('reset');
          pond.removeFiles();
          $('#preview-image').attr('src', '#');
          $('#preview-meta-image').attr('src', '#');
          $('#preview-banner-image').attr('src', '#');
          $("#cardTitle").text('Add new Service');
          $('#icon').val('');
          $('#iconPreview').html('');
          $('#createThisForm').find('.remove-file').remove();
          reloadTable();
          $("#stepsWrapper").html('');
          $("#addStepBtn").trigger('click');
      }
      
      previewImage('#image', '#preview-image');
      previewImage('#meta_image', '#preview-meta-image');
      previewImage('#banner_image', '#preview-banner-image');

      // Status toggle
      $(document).on('change', '.toggle-status', function() {
          var product_id = $(this).data('id');
          var status = $(this).prop('checked') ? 1 : 0;

          $.ajax({
              url: '/admin/products/status',
              method: "POST",
              data: {
                  product_id: product_id,
                  status: status,
                  _token: "{{ csrf_token() }}"
              },
              success: function(res) {
                success(res.message);
                reloadTable();
              },
              error: function(xhr, status, error) {
                console.error(xhr.responseText);
                error('Failed to update status');
              }
          });
      });

      // Show steps section
      $(document).on('click', '.steps', function() {
          let id = $(this).data('id');
          $("#stepsSection").removeClass('d-none').attr('data-id', id);

          $.ajax({
              url: `/admin/products/${id}/process`,
              method: 'GET',
              success: function(res) {
                  $("#process_title").val(res.title || '');
                  $("#process_sub_title").val(res.sub_title || '');
                  
                  $("#stepsWrapper").html('');
                  if(res.steps && res.steps.length) {
                      res.steps.forEach(step => {
                          let html = `
                              <div class="step-row mb-2">
                                  <input type="text" class="form-control step-title mb-1" placeholder="Step title" value="${step.title || ''}">
                                  <textarea class="form-control step-desc" rows="2" placeholder="Step description">${step.description || ''}</textarea>
                                  <button type="button" class="btn btn-sm btn-danger remove-step mt-1">Remove</button>
                              </div>
                          `;
                          $("#stepsWrapper").append(html);
                      });
                  } else {
                      $("#addStepBtn").trigger('click');
                  }
              },
              error: function(xhr) { console.error(xhr.responseText); }
          });
      });

      // Add new step
      $(document).on('click', '#addStepBtn', function() {
          let html = `
              <div class="step-row mb-2">
                  <input type="text" class="form-control step-title mb-1" placeholder="Step title">
                  <textarea class="form-control step-desc" rows="2" placeholder="Step description"></textarea>
                  <button type="button" class="btn btn-sm btn-danger remove-step mt-1">Remove</button>
              </div>
          `;
          $("#stepsWrapper").append(html);
      });

      // Remove step
      $(document).on('click', '.remove-step', function() {
          $(this).closest('.step-row').remove();
      });

      // Close section
      $(document).on('click', '#closeStepsBtn', function() {
          $("#stepsSection").addClass('d-none');
          $("#process_title, #process_sub_title").val('');
          $("#stepsWrapper").html('');
      });

      // Save steps
      $(document).on('click', '#saveStepsBtn', function() {
          let id = $("#stepsSection").attr('data-id');

          let steps = [];
          $("#stepsWrapper .step-row").each(function() {
              let title = $(this).find('.step-title').val();
              let desc = $(this).find('.step-desc').val();
              if(title || desc) {
                  steps.push({ title, description: desc });
              }
          });

          let data = {
              title: $("#process_title").val(),
              sub_title: $("#process_sub_title").val(),
              steps: steps,
              _token: $('meta[name="csrf-token"]').attr('content')
          };

          $.ajax({
              url: `/admin/products/${id}/process`,
              method: 'POST',
              data: data,
              success: function() {
                  success('Process saved successfully');
                  $("#stepsSection").addClass('d-none');
              },
              error: function() {
                  error('Failed to save process');
              }
          });
      });

      //Delete
      $("#contentContainer").on('click','.delete', function(){
          if(!confirm('Are you sure you want to delete this service?')) return;
          codeid = $(this).data('id');
          info_url = url + '/'+codeid;
          $.ajax({
              url: info_url,
              method: "GET",
              success: function(res) {
                clearform();
                success(res.message);
                pageTop();
                reloadTable();
              },
              error: function(xhr) {
                console.error(xhr.responseText);
                pageTop();
                if (xhr.responseJSON && xhr.responseJSON.errors)
                  error(Object.values(xhr.responseJSON.errors)[0][0]);
                else
                  error();
              }
          });
      });

      var table = $('#example1').DataTable({
          processing: true,
          serverSide: true,
          ajax: {
              url: "{{ route('products.index') }}",
              type: "GET",
              error: function (xhr, status, error) {
                  console.error(xhr.responseText);
              }
          },
          columns: [
              {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
              {data: 'thumbnail', name: 'thumbnail', orderable: false, searchable: false},
              {data: 'title', name: 'title'},
              { data: 'category', name: 'category' },
              { data: 'price', name: 'price' },
              {data: 'status', name: 'status', orderable: false, searchable: false},
              {data: 'action', name: 'action', orderable: false, searchable: false},
          ],
          responsive: true,
          lengthChange: false,
          autoWidth: false,
      });

      function reloadTable() {
        table.ajax.reload(null, false);
      }
  });
</script>
@endsection