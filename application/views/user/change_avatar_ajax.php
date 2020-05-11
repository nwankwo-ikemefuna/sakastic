<?php
$attrs = ['id' => 'change_avatar_form', 'class' => 'ajax_form', 'data-type' => 'redirect', 'data-redirect' => '_ajax_dynamic'];
xform_open_multipart('api/user/change_avatar', $attrs); ?>
	<div class="">
		<?php 
		xform_help(['help' => "JPG and PNG images allowed, max 100kb. For better rendering, upload a square image.
			<br /> 
			Click the image to select file"]); ?>
		<img id="profile_avatar" class="img_preview square_avatar_90 clickable mt-2" src="<?php echo base_url($avatar); ?>" />
		<p id="selected_file" class="text-muted"></p>
		<input type="file" name="photo" accept=".jpg,.jpeg,.png" style="display: none;" />
		<?php 
		xform_notice();
		xform_submit('Change', '', ['class' => 'btn-primary btn_raised_sm']); ?>
	</div>
	<?php
xform_close();