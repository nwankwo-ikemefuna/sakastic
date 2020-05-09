jQuery(document).ready(function ($) {
  "use strict";  

  //change pass toggle
  toggle_elem_prop('#change_pass', '#change_pass_section', 'checked');
  toggle_elem_prop_trigger('#change_pass', '#change_pass_section');

  $(document).on('change', '#change_pass', function() {
    $('[name="curr_password"], [name="password"], [name="c_password"]').prop('required', $(this).prop('checked'));
  });

  //avatar change
  $(document).on('click', '#profile_avatar', function() {
    $('[name="photo"]').trigger('click');
  });
  $(document).on('change', '[name="photo"]', function() {
    preview_image(this);
    $('#selected_file').text(this.files[0].name);
  });

}); 