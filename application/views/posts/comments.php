<div class="mt-2">
	<?php 
	post_avatar('<a class="text-primary text-bold clickable" data-toggle="collapse" data-target="#comment_add_'.$pc_id.'">Add a '.$reply_type.'</a>'); ?>
	<div class="collapse m-b-20" id="comment_add_<?php echo $pc_id; ?>">
    	<?php 
    	$ajax_page = 'add';
	    $smt_id = 'add_comment_'.$pc_id;
	    require 'adit.php'; ?>
	</div>
</div>

<h6 id="comments_info_<?php echo $pc_id; ?>" class="mt-2"></h6>
<div id="comments_section_<?php echo $pc_id; ?>" class="collapse show">
	<div class="input-group" id="sort_comments_group_<?php echo $pc_id; ?>" style="display: none;">
        <div class="input-group-prepend">
            <span class="input-group-text comment_sort_input_text">Sort by</span>
        </div>
		<select class="sort_comments" width="100" id="sort_comments_<?php echo $pc_id; ?>" data-post_id="<?php echo $post_id; ?>" data-comment_id="<?php echo $comment_id; ?>">
		  <!-- Render options dymanically-->
		</select>
	</div>
	<div id="comments_<?php echo $pc_id; ?>" class="collapse show mt-2">
		Comments loading... <i class="fa fa-spinner fa-spin"></i>
	</div>
	<div id="comments_pagination_<?php echo $pc_id; ?>" class="pagination-area"></div>
</div>