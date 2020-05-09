<div class="row">
	<div class="col-12 col-lg-8 offset-lg-2">
		<div class="card">
			<?php 
			if ($is_me) { ?>
				<div class="card-header text-center">
					<?php ajax_page_button('ajax_page_container', $this->c_controller.'/dash', 'Dashboard', 'btn-default btn-sm', '', 'dashboard'); ?>
					<?php ajax_page_button('ajax_page_container', $this->c_controller.'/profile', 'Profile', 'btn-default btn-sm', '', 'user'); ?>
					<?php ajax_page_button('ajax_page_container', $this->c_controller.'/edit', 'Edit Profile', 'btn-default btn-sm', '', 'edit'); ?>
					<?php ajax_page_button('ajax_page_container', $this->c_controller.'/change_avatar', 'Change Avatar', 'btn-default btn-sm', '', 'picture-o'); ?>
					<?php ajax_page_button('ajax_page_container', $this->c_controller.'/reset_account', 'Account Settings', 'btn-default btn-sm', '', 'wrench'); ?>
					<?php //ajax_page_button('ajax_page_container', $this->c_controller.'/notification', 'Notification Settings', 'btn-default btn-sm', '', 'bell'); ?>
				</div>
				<?php 
			} ?>
		   	<div class="card-body" <?php echo $is_me ? 'id="ajax_page_container"' : ''; ?>>
				<?php require 'dash_ajax.php'; ?>
			</div>
		</div>
	</div>
</div>

<div class="row mt-5">
	<div class="col-12 col-lg-8 offset-lg-2">
		<input type="hidden" id="user_posts" value="<?php echo $row->username; ?>">
		<input type="hidden" id="type" value="">
		<input type="hidden" id="post_view" value="0">
		<input type="hidden" id="search_post_field" value="">

		<h6><?php echo $is_me ? 'My' : ucfirst($row->username).'\'s'; ?> Recent Posts</h6>
		<div id="posts">
		    <!-- Render posts async -->
		</div>
		<?php if ($row->user_posts > 0) { ?>
			<a class="btn theme_button_red" href="<?php echo base_url('?user_posts='.$row->username); ?>">View All &raquo;</a>
			<?php 
		} else { ?>
			<p>No posts yet.</p>
			<?php 
		} ?>
	</div>
</div>