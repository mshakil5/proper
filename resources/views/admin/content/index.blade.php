@extends('admin.master')

@section('content')

<section class="content" id="newBtnSection">
  <div class="container-fluid">
    <div class="row">
      <div class="col-2">
        <button type="button" class="btn btn-secondary my-3" id="newBtn">Add new</button>
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
            <h3 class="card-title" id="cardTitle">Add new Content</h3>
          </div>
          <div class="card-body">
            <form id="createThisForm" enctype="multipart/form-data">
              @csrf
              <input type="hidden" id="codeid" name="codeid">
              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                    <label>Category <span class="text-danger">*</span></label>
                    <select class="form-control select2" id="category_id" name="category_id">
                        <option value="">-- Select Category --</option>
                        @foreach($categories as $cat)
                          <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                  </div>  
                </div>
                <div class="col-md-5">
                  <div class="form-group">
                    <label>Short Title <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="short_title" name="short_title" placeholder="">
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label>Publishing Date <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" id="publishing_date" name="publishing_date" value="{{ date('Y-m-d') }}" min= "{{ date('Y-m-d') }}">
                  </div>
                </div>
                @if($type != 1)
                <div class="col-md-12">
                    <div class="form-group">
                      <label>long Title</label>
                      <input type="text" class="form-control" id="long_title" name="long_title" placeholder="">
                    </div>
                </div>
                @endif
                <div class="col-md-12">
                    <div class="form-group">
                      <label>Short Description</label>
                      <textarea class="form-control summernote" id="short_description" name="short_description" placeholder=""></textarea>
                    </div>
                </div>
                @if($type != 1)
                <div class="col-md-12">
                  <div class="form-group">
                    <label>long Description</label>
                    <textarea class="form-control summernote" id="long_description" name="long_description" placeholder=""></textarea>
                  </div>
                </div>
                @endif
                <div class="col-12">
                    <div class="form-group">
                        <label>Tags</label>
                        <select class="form-control select2" name="tags[]" id="tags" multiple="multiple" style="width: 100%;">
                            @foreach($tags as $tag)
                                <option value="{{ $tag->id }}">{{ $tag->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-12">
                  <div class="form-group">
                    <label>Feature Image <span style="color:red">*</span></label>
                    <input type="file" class="filepond" id="feature_image" name="feature_image" required>
                  </div>
                  @if($type == 1)
                  <div class="form-group">
                    <label>Additional Images</label>
                    <input type="file" class="filepond" id="images" name="images[]" multiple>
                  </div>
                  @endif
                </div>
                <div class="col-sm-12">
                    <div class="form-group">
                        <label>Meta Title</label>
                        <input type="text" class="form-control" name="meta_title" id="meta_title">
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="form-group">
                        <label>Meta Description</label>
                        <textarea class="form-control" name="meta_description" rows="3" id="meta_description"></textarea>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="form-group">
                        <label>Meta Keywords (comma separated)</label>
                        <input type="text" class="form-control" name="meta_keywords" id="meta_keywords">
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="form-group">
                        <label>Meta Image (1200x630 recommended)</label>
                        <input type="file" class="form-control-file" id="meta_image" name="meta_image" accept="image/*" oninput="document.getElementById('preview-meta-image').src = window.URL.createObjectURL(this.files[0])">
                        <br>
                        <img id="preview-meta-image" width="200" style="margin-top:10px;">
                    </div>
                </div>
              </div>
            </form>
          </div>
          <div class="card-footer">
            <button type="submit" id="addBtn" class="btn btn-secondary" value="Create">Create</button>
            <button type="button" id="FormCloseBtn" class="btn btn-default">Cancel</button>
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
            <h3 class="card-title">{{ $type==1?'Gallery':($type==2?'Blog':($type==3?'Event':'News')) }} List</h3>
          </div>
          <div class="card-body">
            <table id="example1" class="table cell-border table-striped">
              <thead>
                <tr>
                  <th>Sl</th>
                  <th>Feature Image</th>
                  <th>Title</th>
                  <th>Category</th>
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
<link href="https://unpkg.com/filepond/dist/filepond.min.css" rel="stylesheet">
<script src="https://unpkg.com/filepond/dist/filepond.min.js"></script>
<script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.js"></script>
<link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.css" rel="stylesheet">

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function () {
    $("#addThisFormContainer").hide();
    $(".select2").select2({ width: '100%' });

    $("#newBtn").click(function(){
        clearform();
        $("#newBtn").hide(100);
        $("#addThisFormContainer").show(300);
    });

    $("#FormCloseBtn").click(function(){
        $("#addThisFormContainer").hide(200);
        $("#newBtn").show(100);
        clearform();
    });

    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

    let type = {{ $type }};
    let url = "{{ url('admin/content') }}/"+type;
    let upurl = "{{ url('admin/content') }}/"+type+"/update";

    let table = $('#example1').DataTable({
        processing: true,
        serverSide: true,
        ajax: url,
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable:false, searchable:false },
            { data: 'feature_image', name: 'feature_image', orderable:false, searchable:false },
            { data: 'short_title', name: 'short_title' },
            { data: 'category_name', name: 'category_name' },
            { data: 'status', name: 'status', orderable:false, searchable:false },
            { data: 'action', name: 'action', orderable:false, searchable:false },
        ],
        responsive:true,
        lengthChange:false,
        autoWidth:false,
    });

    function reloadTable(){ table.ajax.reload(null,false); }

    FilePond.registerPlugin(FilePondPluginImagePreview);
    const pondFeature = FilePond.create(document.querySelector('#feature_image'), { allowImagePreview:true, allowMultiple:false });
    const pondImages = FilePond.create(document.querySelector('#images'), { allowImagePreview:true, allowMultiple:true });

    $("#addBtn").click(function(){
        var form_data = new FormData();
        form_data.append("short_title", $("#short_title").val());
        form_data.append("publishing_date", $("#publishing_date").val());
        form_data.append("long_title", $("#long_title").length ? $("#long_title").val() : '');
        form_data.append("category_id", $("#category_id").val());
        form_data.append("short_description", $("#short_description").val());
        form_data.append("long_description", $("#long_description").length ? $("#long_description").val() : '');
        form_data.append("meta_title", $("#meta_title").val());
        form_data.append("meta_description", $("#meta_description").val());
        form_data.append("meta_keywords", $("#meta_keywords").val());
        form_data.append("id", $("#codeid").val());

        let metaImageInput = document.getElementById('meta_image');
        if (metaImageInput.files.length > 0) {
            form_data.append("meta_image", metaImageInput.files[0]);
        }

        $("#tags").val().forEach(tag => form_data.append("tags[]", tag));

        if(pondFeature.getFiles().length > 0) form_data.append("feature_image", pondFeature.getFiles()[0].file);
        pondImages.getFiles().forEach(fileItem => form_data.append("images[]", fileItem.file));

        let actionUrl = ($(this).val() == 'Create') ? url : upurl;

        $.ajax({
            url: actionUrl,
            method:"POST",
            contentType:false,
            processData:false,
            data: form_data,
            success:function(res){
                clearform();
                success(res.message);
                reloadTable();
            },
            error: function(xhr){
                console.error(xhr.responseText);
                if(xhr.responseJSON && xhr.responseJSON.errors){
                    console.error(Object.values(xhr.responseJSON.errors)[0][0]);
                    error(Object.values(xhr.responseJSON.errors)[0][0]);
                } else {
                    error('Something went wrong');
                }
            }
        });
    });

    $("#contentContainer").on('click','.edit', function(){
        pagetop();
        $("#cardTitle").text('Update Content');
        let id = $(this).data('id');
        $.get(url+'/'+id+'/edit',{},function(d){
            $("#short_title").val(d.short_title);
            $("#long_title").val(d.long_title);
            $("#category_id").val(d.category_id).trigger('change');
            $('#short_description').summernote('code', d.short_description);
            $('#long_description').summernote('code', d.long_description);
            $("#meta_title").val(d.meta_title);
            $("#meta_description").val(d.meta_description);
            $("#meta_keywords").val(d.meta_keywords);
            $("#publishing_date").val(d.publishing_date);
            let tagIds = d.tags.map(tag => tag.id);  // extract IDs
            $("#tags").val(tagIds).trigger('change');
            $("#codeid").val(d.id);
            $("#addBtn").val('Update').html('Update');
            $("#addThisFormContainer").show(300);
            $("#newBtn").hide(100);

            if(d.meta_image){
                document.getElementById('preview-meta-image').src = "{{ asset('images/content') }}/" + d.meta_image;
            } else {
                document.getElementById('preview-meta-image').src = '';
            }

            pondFeature.removeFiles();
            if(d.feature_image) pondFeature.addFile("{{ asset('images/content') }}/"+d.feature_image);

            pondImages.removeFiles();
            if(d.images && d.images.length){
                d.images.forEach(img => pondImages.addFile("{{ asset('images/content') }}/"+img.image));
            }
        });
    });

    $("#contentContainer").on('click', '.delete', function(){
        if(!confirm('Sure?')) return;
        let id = $(this).data('id');
        $.get(url+'/'+id+'/delete', function(res){ success(res.message); reloadTable(); });
    });

    $(document).on('change','.toggle-status', function(){
        let id = $(this).data('id');
        let status = $(this).prop('checked') ? 1 : 0;
        $.post(url+'/status', {id:id,status:status}, function(res){ success(res.message); reloadTable(); });
    });

    function clearform(){
        $('#createThisForm')[0].reset();
        pondFeature.removeFiles();
        pondImages.removeFiles();
        $("#addBtn").val('Create').html('Create');
        $("#addThisFormContainer").slideUp(200);
        $("#newBtn").slideDown(200);
        $("#cardTitle").text('Add new Content');
        $(".select2").val('').trigger('change');
        $('#short_description').summernote('reset');
        $('#long_description').summernote('reset');
        $('#preview-meta-image').attr('src', '');
    }
});
</script>
@endsection
