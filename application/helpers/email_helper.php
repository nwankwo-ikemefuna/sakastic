<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function email_call2action_red($url, $caption) {
	return '<table>
				<tr>
					<td style="background-color: #cc0821; border-color: #cc0821; border: 2px solid #cc0821; padding: 10px; text-align: center;">
						<a style="display: block; color: #ffffff; font-size: 17px; text-decoration: none; font-size: 18px;" href="' .$url. '">'
							.$caption. ' &raquo;
						</a>
					</td>
				</tr>
			</table>';
}

function email_call2action_blue($url, $caption) {
	return '<table>
				<tr>
					<td style="background-color: #0e67bf; border-color: #0e67bf; border: 2px solid #0e67bf; padding: 10px; text-align: center;">
						<a style="display: block; color: #ffffff; font-size: 17px; text-decoration: none; text-transform: capitalize;" href="' .$url. '">'
							.$caption.
						'</a>
					</td>
				</tr>
			</table>';
}

function email_header($subject) {
	return 	'<center>
				<a href="' . base_url() . '">
					<img src="' . base_url(SITE_LOGO) .'">
				</a>
			</center>';
}

function email_footer() {
	$ci =& get_instance();
	return 	'<br /><br /><hr style="color: #f2f2f2"> 
			<center>
				<a href="' . base_url() . '">' . SITE_NAME . '</a>. 
				Powered by <a href="' . $ci->site_author_url . '">' . $ci->site_author . '</a>
			</center>';
}

function send_mail($email, $subject, $message, $attachment = NULL) {
	$ci =& get_instance();
	$from_email = SITE_NOTIF_MAIL;
	$sender_name = SITE_NAME;
	//[if mso] is hack for Microsoft Outlook, which does not support css margin and max-width properties
	$x_message = 	'<!--[if mso]>
						<div style="text-align: center">
							<table><tr><td width="650">
					<![endif]-->
					<table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin: auto; max-width: 650px; border: 2px solid #f2f2f2; padding: 15px 50px;">
						<tr>
							<td>' . email_header($subject) . $message. email_footer() . '</td>
						</tr>
					</table>
					<!--[if mso]>
							</td></tr></table>
						</div>
					<![endif]-->'; 
	$ci->email->from($from_email, $sender_name); 
	$ci->email->to($email);
	$ci->email->attach($attachment);
	$ci->email->subject($subject); 
	$ci->email->message($x_message);
	return @$ci->email->send();
}