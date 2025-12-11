<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="<?=base_url('assets/images/wma1.png') ?>" type="image/x-icon">
    <title>Blocked</title>
    <!-- Bootstrap Icons CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #f07f19;
            --secondary-color: #d86c08;
            --accent-color: #34495e;
            --light-color: #fff7e6;
            --dark-color: #333;
            --bg-color: #f9f3e0;
        }
        
        body {
            font-family: "Poppins", sans-serif;
            background-color: var(--bg-color);
            margin: 0;
            padding: 0;
            color: var(--dark-color);
        }
        
        .container {
            max-width: 900px;
            margin: 40px auto;
            padding: 20px;
        }
        
        .license-card {
            background-color: #fffbf0;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            border: 1px solid #e6d7b5;
        }
        
        .license-header {
            background-color: var(--primary-color);
            color: white;
            padding: 20px;
            text-align: center;
            position: relative;
        }
        
        .logo {
            /*width: 100px;*/
            /*height: 100px;*/
            margin: 0 auto 15px;
            display: flex;
            align-items: center;
            justify-content: center;
           
        }
        
        .logo img {
            width: 70px;
            height: 70px;
            background: #fff;
            border-radius: 50%;
             padding: 5px;
        }
        
        .license-header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        
        .license-header h4 {
            margin: 10px 0 5px;
            font-size: 18px;
            font-weight: 600;
        }
        
        .license-header h5 {
            margin: 5px 0 10px;
            font-size: 16px;
            font-weight: 500;
            opacity: 0.9;
        }
        
        .license-header p {
            margin: 10px 0 0;
            opacity: 0.9;
        }
        
        .license-body {
          text-align: center;
          padding: 20px;
             /* background-image: linear-gradient(0deg, rgba(240, 127, 25, 0.03) 1px, transparent 1px); */
            background-size: 100% 30px;
        }
        
        .license-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
        }
        
        .info-group {
            margin-bottom: 20px;
        }
        
        .info-group h3 {
            display: flex;
            align-items: center;
            margin: 0 0 8px 0;
            color: var(--dark-color);
            font-size: 16px;
            font-weight: 600;
        }
        
        .info-group h3 i {
            color: var(--primary-color);
            margin-right: 10px;
            width: 20px;
            font-size: 18px;
        }
        
        .info-group p {
            margin: 0;
            padding-left: 30px;
            color: #444;
        }
        
        .license-footer {
            background-color: var(--light-color);
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            /* border-top: 1px solid #e6d7b5; */
        }
        
        .date-info {
            color: var(--dark-color);
            font-weight: 600;
            display: flex;
            align-items: center;
        }
        
        .date-info i {
            margin-right: 8px;
            color: var(--primary-color);
            font-size: 18px;
        }
        
        .expiry {
            color: var(--accent-color);
            font-weight: 600;
            display: flex;
            align-items: center;
        }
        
        @media (max-width: 768px) {
            .license-info {
                grid-template-columns: 1fr;
            }
            
            .license-footer {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .date-info:last-child {
                margin-top: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="license-card">
            <div class="license-header">
                <div class="logo">
                    <img src="<?=base_url('assets/images/wma1.png') ?>" alt="WMA Logo">
                </div>
                <h4>UNITED REPUBLIC OF TANZANIA</h4>
                <h5>WEIGHTS AND MEASURE AGENCY</h5>
            </div>
            
            <div class="license-body">
                <p class="text-center"> <i class="bi bi-exclamation-octagon-fill"></i> Too Many Requests Please Please Try Again Later</p>
              
            </div>
            
            
        </div>
    </div>
</body>
</html>