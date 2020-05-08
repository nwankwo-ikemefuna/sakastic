<?php 
sidebar_card_widget('Trending this Week', 'sb_trending_posts', '?type=trending');
sidebar_card_widget('Recent Posts', 'sb_recent_posts', '?type=recent');
sidebar_card_widget('Posts I\'m Following', 'sb_followed_posts', '?type=followed');
sidebar_card_widget('Top Posters', 'sb_top_posters');
?>

<img class="img-responsive" src="<?php echo base_url('uploads/ads/qsm.png'); ?>" style="width: 100%">

<?php 
function sidebar_card_widget($title, $id, $more_url = '') { ?>
	<div class="card m-b-15" style="display: none;">
	    <div class="card-header">
	        <h6><?php echo $title; ?></h6>
	    </div>
	    <div class="card-body" id="<?php echo $id; ?>">
	        <!-- Render via ajax -->
	    </div>
	    <?php 
	    if (strlen($more_url)) { ?>
		    <div class="card-footer">
		        <a class="theme_link_red text-bold no_deco" href="<?php echo base_url($more_url); ?>">See All &raquo;</a>
		    </div>
		    <?php 
		} ?>
	</div>
	<?php
}