<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<div class="content">
    <div class="content_resize">
        <div class="inner_copy"></div>
        <div class="mainbar">
        	<div class="article">
<?php
	/*==================================================================
	 PayPal Express Checkout Call
	 ===================================================================
	*/

	/*
	'------------------------------------
	' The paymentAmount is the total value of
	' the shopping cart, that was set
	' earlier in a session variable
	' by the shopping cart page
	'------------------------------------
	*/

	$finalPaymentAmount =  $this->session->userdata("Payment_Amount");

	/*
	'------------------------------------
	' Calls the DoExpressCheckoutPayment API call
	'
	' The ConfirmPayment function is defined in the file PayPalFunctions.jsp,
	' that is included at the top of this file.
	'-------------------------------------------------
	*/

	$resArray = ConfirmPayment ( $finalPaymentAmount );
	updatePurchaseOrder($resArray);
	$ack = strtoupper($resArray["ACK"]);

	if( $ack == "SUCCESS" || $ack == "SUCCESSWITHWARNING" )
	{
		/*
		'********************************************************************************************************************
		'
		' THE PARTNER SHOULD SAVE THE KEY TRANSACTION RELATED INFORMATION LIKE
		'                    transactionId & orderTime
		'  IN THEIR OWN  DATABASE
		' AND THE REST OF THE INFORMATION CAN BE USED TO UNDERSTAND THE STATUS OF THE PAYMENT
		'
		'********************************************************************************************************************
		*/
		$transactionId		= isset($resArray["PAYMENTINFO_0_TRANSACTIONID"]) ? $resArray["PAYMENTINFO_0_TRANSACTIONID"]: NULL; // ' Unique transaction ID of the payment. Note:  If the PaymentAction of the request was Authorization or Order, this value is your AuthorizationID for use with the Authorization & Capture APIs.
		$transactionType 	= isset($resArray["PAYMENTINFO_0_TRANSACTIONTYPE"]) ? $resArray["PAYMENTINFO_0_TRANSACTIONTYPE"]: NULL; //' The type of transaction Possible values: l  cart l  express-checkout
		$paymentType		= isset($resArray["PAYMENTINFO_0_PAYMENTTYPE"]) ? $resArray["PAYMENTINFO_0_PAYMENTTYPE"]: NULL;  //' Indicates whether the payment is instant or delayed. Possible values: l  none l  echeck l  instant
		$orderTime 			= isset($resArray["PAYMENTINFO_0_ORDERTIME"]) ? $resArray["PAYMENTINFO_0_ORDERTIME"]: NULL;  //' Time/date stamp of payment
		$amt				= isset($resArray["PAYMENTINFO_0_AMT"]) ? $resArray["PAYMENTINFO_0_AMT"]: NULL;  //' The final amount charged, including any shipping and taxes from your Merchant Profile.
		$currencyCode		= isset($resArray["PAYMENTINFO_0_CURRENCYCODE"]) ? $resArray["PAYMENTINFO_0_CURRENCYCODE"]: NULL;  //' A three-character currency code for one of the currencies listed in PayPay-Supported Transactional Currencies. Default: USD.
		$feeAmt				= isset($resArray["PAYMENTINFO_0_FEEAMT"]) ? $resArray["PAYMENTINFO_0_FEEAMT"]: NULL;  //' PayPal fee amount charged for the transaction
		$settleAmt			= isset($resArray["PAYMENTINFO_0_SETTLEAMT"]) ? $resArray["PAYMENTINFO_0_SETTLEAMT"]: NULL;  //' Amount deposited in your PayPal account after a currency conversion.
		$taxAmt				= isset($resArray["PAYMENTINFO_0_TAXAMT"]) ? $resArray["PAYMENTINFO_0_TAXAMT"]: NULL;  //' Tax charged on the transaction.
		$exchangeRate		= isset($resArray["PAYMENTINFO_0_EXCHANGERATE"]) ? $resArray["PAYMENTINFO_0_EXCHANGERATE"]: NULL;  //' Exchange rate if a currency conversion occurred. Relevant only if your are billing in their non-primary currency. If the customer chooses to pay with a currency other than the non-primary currency, the conversion occurs in the customer’s account.

		/*
		' Status of the payment:
				'Completed: The payment has been completed, and the funds have been added successfully to your account balance.
				'Pending: The payment is pending. See the PendingReason element for more information.
		*/

		$paymentStatus	= isset($resArray["PAYMENTINFO_0_PAYMENTSTATUS"]) ? $resArray["PAYMENTINFO_0_PAYMENTSTATUS"]: NULL;

		/*
		'The reason the payment is pending:
		'  none: No pending reason
		'  address: The payment is pending because your customer did not include a confirmed shipping address and your Payment Receiving Preferences is set such that you want to manually accept or deny each of these payments. To change your preference, go to the Preferences section of your Profile.
		'  echeck: The payment is pending because it was made by an eCheck that has not yet cleared.
		'  intl: The payment is pending because you hold a non-U.S. account and do not have a withdrawal mechanism. You must manually accept or deny this payment from your Account Overview.
		'  multi-currency: You do not have a balance in the currency sent, and you do not have your Payment Receiving Preferences set to automatically convert and accept this payment. You must manually accept or deny this payment.
		'  verify: The payment is pending because you are not yet verified. You must verify your account before you can accept this payment.
		'  other: The payment is pending for a reason other than those listed above. For more information, contact PayPal customer service.
		*/

		$pendingReason	= isset($resArray["PAYMENTINFO_0_PENDINGREASON"]) ? $resArray["PAYMENTINFO_0_PENDINGREASON"]: NULL;

		/*
		'The reason for a reversal if TransactionType is reversal:
		'  none: No reason code
		'  chargeback: A reversal has occurred on this transaction due to a chargeback by your customer.
		'  guarantee: A reversal has occurred on this transaction due to your customer triggering a money-back guarantee.
		'  buyer-complaint: A reversal has occurred on this transaction due to a complaint about the transaction from your customer.
		'  refund: A reversal has occurred on this transaction because you have given the customer a refund.
		'  other: A reversal has occurred on this transaction due to a reason not listed above.
		*/

		$reasonCode		= isset($resArray["PAYMENTINFO_0_REASONCODE"]) ? $resArray["PAYMENTINFO_0_REASONCODE"]: NULL;

		$result = $resArray;

		$updateStatus = array('Processed' => 1, 'Total' => number_format($amt, 2));
		if ($paymentStatus == 'completed') {
		    $updateStatus['Valid'] = 1;
		}

		updatePurchaseStatus($updateStatus);

		?>
	  		<h2><span>Payment</span> Successful</h2>
			<div class="clr"></div>
	  		<h3><span>Payment</span> Confirmation</h3>
	  		<div>Your payment was successful you will receive your shipment in 7 - 10 days.</div>
	  		<div>Check you email for a receipt from Paypal.</div>
			<script type="text/javascript">
			function showConfirmationNotice()
			{
					var title, message, msg, opts, container;

					title = 'Payment Successful';
					message = 'Thank you for your payment. Your account has been debited for $<?php echo number_format($amt, 2);?>'

					opts = {};
					opts.classes = ['simple'];

					opts.classes.push("pushpin");
					opts.hideStyle = {
						opacity: 0,
						left: "400px"
					};
					opts.showStyle = {
						opacity: 1,
						left: 0
					};

					container = '#freeow';
					$(container).freeow(title, message, opts);
			}
			$(document).ready(function() {
				showConfirmationNotice()
			});
			</script>

		<?php
	}
	else
	{
		//Display a user friendly Error on the page using any of the following error information returned by PayPal
		$ErrorCode = urldecode($resArray["L_ERRORCODE0"]);
		$ErrorShortMsg = urldecode($resArray["L_SHORTMESSAGE0"]);
		$ErrorLongMsg = urldecode($resArray["L_LONGMESSAGE0"]);
		$ErrorSeverityCode = urldecode($resArray["L_SEVERITYCODE0"]);

		$error['details'] = "GetExpressCheckoutDetails API confirmation failed. ";
		$error['msg']['detailed_error_message'] = $ErrorLongMsg;
		$error['msg']['short_error_message'] = $ErrorShortMsg;
		$error['msg']['code'] = $ErrorCode;
		$error['msg']['severe_code'] = $ErrorSeverityCode;

		$result = array('error'=>$error);
		echo '<h2><span>Billing/Payment</span> Error</h2><div class="clr"></div>';

	  	echo '<div class="errors"><h3>'.$error['msg']['detailed_error_message'].' (error code: ' . $ErrorCode . ')</h3></div>';
		updatePurchaseStatus(array('Processed' => 1));
	}

?>
      		</div>
      	</div>
        <?php echo showSidebar(); ?>
    </div>
</div>
<div class="clr"></div>