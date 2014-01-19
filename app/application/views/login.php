<?php include 'common.php'; ?>
<?php include 'includes/header.php'?>
         <div class="container">
             <div class="wrapper">
            <!-- content-wrap starts here -->
            <div id="content-wrap">
                  <<?php echo $this->config->item('headerTagType');?>>User Sign-in</<?php echo $this->config->item('headerTagType');?>>
                <?php echo $Errors; ?>
                    <?php echo form_error('login_username'); ?>
                    <?php echo form_error('login_password'); ?>
                <h3>Admin Login</h3>
                <?php echo form_open('login', array('style' => 'text-align:center', 'id' => $metadata['section'].'_form')); ?>
                    <p>
                    <label>Username</label>
                    <input name="login_username" value="<?php echo set_value('login_username'); ?>" type="text" maxlength="20" size="30" /><br /><br />
                    <label>Password</label>
                    <input name="login_password" value="<?php echo set_value('login_password'); ?>" maxlength="20" type="password" size="30" />
                    <br />
                    <br />
                    <?php check_for_last_page(); ?>
                    <input class="button" type="submit" value="Login"/>
                    </p>
                <?php echo form_close(); ?>
            </div>
        </div>
        </div>
<?php include 'includes/footer.php'?>
