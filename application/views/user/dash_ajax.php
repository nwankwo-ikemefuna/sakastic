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
		        	<?php 
		        	_social_link_icon('facebook', $row);
		        	_social_link_icon('twitter', $row);
		        	_social_link_icon('instagram', $row);
		        	_social_link_icon('linkedin', $row);
		        	?>
		        </div>
		    </div>
	    </div>
	</div>
</div>
<div class="mt-3 text-center text-muted" style="font-style: italic;">
	<?php 
	if (strlen($row->quote)) { ?>
		<h6>
			<i class="fa fa-quote-left"></i>
			<?php echo $row->quote; ?>
			<i class="fa fa-quote-right"></i> 
		</h6>
		<?php 
	} ?>
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
} 

function _social_link_icon($social, $row) { 
	$obj = 'social_'.$social;
	$data = $row->$obj;
	if (!strlen($data)) return '';
	//fix linkedin which has in/ before username
	$link = 'https://'.$social.'.com/' . ($social == 'linkedin' ? 'in/'.$data : $data); 
	echo '<a href="'.$link.'" target="_blank" title="Connect on '.ucfirst($social).'"><i class="fa fa-'.$social.' bg-'.$social.'"></i></a>';
}