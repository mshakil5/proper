@extends('admin.master')

@section('content')
<section class="content pt-3">
    <div class="container-fluid">
        <a href="{{ route('allcategories') }}" class="btn btn-secondary mb-3">Back to Categories</a>
        <div class="card card-secondary">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title mb-0">Sort Categories</h3>
                <button id="updateOrderBtnTop" class="btn btn-success btn-sm">
                    <i class="fas fa-save"></i> Update Sorting
                </button>
            </div>
            <div class="card-body">
                <small class="text-muted">Drag rows to change order, then click "Update Sorting".</small>
                <table class="table table-bordered mt-3">
                    <thead>
                        <tr>
                            <th style="width: 80px;">SL</th>
                            <th>Category Name</th>
                            <th style="width: 100px;">Status</th>
                            <th style="width: 150px;">Created At</th>
                        </tr>
                    </thead>
                    <tbody id="sortable">
                        @foreach($categories as $category)
                            <tr data-id="{{ $category->id }}">
                                <td>{{ $category->sl }}</td>
                                <td>{{ $category->name }}</td>
                                <td>
                                    @if($category->status)
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>{{ $category->created_at->format('d M, Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer text-end">
                <button id="updateOrderBtnBottom" class="btn btn-success btn-sm">
                    <i class="fas fa-save"></i> Update Sorting
                </button>
            </div>
        </div>
    </div>
</section>
@endsection

@section('script')
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script>
    $(function() {
        $("#sortable").sortable({
            placeholder: "ui-state-highlight",
            cursor: "grab",
            forcePlaceholderSize: true,
            opacity: 0.8,
            update: function(event, ui) {
                $("#sortable tr").each(function(i) {
                    $(this).find("td:first").text(i + 1);
                });
            }
        });

        function updateOrder() {
            var order = $("#sortable").sortable('toArray', { attribute: 'data-id' });
            $.post("{{ route('categories.updateOrder') }}", {
                _token: '{{ csrf_token() }}',
                order: order
            }, function(res) {
                success(res.message).then(() => {
                    location.reload();
                });
            }).fail(function(xhr) {
                error(xhr.responseJSON?.message || 'Failed to update sorting. Please try again.');
            });
        }

        $('#updateOrderBtnTop, #updateOrderBtnBottom').click(updateOrder);
    });
</script>

<style>
.ui-state-highlight {
    height: 60px;
    background-color: #f8f9fa;
    border: 2px dashed #dee2e6;
}
</style>
@endsection