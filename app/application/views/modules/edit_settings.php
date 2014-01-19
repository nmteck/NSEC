<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<h4>
    <a href="#" class="settingsUpdateToggle" onclick="$('.settingsContainers').toggle(); return false;">Show Update Form</a>
    <a href="#" class="settingsAddNewToggle" onclick="$('.settingsContainers').toggle(); return false;">Add New</a>
</h4>
<div class="settingsContainers addNewSetting" style="display: none;">
<form action="" method="post" name="new" id="addNewSetting">
    <?php echo validation_errors(); ?> <br />
    <div class="admincat">
    <?php
        $fields = $this->db->field_data($pagekey);

        foreach ($fields as $field)
        {
           //$allfields[$field->name] = $field->name;
           $allfields[$field->name]['type'] = $field->type;
           $allfields[$field->name]['length'] = $field->max_length;
           $allfields[$field->name]['isKey'] = $field->primary_key;

           if($field->primary_key != 1)
           {
                echo $this->nsec_filters->getSettingField($field->name, '');
           }
        }

    ?>
    </div>
    <input type="submit" name="addnew" value="Add New"/><hr /><br />
</form>
</div>
<div class="settingsContainers updateSettings">
<form action="" method="post" name="updateform">
    <input type="button" name="cancel" value="Cancel" onClick="window.location='<?php echo site_url("admin/settings") ?>'" >
    <input type="submit" name="save" value="Update All"/><br /><br />
    <?php
        $count=0;

        foreach($settings->result() as $setting) {
            foreach($setting as $field=>$value) {
                if($allfields[$field]['isKey'] == 1) {
                    echo "
                        <a name=\"edit$value\"></a>
                        Key ($field): $value<input type=\"hidden\" name=\"key[$count]\"  value=\"$value||$field\" /><br />
                    ";
                } else {
                    echo $this->nsec_filters->getSettingField($field, $value, true, NULL, $count);
                }
            }

            echo "<hr />";
            $count++;
        }
    ?>


    <input type="button" name="cancel" value="Cancel" onClick="window.location='<?php echo site_url("admin/settings") ?>'" >
    <input type="submit" name="save" value="Update All Above"/>

</form>
<hr />
</div>