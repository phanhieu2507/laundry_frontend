<!DOCTYPE html>
<html>
<head>
    <title>Order Confirmation</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 40px auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        h1 {
            color: #4CAF50;
        }
        p {
            font-size: 16px;
            line-height: 1.5;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <h1>Thank you for your order!</h1>
        <p>Your order with ID: <strong>{{ $orderDetails->order_id }}</strong> has been successfully placed.</p>
        <p>Total Amount: <strong>${{ number_format($orderDetails->total_amount, 2) }}</strong></p>
        <div class="footer">
            If you have any questions, please reply to this email or contact our customer service.
        </div>
    </div>
</body>
</html>
