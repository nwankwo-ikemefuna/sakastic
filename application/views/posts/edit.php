<div class="mt-2">
	<?php 
	post_avatar('<a class="text-primary text-bold clickable" data-toggle="collapse" data-target="#ac_'.$type.'_'.$id.'">Edit '.$reply_type.'</a>'); ?>
	<div class="mt-2 collapse show" id="ac_<?php echo $type.'_'.$id; ?>">
		<?php
		$ajax_page = 'edit';
		$smt_id = 'edit_'.$type.'_'.$id;
		require 'adit.php';
		?>
	</div>
</div>

<?php 
if ($type == 'post') { 
	//double encode to handle double quotes, new lines, etc
	$json = json_encode(json_encode(['content' => $row->content])); ?>
	<script type="text/javascript">
		$(document).ready(function(){
			summernote_init(".smt_<?php echo $smt_id; ?>", {picture: true}, {height: 150});
			//load content into field
			var json = JSON.parse(<?php echo $json; ?>);
			$("#ac_<?php echo $type.'_'.$id; ?>").find('[name="content"]').summernote('code', json.content);
		});
	</script>
<?php }