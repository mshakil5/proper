@extends('admin.master')

@section('content')
<section class="content" id="newBtnSection">
  <div class="container-fluid">
    <button type="button" class="btn btn-secondary my-3" id="newBtn">Add new</button>
  </div>
</section>

<section class="content mt-3" id="addThisFormContainer">
  <div class="container-fluid">
    <div class="card card-secondary">
      <div class="card-header"><h3 id="cardTitle">Add New Plan</h3></div>
      <div class="card-body">
        <form id="createThisForm">@csrf
          <input type="hidden" id="codeid" name="codeid">
          <div class="form-group">
            <label>Name</label>
            <input type="text" id="name" name="name" class="form-control" placeholder="Enter Plan Name">
          </div>
          <div class="form-group">
            <label>Amount (£)</label>
            <input type="number" id="amount" name="amount" class="form-control" placeholder="Enter Amount" step="0.01">
          </div>
          <div class="form-group">
            <label>Included Features</label>
            <textarea id="included_features" class="form-control" rows="3" placeholder='Feature A, Feature B'></textarea>
          </div>
          <div class="form-group">
            <label>Excluded Features</label>
            <textarea id="excluded_features" class="form-control" rows="3" placeholder='Feature X, Feature Y'></textarea>
          </div>
          <div class="form-check">
            <input type="checkbox" class="form-check-input" id="is_recommended">
            <label class="form-check-label" for="is_recommended">Is Recommended?</label>
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
      <div class="card-header"><h3>All Plans</h3></div>
      <div class="card-body">
        <table id="example1" class="table table-striped">
          <thead>
            <tr>
              <th>Sl</th>
              <th>Name</th>
              <th>Amount</th>
              <th>Recommended</th>
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
    $("#newBtn").click(()=>{$("#newBtn").hide();$("#addThisFormContainer").show(300);});
    $("#FormCloseBtn").click(()=>{$("#addThisFormContainer").hide(200);$("#newBtn").show(100);clearform();});

    $.ajaxSetup({headers:{'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')}});

    const url="{{URL::to('/admin/plans')}}";
    const upurl="{{URL::to('/admin/plans-update')}}";

    function parseFeatures(text){
      return text.split(',').map(f=>f.trim()).filter(f=>f.length>0);
    }

    $("#addBtn").click(function(){
      const formData=new FormData();
      formData.append("name",$("#name").val());
      formData.append("amount",$("#amount").val());
      formData.append("is_recommended",$("#is_recommended").is(':checked')?1:0);

      formData.append("included_features",JSON.stringify(parseFeatures($("#included_features").val())));
      formData.append("excluded_features",JSON.stringify(parseFeatures($("#excluded_features").val())));

      const isCreate=$(this).val()=="Create";
      const ajaxUrl=isCreate?url:upurl;
      if(!isCreate) formData.append("codeid",$("#codeid").val());

      $.ajax({
        url:ajaxUrl,method:"POST",data:formData,processData:false,contentType:false,
        success:(res)=>{success(res.message);clearform();reloadTable();},
        error:(xhr)=>{
          console.log(xhr.responseText);
          if(xhr.responseJSON?.errors){
            error(Object.values(xhr.responseJSON.errors)[0][0]);
          }else error('Something went wrong');
        }
      });
    });

    $(document).on('click','.edit',function(){
      $.get(url+'/'+$(this).data('id')+'/edit',{},function(d){
        $("#codeid").val(d.id);
        $("#name").val(d.name);
        $("#amount").val(d.amount);
        $("#is_recommended").prop('checked',d.is_recommended);
        $("#included_features").val((d.included_features||[]).join(', '));
        $("#excluded_features").val((d.excluded_features||[]).join(', '));
        $("#addBtn").val('Update').html('Update');
        $("#newBtn").hide();$("#addThisFormContainer").show(300);
      });
    });

    $(document).on('click','.delete',function(){
      if(!confirm('Sure?'))return;
      $.ajax({url:url+'/'+$(this).data('id'),method:"GET",
        success:(res)=>{success(res.message);reloadTable();},
        error:(xhr)=>error(xhr.responseText)
      });
    });

    $(document).on('change','.toggle-status',function(){
      $.post('/admin/plans-status',{plan_id:$(this).data('id'),status:$(this).prop('checked')?1:0,_token:"{{csrf_token()}}"},
        res=>{success(res.message);reloadTable();}
      );
    });

    let table=$('#example1').DataTable({
      processing:true,serverSide:true,
      ajax:'{{route("allplans")}}',
      columns:[
        {data:'DT_RowIndex',name:'DT_RowIndex',orderable:false,searchable:false},
        {data:'name',name:'name'},
        {data:'amount',name:'amount'},
        {data:'is_recommended',name:'is_recommended',orderable:false,searchable:false},
        {data:'status',name:'status',orderable:false,searchable:false},
        {data:'action',name:'action',orderable:false,searchable:false},
      ],
    });

    function reloadTable(){table.ajax.reload(null,false);}
    function clearform(){
      $('#createThisForm')[0].reset();
      $("#addBtn").val('Create').html('Create');
      $("#addThisFormContainer").slideUp(200);
      $("#newBtn").slideDown(200);
    }
  });
</script>
@endsection