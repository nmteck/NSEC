<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<?php if(in_array($metadata['section'], array('account', 'admin', 'listings'))){?>
<script src="/-/js/accountFunctions.js" type="text/javascript" ></script>
<link rel="stylesheet" href="/-/css/screen/admin.css"/>

<?php if(isset($admin_page) && in_array($admin_page, array('edit_content'))){?>
<script type="text/javascript" language="javascript">
$(document).ready(function() {
    $('#contentManagerForm').contentManager({

    });
});
</script>
<?php
}

echo isset($mcehead) ? $mcehead : NULL;
 ?>

<script type="text/javascript" >
$(document).ready(function() {
    menuWrapper = $('<div />');

    $('#wrapper').accountFunctions({
        menuContainer: '#header',
        listWrapper: menuWrapper
    });
});
</script>
<?php
}
?>