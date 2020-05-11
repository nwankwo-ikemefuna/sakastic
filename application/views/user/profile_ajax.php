<div class="mb-3">
	<img class="round_avatar_90" src="<?php echo base_url($row->avatar); ?>" alt="<?php echo $row->username; ?> avatar" />
</div>

<?php 
data_show_grid('Username', $row->username);
data_show_grid('Email', $row->email);
data_show_grid('Sex', ['', 'Male', 'Female'][$row->sex]);
data_show_grid('Nationality', $row->nationality);
?>

<h6 class="mt-3">Social Links</h6>
<?php
data_show_grid(_social_name('facebook'), _social_link('facebook', $row));
data_show_grid(_social_name('twitter'), _social_link('twitter', $row));
data_show_grid(_social_name('instagram'), _social_link('instagram', $row));
data_show_grid(_social_name('linkedin'), _social_link('linkedin', $row));

function _social_link($social, $row) {
	$obj = 'social_'.$social;
	$data = $row->$obj;
	if (!strlen($data)) return '';
	//fix linkedin which has in/ before username
	$link = 'https://'.$social.'.com/' . ($social == 'linkedin' ? 'in/'.$data : $data);
	return '<a href="'.$link.'" target="_blank">'.$link.'</a>';
}

function _social_name($social) {
	return '<i class="fa fa-'.$social.' profile-social-icon bg-'.$social.'"></i>'.ucfirst($social);
}