<div class="text-center">
	<?php flash_message('error_msg', 'danger'); ?>
    <h2 class="text-bold text-danger">Forbidden!</h2>
    <p class="p-b-10"></p>
    <div>
    	<?php 
		//coming from portal?
		if ($referrer == 'portal') {
	        ajax_page_button('user', 'Save Me!', 'btn-info btn-rounded btn-lg');
	    } else { ?>
	    	<a href="<?php echo base_url(); ?>" class="btn btn-info btn-rounded btn-lg">Go Back</a>
	    <?php } ?>
    </div>
</div>