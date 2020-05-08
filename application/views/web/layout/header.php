<!DOCTYPE html>
<html lang="en">
    <head>
        <?php echo site_meta($page_title); ?>
        <!-- Bootstrap core CSS -->
        <link href="<?php echo base_url(); ?>vendors/bootstrap/css/bootstrap.min.css" rel="stylesheet">
        <!-- Font Awesome -->
        <link href="<?php echo base_url(); ?>vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">
        <!-- Selectpicker-->
        <link rel="stylesheet" href="<?php echo base_url(); ?>vendors/selectpicker/css/bootstrap-select.min.css" type="text/css">
        <!-- Summernote -->
        <link rel="stylesheet" href="<?php echo base_url(); ?>vendors/summernote/summernote-bs4.css" type="text/css">
        <!-- Toast -->
        <link rel="stylesheet" href="<?php echo base_url(); ?>vendors/toast/toast.min.css" type="text/css">
        <!-- Custom styles -->
        <link href="<?php echo base_url(); ?>assets/common/css/helper.css" rel="stylesheet">
        <link href="<?php echo base_url(); ?>assets/web/css/style.css" rel="stylesheet">
    </head>
    <body>
        <div class="page_body">
            <!-- Navigation -->
            <nav class="navbar navbar-expand-lg fixed-top top_nav">
                <div class="container">
                    <a class="navbar-brand" href="<?php echo base_url(); ?>" title="<?php echo $this->site_name; ?> Home">
                        <img src="<?php echo base_url(SITE_LOGO); ?>">
                    </a>
                    <button class="navbar-toggler custom_nav_toggle" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
                        <i class="fa fa-navicon"></i>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarResponsive">
                        <ul class="navbar-nav ml-auto">
                            <li class="nav-item active">
                                <a class="nav-link" href="<?php echo base_url(); ?>">Home</a>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Posts</a>
                                <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                                    <?php 
                                    if ($this->session->user_loggedin) { ?>
                                        <a href="<?php echo base_url('?user_posts='.$this->session->user_username); ?>" class="dropdown-item">My Posts</a>
                                        <?php 
                                    } ?>
                                    <a href="<?php echo base_url('?type=recent'); ?>" class="dropdown-item">Recent Posts</a>
                                    <a href="<?php echo base_url('?type=trending'); ?>" class="dropdown-item">Trending Posts</a>
                                    <a href="<?php echo base_url('?type=followed'); ?>" class="dropdown-item">Followed Posts</a>
                                    <a href="<?php echo base_url(); ?>" class="dropdown-item">All Posts</a>
                                </div>
                            </li>
                            <?php 
                            if ($this->session->user_loggedin) { ?>
                                <li class="nav-item dropdown profile_dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <img src="<?php echo user_avatar(); ?>" width="32" height="32" class="rounded-circle">
                                    </a>
                                    <div class="dropdown-menu header_dropdown" aria-labelledby="navbarDropdownMenuLink">
                                        <a href="<?php echo base_url('user/profile'); ?>" class="dropdown-item"><i class="fa fa-user-o" aria-hidden="true"></i> Profile</a>
                                        <a href="<?php echo base_url('logout'); ?>" class="dropdown-item"><i class="fa fa-sign-out" aria-hidden="true"></i> Logout</a>
                                    </div>
                                </li>
                                <?php 
                            } else  { ?>
                                <li class="nav-item">
                                    <a class="nav-link" href="<?php echo base_url('login'); ?>">Login</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="<?php echo base_url('register'); ?>">Signup</a>
                                </li>
                                <?php 
                            } ?>
                            <li class="nav-item">
                                <button id="header_post_btn" class="btn theme_button_red text-white m-t-5">Post Something</button>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>

            <div class="container page_container">
                <?php 
                if ($this->show_disclaimer) { ?>
                    <div class="disclaimer hide">
                        <span class="info_icon">
                            <i class="fa fa-info"></i>
                        </span>
                        This platform is just for laughs. It does not contain serious content. If you're looking for serious stuff, check the next one, if you find any.
                    </div>
                    <?php 
                } ?>
                <div class="row">
                    <div class="col-12 <?php if ($this->show_sidebar) echo 'col-md-9'; ?>">
                        <?php if ($this->show_page_title) { ?>
                            <h3><?php echo $page_title; ?></h3>
                        <?php } ?>