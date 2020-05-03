<?php 

function ajax_spinner($class = 'ajax_spinner') {
    return ' <i class="fa fa-spinner hide '.$class.'"></i>';
}

function bs_badge($text, $bg = 'info') {
    return '<span class="badge badge-'.$bg.'">'.$text.'</span>';
}

function show_alert($msg, $bg = 'info', $class = '') {
    echo '<div class="alert alert-'.$bg.' '.$class.'">'.$msg.'</div>';
}

function flash_alert($msg, $bg = 'info', $class = '') {
    echo '<div class="alert alert-'.$bg.' alert-dismissable auto_dismiss '.$class.'">'.alert_dismiss().$msg.'</div>';
}

function alert_dismiss() {
    return '<button type="button" class="close" data-dismiss="alert" aria-label="Close" title="Close"> 
        <span aria-hidden="true">&times;</span></button>';
}

function custom_validation_errors() {
    if ( validation_errors() ) { 
        return '<div class="alert alert-danger alert-dismissable auto_dismiss text-center">'
            . alert_dismiss()
            . validation_errors() .
        '</div>';
    }
} 

function flash_message($key = 'success_msg', $type = 'success') {
    $CI = & get_instance(); 
    if ( $CI->session->flashdata($key) ) {                      
        echo '<div class="alert alert-'.$type.' alert-dismissable auto_dismiss text-center">'
                    . alert_dismiss()
                    . $CI->session->flashdata($key) .
                '</div>';
        //unset it
        $CI->session->unset_userdata($key);
    }
}

function grid_col($xs = '12', $sm = '', $md = '', $lg = '', $xl = '') {
    $column = 'col-'.$xs;
    $cols = ['sm' => $sm, 'md' => $md, 'lg' => $lg, 'xl' => $xl];
    foreach ($cols as $col => $val) {
        if (strlen($val)) {
            //do we have an offset? Eg. 5?2 means col-lg-5 offset-lg-2
            if (is_string($val)) {
                $ex = explode('?', $val);
                $offset = isset($ex[1]) && strlen($ex[1]) ? ' offset-'.$col.'-'.$ex[1] : '';
                $column .= ' col-'.$col.'-'.$ex[0].$offset;
            } else {
                $column .= ' col-'.$col.'-'.$val;
            }
        }
    }
    return $column;
}

//bootstrap 3
function grid_col_b3($xs = '12', $sm = '', $md = '', $lg = '', $xl = '') {
    $column = 'col-xs-'.$xs;
    $cols = ['sm' => $sm, 'md' => $md, 'lg' => $lg, 'xl' => $xl];
    foreach ($cols as $col => $val) {
        if (strlen($val)) {
            //do we have an offset? Eg. 5?2 means col-lg-5 col-lg-offset-2
            if (is_string($val)) {
                $ex = explode('?', $val);
                $offset = isset($ex[1]) && strlen($ex[1]) ? ' col-'.$col.'-offset-'.$ex[1] : '';
                $column .= ' col-'.$col.'-'.$ex[0].$offset;
            } else {
                $column .= ' col-'.$col.'-'.$val;
            }
        }
    }
    return $column;
}

/**
 * Template Class: 
 * size - bd-example-modal-lg, bd-example-modal-sm, full-width-modal
 * animation: slide-down|up|right|left|fill-in
 *
 * Dialog: modal-sm, modal-lg
 */
function modal_header($id, $title = '', $class = '', $dialog_class = '', $body_class = '', $with_header = true) { ?>
    <div class="modal custom fade <?php echo $class; ?>" id="<?php echo $id; ?>" tabindex="-1" role="dialog" aria-labelledby="<?php echo $id; ?>" aria-hidden="true">
        <div class="modal-dialog <?php echo $dialog_class; ?>" role="document">
            <div class="modal-content">
                <?php if ($with_header) { ?>
                    <div class="modal-header">
                        <h5 class="modal-title"><?php echo $title; ?></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <?php 
                } ?>
                <div class="modal-body <?php echo $body_class; ?>">
                    <!-- Content goes here -->
    <?php        
}

function modal_footer($with_footer = true, $with_btn = false, $btn_id = 'confirm_btn', $btn_text = 'Yes, Continue', $with_close_btn = true, $close_btn_text = 'Cancel') { ?>
                </div><!--/.modal-body-->
                <?php 
                if ($with_footer) { ?>
                    <div class="modal-footer">
                        <?php 
                        if ($with_btn) { 
                            $btn_text .= ' <i class="fa fa-spinner ajax_spinner hide"></i>'; ?>
                            <button class="btn btn-sm btn-warning text-white" role="button" id="<?php echo $btn_id; ?>"><?php echo $btn_text; ?></button>
                            <?php
                        }
                        //close button?
                        if ($with_close_btn) { ?>
                            <button type="button" class="btn btn-dark" data-dismiss="modal"><?php echo $close_btn_text; ?></button>
                            <?php 
                        } ?>
                    </div>
                    <?php 
                } ?>
            </div><!--/.modal-content-->
        </div><!--/.modal-dialog-->
    </div><!--/.modal-->
    <?php        
}

function ajax_overlay_loader() { ?>
    <div id="ajax_overlay_loader">
        <div id="overlay_content">
            <img id="overlay_image" src="<?php echo base_url('assets/common/img/icons/ajax_loader.gif'); ?>">
            <div id="overlay_text">Loading...</div>
        </div>
    </div>
    <?php
}