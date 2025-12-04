@extends('admin.master')

@section('content')
<section class="content pt-3" id="contentContainer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">All Bookings</h3>
                    </div>
                    <div class="card-body">
                        <table id="example1" class="table cell-border table-striped">
                            <thead>
                                <tr>
                                    <th>Sl</th>
                                    <th>Date</th>
                                    <th>Name</th>
                                    <th>Phone</th>
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

<!-- View Modal -->
<div class="modal fade" id="viewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Booking Details</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Name:</strong> <span id="view-name"></span></p>
                        <p><strong>Phone:</strong> <span id="view-phone"></span></p>
                        <p><strong>Email:</strong> <span id="view-email"></span></p>
                        <p><strong>Date:</strong> <span id="view-date"></span></p>
                        <p><strong>Time:</strong> <span id="view-time"></span></p>
                        <p><strong>People:</strong> <span id="view-people"></span></p>
                        <p><strong>Status:</strong> <span id="view-status" class="badge"></span></p>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <p><strong>Message:</strong></p>
                        <div class="border p-3" id="view-message"></div>
                    </div>
                    <div class="col-12 mb-2">
                        <p><strong>Order Form:</strong></p>
                        <div id="view-order-form"></div>
                    </div>
                    <div class="col-12 mt-3">
                        <p><strong>Menu Selections:</strong></p>
                        <div id="view-menu-selection" class="row"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button></div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
$(document).ready(function () {
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
    var url = "{{ url('/admin/bookings') }}";

    var table = $('#example1').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('bookings.index') }}",
        columns: [
            {data:'DT_RowIndex', name:'DT_RowIndex', orderable:false, searchable:false},
            {data:'date', name:'date'},
            {data:'full_name', name:'full_name'},
            {data:'phone', name:'phone'},
            {data:'status', name:'status', orderable:false, searchable:false},
            {data:'action', name:'action', orderable:false, searchable:false},
        ],
        responsive:true, lengthChange:false, autoWidth:false
    });

    // View booking
    $("#contentContainer").on('click','.view', function(){
        var id = $(this).data('id');
        $.get(url+'/'+id, function(data){
            $('#view-name').text(data.name);
            $('#view-phone').text(data.phone);
            $('#view-email').text(data.email);
            $('#view-date').text(data.date);
            $('#view-time').text(data.time);
            $('#view-people').text(data.people);
            $('#view-message').text(data.message);

            if (data.order_form) {
                $('#view-order-form').html(
                    `<iframe src="${data.order_form}" style="width:100%;height:400px;" frameborder="0"></iframe>`
                );
            } else {
                $('#view-order-form').html('<span class="text-muted">No file uploaded</span>');
            }

            var statusLabels = ['Pending','Approved','Rejected'];
            var badgeClasses = ['badge-warning','badge-success','badge-danger'];
            $('#view-status').text(statusLabels[data.status])
                .removeClass('badge-warning badge-success badge-danger')
                .addClass(badgeClasses[data.status]);

                // Menu selections
                var categories = { soup: [], main: [], side: [], dessert: [] };

                if (data.menu_selection && Object.keys(data.menu_selection).length > 0) {
                    $.each(data.menu_selection, function(key, value){
                        var parts = key.split('_'); // e.g., "soup_vegetable"
                        var cat = parts[0];
                        var item = parts.slice(1).join(' ');

                        if (categories[cat]) {
                            categories[cat].push({ name: item, value: value });
                        }
                    });

                    var html = '';
                    $.each(categories, function(cat, items){
                        if (items.length > 0) {
                            html += `<div class="col-md-3 mb-2">
                                        <h6 class="text-uppercase">${cat}</h6>
                                        <ul class="list-group">`;
                            $.each(items, function(_, item){
                                html += `<li class="list-group-item p-1">
                                            ${item.name}: <strong>${item.value == 1 ? 'Yes' : 'No'}</strong>
                                        </li>`;
                            });
                            html += `</ul></div>`;
                        }
                    });
                } else {
                    html = '<div class="col-12 text-muted">No menu selections</div>';
                }

                $('#view-menu-selection').html(html);

            $('#viewModal').modal('show');
        });
    });

    // Change status
    $("#contentContainer").on('click','.approve, .reject', function(){
        var booking_id = $(this).data('id');
        var status = $(this).hasClass('approve') ? 1 : 2;

        $.post("{{ route('bookings.status') }}", {booking_id:booking_id, status:status}, function(res){
            success(res.message);
            table.ajax.reload(null,false);
        });
    });

    // Delete booking
    $("#contentContainer").on('click','.delete', function(){
        if(!confirm('Are you sure?')) return;
        var id = $(this).data('id');
        $.get(url+'/'+id+'/delete', function(res){
            success(res.message);
            // alert(res.message);
            table.ajax.reload(null,false);
        });
    });
});
</script>
@endsection