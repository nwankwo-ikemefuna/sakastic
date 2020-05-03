jQuery(document).ready(function ($) {
    "use strict";

    //Note: tm is short for trigger_modal 
    
    function confirm_actions(obj, ajax_url, title, msg, success_msg = 'Successful') {
        $('#m_confirm_action .modal-title').text(title); 
        $('#m_confirm_action .modal-body .msg').html(msg); 
        $('#m_confirm_action').modal('show');
        var id = $(obj).data('id'),
            tb = $(obj).data('tb'),
            mod = $(obj).data('mod'),
            md = $(obj).data('md'),
            cmm = $(obj).attr('data-cmm') ? $(obj).data('cmm') : '',
            files = $(obj).attr('data-files') ? $(obj).data('files') : {},
            where = $(obj).attr('data-where') ? $(obj).data('where') : {};
        var post_data = {mod: mod, md: md, tb: tb, id: id, cm: cmm, files: files, where: where};
        ajax_post_btn_data(ajax_url, post_data, 'confirm_btn', 'm_confirm_action', success_msg);
    }
    
    //trash record
    $(document).on( "click", ".trash_record", function() {
        confirm_actions(this, 'common/trash_ajax', 'Trash Record', 'Sure to trash this record?', 'Record trashed successfully');
    });

    //trash all records
    $(document).on( "click", ".tm_trash_all", function() {
        confirm_actions(this, 'common/trash_all_ajax', 'Trash all Records', 'Sure to trash all records? To trash only selected records, use the Bulk Action feature.', 'Records trashed successfully');
    });

    //restore trashed record
    $(document).on( "click", ".restore_record", function() {
        confirm_actions(this, 'common/restore_ajax', 'Restore Record', 'Sure to restore this record?', 'Record restored successfully');
    });

    //restore all records
    $(document).on( "click", ".tm_restore_all", function() {
        confirm_actions(this, 'common/restore_all_ajax', 'Restore all Records', 'Sure to restore all records? To restore only selected records, use the Bulk Action feature.', 'Records restored successfully');
    });

    //delete a trashed record permanently
    $(document).on( "click", ".delete_record", function() {
        confirm_actions(this, 'common/delete_ajax', 'Delete Record', 'Sure to delete this record? This action cannot be undone!', 'Record deleted successfully');
    });

    //delete all trashed records permanently
    $(document).on( "click", ".tm_clear_trash", function() {
        confirm_actions(this, 'common/clear_trash_ajax', 'Clear Trash', 'Sure to clear trash? This action cannot be undone! To permanently delete only selected records, use the Bulk Action feature.', 'Trash cleared successfully');
    });

    //view image
    $(document).on( "click", ".tm_image", function() {
        $('#m_img_view .modal-title').text($(this).attr('title')); 
        var img = $('<img/>').attr({src: $(this).attr('src'), title: $(this).attr('title'), class: 'modal_image img-responsive'});
        $('#m_img_view .modal-body').empty().html(img);
        $('#m_img_view').modal('show');
    });

    //ajax modal button
    $(document).on( "click", ".ajax_extra_modal_btn", function() {
        var id = $(this).data('id'),
            name = $(this).data('name'),
            modal = $(this).data('modal');
        $('[name="'+name+'"]').val(id);
        $(modal).modal('show'); //show the modal
    });

    //table row options
    $(document).on( "click", ".record_extra_options", function() {
        var id = $(this).data('id');
        var options = $(this).data('options'); 
        var butts = "";
        $.each(options, (i, opt) => {
            butts += modal_option_btn(id, opt.text, opt.type, opt.target, opt.icon);
        });
        $('#m_row_options .modal-title').text('More Options'); 
        $('#m_row_options .modal-body').empty().html(butts); 
        $('#m_row_options').modal('show'); //show the modal
    });

    function modal_option_btn(id, text, type, target, icon) {
        if (type == 'url') {
            const url = base_url + target + '/' + id;
            return '<p><a type="button" href="'+url+'" class="btn btn-outline-primary btn-sm btn-block action-btn"><i class="fa fa-'+icon+'"></i> '+text+'</a></p>';
        } else {
            return '<p><button type="button" data-toggle="modal" data-target="#'+target+'" class="btn btn-outline-primary btn-sm btn-block action-btn"><i class="fa fa-'+icon+'"></i> '+text+'</button></p>';
        }
    }


    //bulk action
    //bulk action: disable action button if no bulk action type is selected
    if ($('.ba_apply').length) $('.ba_apply').prop('disabled', true);
    $('[name="ba_option"]').change(function () {
        $('.ba_apply').prop('disabled', ! Boolean($(this).val()));
    });
    
    //bulk action: select all checkbox items if select all is checked
    $(document).on( "change", '.ba_check_all', function() {
        $('.ba_record').prop('checked', $(this).prop('checked'));
    });
    
    $(document).on( "change", '.ba_record', function() {
        if(false == $(this).prop('checked')){ 
            $('.ba_check_all').prop('checked', false); 
        }
        if ($('.ba_record:checked').length == $('.ba_record').length ){
            $('.ba_check_all').prop('checked', true);
        }
    });

    var ba_modal = '',
        ba_val = '',
        selected = '';
    $(document).on( "change", '[name="ba_option"]', function() {
        selected = $('[name="ba_option"] option:selected');
        ba_modal = selected.data('modal');
        ba_val = $(this).val();
    });

    $(document).on( "click", '.ba_apply', function() {
        //get checked records
        var record_idx = checked_records();
        var _records = record_idx.length + ' ' + (record_idx.length == 1 ? 'record' : 'records');
        if (record_idx.length) {
            $('#ba_selected_msg').removeClass('text-danger').addClass('text-success').text(`${_records} selected`);
        } else {
            $('#ba_selected_msg').removeClass('text-success').addClass('text-danger').text('No record selected');
            return false;
        }
        
        //if trash, restore or delete, use common url to process form
        let ba_mod = $(this).data('mod'),
            ba_md = $(this).data('md'),
            ba_tb = $(this).data('tb'),
            m_title = '',
            m_msg = '';
        var post_data = {mod: ba_mod, md: ba_md, tb: ba_tb, id: record_idx.join()};
        switch (ba_val) {

            case 'Trash':
                m_title = 'Bulk Trash';
                m_msg = 'Sure to trash the selected records?';
                ajax_post_btn_data('common/bulk_trash_ajax', post_data, 'ba_confirm_btn', 'm_confirm_ba', 'Records trashed successfully');
                break;

            case 'Restore':
                m_title = 'Bulk Restore';
                m_msg = 'Sure to restore the selected records?';
                ajax_post_btn_data('common/bulk_restore_ajax', post_data, 'ba_confirm_btn', 'm_confirm_ba', 'Records trashed successfully');
                break;

            case 'Delete':
                //check files exist for the records. Files are same for all records, so we get for one.
                var files = $('.delete_record').eq(0).attr('data-files') ? {files: $('.delete_record').eq(0).data('files')} : {};
                post_data = {...post_data, ...files};
                m_title = 'Bulk Delete';
                m_msg = 'Sure to permanently delete the selected records? This action cannot be undone!';
                ajax_post_btn_data('common/bulk_delete_ajax', post_data, 'ba_confirm_btn', 'm_confirm_ba', 'Records trashed successfully');
                break;

            default:
                //custom
                m_title = 'Bulk Action';
                var id_field = selected.attr('data-id_field') ? selected.data('id_field') : 'id';
                $('[name="'+id_field+'"]').val(record_idx.join());
                break;

        }
        $('#'+ba_modal+ ' .modal-title').empty().text(`${m_title} (${_records})`);
        $('#'+ba_modal+ ' .modal-body .ba_msg').text(m_msg);
        $('#'+ba_modal).modal('show');
    });

    function checked_records() {
        //get checked records
        var record_idx = [];
        $.each($('[name="ba_record_idx[]"]:checked'), function(){
            record_idx.push($(this).val());
        });
        return record_idx;
    }


    //email user
    $(document).on( "click", ".tm_email_user", function() {
        //get data value params
        var title = $(this).data('title'); 
        var email = $(this).data('email');
        $('#modal_email_user .modal-title').text(title); 
        $('#modal_email_user .modal-body #m_user_email').val(email);
        $('#modal_email_user').modal('show'); //show the modal
    });

    //media
    $(document).on( "click", ".tm_media", function() {
        //get data value params
        var title = $(this).data('title'); 
        var type = $(this).data('type'); 
        var file_exists = $(this).data('file_exists'); 
        $('#modal_media .modal-body').empty(); 
        $('#modal_media .modal-footer').empty(); 
        $('#modal_media .modal-title').text(title); 
        if (file_exists) {
            var file_path = $(this).data('file_path'); 
            var file_index = $(this).data('file_index'); 
            var file_name = $(this).data('file_name'); 
            var download_url = base_url+'misc/download/'+file_index+'/'+file_name; 
            var body; 
            switch (type) {
                case 'img':
                    body = `<img class="img-responsive" src="${file_path}" />`;
                    break;
                case 'pdf':
                    body =  `<div id="doc_area">
                              <object data="${file_path}?#zoom=85&scrollbar=0&toolbar=1navpanes=0" width="100%" height="400">
                                <p class="text-danger text-center">Unable to render PDF document! Check your browser settings or switch to a different browser.</p>
                              </object>
                            </div>`;
                    break;
                default: 
                    body = '<h4 class="text-danger m-b-15">Online media renderer unavailable, download for offline view.</h4>';
                    break;
            }
            $('#modal_media .modal-body').html(body); 
            $('#modal_media .modal-footer').append('<a type="button" class="btn btn-primary f_download"><i class="fa fa-download"></i> Download</a>'); 
            $('#modal_media .modal-footer .f_download').attr('href', download_url); 
        } else {
            $('#modal_media .modal-body').html('<h4 class="text-danger">File not found!</h4>'); 
            $('#modal_media .modal-footer').empty(); 
        }
        $('#modal_media').modal('show'); //show the modal
    });

});
 

