<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<h2>Create New User</h2>
<?php  echo $this->form_validation->error_string; ?>
<div id="profile_details_form_area">

    <form action="" name="user_create" method="post">
    <table>
        <tr>
            <td width="150">Full Name <font class="error">*</font></td>
            <td><input name="contact_name" type="text"></td>

            </tr>
            <tr>
                <td colspan="2"><br></td>

            </tr>
            </table>

              <h2>Login Information</h2>
            <table>
            <tr>
                <td width="150">Email Address <font class="error">*</font></td>
                <td><input name="email_address" type="text"></td>

        </tr>
        <tr>
            <td>Username <font class="error">*</font></td>
            <td><input name="username" type="text"></td>
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

        <tr>
            <td colspan="2" align="center"><input name="create" value="Create User" type="submit"><br><br></td>
        </tr>
    </table>
    </form>
</div>
