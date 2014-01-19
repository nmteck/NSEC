<?php 
include 'common.php';
include 'includes/header.php';

?>
         <div class="container">
         	<div class="wrapper"> 
			<!-- content-wrap starts here -->	
			<div id="content-wrap">  
					<div id="sidebar"></div>
			
					<div id="intro">
					<?php echo isset($Errors) ? $Errors : NULL; ?>	
						<?php if(isset($contentPage)) include "modules/$contentPage.php"; ?>			
									
			  		</div>			
		
				<script type='text/html' id="loading_notice">Loading...</script>
				<script type='text/javascript'>
		                    <!--
		                    $(document).ready(function() {
								hideLoadingPopup();
		                    });
		                    //-->
				</script>
								
			<!-- content-wrap ends here -->	
			</div>
		</div>
		</div>
<?php include 'includes/footer.php'?>
