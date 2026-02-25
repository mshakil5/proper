@extends('admin.pages.master')
@section('title', 'Points History')

@section('content')
<div class="container-fluid">

    <div class="card mb-3" id="addPointsContainer" style="display:none;">
        <div class="card-header">
            <h4 class="card-title mb-0">Add Points Manually</h4>
        </div>
        <div class="card-body">
            <form id="addPointsForm">
                @csrf
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Client <span class="text-danger">*</span></label>
                            <select id="pointUserId" name="user_id" class="form-control select2" required>
                                <option value="">Search client...</option>
                                @foreach(\App\Models\User::where('user_type', 2)->where('status', 1)->orderBy('first_name')->get() as $u)
                                    <option value="{{ $u->id }}">{{ $u->first_name }} {{ $u->last_name }} ({{ $u->email }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="mb-3">
                            <label class="form-label">Points <span class="text-danger">*</span></label>
                            <input type="number" id="pointAmount" name="point" class="form-control" min="1" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label">Source</label>
                            <select id="pointSource" name="source" class="form-control">
                                <option value="google_review">Google Review</option>
                                <option value="manual">Manual</option>
                                <option value="referral">Referral</option>
                                <option value="social_share">Social Share</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <input type="text" id="pointDescription" name="description" class="form-control" placeholder="e.g. Google review reward">
                        </div>
                    </div>
                    <div class="col-12 text-end">
                        <button type="button" id="savePointsBtn" class="btn btn-primary">Add Points</button>
                        <button type="button" id="cancelPointsBtn" class="btn btn-light">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <a href="{{ url()->previous() }}" class="btn btn-light btn-sm"><i class="ri-arrow-left-line"></i> Back</a>
                <h4 class="card-title mb-0">Points History</h4>
            </div>
            <button class="btn btn-success btn-sm" id="addPointsBtn"><i class="ri-add-line"></i> Add Points</button>
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
    const clientId = new URLSearchParams(window.location.search).get('client_id') || '';

    var table = $('#pointsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('points.index') }}" + window.location.search,
            data: function(d) {
                d.client_id = clientId;
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'client_name', name: 'client_name' },
            { data: 'point', name: 'point' },
            { data: 'source', name: 'source' },
            { data: 'description', name: 'description' },
            { data: 'created_at', name: 'created_at' }
        ],
        order: [[5, 'desc']]
    });

    if (clientId) {
        $('#pointUserId').val(clientId).trigger('change');
    }

    $('#addPointsBtn').click(function() {
        $('#addPointsContainer').show(300);
        $(this).hide();
    });

    $('#cancelPointsBtn').click(function() {
        $('#addPointsContainer').hide(200);
        $('#addPointsBtn').show();
        $('#addPointsForm')[0].reset();
        if (clientId) $('#pointUserId').val(clientId).trigger('change');
    });

    $('#savePointsBtn').click(function() {
        var userId = $('#pointUserId').val();
        var point = $('#pointAmount').val();
        var source = $('#pointSource').val();
        var description = $('#pointDescription').val();

        if (!userId || !point) {
            showError('Client and points are required');
            return;
        }

        $.ajax({
            url: "{{ route('points.store') }}",
            type: 'POST',
            data: { user_id: userId, point: point, source: source, description: description },
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function(res) {
                showSuccess(res.message);
                $('#addPointsContainer').hide();
                $('#addPointsBtn').show();
                $('#addPointsForm')[0].reset();
                if (clientId) $('#pointUserId').val(clientId).trigger('change');
                table.ajax.reload(null, false);
            },
            error: function(xhr) {
                showError(xhr.responseJSON?.message ?? 'Something went wrong');
            }
        });
    });
});
</script>
@endsection