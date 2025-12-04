@extends('admin.master')

@section('content')
<section class="content" id="newBtnSection">
  <div class="container-fluid">
    <button type="button" class="btn btn-secondary my-3" id="newBtn">Add New Category</button>
  </div>
</section>

<section class="content pt-3" id="addThisFormContainer">
  <div class="container-fluid">
    <div class="card card-secondary">
      <div class="card-header"><h3 id="cardTitle">Add New Category</h3></div>
      <div class="card-body">
        <form id="createThisForm">@csrf
          <input type="hidden" id="codeid" name="codeid">
          <div class="form-group">
            <label>Category Name</label>
            <input type="text" id="name" name="name" class="form-control" placeholder="Enter Category Name">
          </div>
        </form>
      </div>
      <div class="card-footer">
        <button id="addBtn" class="btn btn-secondary" value="Create">Create</button>
        <button id="FormCloseBtn" class="btn btn-default">Cancel</button>
      </div>
    </div>
  </div>
</section>

<section class="content" id="contentContainer">
  <div class="container-fluid">
    <div class="card card-secondary">
      <div class="card-header"><h3>All Categories</h3></div>
      <div class="card-body">
        <table id="example1" class="table table-striped">
          <thead>
            <tr>
              <th>Sl</th>
              <th>Name</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>
</section>
@endsection

@section('script')
<script>
  $(function(){
    $("#addThisFormContainer").hide();
    $("#newBtn").click(()=>{
      $("#newBtn").hide();
      $("#addThisFormContainer").show(300);
    });
    
    $("#FormCloseBtn").click(()=>{
      $("#addThisFormContainer").hide(200);
      $("#newBtn").show(100);
      clearform();
    });

    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    const url = "{{URL::to('/admin/categories')}}";
    const upurl = "{{URL::to('/admin/categories-update')}}";

    $("#addBtn").click(function(){
      const formData = new FormData();
      formData.append("name", $("#name").val());

      const isCreate = $(this).val() == "Create";
      const ajaxUrl = isCreate ? url : upurl;
      if(!isCreate) formData.append("codeid", $("#codeid").val());

      $.ajax({
        url: ajaxUrl,
        method: "POST",
        data: formData,
        processData: false,
        contentType: false,
        success: (res) => {
          success(res.message);
          clearform();
          reloadTable();
        },
        error: (xhr) => {
          console.log(xhr.responseText);
          if(xhr.responseJSON?.errors){
            error(Object.values(xhr.responseJSON.errors)[0][0]);
          } else {
            error('Something went wrong');
          }
        }
      });
    });

    $(document).on('click', '.edit', function(){
      $.get(url + '/' + $(this).data('id') + '/edit', {}, function(d){
        $("#codeid").val(d.id);
        $("#name").val(d.name);
        $("#addBtn").val('Update').html('Update');
        $("#cardTitle").html('Edit Category');
        $("#newBtn").hide();
        $("#addThisFormContainer").show(300);
      });
    });

    $(document).on('click', '.delete', function(){
      if(!confirm('Are you sure you want to delete this category?')) return;
      $.ajax({
        url: url + '/' + $(this).data('id'),
        method: "GET",
        success: (res) => {
          success(res.message);
          reloadTable();
        },
        error: (xhr) => error(xhr.responseText)
      });
    });

    $(document).on('change', '.toggle-status', function(){
      $.post('/admin/categories-status', {
        category_id: $(this).data('id'),
        status: $(this).prop('checked') ? 1 : 0,
        _token: "{{csrf_token()}}"
      }, res => {
        success(res.message);
        reloadTable();
      });
    });

    let table = $('#example1').DataTable({
      processing: true,
      serverSide: true,
      ajax: '{{route("allcategories")}}',
      columns: [
        {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
        {data: 'name', name: 'name'},
        {data: 'status', name: 'status', orderable: false, searchable: false},
        {data: 'action', name: 'action', orderable: false, searchable: false},
      ],
    });

    function reloadTable() {
      table.ajax.reload(null, false);
    }
    
    function clearform() {
      $('#createThisForm')[0].reset();
      $("#addBtn").val('Create').html('Create');
      $("#cardTitle").html('Add New Category');
      $("#addThisFormContainer").slideUp(200);
      $("#newBtn").slideDown(200);
    }
  });
</script>
@endsection