<div class="post_action_container">
	<?php
	$ajax_page = 'edit';
	require 'adit.php';
	?>
</div>

<script type="text/javascript">
	$(document).ready(function(){
		summernote_init('.post_summernote', {picture: true});
	});
</script>