jQuery(document).ready(function ($) {
  "use strict"; 

  /* ================ Posts =============== */

  //object to hold our post data
  var post_data = {
    search: '',
    sort_by: '',
    user: ''
  },
  post_container = function(row) {
    return `<div class="post_container m-b-15" id="post_con_${row.id}">
        ${post_widget(row, 'post')}</div>`;
  },
  posts_callback = function(jres) {
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
    paginate_data(posts_url, elem, post_container, pagination, 0, post_data, posts_callback, null, true, 'Fetching posts');
  }
  ci_paginate(posts_url, elem, post_container, pagination, post_data, elem, posts_callback);

  //post action callback
  var hard_post_action = function(jres, toast_title, toast_success) {
    if (jres.status) {
      show_toast(toast_title, toast_success, 'success');
      paginate_data(posts_url, elem, post_container, pagination, 0, post_data, posts_callback, null, true, 'Loading');
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

  //deleting
  $(document).on('click', '.delete_post.post', function() {
    delete_post_action(this, hard_post_action, 'Delete Notice', 'Successful');
  });

  //searching
  $(document).on('click', '#search_posts', function() {
    var search = $('[name="search"]').val();
    if (search.length) {
      post_data.search = search;
      paginate_data(posts_url, elem, post_container, pagination, 0, post_data, posts_callback, null, true, 'Running search');
    }
  });

  //cancel
  $(document).on('click', '#cancel_search', function() {
    var search = $('[name="search"]').val();
    if (search.length) {
      $('[name="search"]').val('');
      post_data.search = '';
      paginate_data(posts_url, elem, post_container, pagination, 0, post_data, posts_callback, null, true, 'Applying changes');
    }
  });

  //sorting
  $(document).on('change', '[name="sort_by"]', function() {
    var sort_by = $(this).val();
    post_data.sort_by = sort_by;
    paginate_data(posts_url, elem, post_container, pagination, 0, post_data, posts_callback, null, true, 'Applying sort');
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
        comment_data = {post_id, comment_id, sort_by: ''};
    $('#'+container).load(base_url+'comments/list_ajax/'+post_id+'/'+comment_id, function() {

      //initialize summernote
      summernote_init('.smt_add_comment_'+pc_id, {picture: true}, {height: 100});

      var comment_sort_radio = function(label, val, is_default = false, is_first = false) {
        var checked = (comment_data.sort_by == '' && is_default) || (comment_data.sort_by == val)  ? 'checked' : '';
        return `<span class="form-check form-check-inline ${ ! is_first ? 'm-l-10-n' : ''}">
          <input type="radio" class="form-check-input sort_comments" name="sort_comments_${pc_id}" id="csort_${val}_${pc_id}" value="${val}" ${checked}>
          <label class="form-check-label" for="csort_${val}_${pc_id}" style="font-weight: 400;">${label}</label>
        </span>`;
      }

      var comment_container = function(row) {
          return `<div class="post_container m-b-15" id="comment_con_${row.comment_id}">
              ${post_widget(row, 'comment')}</div>`;
        },
        comments_callback = function(jres) {
          var count = jres.body.msg.total_rows_formatted,
            info_msg = (type == 'post' ? 'Comments' : 'Replies') + ` (${count})`;
            info_msg += count > 0 ? ` <span class="m-l-10"><i class="fa fa-cog text-secondary f-s-11"></i> <a class="text-bold clickable" data-toggle="collapse" data-target="#comments_section_${pc_id}">hide/show</a></span>
              <span class="m-l-10"><i class="fa fa-cog text-secondary f-s-11"></i> sort by:
                ${comment_sort_radio('oldest first', 'oldest', true, true)}
                ${comment_sort_radio('newest first', 'newest')}
                ${comment_sort_radio('most upvoted', 'voted')}
                ${comment_sort_radio('most replied', 'popular')}
              </span>` : 
            '';
          $('#comments_info_'+pc_id).html(info_msg);
        },
        comments_url = 'api/comments/list',
        comments_elem = 'comments_'+pc_id,
        comments_pagination = 'comments_pagination_'+pc_id;
      paginate_data(comments_url, comments_elem, comment_container, comments_pagination, 0, comment_data, comments_callback, null, true, `Fetching ${type == 'post' ? 'comments' : 'replies'}`);
      ci_paginate(comments_url, comments_elem, comment_container, comments_pagination, comment_data, comments_elem, comments_callback);

      var hard_comment_action = function(jres, toast_title, toast_success) {
        if (jres.status) {
          show_toast(toast_title, toast_success, 'success');
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
        var sort_by = $(this).val();
        comment_data.sort_by = sort_by;
        paginate_data(comments_url, comments_elem, comment_container, comments_pagination, 0, comment_data, comments_callback, null, true, 'Loading');   
      });

    });
  });


  /* ================ Common =============== */

  //post actions: will re-render post after some action such as editing and voting, which do not affect post count
  var soft_post_action = function(type, url, data, container, is_form_data = false, loading = false, scroll_to = false, success_toast = false, toast_title = '') {
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
        controller = type+'s';
    var callback = function (jres) {
      if (typeof hard_callback === "function") {
        hard_callback(jres, toast_title, toast_success);
      }
      //clear form fields
      content.summernote('reset');
      container.find('[name="smt_images"]').val('');
    }
    var form_data = new FormData(obj);
    post_data_ajax(base_url+'api/'+controller+'/add', form_data, true, callback, null, true);
  }

  //deleting
  function delete_post_action(obj, hard_callback, toast_title, toast_success) {
    if (!user_loggedin(login_prompt)) return false;
    if (!confirm('Sure to delete?')) return false;
    var callback = function(jres) {
      if (typeof hard_callback === "function") {
        hard_callback(jres, toast_title, toast_success);
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
        form_data = new FormData(this);
    soft_post_action(type, 'api/'+controller+'/edit', form_data, container, true, true, true, true, 'Edit Notice');
  });

  //reloading
  $(document).on('click', '.reload_post', function() {
    var type = $(this).data('type'),
        id = $(this).data('id'),
        container = $(this).closest('.post_container').attr('id'),
        controller = type+'s';
    soft_post_action(type, 'api/'+controller+'/view', {id}, container);
  });

  //voting
  $(document).on('click', '.vote', function() {
    if (!user_loggedin(login_prompt)) return false;
    var type = $(this).data('type'),
        id = $(this).data('id'),
        container = $(this).closest('.post_container').attr('id'),
        controller = type+'s';
    soft_post_action(type, 'api/'+controller+'/vote', {id}, container, false, false, false, false, 'Vote Notice');
  });

  //Posts/comments/replies widget
  function post_widget(row, type = 'post') {
    var post_url = base_url+'posts/view/'+row.id,
      controller = type+'s',
      pc_id = row.post_id+'_'+row.comment_id;
    return `
    <div class="card">
      <div class="card-header post_info card_header_${type}">
        <span>
          <img src="${row.avatar}" width="28" height="28" class="rounded-circle">
          ${ $('#user_posts').val().length == 0 ? 
            `<a class="clickable" href="${base_url}?user_posts=${row.username}">${row.username}</a>` : row.username }
          <a class="user_stats" title="votes"><i class="fa fa-circle text-secondary"></i> ${row.user_votes}</a>
          <a class="user_stats" title="posts"><i class="fa fa-circle text-secondary"></i> ${row.user_posts}</a>
          <a class="user_stats" title="comments"><i class="fa fa-circle text-secondary"></i> ${row.user_comments}</a>
        </span>
        <span><i class="fa fa-clock-o"></i> ${$.timeago(row.date_created)}</span>
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
            </div>
            <p class="mt-1">${ajax_page_link(type+'_view_container_'+row.id, controller+'/view_ajax/'+row.id, 'Read more', 'theme_link_red clickable text-bold', '', '', '', '', '', 0)}</p>` : 
            `<div>${row.content}</div>`
          ) +
        `</div>
        <div class="post_extra m-t-20">
          ${row.voted == 1 ? '<small class="d-block text-info">You upvoted this.</small>' : ''}
          <span class="extra"><a class="comment_replies" data-type="${type}" data-post_id="${row.post_id}" data-comment_id="${row.comment_id}"><i class="fa fa-comments"></i> ${type == 'post' ? 'Comments' : 'Replies'}</a>
            <i class="fa fa-circle text-secondary d_icon"></i> ${row.comment_count}
          </span>
          <span class="extra">` + 
            (row.is_user_post == 0 ?
              `<a class="vote" data-type="${type}" data-id="${row.id}"><i class="fa fa-thumbs-${row.voted == 1 ? 'down text-warning' : 'up text-success'}"></i> ${row.voted == 1 ? 'Downvote' : 'Upvote'}</a>` : `<i class="fa fa-thumbs-up"></i> Upvotes`
            ) + 
            `<i class="fa fa-circle text-secondary d_icon"></i> ${row.votes}
          </span>` +
          (row.is_user_post == 1 ?
            `<span class="extra">${ajax_page_link(type+'_action_container_'+pc_id, controller+'/edit_ajax/'+row.id, 'Edit', '', '', 'edit', '', '', '', 0)}</span>
            <span class="extra"><a class="delete_post ${type}" data-type="${type}" data-id="${row.id}"><i class="fa fa-trash"></i> Delete</a></span>` : 
            ''
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

});