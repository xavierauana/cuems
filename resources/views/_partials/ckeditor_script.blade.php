<script src="//cdn.ckeditor.com/4.12.1/standard/ckeditor.js"></script>
<script>
	  var options = {
        filebrowserImageBrowseUrl: '/laravel-filemanager?type=Images',
        filebrowserImageUploadUrl: '/laravel-filemanager/upload?type=Images&_token=',
        filebrowserBrowseUrl     : '/laravel-filemanager?type=Files',
        filebrowserUploadUrl     : '/laravel-filemanager/upload?type=Files&_token='
      };

      document.querySelectorAll('.ckeditor').forEach(function (item) {
        CKEDITOR.replace(item, options);
      })
</script>
	