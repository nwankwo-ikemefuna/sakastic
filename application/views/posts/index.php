<div id="post_section">
    <?php post_avatar("What's funny?", 'h5'); ?>
    <?php
    $ajax_page = 'add';
    $type = 'post';
    $smt_id = 'add_post';
    require 'adit.php'; ?>
</div>

<div class="row m-t-20 m-b-20">
    <div class="<?php echo grid_col(12, '', 8); ?> p-b-10">
        <div class="input-group search_btn">
            <span class="input-group-prepend">
                <button type="button" id="cancel_search" class="btn " title="Cancel search"><i class="fa fa-remove"></i></button>
            </span>
            <input type="text" name="search" class="form-control" value="<?php echo urldecode(xget('search')); ?>" placeholder="what are you looking for?">
            <span class="input-group-append">
                <button type="button" id="search_posts" class="btn" title="Search posts"><i class="fa fa-search"></i></button>
            </span>
        </div>
    </div>
    <div class="<?php echo grid_col(12, '', 4); ?> p-b-10">
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text">Sort by:</span>
            </div>
            <?php
            $options = ['newest' => 'Newest First', 'oldest' => 'Oldest First', 'voted' => 'Most Upvoted', 'popular' => 'Most Commented'];
            xform_select('sort_by', 'newest', false, ['options' => $options, 'blank' => false]); ?>
        </div>
    </div>
</div>

<input type="hidden" id="user_posts" value="<?php echo xget('user_posts'); ?>">

<h6 id="posts_info" style="display: none;"></h6>
<div id="posts">
    <!-- Render posts async -->
</div>
<div id="pagination" class="pagination-area m-b-20"></div>
