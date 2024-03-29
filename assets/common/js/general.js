function initializePlugins() {
    $('.selectpicker').selectpicker({
        liveSearch: true,
        virtualScroll: true
    });

    $(".toasty").toast();
}

$(document).ajaxComplete(function() {
    initializePlugins();
});

jQuery(document).ready(function ($) {
    "use strict";  

    initializePlugins();

    //auto-close flashdata alert boxes
    $(".alert-dismissable.auto_dismiss").delay(10000).fadeOut('slow', function() {
        $(this).alert('close');
    });

    var req_attr = $('input.form-control').attr('required');
	if (typeof req_attr !== typeof undefined && req_attr !== false) {
	    $('input.form-control').css('border-color', '#f2f2f2');
	    $(this).focusout(function(){
	    	if ($(this).val() == '') {
	    		$(this).css('border-color', '#eb7374');
	    	} 
	    });
	    $(document).on('input', 'input.form-control', function(){
	    	if ($(this).val() !== '') {
	    		$(this).css('border-color', '#f2f2f2');
	    	} 
	    });
	}

    $(document).on('change', '.file_input', function(){ 
        if (window.File && window.FileReader && window.FileList && window.Blob) //check File API supported browser
        {
            var parent = $(this).closest('.file_preview_area');
            $(parent).find('.file_preview').html(''); //clear html of output element
            var data = $(this)[0].files; //this file data 

            $.each(data, function(i, file){ 
                if(/(\.|\/)(gif|jpe?g|png)$/i.test(file.type)){ //check supported file type
                    var fRead = new FileReader(); //new filereader
                    fRead.onload = (function(file){ //trigger function on successful read
                    return function(e) {
                        var card = 
                        `<div class="card preview_thumb">
                          <img src="${e.target.result}" class="card-img-top" title="${file.name}">
                          <div class="card-body">
                            <p class="card-text hide">${file.name}</p>
                          </div>
                        </div>`;
                        $(parent).find('.file_preview').append(card); //append image to output element
                    };
                    })(file);
                    fRead.readAsDataURL(file); //URL representing the file's data.
                } else {
                    var ext = file.name.split('.').pop();
                    var src = file_preview_src(ext);
                    var card = 
                    `<div class="card preview_thumb">
                      <img src="${src}" class="card-img-top" title="${file.name}">
                      <div class="card-body">
                        <p class="card-text hide">${file.name}</p>
                      </div>
                    </div>`;
                    $(parent).find('.file_preview').append(card); //append image to output element
                }
            });
            
        }else{
            alert("Your browser doesn't support File API!"); //if File API is absent
        }
    });

});

function user_loggedin(false_callback) {
    if (is_loggedin) return true;
    if (typeof false_callback === 'function') {
        false_callback();
    }
    return false;
}

function login_prompt() {
    $('#m_login_prompt').modal('show');
}

function inflect(count, word, affix = 's') {
    return count == 1 ? word : word+affix;
}

function toggle_elem_prop(elem, targets, prop, invert = false) {
    if ($(elem).prop(prop)) {
        invert ? $(targets).hide() : $(targets).show();
    } else {
        invert ? $(targets).show() : $(targets).hide();
    }
}

function toggle_elem_prop_trigger(elem, targets, invert = false, event = 'change') {
    $(document).on(event, elem, function() {
        toggle_elem_prop(elem, targets, 'checked', invert);
    });
}

function toggle_elem_val(elem, targets_show, targets_hide, val) {
    if ($(elem).val() == val) {
        $(targets_show).show();
        $(targets_hide).hide();
    } else {
        $(targets_show).hide();
        $(targets_hide).show();
    }
}

function toggle_elem_val_trigger(elem, targets_show, targets_hide, val, event = 'change') {
    $(document).on(event, elem, function() {
        toggle_elem_val(this, targets_show, targets_hide, val);
    });
}

function url_title(str) {
    return str.toLowerCase().replace(/ /g, '-').replace(/[-]+/g, '-').replace(/[^\w-]+/g, '');
}

function image_exists(url){
    var http = new XMLHttpRequest();
    http.open('HEAD', url, false);
    http.send();
    return http.status != 404;
}

function preview_image(input, preview = '.img_preview') {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            $(preview).attr('src', e.target.result);
        }
        reader.readAsDataURL(input.files[0]); // convert to base64 string
    }
}

function find_element(selector, parent = 'html') {
    var pos = $(selector).position().top;
    $(parent).scrollTop(pos);
}

function rating_stars(rating) {
  var diff = 5 - rating, rated = '', unrated = '';
  //rated
  for (var i = 0; i < rating; i++) {
    rated += '<i class="fa fa-star"></i>'; 
  }
  //unrated
  if (diff > 0) {
      for (var i = 0; i < diff; i++) {
        unrated += '<i class="fa fa-star-o"></i>'; 
      }
  }
  return '<span class="rating">'+rated+unrated+'</span>';
}

function print_color(code, name = '', pos = 'right', icon = 'square') {
    if (code == '') return '';
    var color = '<i class="fa fa-'+icon+'" style="color: '+code+'"></i> ';
    //if name is not set, use only color
    if ( ! name.length) return color;
    color = pos == 'left' ? (color+' '+name) : (name+' '+color);
    return color;
}

function print_colors(codes, names = '', pos = 'left', icon = 'square', $return = 'string') {
    if (codes == null) return '';
    var colors_arr = [];
    //if names is not set, use only colors
    if (names == '') {
        var colors = codes.split(',');
        $.each(colors, function(i, code) {
            colors_arr.push(print_color(code, '', pos, icon));
        });
        return $return == 'array' ? colors_arr : colors_arr.join(' ');
    } 
    //combine array in code => name pairs
    var colors = Object.assign(...codes.split(',').map((k, i) => ({[k]: names.split(',')[i]})));
    console.log(colors);
    $.each(colors, function(code, name) {
        colors_arr.push(print_color(code, name, pos, icon));
    });
    return $return == 'array' ? colors_arr : colors_arr.join(', ');
}

function range_slider(id, min = 0, max = 100, val_min = 100, val_max = 1000) {
    $('#'+id).slider({
        range: true,
        min: min,
        max: max,
        values: [val_min, val_max],
        slide: function(e, ui) {
            $(this).closest('.slider-range').find('.price_min').val(ui.values[0]);
            $(this).closest('.slider-range').find('.price_max').val(ui.values[1]);
        }
  });
}

function format_date(date, type = 'both') {
    const d = new Date(date);
    const year = d.toLocaleString('en', { year: '2-digit' });
    const month = d.toLocaleString('en', { month: 'short' });
    const day = d.toLocaleString('en', { day: '2-digit' });
    const time = d.toLocaleString('en', { hour: 'numeric', minute: 'numeric', hour12: true });
    const full_date = `${day} ${month} '${year}`;
    switch (type) {
        case 'date':
            return full_date;
        case 'time':
            return time;
        default: 
            return `${full_date} ${time}`; 
    }
}

function count_words(str) {
    return str.trim().split(/\s+/).length;
}

function truncate_str(str, max = 100, separator = ' ') {
    if (str <= max) return str;
    return str.substr(0, str.lastIndexOf(separator, max));
}

function more_less(elem, link_class = '') {
    //Expand/collapse long posts/comments 
    $('.'+elem).showMore({
        minheight: 120,
        buttontxtmore: `<a class="m-t-10 clickable ${link_class}">Read more</a>`,
        buttontxtless: `<a class="m-t-10 clickable ${link_class}">Read less</a>`,
        animationspeed: 250
    });
}

function show_toast(title, msg = 'All done!', type = 'info', container ='body', delay = 5, subtitle = '') {
    $.toast({
        container: $(container),
        title: title,
        subtitle: subtitle,
        content: msg,
        type: type,
        delay: delay*1000,
        pause_on_hover: true
    });
}

function ajax_page_link(container, url, html = '', x_class = '', title = '', icon = '', attrs = '', id = '', callback = '', loading = 1, loading_text = '') { 
    var id_attr = id.length ? `id=${id}` : '';
    var html = (icon.length ? `<i class="fa fa-${icon}"></i> ` : '') + html;
    var btn = `<a 
        data-container='${container}' 
        data-url='${url}'
        class='tload_ajax ${x_class}' 
        data-callback='${callback}' 
        data-loading='${loading}' 
        data-loading_text='${loading_text}'
        title='${title}'
        ${id_attr} ${attrs}>
        ${html}
    </a>`;
    return btn;
}

function ajax_page_button(container, url, html = '', x_class = '', title = '', icon = '', attrs = '', id = '', callback = '', loading = 1, loading_text = '') { 
    var id_attr = id.length ? `id=${id}` : '';
    var html = (icon.length ? `<i class="fa fa-${icon}"></i> ` : '') + html;
    var btn = `<button 
        data-container='${container}' 
        data-url='${url}'
        class='tload_ajax btn ${x_class}' 
        data-callback='${callback}' 
        data-loading='${loading}' 
        data-loading_text='${loading_text}'
        title='${title}'
        ${id_attr} ${attrs}>
        ${html}
    </button>`;
    return btn;
}

function status_box(elem, msg, type = 'success', elem_type = 'class', delay = 10000) {
    var status_div = 
    `<div class="alert alert-${type} alert-dismissible" role="alert">
        ${msg}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close" title="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>`;
    elem = elem_type == 'id' ? ('#'+elem) : ('.'+elem);
    $(elem).html(status_div)
        .fadeIn( 'fast' )
        .delay( delay )
        .fadeOut( 'slow' );
}

function check_loading(start, after, html) {
    var now = new Date();
    var time_diff = (now - start) / 1000; //in ms
    var time_secs = Math.round(time_diff);
    if (time_secs > after) {
        $('#ajax_overlay_loader #overlay_text').html(html);
    }
}

function ajax_loading_show(show, loading_msg) {
    if (show) {
        //any other one open?
        var start;
        if ($('#ajax_overlay_loader').is(':visible') == false) {
            start = new Date();
            $('#ajax_overlay_loader #overlay_text').text(loading_msg+'...');
            $('#ajax_overlay_loader').show();
        }
        /*if ($('#ajax_overlay_loader').is(':visible') == true) {
            //stayed for 5+ seconds?
            check_loading(start, 5, 'Still trying to complete errand. Please wait');
            //for 10+ seconds?
            check_loading(start, 10, loading_msg+'...Please wait');
            //for 15+ seconds? may day!
            var final_check = `
                Unable to complete request. <br />
                <button class="btn btn-secondary btn-sm" onclick="${ajax_loading_hide()}">Close</button>
            `;
            check_loading(start, 15, final_check);
        }*/
    }
}

function ajax_loading_hide() {
    if ($('#ajax_overlay_loader').is(':visible') == true) {
        $('#ajax_overlay_loader').hide();
        $('#ajax_overlay_loader #overlay_text').text('');
    }
}

function secure_req_data (data, is_form_data) {
    // append csrf token to data for journey to server
    var csrf_hash = $('.'+csrf_token_name).val() || '';
    if ( ! csrf_hash.length) {
        //log for debugging
        console.error('CSRF: CSRF token not set!');
    }
    data = is_form_data ? data : {...data, ...{[csrf_token_name]: csrf_hash}};
    return data;
}

function update_csrf_token(jres) {
    //update csrf token against next request is regeneration is required for every new request
    var curr_csrf_hash = $('.'+csrf_token_name).val();
    var csrf_hash = jres[csrf_token_name] || '';
    // console.log('New token:', csrf_hash);
    if ( ! csrf_hash.length) {
        //log for debugging
        console.error('CSRF: CSRF token not set!');
        return false;
    }
    //update
    $('.'+csrf_token_name).val(csrf_hash);
    //ensure the new token has been updated
    return ($('.'+csrf_token_name).val() != curr_csrf_hash && $('.'+csrf_token_name).val() == csrf_hash);
}

function resolve_promise(resolve, reject, jres) {
    if (update_csrf_token(jres)) { 
        resolve(jres);
    } else {
        reject_promise(reject, 'Could not update CSRF token!');
    }
}

function reject_promise(reject, error) {
    reject(error);
    regenerate_csrf_token();
}

function finalize_promise() {
    //hide the loader if it was visible
    ajax_loading_hide();
}

function retry_promise(operation, delay, retries) {
    return new Promise((resolve, reject) => {
        return operation()
            .then(resolve)
            .catch((reason) => {
                if (retries - 1 > 0) {
                    return waitfor(delay)
                    .then(retry_promise().bind(null, operation, delay, retries - 1))
                    .then(resolve)
                    .catch(reject);
                }
            return reject(reason);
        });
    });
}

function waitfor(t = 3) {
    return new Promise(resolve => setTimeout(resolve, t*1000));
}

function delay(t = 3) {
    return function (x) {
        return new Promise(resolve => setTimeout(() => resolve(x), t*1000));
    }
}

function handle_promise(promise, then_callback, catch_callback = null, finally_callback = null) {
    promise.then(function(res) {
        if (typeof then_callback === 'function') {
            then_callback(res);
        }
        console.log('Promise success:', res);
    });
    promise.catch(function(res) {
        if (typeof catch_callback === 'function') {
            then_callback(res);
        }
    });
    promise.finally(function() {
        if (typeof finally_callback === 'function') {
            finally_callback();
        }
    });
}

/**
 * [fill_form_fields: fill fields with names matching the data keys]
 * @param  {[string]} form_id
 * @param  {[object]} data
 * @return {[void]}
 */
function fill_form_fields(form_id, data, exclude = []) {
    var inputs = $('form#'+form_id+' :input');
    $.each(inputs, (i, input) => {
        var name = $(input).attr('name');
        //exclude us please...
        if ($.inArray(name, exclude) !== -1) return;
        if (typeof name !== "undefined") {
            var val = data[name];
            var field = $('#'+form_id).find(':input[name="' + name + '"]');
            //get input type or tagname if type is undefined (eg select, textarea)
            var type = field.attr('type') || field.prop('tagName').toLowerCase();
            if (type == 'select' || type == 'checkbox' || type == 'radio') {
                //we need to call change event on these guys
                field.val(val).change();
            } else {
                field.val(val);
            }
        }
    });
}

function file_preview_src(ext) {
    var path = base_url+'assets/common/img/icons/';
    switch (ext.toLowerCase()) {
        case 'pdf':
            return path += 'pdf.png';
            break;
        case 'doc':
        case 'docx':
            return path += 'word.png';
            break;
        case 'xls':
        case 'xlsx':
        case 'ods':
            return path += 'excel.png';
            break;
        case 'pptm':
        case 'ppsm':
            return path += 'ppt.png';
            break;
        case 'zip':
        case 'rar':
            return path += 'zip.png';
            break;
        case 'mp2':
        case 'mp3':
        case 'wav':
        case 'wma':
        case 'acc':
        case 'amr':
            return path += 'audio.png';
            break;
        case 'mp4':
        case 'avi':
        case 'mpg':
        case '3gp':
        case 'mov':
        case 'mkv':
        case 'ogv':
        case 'flv':
            return path += 'video.png';
            break;
        case 'exe':
            return path += 'exe.png';
            break;
        default:
            return path += 'file.png';
            break;
    }
}
