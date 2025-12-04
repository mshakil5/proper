  function pagetop() {
      window.scrollTo({
          top: 130,
          behavior: 'smooth',
      });
  }

  // Success
function success(msg) {
  toastr.success(msg ?? 'Success!');
}

// Error
function error(msg) {
  toastr.error(msg ?? 'Something went wrong!');
}

//
function reload(ms = 2000) {
    setTimeout(() => {
        location.reload();
    }, ms);
}

function pageTop() {
    window.scrollTo({
        top: 50,
        behavior: 'smooth',
    });
}

// Preview image
function previewImage(inputSelector, imgSelector) {
  $(inputSelector).change(function (e) {
    if (this.files && this.files[0]) {
      var reader = new FileReader();
      reader.onload = function (e) {
        $(imgSelector).attr('src', e.target.result);
      };
      reader.readAsDataURL(this.files[0]);
    }
  });
}

$(document).ready(function () {
  // Summernote
  $('.summernote').summernote({
    height: 200,
    resize: true,
    toolbar: [
      ['style', ['style']],
      ['font', ['bold', 'italic', 'underline', 'strikethrough', 'superscript', 'subscript', 'clear']],
      ['fontname', ['fontname']],
      ['fontsize', ['fontsize']],
      ['color', ['color']],
      ['para', ['ul', 'ol', 'paragraph', 'height']],
      ['table', ['table']],
      ['insert', ['link', 'picture', 'video', 'hr']],
      ['view', ['fullscreen', 'codeview', 'help']]
    ],
    fontSizes: [
      '8', '9', '10', '11', '12', '13', '14', '15', '16', '18', '20',
      '22', '24', '26', '28', '30', '32', '34', '36', '38', '40', '42',
      '44', '46', '48', '50', '60', '72', '84', '96'
    ]
  });

  //Selct2
  $('.select2').select2({
      width: '100%'
  });
});

// Global remove button handler
$(document).on('click', '.remove-file', function() {
    const btn = $(this);
    const filename = btn.data('filename');
    const path = btn.data('path');
    const model = btn.data('model');
    const id = btn.data('id');
    const col = btn.data('col');

    if (!filename || !path || !model || !id || !col) return;

    if(!confirm('Are you sure?')) return;

    $.ajax({
        url: '/admin/remove-file',
        type: 'POST',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            filename: filename,
            path: path,
            model: model,
            id: id,
            col: col
        },
        success: function(res) {
            btn.prev('img').remove();
            btn.remove();  
            success(res.message);
        },
        error: function(xhr) {
            console.error(xhr.responseText);
            pageTop();
            if (xhr.responseJSON && xhr.responseJSON.errors)
                error(Object.values(xhr.responseJSON.errors)[0][0]);
            else
                error();
        }
    });
});