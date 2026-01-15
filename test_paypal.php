<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PayPal Connection Test</title>
    <!-- Simple styling -->
    <style> body { font-family: sans-serif; padding: 50px; text-align: center; } .box { max-width: 400px; margin: 0 auto; padding: 20px; border: 1px solid #ccc; border-radius: 10px; } </style>
</head>
<body>

<div class="box">
    <h3>PayPal Sandbox Test</h3>
    <p>Price: $1.00 USD</p>
    <div id="paypal-button-container"></div>
    <div id="msg" style="margin-top:20px;"></div>
</div>

<?php require_once 'config/config.php'; ?>

<!-- Load PayPal SDK with Debugging -->
<script src="https://www.paypal.com/sdk/js?client-id=<?php echo PAYPAL_CLIENT_ID; ?>&currency=USD&debug=true"></script>

<script>
    paypal.Buttons({
        createOrder: function(data, actions) {
            return actions.order.create({
                purchase_units: [{
                    amount: {
                        value: '1.00'
                    }
                }]
            });
        },
        onApprove: function(data, actions) {
            return actions.order.capture().then(function(details) {
                alert('Transaction completed by ' + details.payer.name.given_name);
                document.getElementById('msg').innerText = "Success! ID: " + details.id;
            });
        },
        onError: function (err) {
            console.error(err);
            document.getElementById('msg').innerText = "Error: Check Console log";
            document.getElementById('msg').style.color = "red";
        }
    }).render('#paypal-button-container');
</script>

</body>
</html>
