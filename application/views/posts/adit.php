<?php
$img_max_size = 200;
$row = $ajax_page == 'edit' ? $row : '';
$form_attributes = [
	'class' => $ajax_page.'_post_form '.$type,
	'data-type' => $type
];
xform_open(NULL, $form_attributes); 
	if ($ajax_page == 'edit') { 
        xform_input('id', 'hidden', $id);
    } 
    if ($ajax_page == 'add' && $type == 'comment') {
    	xform_input('post_id', 'hidden', $post_id);
    	xform_input('comment_id', 'hidden', $comment_id);
    } ?>
    <?php
    //show summernote if type is post
    //NOTE: 
	if ($type == 'post') { ?>
		<div class="smt_wrapper">
			<?php
			//TL;DR
			//on edit, content will be loaded into textarea when page is fully loaded to prevent rendering html tags before summernote initializes, so the value field is left empty
			xform_input('content', 'textarea', '', false, ['rows' => 5, 'class' => 'smt_'.$smt_id]);
			$this->summernote->config([
				'path' => 'posts', 
				'size' => $img_max_size, 
				'resize_width' => 100, 
				'resize_height' => 100,
				'reqlog' => 1,
				'content' => adit_value($row, 'content')
			]); ?>
		</div>
		<?php
	} else { 
		//regular textarea
		xform_input('content', 'textarea', adit_value($row, 'content', '', true), false, ['rows' => 3, 'class' => '']);
	} ?>
	<div class="form-group m-t-10">
		<button type="submit" class="btn-primary btn_raised_<?php echo $type == 'post' ? 'md' : 'xs'; ?>"><?php echo $ajax_page == 'add' ? 'Post It' : 'Update'; ?></button>
		<?php
		if ($ajax_page == 'add' && $type == 'comment') { ?>
			<button type="button" class="btn-secondary btn_raised_<?php echo $type == 'post' ? 'md' : 'xs'; ?>" data-toggle="collapse" data-target="#comment_add_<?php echo $pc_id; ?>">Cancel</button>
			<?php
		} ?>
		<?php
		if ($ajax_page == 'edit') { ?>
			<button type="button" class="btn-secondary btn_raised_<?php echo $type == 'post' ? 'md' : 'xs'; ?>" data-toggle="collapse" data-target="#ac_<?php echo $type.'_'.$id; ?>">Cancel</button>
			<?php
		} ?>
	</div>
	<?php
xform_close();