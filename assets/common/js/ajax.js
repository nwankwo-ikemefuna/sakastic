//selectfield params to update and refresh selectfield when a new item is added from another form
var selectfield_url = ''; 
var selectfield_name = ''; 

jQuery(document).ready(function ($) {
    "use strict"; 

    //load ajax page
    load_page_ajax_def();

    //process ajax form
    $(document).on( "submit", ".ajax_form", function(e) {
        $(this).off("submit"); //unbind event to prevent multiple firing
        e.preventDefault();
        let obj = $(this);
        var url = obj.attr('action'),
            form_id = obj.attr('id'),
            type = obj.attr('data-type') ? obj.data('type') : 'none',
            msg = obj.attr('data-msg') ? obj.data('msg') : 'Successful',
            notice = obj.attr('data-notice') ? obj.data('notice') : 'status_msg',
            modal = obj.attr('data-modal') ? obj.data('modal') : null,
            reload = obj.attr('data-reload') ? Boolean(obj.data('reload')) : true,
            status_modal = obj.attr('data-status_modal') ? Boolean(obj.data('status_modal')) : false,
            loading_msg = obj.attr('data-loading_msg') ? obj.data('loading_msg') : 'Processing...Please wait',
            clear = obj.attr('data-clear') ? Boolean(obj.data('clear')) : false;
        let form_data = new FormData(this);
        if (url.length && form_id.length && type.length) {
            switch (type) {

                //modal datatables, with selectpicker
                case 'modal_dt':
                case 'modal_sp':
                    if (modal.length) {
                        ajax_post_form_refresh(form_id, form_data, url, modal, type, msg, reload, notice, status_modal, loading_msg, clear);
                    }
                    break;

                //none, alert, redirect
                case 'none':
                case 'js_alert':
                case 'redirect':
                    var redirect = obj.attr('data-redirect') ? obj.data('redirect') : '_self';
                    var ajax_container = obj.attr('data-container') ? obj.data('container') : 'ajax_page_container';
                    var ajax_callback = obj.attr('data-callback') ? obj.data('callback') : null;
                    var ajax_loading = obj.attr('data-loading') ? obj.data('loading') : 1;
                    var ajax_loading_text = obj.attr('data-loading_text') ? obj.data('loading_text') : 'Loading';
                    var ajax_delay = obj.attr('data-delay') ? obj.data('delay') : 3;
                    ajax_post_form(form_id, form_data, url, type, redirect, msg, notice, status_modal, loading_msg, clear, ajax_container, ajax_callback, ajax_delay, ajax_loading, ajax_loading_text);
                    break;
            }
        } else {
            console.error('Setup Error: url, form id or type missing');
        }
    });

    //Edit item: modal
    $(document).on( "click", ".edit_record", function() {
        var modal = $(this).data('x_modal'),
            form_id = $(this).data('x_form_id');
        //clear form
        $('#'+form_id)[0].reset();
        var inputs = $('form#'+form_id+' :input');
        $.each(inputs, (i, input) => {
            var name = $(input).attr('name');
            if (typeof name !== "undefined") {
                var val = $(this).data(name);
                var field = $('#'+form_id).find(':input[name="'+name+'"]');
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
        $('#'+modal+ ' .modal-title').text('Edit Record');
        $('#'+modal).modal('show'); //show the modal
    });

    
    //selectpicker multiple items render on edit
    var select_mult = $('.select_mult'); 
    if ($(select_mult).length) {
        $.each($(select_mult), (i, obj) => {
            if (i % 2 !== 0) { //at every odd position
                console.log($(obj));
                if (typeof $(obj).attr('data-selected') !== "undefined") {
                    var selectfield = $(obj).attr('name'),
                        selected = $(obj).data('selected');
                    $('[name="'+selectfield+'"]').selectpicker('val', selected).change();
                }
            }
        });
    }

    //set selectfield url and name
    $(document).on( "click", ".ajax_select_btn", function() {
        selectfield_url = $(this).data('url');
        selectfield_name = $(this).data('selectfield');
    });

});

function get_select_options(url, selectfield, current_val) {
    $.ajax({
        url: base_url+url, 
        type: 'GET',
        dataType: 'json',
    }).done( jres => {
        $('[name="'+selectfield+'"]').empty(); //empty selectfield
        var options = '<option value="">Select</option>';
        if (jres.status) { 
            var result = jres.body.msg;
            if (result.length) {
                $.each(result, (i, row) => {
                    options += `<option ${row.id == current_val ? 'selected' : ''} value="${row.id}">${row.name}</option>`;
                });
            } else {
                options += `<option value="" style="color: red">${jres.error}</option>`;
            }
        } else {
            //status is false, show error message in red
            options += `<option value="" style="color: red">${jres.error}</option>`;
        }
        //append the options to the select field
        $('[name="'+selectfield+'"]').append(options);
        $('[name="'+selectfield+'"]').selectpicker('refresh');
    });
}

function ajax_post_form(form_id, form_data, url, fm_type, redirect_url = '', success_msg = 'Successful', notice_elem = 'status_msg', status_modal = false, loading_msg = 'Processing... Please wait', clear = false, ajax_container = 'ajax_page_container', ajax_delay = 3, ajax_callback = null, ajax_loading = 1, ajax_loading_text = 'Loading') {
    $.ajax({
        url: url, 
        type: 'POST',
        data: form_data,
        dataType: 'json',
        contentType: false,
        cache: false,
        processData: false,
        beforeSend: function() {
            $('.'+notice_elem).empty();
            if (status_modal) {
                ajax_loading_show(status_modal, loading_msg);
            } else {
                $('.ajax_spinner').removeClass('hide').addClass('fa-spin');
            }
        },
        complete: function() {
            if (status_modal) {
                ajax_loading_hide();
            } else {
                $('.ajax_spinner').addClass('hide').removeClass('fa-spin');
            }
        }
    }).done(function(jres) {
        if (jres.status) {
            if (clear) {
                $('#'+form_id)[0].reset();
            }
            if (fm_type == 'js_alert') {
                alert(success_msg);
                // alert(jQuery(success_msg).text());
            } else {
                status_box(notice_elem, success_msg, 'success');
            }
            setTimeout(function() { 
                switch (redirect_url) {
                    case '_void':
                        //no redirect
                        //do nothing
                        break;
                    case '_self':
                        //self redirect
                        location.reload();
                        break;
                    case '_dynamic':
                        //dynamic redirect
                        $(location).attr('href', base_url+jres.body.msg.redirect);
                        break;
                    case '_ajax':
                        load_page_ajax(redirect_url, ajax_callback, ajax_delay, ajax_container, ajax_loading, ajax_loading_text);
                        break;
                    case '_ajax_dynamic':
                        load_page_ajax(jres.body.msg.redirect, ajax_callback, ajax_delay, ajax_container, ajax_loading, ajax_loading_text);
                        break;
                    default:
                        //as provided
                        $(location).attr('href', redirect_url);
                        break;
                }
            }, 3000);  
        } else {
            if (fm_type == 'js_alert') {
                alert(jQuery(jres.error).text());
            } else {
                status_box(notice_elem, jres.error, 'danger');
            }
        }
    });
}

function ajax_post_form_refresh(form_id, form_data, url, modal_id = '', fm_type = 'modal_dt', success_msg = 'Successful!', refresh = true, notice_elem = 'status_msg', status_modal = false, loading_msg = 'Processing... Please wait', clear = false) {
    $.ajax({
        url: url, 
        type: 'POST',
        data: form_data,
        dataType: 'json',
        contentType: false,
        cache: false,
        processData: false,
        beforeSend: function() {
            $('.'+notice_elem).empty();
            if (status_modal) {
                ajax_loading_show(status_modal, loading_msg);
            } else {
                $('.ajax_spinner').removeClass('hide').addClass('fa-spin');
            }
        },
        complete: function() {
            if (status_modal) {
                ajax_loading_hide();
            } else {
                $('.ajax_spinner').addClass('hide').removeClass('fa-spin');
            }
        }
    }).done(function(jres) {
        if (jres.status) {
            if (clear) {
                $('#'+form_id)[0].reset();
            }
            if (refresh) {
                if (fm_type == 'modal_sp') {
                    //refresh selectfield
                    get_select_options(selectfield_url, selectfield_name);
                } else {
                    $('.ajax_dt_table').DataTable().ajax.reload();
                }
            }
            status_box(notice_elem, success_msg, 'success');
            if (modal_id.length) {
                setTimeout(function() { 
                    $('#'+modal_id).modal('hide');
                }, 3000);  
            }
        } else {
            status_box(notice_elem, jres.error, 'danger');
        }
    });
}

function ajax_post_btn_data(url, post_data, btn_id, modal_id = '', success_msg = 'Successful', reload_table = true, status_modal = false, loading_msg = 'Processing... Please wait') {
    // post_data = extra.length ? {...post_data, ...extra} : post_data;
    $(document).off('click', '#'+btn_id); //unbind event to prevent multiple firing
    $(document).on('click', '#'+btn_id, function(e) {
        e.preventDefault();
        $.ajax({
            url: base_url+url, 
            type: 'POST',
            data: post_data,
            dataType: 'json',
            beforeSend: function() {
                $('.confirm_status').empty();
                if (status_modal) {
                    ajax_loading_show(status_modal, loading_msg);
                } else {
                    $('.ajax_spinner').removeClass('hide').addClass('fa-spin');
                }
            },
            complete: function() {
                if (status_modal) {
                    ajax_loading_hide();
                } else {
                    $('.ajax_spinner').addClass('hide').removeClass('fa-spin');
                }
            }
        }).done(function(jres) {
            if (jres.status) {
                if (modal_id.length) {
                    status_box('confirm_status', success_msg, 'success');
                    setTimeout(function() { 
                        $('#'+modal_id).modal('hide');
                    }, 3000);
                }
                if (reload_table) {
                    $('.ajax_dt_table').DataTable().ajax.reload();
                }
            } else {
                status_box('confirm_status', jres.error, 'danger');
            }
        });
    });
}

function post_data_ajax(url, data, is_form_data = false, success_callback, error_callback = null, status_modal = false, loading_msg = 'Processing... Please wait') { 
    var ajax_setup = {
        url: url,
        type: 'POST',
        dataType: "json",
        data: data,
        beforeSend: function() { ajax_loading_show(status_modal, loading_msg) },
        complete: function() { ajax_loading_hide() }
    };
    //if form data, add processdata and contenttype to setup
    ajax_setup = is_form_data ? {...ajax_setup, ...{processData: false, contentType: false}} : ajax_setup;
    $.ajax(ajax_setup)
    .done(function (jres) {
        if (typeof success_callback === 'function') {
            success_callback(jres);
        }
    })
    .fail(function () {
        if (typeof error_callback === 'function') {
            error_callback();
        }
    });
}

function fetch_data_ajax(url, data, type = 'POST', success_callback, error_callback = null, status_modal = false, loading_msg = 'Processing... Please wait') { 
    $.ajax({
        url: url,
        type: type,
        dataType: "json",
        data: data,
        beforeSend: function() { ajax_loading_show(status_modal, loading_msg) },
        complete: function() { ajax_loading_hide() }
    })
    .done(function (jres) {
        if (typeof success_callback === 'function') {
            success_callback(jres);
        }
    })
    .fail(function () {
        if (typeof error_callback === 'function') {
            error_callback();
        }
    });
}

function ci_paginate(url, elem, row_render, pagination = 'pagination', data = {}, scroll_to = '', succ_callbk = null, err_callbk = null) {
    $('#'+pagination).on('click', 'ul li a', function(e){
        e.preventDefault();
        var page_num = $(this).attr('data-ci-pagination-page');
        paginate_data(url, elem, row_render, pagination, page_num, data, succ_callbk, err_callbk, true, 'Navigating');
        //jump to beginning of item list
        var top = $('#'+scroll_to).position().top;
        $('html').scrollTop(top);
    });
}

function paginate_data(url, elem, row_render, paginate_elem = 'pagination', page_num = 0, data = {}, succ_callbk = null, err_callbk = null, status_modal = false, loading_msg = 'Processing... Please wait') {
    $.ajax({
        url: base_url+url+'/'+page_num,
        type: 'POST',
        dataType: 'json',
        data: data,
        beforeSend: function() { ajax_loading_show(status_modal, loading_msg) },
        complete: function() { ajax_loading_hide() }
    }).done(function(jres) {
        $('#'+paginate_elem).html(jres.body.msg.pagination);
        $('#'+elem).empty();
        if (jres.status) {
            var accum = '';
            $.each(jres.body.msg.records, (i, row) => {
                if ( typeof row_render == "function" )
                    accum += row_render(row);
            });
            $('#'+elem).html(accum);
            if ( typeof succ_callbk == "function" ) 
                succ_callbk(jres);
        } 
    }).fail(function(jres) {
        if ( typeof err_callbk == "function" ) 
            err_callbk(jres);
    });
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

function load_page_ajax_def() {
    $(document).on('click', '.tload_ajax', function(e) {
        e.preventDefault();
        var container = $(this).attr('data-container') ? $(this).data('container') : 'ajax_page_container',
            url = $(this).data('url'),
            callback = $(this).attr('data-callback') ? $(this).data('callback') : null,
            loading = $(this).attr('data-loading') ? Boolean($(this).data('loading')) : true,
            loading_text = $(this).attr('data-loading_text') ? $(this).data('loading_text') : 'Loading';
        if (!container.length || !url.length) {
            console.error('Setup Error: container or url not specified');
            return false;
        }
        if (loading) {
            ajax_loading_show(true, loading_text);
        }
        $('#'+container).load(base_url+url, function() {
            if (typeof window[callback] === 'function') {
                window[callback]();
            }
            ajax_loading_hide();
        });
    });
}

function load_page_ajax(url, callback = null, delay = 3, container = 'ajax_page_container', loading = true, loading_text = 'Loading') {
    if (!url.length) {
        console.error('Setup Error: url not specified');
        return false;
    }
    setTimeout(function(){
        if (loading) {
            ajax_loading_show(true, loading_text);
        }
        $('#'+container).load(base_url+url, function() {
            if (typeof window[callback] === 'function') {
                window[callback]();
            }
            ajax_loading_hide();
        });
    }, delay*1000);
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
