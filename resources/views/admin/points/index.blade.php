@extends('admin.pages.master')
@section('title', 'Points History')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title mb-0">Points History</h4>
        </div>
        <div class="card-body">
            <table id="pointsTable" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Client Name</th>
                        <th>Points</th>
                        <th>Source</th>
                        <th>Description</th>
                        <th>Date</th>
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
    var table = $('#pointsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('points.index') }}" + window.location.search,
            data: function(d) {
                d.client_id = new URLSearchParams(window.location.search).get('client_id') || '';
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
                data: 'client_name',
                name: 'client_name'
            },
            {
                data: 'point',
                name: 'point'
            },
            {
                data: 'source',
                name: 'source'
            },
            {
                data: 'description',
                name: 'description'
            },
            {
                data: 'created_at',
                name: 'created_at'
            }
        ],
        order: [[5, 'desc']]
    });
});
</script>
@endsection