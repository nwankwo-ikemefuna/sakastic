<?php
$row = $ajax_page == 'edit' ? $row : '';
$form_attributes = ['class' => $ajax_page.'_post_form'];
echo form_open(NULL, $form_attributes); 
	if ($ajax_page == 'edit') { 
        xform_input('id', 'hidden', $id);
    } ?>
	<div class="smt_wrapper">
		<?php
		xform_input('content', 'textarea', adit_value($row, 'content'), false, ['rows' => 5, 'class' => 'post_summernote', 'data-height' => 150]);
		$this->summernote->config('posts', 100, 'smt_images', adit_value($row, 'content')); ?>
	</div>
	<?php
	xform_help(['help' => 'Note: Images above 100KB will not be uploaded.']); ?>
	<div class="form-group m-t-10">
		<button type="submit" class="btn-primary clickable"><?php echo $ajax_page == 'add' ? 'Post It' : 'Update'; ?></button>
		<?php
		if ($ajax_page == 'edit') { ?>
			<button type="button" class="btn-secondary clickable reload_post" data-id="<?php echo $id; ?>" data-type="post">Cancel</button>
			<?php
		} ?>
	</div>
	<?php
echo form_close();
