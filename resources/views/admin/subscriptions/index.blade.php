@extends('admin.master')

@section('content')
<section class="content">
  <div class="container-fluid">
    <div class="card card-secondary">
      <div class="card-header"><h3>All Subscriptions</h3></div>
      <div class="card-body">
        <table id="subscriptionsTable" class="table table-striped">
          <thead>
            <tr>
              <th>Sl</th>
              <th>User</th>
              <th>Email</th>
              <th>Plan</th>
              <th>Amount</th>
              <th>Payment Status</th>
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
    let table = $('#subscriptionsTable').DataTable({
        processing:true, serverSide:true,
        ajax:'{{ route("allsubscriptions") }}',
        columns:[
            {data:'DT_RowIndex',name:'DT_RowIndex', orderable:false, searchable:false},
            {data:'user', name:'user'},
            {data:'email', name:'email'},
            {data:'plan', name:'plan'},
            {data:'amount', name:'amount'},
            {data:'payment_status', name:'payment_status'},
            {data:'action', name:'action', orderable:false, searchable:false},
        ],
    });
});
</script>
@endsection