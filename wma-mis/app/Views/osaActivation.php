<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to OSA</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            /* background-color: #F5803E; */
        }

        .container {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        .header {
            text-align: center;
            color: #F5803E;
            border-bottom: 2px solid #F5803E;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
        }

        .activation-button {
            display: block;
            width: 200px;
            margin: 25px auto;
            padding: 12px 20px;
            background-color: #F5803E;
            color: #fff;
            text-align: center;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .activation-button:hover {
            background-color: #e4702d;
        }

        .content {
            color: #333;
            margin-bottom: 20px;
        }

        .footer {
            text-align: center;
            margin-top: 20px;
            color: #666;
            font-size: 0.8em;
            border-top: 1px solid #F5803E;
            padding-top: 15px;
        }

        .logo-placeholder {
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="logo-placeholder">
            <h1>OSA</h1>
        </div>

        <div class="header">
            <h1>Welcome to OSA</h1>
        </div>

        <div class="content">
            <p>Hello <?= $name ?></p>

            <p>Welcome to the OSA system! We're thrilled to have you join our platform. To complete your registration and activate your account, please click the button below:</p>

            <a href="<?= $link ?>" class="activation-button">Activate Your Account</a>

            <!-- <p>If the button above doesn't work, please copy and paste the following link into your web browser:</p> -->

            <!-- <p style="word-break: break-all;">[ACTIVATION_LINK]</p> -->

            <p>This activation link will expire in 2 hours. If you did not create an account, please disregard this email.</p>
        </div>

        <div class="footer">
            <p>© <?= date('Y') ?> Weights And Measures Agency. All rights reserved.</p>
            <p>Need help? Contact our support at ictsupport@wma.go.tz</p>
        </div>
    </div>
</body>

</html>