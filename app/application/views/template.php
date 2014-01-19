<?php
include 'common.php';
include 'includes/header.php';

if (isset($content->Content)) {
    $ThisContent = $content->Content;
    include 'includes/content.php';
}  elseif(isset($contentPage)) {
    include "content/$contentPage.php";
}

include 'includes/footer.php';
?>