@extends('admin.master')

@section('content')
<section class="content pt-3">
    <div class="container-fluid">
        <div class="card card-secondary">
            <div class="card-header">
                <h3 class="card-title">Manage Sections</h3>
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
                        @foreach($sections as $section)
                        <tr data-id="{{ $section->id }}">
                            <td>{{ $section->sl }}</td>
                            <td>{{ $section->name }}</td>
                            <td>
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input toggle-status" id="customSwitchStatus{{ $section->id }}" data-id="{{ $section->id }}" {{ $section->status ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="customSwitchStatus{{ $section->id }}"></label>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <small class="text-muted">Drag & drop rows to change order</small>
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
              var order = $(this).sortable('toArray', { attribute: 'data-id' });
              $.post("{{ route('sections.updateOrder') }}", {
                  _token: '{{ csrf_token() }}',
                  order: order
              }, function(res) {
                  success(res.message);
                  $("#sortable tr").each(function(i){
                      $(this).find("td:first").text(i+1);
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
              success(res.message);
          });
      });
  });
</script>
@endsection