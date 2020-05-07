<?php 
function post_avatar($title, $h = 'h6') { ?>
    <<?php echo $h; ?>><img src="<?php echo user_avatar(); ?>" width="28" height="28" class="rounded-circle"> <?php echo $title; ?></<?php echo $h; ?>>
    <?php
}