@extends('admin.master')

@section('content')

<!-- Add New Button -->
<section class="content" id="newBtnSection">
  <div class="container-fluid">
    <div class="row">
      <div class="col-2">
        <button type="button" class="btn btn-secondary my-3" id="newBtn">Add new</button>
      </div>
    </div>
  </div>
</section>

<!-- Form Section -->
<section class="content mt-3" id="addThisFormContainer">
  <div class="container-fluid">
    <div class="row justify-content-md-center">
      <div class="col-md-6">
        <div class="card card-secondary">
          <div class="card-header">
            <h3 class="card-title" id="cardTitle">Add new banner</h3>
          </div>
          <div class="card-body">
            <form id="createThisForm">
              @csrf
              <input type="hidden" id="codeid" name="codeid">
              <div class="form-group">
                <label>Page</label>
                <select name="page" id="page" class="form-control">
                  <option value="">Select</option>
                  <option value="About">About</option>
                  <option value="Menu">Menu</option>
                  <option value="Gallery">Gallery</option>
                  <option value="Services">Services</option>
                  <option value="Book">Book</option>
                  <option value="Contact">Contact</option>
                </select>
              </div>

              
              <div class="row">
                  <div class="col-md-12">
                      <div class="form-group">
                          <label for="image">Image (800x600)</label>
                          <input type="file" class="form-control-file" id="image" name="image" accept="image/*">
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

<!-- Table Section -->
<section class="content" id="contentContainer">
  <div class="container-fluid">
    <div class="card card-secondary">
      <div class="card-header">
        <h3 class="card-title">All Banner Image</h3>
      </div>
      <div class="card-body">
        <table id="example1" class="table table-striped">
          <thead>
            <tr>
              <th>Sl</th>
              <th>Name</th>
              <th>Image</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>
</section>
<!-- Image Modal -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-body text-center">
        <img src="" id="modalImage" class="img-fluid rounded" alt="Full Image">
      </div>
    </div>
  </div>
</div>

@endsection

@section('script')
<script>
$(document).ready(function () {

    $("#addThisFormContainer").hide();

    $("#newBtn").click(function(){
        clearForm();
        $("#newBtn").hide(100);
        $("#addThisFormContainer").show(300);
    });

    $("#FormCloseBtn").click(function(){
        $("#addThisFormContainer").hide(200);
        $("#newBtn").show(100);
        clearForm();
    });

    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

    var url = "{{ url('/admin/banner') }}";
    var upurl = "{{ route('banner.update') }}";

    let table = $('#example1').DataTable({
          processing: true,
          serverSide: true,
          ajax: '{{ route("banner.index") }}',
          columns: [
              { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable:false, searchable:false },
              { data: 'page', name: 'page' },
              { data: 'feature_image', name: 'feature_image', orderable:false, searchable:false },
              { data: 'status', name: 'status', orderable:false, searchable:false },
              { data: 'action', name: 'action', orderable:false, searchable:false },
          ]
      });


    function reloadTable() { table.ajax.reload(null,false); }

    // Show full image in modal
    $(document).on('click', '.feature-img', function() {
        let imgSrc = $(this).data('full');
        $('#modalImage').attr('src', imgSrc);
        $('#imageModal').modal('show');
    });


    // CREATE & UPDATE
    $("#addBtn").click(function(){
        var form_data = new FormData();
        form_data.append("page", $("#page").val());

        // Image upload
          var imageInput = document.getElementById('image');
          if(imageInput.files && imageInput.files[0]) {
              form_data.append("image", imageInput.files[0]);
          }

        if($(this).val() == 'Create'){
            $.ajax({
                url: url,
                method: "POST",
                data: form_data,
                contentType: false,
                processData: false,
                success: function(res){
                  console.log(res);
                    clearForm();
                    success(res.message);
                    reloadTable();
                },
                error: function(xhr){
                    if(xhr.responseJSON && xhr.responseJSON.errors)
                        error(Object.values(xhr.responseJSON.errors)[0][0]);
                }
            });
        } else {
            form_data.append("id", $("#codeid").val());
            $.ajax({
                url: upurl,
                method: "POST",
                data: form_data,
                contentType: false,
                processData: false,
                success: function(res){
                  console.log(res);
                    clearForm();
                    success(res.message);
                    reloadTable();
                },
                error: function(xhr){
                    if(xhr.responseJSON && xhr.responseJSON.errors)
                        error(Object.values(xhr.responseJSON.errors)[0][0]);
                }
            });
        }
    });

    // EDIT
    $("#contentContainer").on('click','.edit', function(){
        let id = $(this).data('id');
        $.get(url+'/'+id+'/edit', function(d){
            $("#page").val(d.page);
            $("#codeid").val(d.id);
            $("#addBtn").val('Update').html('Update');
            $("#cardTitle").text('Update category');
            $("#addThisFormContainer").show(300);
            $("#newBtn").hide(100);
        });
    });

    // DELETE
    $("#contentContainer").on('click','.delete', function(){
        if(!confirm('Sure?')) return;
        let id = $(this).data('id');
        $.get(url+'/'+id+'/delete', function(res){
            success(res.message);
            reloadTable();
        });
    });

    // STATUS TOGGLE
    $(document).on('change','.toggle-status', function(){
        let id = $(this).data('id');
        let status = $(this).prop('checked')?1:0;
        $.post('{{ url("/admin/banner-status") }}', {id:id,status:status,_token:'{{ csrf_token() }}'}, function(res){
            success(res.message);
        });
    });

    function clearForm(){
        $('#createThisForm')[0].reset();
        $("#addBtn").val('Create').html('Create');
        $("#cardTitle").text('Add new category');
        $("#codeid").val('');
        $("#addThisFormContainer").hide(200);
        $("#newBtn").show(100);
    }

});
</script>
@endsection
