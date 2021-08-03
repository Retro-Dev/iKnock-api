<?php
$amount = 5.65;
$referenceNumber = 0;
$responseMessage = "Declined";
function isPost($server){
    return (strtoupper($server['REQUEST_METHOD']) == 'POST');
}

function requestSale($token, $amount){
    global $referenceNumber, $responseMessage;
    $client = new SoapClient('https://ps1.merchantware.net/Merchantware/ws/retailTransaction/v4/credit.asmx?WSDL', array('trace' => true));
    $response = $client->SaleVault(
        array(
            'merchantName'           => 'TEST',
            'merchantSiteId'         => 'XXXXXXXX',
            'merchantKey'            => 'XXXXX-XXXXX-XXXXX-XXXXX-XXXXX',
            'invoiceNumber'          => '123',
            'amount'                 => $amount,
            'vaultToken'             => $token,
            'forceDuplicate'         => 'true',
            'registerNumber'         => '123',
            'merchantTransactionId'  => '1234'
        )
    );
    $result = $response->SaleVaultResult;
    $responseMessage = $result->ApprovalStatus;
    $amount = $result->Amount;
    $referenceNumber = $result->Token;
}
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head runat="server">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>PWMS</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/payment.css" rel="stylesheet">

    <script src="http://code.jquery.com/jquery-1.11.3.min.js" type="text/javascript"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js" type="text/javascript"></script>
    <script src="https://ecommerce.merchantware.net/v1/CayanCheckout.js" type="text/javascript"></script>
</head>
<body>

<?php
if(isPost($_SERVER) && $_POST["TokenHolder"]){
    //requestSale($_POST["TokenHolder"], $amount);
}
?>


<div class="container margin-top-10 card-entry-form">

    <?php if($referenceNumber !== 0): ?>
    <div id="ResponseMessageContainer" class="panel panel-success panel-success">
        <div class="panel-heading">Order Results</div>
        <div class="panel-body">
            <p><strong>Status: </strong><span id="ResponseMessage">Approved</span></p>
            <p><strong>Reference #: </strong><span id="ResponseRef"><?php echo $referenceNumber;?></span></p>
        </div>
    </div>
    <?php else: ?>
    <div id="CheckoutPanel" class="panel panel-default">
        <!-- Default panel contents -->
        <div class="panel-heading">Card Information</div>
        <div class="panel-body">
            <div id="LoadingImage" class="form-loading" style="display:none;">
                <img src="../image/wait24.gif" />
            </div>
            <form method="post" id="PaymentForm" class="form-horizontal" role="form">
                <div class="form-group">
                    <label for="CardHolder" id="CardholderLabel" class="control-label col-sm-3">Card Holder Name</label>
                    <div class="col-sm-9">
                        <input name="CardHolder" type="text" id="CardHolder" class="form-control" placeholder="Enter card holder name" data-cayan="cardholder" />
                    </div>
                </div>
                <div class="form-group">
                    <label for="CardNumber" id="CardLabel" class="control-label col-sm-3">Card Number</label>
                    <div class="col-sm-9">
                        <input name="CardNumber" type="text" id="CardNumber" class="form-control" placeholder="Enter card number" data-cayan="cardnumber" />
                    </div>
                </div>
                <div class="form-group">
                    <label for="ExpirationMonth" id="ExpirationDateLabel" class="control-label col-sm-3">Expiration Date</label>
                    <div class="col-sm-4">
                        <select name="ExpirationMonth" id="ExpirationMonth" data-cayan="expirationmonth" class="form-control">
                            <option value="01">01 January</option>
                            <option value="02">02 February</option>
                            <option value="03">03 March</option>
                            <option value="04">04 April</option>
                            <option value="05">05 May</option>
                            <option value="06">06 June</option>
                            <option value="07">07 July</option>
                            <option value="08">08 August</option>
                            <option value="09">09 September</option>
                            <option value="10">10 October</option>
                            <option value="11">11 November</option>
                            <option selected="selected" value="12">12 December</option>
                        </select>
                    </div>
                    <div class="col-sm-5">
                        <select name="ExpirationYear" id="ExpirationYear" data-cayan="expirationyear" class="form-control">
                            <option value="15">2015</option>
                            <option selected="selected" value="16">2016</option>
                            <option value="17">2017</option>
                            <option value="18">2018</option>
                            <option value="19">2019</option>
                            <option value="20">2020</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="CVV" id="CVVLabel" class="control-label col-sm-3">CVV/CVS</label>
                    <div class="col-sm-9">
                        <input name="CVV" type="text" id="CVV" class="form-control" placeholder="Enter the 3 or 4 digit CVV/CVS code" data-cayan="cvv" />
                    </div>
                </div>
                <div class="form-group">
                    <label for="StreetAddress" id="StreetAddressLabel" class="control-label col-sm-3">Billing Address</label>
                    <div class="col-sm-9">
                        <input name="StreetAddress" type="text" id="StreetAddress" class="form-control" placeholder="Enter billing address" data-cayan="streetaddress" />
                    </div>
                </div>
                <div class="form-group">
                    <label for="ZipCode" id="ZipLabel" class="control-label col-sm-3">Zip code</label>
                    <div class="col-sm-9">
                        <input name="ZipCode" type="text" id="ZipCode" class="form-control" placeholder="Enter 5-digit zip-code" data-cayan="zipcode" />
                    </div>
                </div>
                <div class="form-actions">
                    <input type="button" name="SubmitButton" value="Complete Checkout" id="SubmitButton" class="btn btn-primary btn-my" />
                    <input type="button" name="SaleButton" value="Submit Sale" onclick="javascript:__doPostBack(&#39;SaleButton&#39;,&#39;&#39;)" id="SaleButton" class="btn btn-primary" style="display: none;" />
                </div>
                <div id="TokenMessageContainer" class="alert" style="display:none;">
                    <span id="tokenMessage" data-cayan="tokenMessage"></span>
                </div>
                <input name="TokenHolder" type="text" id="TokenHolder" style="display:none;" />
            </form>
        </div>
    </div>
</div>
<?php endif; ?>
</div>
</body>

<script>
    // set credentials to enable use of the API.
    CayanCheckout.setWebApiKey("ABCDEF0123456789");
    function clearTokenMessageContainer(tokenMessageContainer) {
        tokenMessageContainer.removeClass('alert-danger');
        tokenMessageContainer.removeClass('alert-success');
        tokenMessageContainer.removeClass('alert-info');
    }

    function toggleForm() {
        $("#PaymentForm").toggle();
        $("#LoadingImage").toggle();
    }
    // client defined callback to handle the successful token response
    function HandleTokenResponse(tokenResponse) {
        var tokenHolder = $("#TokenHolder");
        if (tokenResponse.token !== "") {
            tokenHolder.val(tokenResponse.token);
            $("input#tokenHolder").val(tokenResponse.token);
        }else{
            toggleForm();
        }
        // Show "waiting" gif
        $("#SaleButtonSpan").html("<img src='content/wait24.gif' />");
        $("#PaymentForm").submit();
    }
    // client-defined callback to handle error responses
    function HandleErrorResponse(errorResponses) {
        toggleForm();
        var errorText = "";
        for (var key in errorResponses) {
            errorText += " Error Code: " + errorResponses[key].error_code + " Reason: " + errorResponses[key].reason + "\n";
        }
        alert(errorText);
    }
    // create a submit action handler on the payment form, which calls CreateToken
    $("#SubmitButton").click(function (ev) {
        toggleForm();
        CayanCheckout.createPaymentToken({ success: HandleTokenResponse, error: HandleErrorResponse });
        // AJAX SOAP request here
        ev.preventDefault();
    });
</script>
</html>
