jQuery(document).ready(function ($) {
  "use strict"; 

  /* ================ Posts =============== */

  //object to hold post data
  var post_data = {
    search: '',
    sort_by: 'newest',
    user: '',
    type: '',
    id: ''
  },
  post_container = function(row, config = {}) {
    return `<div class="post_container m-b-15" id="post_con_${row.id}">
        ${post_widget(row, 'post', config)}</div>`;
  },
  posts_callback = function(jres) {
    //viewing user posts?
    var count = jres.body.msg.total_rows_formatted,
        user = $('#user_posts').val(),
        type = $('#type').val(),
        search = $('#search_post_field').val();
    if (user.length) {
      //user posts
      $('#posts_info').show().html(`${user == username ? 'My' : (user+"'s")} posts (${count})`);
    } else if (search.length) {
      //searching
      $('#posts_info').show().html(`Search results for <em class="text-primary">${search}</em> (${count})`);
    } else {
      if (type.length) {
        var types = {recent: 'Recent posts', trending: 'Posts trending this week', followed: 'Posts I\'m following'};
        //no point showing count on recent posts
        if (type != 'recent') {
          $('#posts_info').show().html(`${types[type]} (${count})`);
        }
      } else {
        $('#posts_info').hide();
      }
    } 
  },
  posts_url = 'api/posts/list',
  posts_elem = 'posts',
  posts_pagination = 'posts_pagination';
  //params from get
  post_data.user = $('#user_posts').val();
  post_data.type = $('#type').val();
  post_data.search = $('#search_post_field').val();
  if (['home', 'dash'].includes(current_page)) {
    paginate_data(posts_url, posts_elem, post_container, posts_pagination, 0, post_data, posts_callback, null, true, 'Fetching posts');
  }
  ci_paginate(posts_url, posts_elem, post_container, posts_pagination, post_data, posts_elem, posts_callback);

  //sponsored posts
  if (['home', 'posts', 'post_view'].includes(current_page)) {
    custom_post_request('api/posts/sponsored', 'sponsored_posts', {}, (current_page == 'post_view'));
  }

  function custom_post_request(url, container, data = {}, hide_actions = false) {
    var callback = function(jres) {
      if (jres.status) {
        var rows = jres.body.msg;
        if (!$.isEmptyObject(rows)) {
          var html = "";
          $.each(rows, (i, row) => {
            html += post_container(row, {hide_actions: hide_actions});
          });
          $('#'+container).show().html(html);
        }
      } 
    };
    fetch_data_ajax(base_url+url, data, 'POST', callback);
  }

  //post view
  if (current_page == 'post_view') {
    var id = $('#id').val(),
        is_post_view = $('#is_post_view').val(),
        post_view_callback = function(jres) {
          if (jres.status) {
            var row = jres.body.msg;
            var post_container = `<div class="post_container m-b-15" id="post_con_${id}">${post_widget(row, 'post', {hide_view: true})}</div>`;
            $('#post_view').html(post_container);
            //load comments by triggering the click event
            //exclude sponsored posts
            $('#post_view .comment_replies').trigger('click');
          } else {
            $('#post_view').html('<h6 class="text-danger">Something went wrong. Please refresh this page.</h6>');
          }
        };
    fetch_data_ajax(base_url+'api/posts/view', {id, is_post_view}, 'POST', post_view_callback);
  }

  //post action callback
  var hard_post_action = function(jres, toast_title, toast_success, redirect = false) {
    if (jres.status) {
      show_toast(toast_title, toast_success, 'success');
      if (redirect) {
        setTimeout(function(){
          location.href = base_url+'?user_posts='+username;
        }, 3000);
      }
      paginate_data(posts_url, posts_elem, post_container, posts_pagination, 0, post_data, posts_callback, null, true, 'Loading');
    } else {
      show_toast(toast_title, jres.error, 'danger');
    }
  }

  //posting
  $(document).on("submit", ".add_post_form.post", function(e) {
    e.preventDefault();
    var container = $('#post_section');
    add_post_action(this, container, hard_post_action, 'Post Notice', 'Posted successfully');
  });

  //following
  $(document).on('click', '.follow_post', function() {
    var id = $(this).data('id'),
        container = $(this).closest('.post_container').attr('id'),
        controller = type+'s',
        is_post_view = $('#is_post_view').val();
    soft_post_action('post', 'api/posts/follow', {id, is_post_view}, container, false, false, false, false, 'Follow Notice');
  });

  //deleting
  $(document).on('click', '.delete_post.post', function() {
    //if post view, redirect to user posts on success
    var redirect = ($('#is_post_view').val() == 1);
    delete_post_action(this, hard_post_action, 'Delete Notice', 'Successful', redirect);
  });

  //searching
  $(document).on('click', '#search_posts', function() {
    var search = $('#search_post_field').val();
    if (search.length) {
      post_data.search = search;
      paginate_data(posts_url, posts_elem, post_container, posts_pagination, 0, post_data, posts_callback, null, true, 'Running search');
    }
  });

  //cancel
  $(document).on('click', '#cancel_search', function() {
    var search = $('#search_post_field').val();
    if (search.length) {
      $('#search_post_field').val('');
      post_data.search = '';
      paginate_data(posts_url, posts_elem, post_container, posts_pagination, 0, post_data, posts_callback, null, true, 'Applying changes');
    }
  });

  //sorting
  $(document).on('change', '#sort_posts', function() {
    var sort_by = $(this).val();
    post_data.sort_by = sort_by;
    paginate_data(posts_url, posts_elem, post_container, posts_pagination, 0, post_data, posts_callback, null, true, 'Applying sort');
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


  /* ================ Comments & Replies =============== */

  //load comments and replies
  $(document).on('click', '.comment_replies', function() {
    var type = $(this).data('type'),
        post_id = $(this).data('post_id'),
        comment_id = $(this).data('comment_id'),
        pc_id = post_id+'_'+comment_id,
        container = type+'_action_container_'+pc_id,
        sort_by = 'oldest',
        comment_data = {post_id, comment_id, sort_by};
    $('#'+container).load(base_url+'comments/list_ajax/'+post_id+'/'+comment_id, function() {
      var comment_container = function(row) {
          return `<div class="post_container m-b-15" id="comment_con_${row.comment_id}">
              ${post_widget(row, 'comment')}</div>`;
        },
        comment_sort_options = function(current_val) {
          var sorts = {
              newest: 'Newest first',
              oldest: 'Oldest first',
              voted: 'Most upvoted',
              popular: 'Most replied'
            },
            options = "", selected = "";
          Object.keys(sorts).map(function(value) {
            selected = (value == current_val)  ? 'selected' : '';
            options += `<option ${selected} value="${value}">${sorts[value]}</option>`;
          });
          return options;
        },
        comments_callback = function(jres) {
          var count = jres.body.msg.total_rows_formatted,
            info_msg = (type == 'post' ? 'Comments' : 'Replies') + ` (${count})`;
            info_msg += count > 0 ? ` <span class="m-l-10"><i class="fa fa-eye-slash text-secondary"></i> <a class="text-primary text-bold clickable f-s-14" data-toggle="collapse" data-target="#comments_section_${pc_id}">Hide/show</a></span>` : 
            '';
          if (count > 0) {
            $('#sort_comments_group_'+pc_id).show();
            $('#sort_comments_'+pc_id).html(comment_sort_options(comment_data.sort_by));
          }
          $('#comments_info_'+pc_id).html(info_msg);
        },
        comments_url = 'api/comments/list',
        comments_elem = 'comments_'+pc_id,
        comments_pagination = 'comments_pagination_'+pc_id;
      paginate_data(comments_url, comments_elem, comment_container, comments_pagination, 0, comment_data, comments_callback, null, true, `Fetching ${type == 'post' ? 'comments' : 'replies'}`);
      ci_paginate(comments_url, comments_elem, comment_container, comments_pagination, comment_data, comments_elem, comments_callback);

      var hard_comment_action = function(jres, toast_title, toast_success, sort_by_newest = false) {
        if (jres.status) {
          show_toast(toast_title, toast_success, 'success');
          //if called when adding comment, we force sorting to newest first so the user can see their brand new comment
          comment_data.sort_by = sort_by_newest ? 'newest' : comment_data.sort_by;
          paginate_data(comments_url, comments_elem, comment_container, comments_pagination, 0, comment_data, comments_callback, null, true, 'Loading');
        } else {
          show_toast(toast_title, jres.error, 'danger');
        }
      }

      //posting
      //off is required to detach (any) previous submit events
      $(".add_post_form.comment").off('submit').on("submit", function(e) {
        e.preventDefault();
        var container = $(this).closest('.post_container');
        add_post_action(this, container, hard_comment_action, 'Comment Notice', 'Comment posted successfully');
      });

      //deleting
      $(document).on('click', ".delete_post.comment", function(e) {
        //stop bubbling up to ancestors
        e.stopImmediatePropagation();
        delete_post_action(this, hard_comment_action, 'Delete Notice', 'Successful');
      });

      //sorting
      $(document).on('change', '.sort_comments', function(e) {
        //stop bubbling up to ancestors
        e.stopImmediatePropagation();
        post_id = $(this).data('post_id');
        comment_id = $(this).data('comment_id');
        sort_by = $(this).val();
        pc_id = pc_id = post_id+'_'+comment_id;
        comments_elem = 'comments_'+pc_id;
        comments_pagination = 'comments_pagination_'+pc_id;
        comment_data = {post_id, comment_id, sort_by};
        paginate_data(comments_url, comments_elem, comment_container, comments_pagination, 0, comment_data, comments_callback, null, true, 'Loading');   
      });

    });
  });


  /* ================ Common =============== */

  //post actions: will re-render post after some action such as editing and voting, which do not affect post count
  function soft_post_action(type, url, data, container, is_form_data = false, loading = false, scroll_to = false, success_toast = false, toast_title = '') {
    var callback = function(jres) {
      if (jres.status) {
        if (success_toast) {
          show_toast(toast_title, 'Successful', 'success');
        }
        var row = jres.body.msg;
        //re-render updated post
        $('#'+container).html(post_widget(row, type));
        if (scroll_to) {
          find_element('#'+container);
        }
        //if type is post on view page, load comments by triggering the click event
        var is_post_view = $('#is_post_view').val();
        if (is_post_view == 1 && type == 'post') {
          $('#post_view .comment_replies').trigger('click');
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
  summernote_init('.smt_add_post', {picture: true}, {height: 150});
  function add_post_action(obj, container, hard_callback, toast_title, toast_success) {
    var content = container.find('[name="content"]');
    if (content.val().trim() == "" || !user_loggedin(login_prompt)) return false;
    var type = $(obj).data('type'),
        controller = type+'s',
        is_post = (type == 'post');
    var callback = function (jres) {
      if (typeof hard_callback === "function") {
        hard_callback(jres, toast_title, toast_success, !is_post);
      }
      //are we using summernote?
      if (is_post) {
        //clear form fields
        content.summernote('reset');
        container.find('[name="smt_images"]').val('');
      } else {
        content.val('');
      }
    }
    var form_data = new FormData(obj);
    post_data_ajax(base_url+'api/'+controller+'/add', form_data, true, callback, null, true);
  }

  //deleting
  function delete_post_action(obj, hard_callback, toast_title, toast_success, redirect = false) {
    if (!user_loggedin(login_prompt)) return false;
    if (!confirm('Sure to delete?')) return false;
    var callback = function(jres) {
      if (typeof hard_callback === "function") {
        hard_callback(jres, toast_title, toast_success, redirect);
      }
    }
    var type = $(obj).data('type'),
        id = $(obj).data('id'),
        controller = type+'s';
    post_data_ajax(base_url+'api/'+controller+'/delete', {id}, false, callback, null, true);
  }

  //editing
  $(document).on("submit", ".edit_post_form", function(e) {
    e.preventDefault();
    var content = $(this).closest('.post_container').find('[name="content"]');
    if (content.val().trim() == "" || !user_loggedin(login_prompt)) return false;
    var type = $(this).data('type'),
        controller = type+'s',
        container = $(this).closest('.post_container').attr('id'),
        is_post_view = $('#is_post_view').val(),
        form_data = new FormData(this);
    //appendages
    form_data.append('is_post_view', is_post_view);
    soft_post_action(type, 'api/'+controller+'/edit', form_data, container, true, true, true, true, 'Edit Notice');
  });

  //reloading
  $(document).on('click', '.reload_post', function() {
    var type = $(this).data('type'),
        id = $(this).data('id'),
        container = $(this).closest('.post_container').attr('id'),
        controller = type+'s',
        is_post_view = $('#is_post_view').val();
    soft_post_action(type, 'api/'+controller+'/view', {id, is_post_view}, container);
  });

  //voting
  $(document).on('click', '.vote', function() {
    if (!user_loggedin(login_prompt)) return false;
    var type = $(this).data('type'),
        id = $(this).data('id'),
        container = $(this).closest('.post_container').attr('id'),
        controller = type+'s',
        is_post_view = $('#is_post_view').val();
    soft_post_action(type, 'api/'+controller+'/vote', {id, is_post_view}, container, false, false, false, false, 'Vote Notice');
  });

  //Posts/comments/replies widget
  function post_widget(row, type = 'post', config = {}) {
    var post_url = base_url+'posts/view/'+row.id,
      controller = type+'s',
      pc_id = row.post_id+'_'+row.comment_id,
      user_posts_url = base_url+'?user_posts='+row.username,
      hide_actions = config.hide_actions ? true : false,
      hide_view = config.hide_view ? true : false;
    return `
    <div class="card">
      <div class="card-header post_info card_header_${type}">
        <span>
          <a class="clickable no_deco" href="${base_url}profile/${row.username}">
            <img src="${base_url+row.avatar}" width="28" height="28" class="rounded-circle">
          </a>
          ${ $('#user_posts').val().length == 0 ? 
            `<a class="clickable no_deco" href="${base_url}profile/${row.username}">${row.username}</a>` : row.username }
          <a class="user_stats" href="${user_posts_url}" title="votes"><i class="fa fa-circle text-secondary"></i> ${row.user_votes}</a>
          <a class="user_stats" href="${user_posts_url}" title="posts"><i class="fa fa-circle text-secondary"></i> ${row.user_posts}</a>
          <a class="user_stats" href="${user_posts_url}" title="comments"><i class="fa fa-circle text-secondary"></i> ${row.user_comments}</a>
        </span>
        <span><i class="fa fa-clock-o"></i> ${$.timeago(row.date_created)}</span>
        ${row.sponsored == 1 ? `<small class="text-muted float-sm-right d-inline-block">Sponsored</small>` : ''}
      </div>
      <div class="card-body">
        <div id="${type}_view_container_${row.id}" class="post_content">` + 
          (row.truncated ? 
            `<div>
              ${row.feat_image.length ? 
                `<div class="">
                  <img class="img_thumb float-sm-right" src="${row.feat_image}">
                </div>` : ''}
              ${row.content}
            </div>` + 
            ( ! hide_actions ? 
              `<p class="mt-1">${ajax_page_link(type+'_view_container_'+row.id, controller+'/view_ajax/'+row.id, 'Read more', 'theme_link_red clickable text-bold', '', '', '', '', '', 0)}</p>` : ''
            ) : 
            `<div>${row.content}</div>`
          ) +
        `</div>
        <div class="post_extra m-t-20">
          ${row.voted == 1 ? '<small class="d-none text-muted">You upvoted this.</small>' : ''}
          ` + 
          (type == 'post' && ! hide_view ?
            `<span class="extra"><a class="view_link" href="${base_url}posts/view/${row.id}"><i class="fa fa-external-link"></i> View</a></span>` : ''
          ) +
          ( ! hide_actions ? 
            `<span class="extra"><a class="comment_replies" data-type="${type}" data-post_id="${row.post_id}" data-comment_id="${row.comment_id}"><i class="fa fa-comments"></i> ${type == 'post' ? 'Comments' : 'Replies'}</a>
              <i class="fa fa-circle text-secondary d_icon"></i> ${row.comment_count}
            </span>
            <span class="extra">` + 
              (row.is_user_post == 0 ?
                `<a class="vote" data-type="${type}" data-id="${row.id}"><i class="fa fa-thumbs-${row.voted == 1 ? 'down' : 'o-up'}"></i> ${row.voted == 1 ? 'Downvote' : 'Upvote'}</a>` : `<i class="fa fa-thumbs-up"></i> Upvotes`
              ) + 
              `<i class="fa fa-circle text-secondary d_icon"></i> ${row.votes}
            </span>` +
            (type == 'post' ?
              `<span class="extra"><a class="follow_post" data-id="${row.id}"><i class="fa fa-bookmark${row.followed ? '' : '-o'}"></i> ${row.followed ? 'Unfollow' : 'Follow'}</a></span>` : ''
            ) +
            (row.is_user_post == 1 ?
              `<span class="extra">${ajax_page_link(type+'_action_container_'+pc_id, controller+'/edit_ajax/'+row.id, 'Edit', '', '', 'edit', '', '', '', 0)}</span>
              <span class="extra"><a class="delete_post ${type}" data-type="${type}" data-id="${row.id}"><i class="fa fa-trash"></i> Delete</a></span>` : 
              ''
            ) 
            : ''
          ) +
          (type == 'post' ?
            `<span class="social_share">
              <a class="share_post clickable"><i class="fa fa-refresh"></i> Share</a>
              <span class="social_btns d-none">:
                <a class="bg-twitter" href="https://twitter.com/share?url=${post_url}&text=${truncate_str(row.content)}" target="_blank" title="tweet this"><i class="fa fa-twitter"></i></a>
                <a class="bg-facebook" href="https://www.facebook.com/sharer/sharer.php?u=${post_url}" target="_blank" title="share on your timeline"><i class="fa fa-facebook"></i></a>
                <a class="bg-whatsapp" href="whatsapp://send?text=${truncate_str(row.content)} ${post_url}" data-action="share/whatsapp/share" target="_blank" title="share on WhatsApp"><i class="fa fa-whatsapp"></i></a>
              </span>
            </span>` : ''
          ) +
        `</div>
        <div id="${type}_action_container_${pc_id}"></div>
      </div>
    </div>`;
  }



  /* ================ Sidebar ================== */

  if (['home', 'posts', 'post_view'].includes(current_page)) {
    //trending posts
    setTimeout(function() {
      sidebar_card_widget('sb_trending_posts', 'api/posts/trending', {sidebar: 1});
    }, 2000);
    //recent posts
    setTimeout(function() {
      sidebar_card_widget('sb_recent_posts', 'api/posts/recent', {sidebar: 1});
    }, 4000);
    //followed posts
    setTimeout(function() {
      sidebar_card_widget('sb_followed_posts', 'api/posts/followed', {sidebar: 1});
    }, 6000);
  }

  function sidebar_card_widget(container, url, data = {}) {
    var callback = function(jres) {
      if (jres.status) {
        var rows = jres.body.msg;
        if (!$.isEmptyObject(rows)) {
          var html = "";
          $.each(rows, (i, row) => {
            html += `
            <div class="sb_post_item">
              <a href="${base_url+'posts/view/'+row.id}" class="no_deco content">${row.content}</a>
              <div class="sb_post_info">
                <small>
                  <i class="fa fa-user"></i>
                  <a class="clickable no_deco" href="${base_url}profile/${row.username}">${row.username}</a>
                </small>
                <small><i class="fa fa-comments"></i> ${row.comment_count}</small>
                <small><i class="fa fa-thumbs-up"></i> ${row.votes}</small>
                <small><i class="fa fa-clock-o"></i> ${$.timeago(row.date_created)}</small>
              </div>
            </div>`;
          });
          $('#'+container).closest('.card').show();
          $('#'+container).html(html);
        }
      } 
    };
    fetch_data_ajax(base_url+url, data, 'POST', callback);
  }

});