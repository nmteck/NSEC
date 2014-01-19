<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
                <h2>Account Profile</h2>

                <div id="profile_details_form_area">

                    <form action="" name="profile_update" method="post">
                    <table>
                    <tr>
                        <td width="150">Full Name <font class="error">*</font></td>
                        <td><input value="<?php echo $this->session->userdata('contact_name'); ?>" name="fname" type="text"></td>

                    </tr>
                    <tr>
                        <td colspan="2"><br></td>

                    </tr>
                    </table>

                      <h2>Login Information</h2>
                    <table>
                    <tr>
                        <td width="150">Email Address <font class="error">*</font></td>
                        <td><input value="<?php echo $this->session->userdata('email_address'); ?>" name="email_add" type="text"></td>

                    </tr>
                    <tr>
                        <td>Username <font class="error">*</font></td>
                        <td><input disabled value="<?php echo $this->session->userdata('username'); ?>" name="username" type="text"></td>
                    </tr>
                    <tr>

                        <td>Password <font class="error">*</font></td>
                        <td><input name="password" type="password"></td>
                    </tr>
                    <tr>
                        <td>Confirm Password <font class="error">*</font></td>
                        <td><input name="confirm_password" type="password"></td>
                    </tr>

                    <tr>
                        <td>Email Preferences</td>
                        <td>
                            We'll send you service announcements that relate to your agreement with <?php echo $this->config->item('SiteName');?>.<br>
                            <input name="email_prefer" checked="checked" type="checkbox">
                            In addition, send me periodic newsletters with tips and best
                            practices and occasional surveys to help us improve <?php echo $this->config->item('SiteName');?>.
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2"><br></td>
                    </tr>

                    <tr>
                        <td colspan="2" align="center"><input value="Save Changes" type="submit"><br><br></td>
                    </tr>
                    </table>
                    </form>
                </div>
                <div id="banner_details"></div>
