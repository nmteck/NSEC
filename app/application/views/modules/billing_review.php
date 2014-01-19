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
// Check to see if the Request object contains a variable named 'token'
$token = "";
if ($this->session->userdata('TOKEN'))
{
	$token = $this->session->userdata('TOKEN');
}

// If the Request object contains the variable 'token' then it means that the user is coming from PayPal site.
if ( $token != "" )
{


	/*
	'------------------------------------
	' Calls the GetExpressCheckoutDetails API call
	'
	' The GetShippingDetails function is defined in PayPalFunctions.jsp
	' included at the top of this file.
	'-------------------------------------------------
	*/


	$resArray = GetShippingDetails( $token );
	$ack = strtoupper($resArray["ACK"]);
	if( $ack == "SUCCESS" || $ack == "SUCESSWITHWARNING")
	{
		/*
		' The information that is returned by the GetExpressCheckoutDetails call should be integrated by the partner into his Order Review
		' page
		*/
		$email 				= isset($resArray["EMAIL"]) ? $resArray["EMAIL"] : NULL; // ' Email address of payer.
		$payerId 			= isset($resArray["PAYERID"]) ? $resArray["PAYERID"] : NULL; // ' Unique PayPal customer account identification number.
		$payerStatus		= isset($resArray["PAYERSTATUS"]) ? $resArray["PAYERSTATUS"] : NULL; // ' Status of payer. Character length and limitations: 10 single-byte alphabetic characters.
		$salutation			= isset($resArray["SALUTATION"]) ? $resArray["SALUTATION"] : NULL; // ' Payer's salutation.
		$firstName			= isset($resArray["FIRSTNAME"]) ? $resArray["FIRSTNAME"] : NULL; // ' Payer's first name.
		$middleName			= isset($resArray["MIDDLENAME"]) ? $resArray["MIDDLENAME"] : NULL; // ' Payer's middle name.
		$lastName			= isset($resArray["LASTNAME"]) ? $resArray["LASTNAME"] : NULL; // ' Payer's last name.
		$suffix				= isset($resArray["SUFFIX"]) ? $resArray["SUFFIX"] : NULL; // ' Payer's suffix.
		$cntryCode			= isset($resArray["COUNTRYCODE"]) ? $resArray["COUNTRYCODE"] : NULL; // ' Payer's country of residence in the form of ISO standard 3166 two-character country codes.
		$business			= isset($resArray["BUSINESS"]) ? $resArray["BUSINESS"] : 'N/A'; // ' Payer's business name.
		$shipToName			= isset($resArray["SHIPTONAME"]) ? $resArray["SHIPTONAME"] : NULL; // ' Person's name associated with this address.
		$shipToStreet		= isset($resArray["SHIPTOSTREET"]) ? $resArray["SHIPTOSTREET"] : NULL; // ' First street address.
		$shipToStreet2		= isset($resArray["SHIPTOSTREET2"]) ? $resArray["SHIPTOSTREET2"] : NULL; // ' Second street address.
		$shipToCity			= isset($resArray["SHIPTOCITY"]) ? $resArray["SHIPTOCITY"] : NULL; // ' Name of city.
		$shipToState		= isset($resArray["SHIPTOSTATE"]) ? $resArray["SHIPTOSTATE"] : NULL; // ' State or province
		$shipToCntryCode	= isset($resArray["SHIPTOCOUNTRYCODE"]) ? $resArray["SHIPTOCOUNTRYCODE"] : NULL; // ' Country code.
		$shipToZip			= isset($resArray["SHIPTOZIP"]) ? $resArray["SHIPTOZIP"] : NULL; // ' U.S. Zip code or other country-specific postal code.
		$addressStatus 		= isset($resArray["ADDRESSSTATUS"]) ? $resArray["ADDRESSSTATUS"] : NULL; // ' Status of street address on file with PayPal
		$invoiceNumber		= isset($resArray["INVNUM"]) ? $resArray["INVNUM"] : NULL; // ' Your own invoice or tracking number, as set by you in the element of the same name in SetExpressCheckout request .
		$phonNumber			= isset($resArray["PHONENUM"]) ? $resArray["PHONENUM"] : 'N/A'; // ' Payer's contact telephone number. Note:  PayPal returns a contact telephone number only if your Merchant account profile settings require that the buyer enter one.

		savePayerInformation($resArray);
		?>
    			<h2>NoWayNoHow.Net Merchandise purchase</h2>
          		<div class="clr"></div>
    			<h2>Order:</h2>
          		<div class="clr"></div>
    			<div>
    				<?php echo orderDetails(); ?>
    			</div>
    			<p class="review">
    				<h2>Shipping Information:</h2>
          			<div class="clr"></div>
          			<img style="float: right;" src="/-/img/shirts/gray_shirt_thumb.jpg"
          				alt="Rob Parker - No Way No How Gray Shirt" style="border: 0px none;" width="200" height="197" border="0">
    				<label>Contact:</label> <?php echo $firstName.' '.$lastName;?><br />
    				<label>Company:</label> <?php echo $business;?><br />
    				<label>Email:</label> <?php echo $email;?><br />
    				<label>Location:</label> <?php echo $shipToCity.', '.$shipToState.' ('.$cntryCode;?>)<br />
    				<label>Phone:</label> <?php echo $phonNumber; ?><br /><br />
    				<h3>Sub-Total: $<?php echo number_format($this->session->userdata("Payment_Amount"), 2);?></h3>
    				<h3>Shipping: $<?php echo number_format(calculateShipping(), 2);?></h3><hr />
    				<h2>Total: $<?php echo number_format((calculateShipping() + $this->session->userdata("Payment_Amount")), 2);?></h2>
					<div class="clr"></div>
    				<h2><a style="color: #000" href="/billing/confirmation"><span>*Confirm Purchase & Continue</span></a></h2>
          			<div class="clr"></div>
          			<sup>* Payments will show on your statement as New Millennium Tecknology (NMTeck)</sup>
    			</p>

		<?

	}
	else
	{
		//Display a user friendly Error on the page using any of the following error information returned by PayPal
		$ErrorCode = urldecode($resArray["L_ERRORCODE0"]);
		$ErrorShortMsg = urldecode($resArray["L_SHORTMESSAGE0"]);
		$ErrorLongMsg = urldecode($resArray["L_LONGMESSAGE0"]);
		$ErrorSeverityCode = urldecode($resArray["L_SEVERITYCODE0"]);

		echo "GetExpressCheckoutDetails API call failed. ";
		echo "Detailed Error Message: " . $ErrorLongMsg;
		echo "Short Error Message: " . $ErrorShortMsg;
		echo "Error Code: " . $ErrorCode;
		echo "Error Severity Code: " . $ErrorSeverityCode;
	}
}

?>
      		</div>
      	</div>
    </div>
</div>
<div class="clr"></div>