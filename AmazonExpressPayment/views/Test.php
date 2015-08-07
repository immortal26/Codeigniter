<html>

<head>
    <!--<script type='text/javascript' src="https://static-na.payments-amazon.com/OffAmazonPayments/us/sandbox/js/Widgets.js"></script>-->
    <!--<script type='text/javascript' src="https://static-na.payments-amazon.com/OffAmazonPayments/us/js/Widgets.js"></script>-->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>

    <style>
        label,
        div {
            margin: 10px;
        }
    </style>
</head>

<body>
<input type="hidden" name="csrf" id="csrf" value="<?php echo $token; ?>">
<label id="itemname" for="tshirt">Item Name: Long Sleeve Tee</label>
<div id="amount" value="100">Price: $100</div>

<label for="QuantitySelect">Qty:</label>
<select id="QuantitySelect">
    <option value="1">1</option>
    <option value="2">2</option>
    <option value="3">3</option>
    <option value="4">4</option>
</select>

<div id="AmazonPayButton"></div>

<script type="text/javascript">
    OffAmazonPayments.Button("AmazonPayButton", "A1DGE38F8TJYVO", {

        type: "hostedPayment",

        hostedParametersProvider: function(done) {

            $.getJSON("<?php echo base_url('Test/aws_signature') ?>", {
                amount: parseInt($("#amount").attr("value")) * parseInt($("#QuantitySelect option:selected").val()),
                currencyCode: 'USD',
                sellerNote: $("#itemname").text() + ' QTY: ' + $("#QuantitySelect option:selected").val(),
                csrf:$("#csrf").val()

            }, function(data) {
                done(data);
            })
        },
        onError: function(errorCode) {
            console.log(errorCode.getErrorCode() + " " + errorCode.getErrorMessage());
        }
    });
</script>
</body>
<html>