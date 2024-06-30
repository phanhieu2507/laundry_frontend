<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            background-color: #ffffff;
            width: 80%;
            margin: 20px auto;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.05);
        }
        .header {
            background-color: #007BFF;
            color: #ffffff;
            padding: 10px;
            text-align: center;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
        }
        .content {
            padding: 20px;
            text-align: center;
        }
        .footer {
            text-align: center;
            padding: 10px;
            font-size: 12px;
            color: #888;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            margin-top: 20px;
            background-color: #28a745;
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            Promo Code Gift Notification
        </div>
        <div class="content">
            <h1>You've Received a Promo Code!</h1>
            <p>Hello,</p>
            <p>You have been gifted <strong>{{ $quantity }}</strong> promo code(s). Please check your promo code section in your account for more details.</p>
            <a href="http://yourwebsite.com/user/promo-codes" class="button">View My Promo Codes</a>
        </div>
        <div class="footer">
            This is an automated message, please do not reply.
        </div>
    </div>
</body>
</html>
