@extends('admin.pages.master')
@section('title', 'Section List')
@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title mb-0 flex-grow-1">Manage Sections</h3>
                        <small class="text-muted">Drag & drop rows to change order</small>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>SL</th>
                                    <th>Name</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="sortable">
                                @foreach ($sections as $section)
                                    <tr data-id="{{ $section->id }}">
                                        <td>{{ $section->sl }}</td>
                                        <td>{{ $section->name }}</td>
                                        <td>
                                            <div class="form-check form-switch">
                                                <input type="checkbox" class="form-check-input toggle-status"
                                                    id="customSwitchStatus{{ $section->id }}" data-id="{{ $section->id }}"
                                                    {{ $section->status ? 'checked' : '' }}>
                                                <label class="custom-control-label"
                                                    for="customSwitchStatus{{ $section->id }}"></label>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
                    var order = $(this).sortable('toArray', {
                        attribute: 'data-id'
                    });
                    $.post("{{ route('sections.updateOrder') }}", {
                        _token: '{{ csrf_token() }}',
                        order: order
                    }, function(res) {
                        showSuccess(res.message);
                        $("#sortable tr").each(function(i) {
                            $(this).find("td:first").text(i + 1);
                        });
                    });
                }
            });

            $('.toggle-status').change(function() {
                var id = $(this).data('id');
                var status = $(this).is(':checked') ? 1 : 0;
                $.post("{{ route('sections.toggleStatus') }}", {
                    _token: '{{ csrf_token() }}',
                    id: id,
                    status: status
                }, function(res) {
                    showSuccess(res.message);
                });
            });
        });
    </script>
@endsection
