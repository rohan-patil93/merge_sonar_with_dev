  'use strict';
  $(document).ready(function() {
      // tinymce.init({
      //     selector: 'textarea.textediters',
      //     height: 200,
      //     theme: 'modern',
      //     plugins: [
      //         'advlist autolink lists link image charmap print preview hr anchor pagebreak',
      //         'searchreplace wordcount visualblocks visualchars code fullscreen',
      //         'insertdatetime media nonbreaking save table contextmenu directionality',
      //         'emoticons template paste textcolor colorpicker textpattern imagetools codesample toc'
      //     ],
      //     toolbar1: 'undo redo | insert | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image',
      //     toolbar2: 'print preview media | forecolor backcolor emoticons | codesample',
      //     image_advtab: true
      // });


tinymce.init({
  selector: 'textarea.textediters',
  height: 400,
 plugins: 'image code',
  // plugins: [
  //             'advlist autolink lists link image code charmap print preview hr anchor pagebreak',
  //             'searchreplace wordcount visualblocks visualchars fullscreen',
  //             'insertdatetime media nonbreaking save table contextmenu directionality',
  //             'emoticons template paste textcolor colorpicker textpattern imagetools codesample toc'
  //         ],
 // toolbar: 'undo redo | link image | code',
  toolbar1: 'undo redo | link image | code | insert | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image',
  toolbar2: 'print preview media | forecolor backcolor emoticons | codesample',
  image_title: true,
  automatic_uploads: true,
  file_picker_types: 'image',
  /* and here's our custom image picker*/
  file_picker_callback: function (cb, value, meta) {
    var input = document.createElement('input');
    input.setAttribute('type', 'file');
    input.setAttribute('accept', 'image/*');
   input.onchange = function () {
      var file = this.files[0];

      var reader = new FileReader();
      reader.onload = function () {
        /*
          Note: Now we need to register the blob in TinyMCEs image blob
          registry. In the next release this part hopefully won't be
          necessary, as we are looking to handle it internally.
        */
        var id = 'blobid' + (new Date()).getTime();
        var blobCache =  tinymce.activeEditor.editorUpload.blobCache;
        var base64 = reader.result.split(',')[1];
        var blobInfo = blobCache.create(id, file, base64);
        blobCache.add(blobInfo);

        /* call the callback and populate the Title field with the file name */
        cb(blobInfo.blobUri(), { title: file.name });
      };
      reader.readAsDataURL(file);
    };

    input.click();
  },
  content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }'
});




  });
