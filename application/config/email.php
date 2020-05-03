<?php 
defined('BASEPATH') OR die('No direct script access allowed');

//if using gmail, access to less secure apps must be turned ON from the email account for this to work
$config['useragent'] = 'CodeIgniter';
$config['protocol'] = 'smtp';
$config['mailpath'] = '/usr/sbin/sendmail';
$config['smtp_host'] = 'mail.sakastic.com';
$config['smtp_user'] = 'notify@sakastic.com';
$config['smtp_pass'] = '$UKYjZlrWG%M';
$config['smtp_port'] = 587; //non SSL, 465 SSL
$config['smtp_timeout'] = 30;
$config['wordwrap'] = TRUE;
$config['wrapchars'] = 76;
$config['mailtype'] = 'html';
$config['charset'] = 'utf-8';
$config['validate'] = FALSE;
$config['priority'] = 3;
$config['crlf'] = "\r\n";
$config['newline'] = "\r\n";
$config['bcc_batch_mode'] = FALSE;
$config['bcc_batch_size'] = 200;
?>