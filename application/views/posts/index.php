<div id="post_section">
    <?php post_avatar("What's funny?", 'h5'); ?>
    <?php
    $ajax_page = 'add';
    $type = 'post';
    $smt_id = 'add_post';
    require 'adit.php'; ?>
</div>

<h6 class="m-t-20">Sponsored Posts</h6>
<div id="sponsored_posts" class="m-b-50" style="display: none;">
    <!-- Render posts async -->
</div>

<div class="row m-t-20 m-b-20">
    <div class="<?php echo grid_col(12, '', 8); ?> p-b-10">
        <div class="input-group search_btn">
            <span class="input-group-prepend">
                <button type="button" id="cancel_search" class="btn " title="Cancel search"><i class="fa fa-remove"></i></button>
            </span>
            <input type="text" id="search_post_field" class="form-control" value="<?php echo urldecode(xget('search')); ?>" placeholder="what are you looking for?">
            <span class="input-group-append">
                <button type="button" id="search_posts" class="btn" title="Search posts"><i class="fa fa-search"></i></button>
            </span>
        </div>
    </div>
    <div class="<?php echo grid_col(12, '', 4); ?> p-b-10">
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text">Sort by</span>
            </div>
            <?php
            $options = ['newest' => 'Newest first', 'oldest' => 'Oldest first', 'voted' => 'Most upvoted', 'popular' => 'Most commented'];
            xform_select('', 'newest', false, ['options' => $options, 'blank' => false, 'extra' => ['id' => 'sort_posts']]); ?>
        </div>
    </div>
</div>

<input type="hidden" id="user_posts" value="<?php echo xget('user_posts'); ?>">
<input type="hidden" id="type" value="<?php echo xget('type'); ?>">
<input type="hidden" id="post_view" value="0">

<h6 id="posts_info" style="display: none;"></h6>
<div id="posts">
    Posts loading... <i class="fa fa-spinner fa-spin"></i>
    <!-- Render posts async -->
</div>
<div id="posts_pagination" class="pagination-area m-b-20"></div>
