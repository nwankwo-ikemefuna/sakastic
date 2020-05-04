jQuery(document).ready(function ($) {
    "use strict"; 

    var post_widget = function(row) {
      var post_url = base_url+'posts/view/'+row.id;
      return `
      <div class="card">
        <div class="card-header post_info">
          <span>
            <img src="${row.avatar}" width="35" height="35" class="rounded-circle">
            ${ $('#user_posts').val().length == 0 ? 
              `<a class="clickable" href="${base_url}?user_posts=${row.username}">${row.username}</a>` : row.username }
            <a class="user_stats" title="votes"><i class="fa fa-circle text-secondary"></i> ${row.user_votes}</a>
            <a class="user_stats" title="posts"><i class="fa fa-circle text-secondary"></i> ${row.user_posts}</a>
            <a class="user_stats" title="comments"><i class="fa fa-circle text-secondary"></i> ${row.user_comments}</a>
          </span>
          <span><i class="fa fa-clock-o"></i> ${$.timeago(row.date_created)}</span>
          <span><i class="fa fa-thumbs-up"></i> ${row.votes}</span>
          <span><i class="fa fa-comments"></i> ${row.comment_count}</span>
        </div>
        <div class="card-body">
          <div id="post_view_container_${row.id}">` + 
            (row.truncated ? 
              `<div class="post_content">
                ${row.feat_image.length ? 
                  `<div class="feat_image"><img src="${row.feat_image}"></div>` : ''
                }
                ${row.content}
              </div>
              <p class="mt-1">${ajax_page_link('post_view_container_'+row.id, 'posts/view_ajax/'+row.id, 'Read more', 'theme_link_red clickable text-bold', '', '', '', '', '', 0)}</p>` : 
              `<div class="post_content">${row.content}</div>`
            ) +
          `</div>
          <div class="post_extra m-t-20">
            ${row.voted == 1 ? '<small class="d-block text-info">You upvoted this.</small>' : ''}
            <span class="extra">${ajax_page_link('post_action_container_'+row.id, 'comments/list_ajax/'+row.id, 'Comments', '', '', 'comments', '', '', '', 0)}</span>` + 
            (row.is_user_post == 0 ?
              `<span class="extra"><a class="vote" data-type="post" data-id="${row.id}"><i class="fa fa-thumbs-${row.voted == 1 ? 'down text-warning' : 'up text-success'}"></i> ${row.voted == 1 ? 'Downvote' : 'Upvote'}</a></span>` : ''
            ) + 
            (row.is_user_post == 1 ?
              `<span class="extra">${ajax_page_link('post_action_container_'+row.id, 'posts/edit_ajax/'+row.id, 'Edit', '', '', 'edit', '', '', '', 0)}</span>
              <span class="extra"><a class="delete_post" data-type="post" data-id="${row.id}"><i class="fa fa-trash"></i> Delete</a></span>` : 
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
          <div id="post_action_container_${row.id}"></div>
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
      var count = jres.body.msg.total_rows_formatted;
      var user = $('#user_posts').val();
      var search = $('[name="search"]').val();
      var search_msg = `Search results for <em class="text-primary">${search}</em> (${count})`;
      if (user.length) {
        if (search.length) {
          $('#posts_info').show().html(search_msg);
        } else {
          $('#posts_info').show().html(`${user == username ? 'My' : (user+"'s")} posts (${count})`);
        }
      } else {
        if (search.length) {
          $('#posts_info').show().html(search_msg);
        } else {
          $('#posts_info').hide();
        }
      }
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
      } else {
        show_toast(toast_title, jres.error, 'danger');
      }
    }

    //post actions: will re-render post after some action such as editing and voting, which do not affect post count
    var do_post_action = function(url, data, container, toast_title = '', is_form_data = false, loading = false, scroll_to = true) {
      var callback = function(jres) {
        if (jres.status) {
          if (toast_title.length) {
            show_toast(toast_title, 'Successful', 'success');
          }
          var row = jres.body.msg;
          //re-render updated post
          $('#'+container).html(post_widget(row));
          if (scroll_to) {
            find_element('#'+container);
          }
        } else {
          if (toast_title.length) {
            show_toast(toast_title, jres.error, 'danger');
          }
        }
      } 
      post_data_ajax(base_url+url, data, is_form_data, callback, null, loading);
    }

    //posting
    summernote_init('.post_summernote', {picture: true});
    $(document).on("submit", ".add_post_form", function(e) {
      e.preventDefault();
      if ($('[name="content"]').val().trim() == "" || !user_loggedin(login_prompt)) return false;
      var callback = function (jres) {
        default_callback(jres, 'Post Notice', 'Posted successfully');
        $('[name="content"]').summernote('reset');
        $('[name="smt_images"]').val('');
      }
      var form_data = new FormData(this);
      post_data_ajax(base_url+'api/posts/add', form_data, true, callback, null, true);
    });

    //reload post
    $(document).on('click', '.reload_post', function() {
      var type = $(this).data('type'),
          id = $(this).data('id'),
          container = $(this).closest('.post_container').attr('id');
      do_post_action('api/posts/view', {type, id}, container);
    });

    //edit post
    $(document).on("submit", ".edit_post_form", function(e) {
      e.preventDefault();
      var content = $(this).closest('.post_container').find('[name="content"]');
      if (content.val().trim() == "" || !user_loggedin(login_prompt)) return false;
      var id = $(this).data('id'),
          container = $(this).closest('.post_container').attr('id'),
          form_data = new FormData(this);
      do_post_action('api/posts/edit', form_data, container, 'Edit Notice', true, true);
    });

    //voting
    $(document).on('click', '.vote', function() {
      if (!user_loggedin(login_prompt)) return false;
      var type = $(this).data('type'),
          id = $(this).data('id'),
          container = $(this).closest('.post_container').attr('id');
      do_post_action('api/posts/vote', {type, id}, container, 'Vote Notice', false, false, false);
    });

    //deleting
    $(document).on('click', '.delete_post', function() {
      if (!user_loggedin(login_prompt)) return false;
      if (!confirm('Sure to delete this post?')) return false;
      var callback = function(jres) {
        default_callback(jres, 'Delete Notice', 'Post deleted successfully');
      }
      var type = $(this).data('type'),
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

    //reveal share butts
    $(document).on('click', '.share_post', function() {
      var social_share = $(this).closest('.social_share').find('.social_btns');
      if (social_share.hasClass('d-none')) {
        social_share.removeClass('d-none');
      } else {
        social_share.addClass('d-none');
      }
    });


    /* ============== Comments ============= */
});