jQuery(document).ready(function ($) {
    "use strict"; 

    var post_widget = function(row) {
      var post_url = base_url+'posts/view/'+row.id;
      return `
      <div class="card">
        <div class="card-header post_info">
          <span><i class="fa fa-user"></i> 
            ${ $('#user_posts').val().length == 0 ? 
              `<a class="clickable" href="${base_url}?user_posts=${row.username}">${row.username}</a>` : row.username }
            </span>
          <span><i class="fa fa-clock-o"></i> ${format_date(row.date_created)}</span>
          <span><i class="fa fa-thumbs-up"></i> ${row.votes}</span>
          <span><i class="fa fa-comments"></i> ${row.comment_count}</span>
        </div>
        <div class="card-body">
          <div class="more_posts">${row.content}</div>
          <div class="post_extra m-t-20">
            ${row.voted == 1 ? '<small class="d-block">You upvoted this.</small>' : ''}
            <span class="extra"><a class="comments"><i class="fa fa-comments"></i> Comments</a></span>` + 
            (row.voted == 1 ?
              `<span class="extra"><a class="vote" data-vtype="post" data-id="${row.id}"><i class="fa fa-thumbs-down"></i> Downvote</a></span>` : 
              `<span class="extra"><a class="vote" data-vtype="post" data-id="${row.id}"><i class="fa fa-thumbs-up"></i> Upvote</a></span>`
            ) + 
            (row.is_user_post == 1 ?
              `<span class="extra"><a class="edit_post" data-vtype="post" data-id="${row.id}"><i class="fa fa-edit"></i> Edit</a></span>
              <span class="extra"><a class="delete_post" data-vtype="post" data-id="${row.id}"><i class="fa fa-trash"></i> Delete</a></span>` : 
              ''
            ) +
            `<span class="social_share">
              <a class="share_post clickable"><i class="fa fa-share"></i> Share</a>
              <span class="social_btns d-none">:
                <a class="bg-twitter" href="https://twitter.com/share?url=${post_url}&text=${truncate_str(row.content)}" target="_blank" title="tweet this"><i class="fa fa-twitter"></i></a>
                <a class="bg-facebook" href="https://www.facebook.com/sharer/sharer.php?u=${post_url}" target="_blank" title="share on your timeline"><i class="fa fa-facebook"></i></a>
                <a class="bg-whatsapp" href="whatsapp://send?text=${truncate_str(row.content)} ${post_url}" data-action="share/whatsapp/share" target="_blank" title="share on WhatsApp"><i class="fa fa-whatsapp"></i></a>
              </span>
            </span>
          </div>
        </div>
      </div>`;
    }

    //object to hold our post data
    var post_data = {
      search: '',
      sort_by: '',
      user: ''
    },
    post_container = function(row) {
      return `<div class="post_container m-b-15" id="post_con_${row.id}">
          ${post_widget(row)}</div>`;
    },
    posts_succ_callbk = function(jres) {
      //viewing user posts?
      var user = $('#user_posts').val();
      if (user.length) {
        var found = jres.body.msg.total_rows;
        $('#posts_info').show().html(`${user == username ? 'My' : (user+"'s")} posts (${jres.body.msg.total_rows_formatted})`);
      } else {
        $('#posts_info').hide();
      }
      //more or less
      more_less('more_posts', 'theme_link_red underline-link');
    },
    posts_url = 'api/posts/list',
    elem = 'posts',
    pagination = 'pagination';
    //params from get
    post_data.user = $('#user_posts').val();
    post_data.search = $('[name="search"]').val();
    if (current_page == 'home') {
      paginate_data(posts_url, elem, post_container, pagination, 0, post_data, posts_succ_callbk, null, true, 'Fetching posts');
    }
    ci_paginate(posts_url, elem, post_container, pagination, post_data, elem, posts_succ_callbk);

    //default posts callback
    var default_callback = function(jres, toast_title, toast_success) {
      if (jres.status) {
        show_toast(toast_title, toast_success, 'success');
        paginate_data(posts_url, elem, post_container, pagination, 0, post_data, posts_succ_callbk, null, true, 'Loading');
        more_less('more_posts', 'theme_link_red underline-link');
      } else {
        show_toast(toast_title, jres.error, 'danger');
      }
    }

    //posting
    summernote_init('.post_summernote', {picture: true});
    $(document).on('click', '#quick_post', function() {
      var content = $('[name="content"]').val(),
          smt_images = $('[name="smt_images"]').val();
      if (!content.length || !login_restricted(login_prompt)) return false;
      var callback = function(jres) {
        default_callback(jres, 'Post Notice', 'Posted successfully');
        $('[name="content"]').summernote('reset');
        $('[name="smt_images"]').val('');
      }
      post_data_ajax(base_url+'api/posts/add', {content, smt_images}, false, callback, null, true);
    });

    //deleting
    $(document).on('click', '.delete_post', function() {
      if (!login_restricted(login_prompt)) return false;
      if (!confirm('Sure to delete this post?')) return false;
      var callback = function(jres) {
        default_callback(jres, 'Delete Notice', 'Post deleted successfully');
      }
      var type = $(this).data('vtype'),
          id = $(this).data('id');
      post_data_ajax(base_url+'api/posts/delete', {type, id}, false, callback, null, true);
    });

    //searching
    $(document).on('click', '#search_posts', function() {
      var search = $('[name="search"]').val();
      if (search.length) {
        post_data.search = search;
        paginate_data(posts_url, elem, post_container, pagination, 0, post_data, posts_succ_callbk, null, true, 'Running search');
      }
    });
    //cancel
    $(document).on('click', '#cancel_search', function() {
      var search = $('[name="search"]').val();
      if (search.length) {
        $('[name="search"]').val('');
        post_data.search = '';
        paginate_data(posts_url, elem, post_container, pagination, 0, post_data, posts_succ_callbk, null, true, 'Applying changes');
      }
    });
    //sorting
    $(document).on('change', '[name="sort_by"]', function() {
      var sort_by = $(this).val();
      post_data.sort_by = sort_by;
      paginate_data(posts_url, elem, post_container, pagination, 0, post_data, posts_succ_callbk, null, true, 'Applying sort');
    });

    var do_post_action = function(url, data, container, toast_title = 'Notice') {
      var callback = function(jres) {
        if (jres.status) {
          show_toast(toast_title, '', 'success');
          var row = jres.body.msg;
          //re-render updated post
          $('#'+container).html(post_widget(row));
          more_less('more_posts', 'theme_link_red underline-link');
        } else {
          show_toast(toast_title, jres.error, 'danger');
        }
      } 
      post_data_ajax(base_url+url, data, true, callback, null, true);
    }

    //voting
    $(document).on('click', '.vote', function() {
      if (!login_restricted(login_prompt)) return false;
      var type = $(this).data('vtype'),
          id = $(this).data('id'),
          container = $(this).closest('.post_container').attr('id');
      console.log('container', container);
      do_post_action('api/posts/vote', {type, id}, container, 'Vote Notice');
    });

    //reveal share butts
    $(document).on('click', '.share_post', function() {
      var social_share = $(this).closest('.social_share').find('.social_btns');
      if (social_share.hasClass('d-none')) {
        social_share.removeClass('d-none');
      } else {
        social_share.addClass('d-none');
      }
    });

});