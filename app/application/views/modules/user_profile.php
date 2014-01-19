<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<?php
    $userInfo = (object) $user_array[0];
    $user_details = json_decode($userInfo->comments);
    echo $this->form_validation->error_string;
    //var_dump($userInfo);
?>
<h2>Edit User Profile</h2>
<a href="/admin/users/">Manage Users</a> \ <?php echo $userInfo->username; ?><hr /><br />
<div id="profile_details_form_area">

    <form action="" name="profile_update" method="post">
        <div id="user_access">
        <table>
            <tr>
                <td colspan="2">Access Levels:<br />
                <?php
                    getUserAccessData();

                    echo form_multiselect('access[]', $this->userAccessGroups, $this->userAccessTypes, 'size="20" style="background: white;"');
                ?>
                </td>
            </tr>
        </table>
        </div>
        <div id="user_details">
            <table>
            <tr>
                <td width="150">Full Name <font class="error">*</font></td>
                <td><input value="<?php echo $userInfo->contact_name; ?>" name="contact_name" type="text"></td>
            </tr>
            <tr>
                <td width="150">Active <font class="error">*</font></td>
                <td>
                    <input value="0" name="active" type="hidden">
                    <input value="1" <?php echo ($userInfo->active == 1) ? 'checked' : ''; ?> name="active" type="checkbox">
                </td>
            </tr>
            <tr>
                <td colspan="2"><br></td>
            </tr>
            </table>

            <h2>Login Information</h2>
            <table>
                <tr>
                    <td width="150">Email Address <font class="error">*</font></td>
                    <td><input value="<?php echo $userInfo->email_address; ?>" name="email_address" type="text"></td>
            </tr>
            <tr>
                <td>Username <font class="error">*</font></td>
                <td><input disabled value="<?php echo $userInfo->username; ?>" name="username" type="text"></td>
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
                <td colspan="2"><br></td>
            </tr>
            <?php
                $profileDetailsJSON = json_decode($userInfo->comments);
                $additionalDetails = $this->config->item('profile_details');

                if (is_array($additionalDetails)) {
            ?>
            <tr>
                <td colspan="2">
                <div id="additional_details">
                    <h2>Profile Detials</h2>
                    <?php
                            foreach ($additionalDetails as $_detail) {
                                $value = null;
                                if (isset($profileDetailsJSON->$_detail)) {
                                    $value = $profileDetailsJSON->$_detail;
                                }

                                echo $this->nsec_filters->getSettingField($_detail, $value, false);
                            }
                    ?>
                </div>
                </td>
            </tr>
            <?php
            }
            ?>
            <tr>
                <td colspan="2" align="center"><input name="update" value="Save Changes" type="submit"><br><br></td>
            </tr>
            </table>
        </div>
    </form>
</div>
