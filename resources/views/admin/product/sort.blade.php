@extends('admin.pages.master')
@section('title', 'Sort Products - ' . $category->name)
@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col">
            <h4>Sort Products: <strong>{{ $category->name }}</strong></h4>
            <p class="text-muted">Drag and drop to reorder products.</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('allproducts') }}" class="btn btn-light">Back</a>
            <button id="saveOrder" class="btn btn-primary">Save Order</button>
        </div>
    </div>
    <div class="card">
        <div class="card-body p-0">
            <ul class="list-group list-group-flush" id="sortableList">
                @foreach($products as $product)
                <li class="list-group-item d-flex align-items-center gap-3 py-3" data-id="{{ $product->id }}">
                    <i class="ri-drag-move-fill text-muted fs-5" style="cursor:grab;"></i>
                    <img src="{{ asset($product->image) }}" style="width:50px;height:50px;object-fit:cover;" class="rounded">
                    <span>{{ $product->title }}</span>
                    <span class="badge bg-light text-dark ms-auto">£{{ number_format($product->price, 2) }}</span>
                </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>
<script>
    new Sortable(document.getElementById('sortableList'), {
        animation: 150,
        handle: '.ri-drag-move-fill',
        ghostClass: 'bg-light'
    });

    $('#saveOrder').click(function() {
        let order = [];
        $('#sortableList li').each(function() {
            order.push($(this).data('id'));
        });

        $.post('/admin/products-reorder', {
            _token: '{{ csrf_token() }}',
            order: order
        }, function(d) {
            showSuccess(d.message);
        }).fail(() => showError('Failed to save order'));
    });
</script>
@endsection