<div class="row m-t-50">
	<div class="col-md-6 offset-md-3">
	    <div class="card profile-card">
	        <div class="background-block">
	            <img src="<?php echo base_url('assets/common/img/bg/profile-bg.jpg'); ?>" alt="profile-sample1" class="background"/>
	        </div>
	        <div class="profile-thumb-block">
	        	<img src="<?php echo base_url($row->avatar); ?>" alt="<?php echo $row->username; ?> avatar" class="profile" />
	        </div>
	        <div class="card-content">
	        	<h2><?php echo $row->username; ?></h2>
		        <small class="text-muted"><?php echo $row->nationality; ?></small>
		        <div class="icon-block">
		        	<a href="<?php echo strlen($row->social_facebook) ? 'https://facebook.com/'.$row->social_facebook : '#!'; ?>" title="Connect on Facebook" target="_blank"> <i class="fa fa-facebook bg-facebook"></i></a>
		        	<a href="<?php echo strlen($row->social_twitter) ? 'https://twitter.com/'.$row->social_twitter : '#!'; ?>" title="Connect on Twitter" target="_blank"> <i class="fa fa-twitter bg-twitter"></i></a>
		        	<a href="<?php echo strlen($row->social_instagram) ? 'https://instagram.com/'.$row->social_instagram : '#!'; ?>" title="Connect on Instagram" target="_blank"> <i class="fa fa-instagram bg-instagram"></i></a>
		        	<a href="<?php echo strlen($row->social_linkedin) ? 'https://linkedin.com/in/'.$row->social_linkedin : '#!'; ?>" title="Connect on LinkedIn"target="_blank"> <i class="fa fa-linkedin bg-linkedin"></i></a>
		        </div>
		    </div>
	    </div>
	</div>
</div>
<div class="mt-3 text-center text-muted" style="font-style: italic;">
	<h6>
		<i class="fa fa-quote-left"></i>
		<?php echo $row->quote; ?>
		<i class="fa fa-quote-right"></i> 
	</h6>
</div>
<div class="row mt-5">
    <?php _info_card('Posts', $row->user_posts, 'book'); ?>
    <?php _info_card('Comments', $row->user_comments, 'comments'); ?>
    <?php _info_card('Upvotes', $row->user_votes, 'thumbs-up'); ?>
</div>

<?php 
function _info_card($title, $data, $icon) { ?>
	<div class="col-md-4 col-sm-4 p-b-50">
        <div class="card border-info mx-sm-1 p-3">
            <div class="card border-info shadow text-info p-3 round-stat-icon ">
            	<span class="fa fa-<?php echo $icon; ?>" aria-hidden="true"></span>
            </div>
            <div class="text-info text-center mt-3"><h6><?php echo $title; ?></h6></div>
            <div class="text-info text-center mt-2"><h3><?php echo shorten_number($data); ?></h3></div>
        </div>
    </div>
    <?php
} ?>