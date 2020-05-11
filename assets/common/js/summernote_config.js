$(document).ready(function() {

    //Monkey patch to fix excess whitespace on summernote textfield
    $.summernote.dom.emptyPara = "<div><br/></div>";

    //initialize
    summernote_init('.summernote');

});

function summernote_init(selector = '.summernote', tools = {}, extra = {}) {
    $(selector).summernote({
        height: ('height' in extra) ? extra.height : 200,
        placeholder: ('placeholder' in extra) ? extra.placeholder : '',
        followingToolbar: false, //disable page trembling effect
        toolbar: [
            // [groupName, [list of button]]
            tools.style ? ['style', ['style']] : '',
            ['font', ['bold', 'italic', 'underline', 'strikethrough']],
            tools.fontname ? ['fontname', ['fontname']] : '',
            tools.fontsize ? ['fontsize', ['fontsize']] : '',
            tools.color ? ['color', ['color']] : '',
            tools.para ? ['para', ['ul', 'ol', 'paragraph']] : '',
            tools.table ? ['table', ['table']] : '',
            ['insert', ['link', tools.picture ? 'picture' : '']],
            ['view', ['undo', 'redo', 'fullscreen', tools.codeview ? 'codeview' : '', 'help']],
        ],
        callbacks: {
            onImageUpload: function(image) {
                upload_smt_image(selector, image[0]);
            },
            onMediaDelete : function(target) {
                delete_smt_image(target[0].src);
            }
        }
    });
}

function upload_smt_image(selector, file) {
    var data = new FormData(),
        wrapper = $(selector).closest('.smt_wrapper'),
        reqlog = wrapper.find('.smt_reqlog').val();
    if (reqlog && !user_loggedin(login_prompt)) return false;
    //appendages
    data.append('smt_file', file);
    data.append('smt_path', wrapper.find('.smt_path').val());
    data.append('smt_size', wrapper.find('.smt_size').val());
    data.append('smt_resize', wrapper.find('.smt_resize').val());
    data.append('smt_resize_width', wrapper.find('.smt_resize_width').val());
    data.append('smt_resize_height', wrapper.find('.smt_resize_height').val());
    data.append('smt_reqlog', reqlog);
    data.append(csrf_token_name, $('.'+csrf_token_name).val());

    var success_callback = function(jres) {
        if (jres.status) {
            var img_src = jres.body.msg;
            var image = $('<img>').attr('src', img_src);
            $(selector).summernote("insertNode", image[0]);
            //insert into hidden input for later use
            var images_container = wrapper.find('.smt_images'),
                uploaded_images = images_container.val(),
                delim = "[***]",
                just_uploaded = img_src.split("/"),
                //get the image name
                img_name = just_uploaded[just_uploaded.length - 1];
            uploaded_images += uploaded_images.length ? (delim + img_name) : img_name;
            images_container.val(uploaded_images);
            // show_toast('Upload Notice', 'Image uploaded successfully', 'success');
        } else {
            show_toast('Upload Notice', jres.error, 'danger');
        }
    };
    post_data_ajax(base_url+'api/common/upload_smt_image', data, true, success_callback);
}

function delete_smt_image(src) {
    var success_callback = function(jres) {
        if (jres.status) {
            // show_toast('Delete Notice', 'Image deleted successfully', 'success');
        } else {
            show_toast('Delete Notice', jres.error, 'danger');
        }
    };
    post_data_ajax(base_url+'api/common/delete_smt_image', {src : src}, false, success_callback);
}