            </div><!-- /.col-12 -->
            <?php if ($this->show_sidebar) { ?>
                <div class="col-12 col-md-3">
                    <?php require 'right_sidebar.php'; ?>
                </div>
                <?php 
            } ?>
        </div><!-- /.row -->
    </div><!-- /.container-->
</div><!-- /.page_body -->


<?php csrf_input(); ?>

<!-- Footer -->
<footer class="py-3 bg-dark">
    <div class="container">
        <p class="m-0 text-center text-white"> &copy; Powered by <a href="<?php echo $this->site_author_url; ?>" target="_blank"><?php echo $this->site_author; ?></a></p>
    </div>
</footer>

<?php
//the guy that handles loading of stuff 
ajax_overlay_loader(); 
//login prompt
modal_header('m_login_prompt', 'Just a moment!');
    show_alert('I think you forgot to login...or sign up', 'info');
    $vtype = 'floating'; ?>
    <div id="floating_login">
        <?php require 'application/views/auth/login.php'; ?>
    </div>
    <div id="floating_register" style="display: none;">
        <?php require 'application/views/auth/register.php'; ?>
    </div>
    <?php
modal_footer(false); 
?>

<!-- jQuery -->
<script src="<?php echo base_url(); ?>vendors/jquery/jquery.min.js"></script>
<!-- Popper -->
<script src="<?php echo base_url(); ?>vendors/popper.min.js"></script>
<!-- Bootstrap -->
<script src="<?php echo base_url(); ?>vendors/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- Toast -->
<script src="<?php echo base_url(); ?>vendors/toast/toast.min.js"></script>
<!-- Selectpicker-->
<script src="<?php echo base_url(); ?>vendors/selectpicker/js/bootstrap-select.min.js" type="text/javascript"></script>
<!-- Summernote-->
<script src="<?php echo base_url(); ?>vendors/summernote/summernote-bs4.min.js" type="text/javascript"></script>
<!-- TimeAgo-->
<script src="<?php echo base_url(); ?>vendors/timeago.js" type="text/javascript"></script>
        
<!-- General Custom scripts -->
<script src="<?php echo base_url(); ?>assets/common/js/showmore.js"></script>
<script src="<?php echo base_url(); ?>assets/common/js/summernote_config.js"></script>
<script src="<?php echo base_url(); ?>assets/common/js/general.js"></script>
<!-- <script src="<?php //echo base_url(); ?>assets/common/js/utils/data_table.js"></script> -->
<script src="<?php echo base_url(); ?>assets/common/js/ajax.js"></script>

<?php
//custom scripts
load_scripts($this->page_scripts, 'assets'); 
?>

<script>
    //pass vars to javascript
    var base_url = "<?php echo base_url(); ?>",
        c_controller = "<?php echo $this->c_controller; ?>",
        current_page = "<?php echo $current_page; ?>",
        is_loggedin = <?php echo json_encode((Bool)$this->session->user_loggedin); ?>,
        username = "<?php echo $this->session->user_username; ?>",
        trashed = 0,
        csrf_token_name = "<?php echo $this->security->get_csrf_token_name(); ?>";

    //post button navigation
    $(document).ready(function(){
        $(document).on('click', '#header_post_btn', function(){
            //homepage?
            if (current_page == 'home') {
                find_element('#post_section');
            } else {
                location.href = base_url+'#post_section';
            }
        });

        //login/signup toggle
        $(document).on('click', '.acc_form_toggle', function(){
            var id = $(this).data('id');
            if (id === 'floating_login') {
                $('#floating_login').show();
                $('#floating_register').hide();
            } else {
                $('#floating_login').hide();
                $('#floating_register').show();
            }
        });
    });
</script>

</body>
</html>
