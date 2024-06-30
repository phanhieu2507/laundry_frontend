<!DOCTYPE html>
<html>
<head>
    <title>Request Status Updated</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .email-container {
            background-color: #ffffff;
            width: 100%;
            max-width: 600px;
            margin: 40px auto;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.05);
        }
        h1 {
            color: #333;
            font-size: 24px;
            text-align: center;
        }
        p {
            color: #555;
            font-size: 16px;
            line-height: 1.5;
            margin: 10px 0;
        }
        .highlight {
            color: #007BFF;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <h1>Your Request Status has been Updated</h1>
        <p>Hello,</p>
        <p>Your request with ID: <span class="highlight">{{ $requestOrder->request_order_id }}</span> has been updated to: <span class="highlight">{{ $requestOrder->status }}</span>.</p>
    </div>
</body>
</html>
