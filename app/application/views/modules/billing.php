<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<div class="content">
    <div class="content_resize">
        <div class="inner_copy"></div>
        <div class="mainbar">
        	<div class="article">
          		<h2><span>Billing</span> Options</h2>
          		<div class="clr"></div>
				<div id="billingMessage">
					<b>Note:</b> A portion of the proceeds will go towards helping fund a little league team in Detroit in 2013.<br /><br />
					<b>Note:</b> Click the button below to proceed with your payment. Shipping and handling will be calculated during your checkout.
				</div>
          		<h2 id="current_balance">$<span><?php echo getUserBalance();?> Balance</span></h2>
          		<div class="clr"></div>
                <form action='/billing/checkout' METHOD='POST'>
                <input type="hidden" name="billing_amount" value="<?php echo getUserBalance(); ?>" />
                <input type='image' name='submit' src='https://www.paypal.com/en_US/i/btn/btn_xpressCheckout.gif'
                	border='0' align='top' alt='Check out with PayPal'/>

        		</form>
      		</div>
      	</div>
    </div>
</div>
<div class="clr"></div>