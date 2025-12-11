<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="<?=getImage('assets/images/wma1.png')  ?>" type="image/x-icon">
    <title>Link Expired</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css" integrity="sha512-jnSuA4Ss2PkkikSOLtYs8BlYIeeIK1h99ty4YfvRPAlzr377vr3CXDb7sb7eEEBYjDtcYj+AjBH3FLv5uSJuXg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        body {
            background: #FFF8E8 !important;
            font-size: small;

        }

        .center-content {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
            text-align: center;
        }
    </style>
</head>

<body>
    <section class="mb-4">
        <div class="container">
            <div class="row mt-5 justify-content-center text-center">
                <div class="col-md-3 col-xs-2 mb-2">
                    <div class="logo">
                        <img src="<?= getImage('assets/images/emblem.png')  ?>" alt="Logo" width="70" />
                    </div>
                </div>
                <div class="col-md-6 col-xs-6">
                    <div class="heading">
                        <h6><b>THE UNITED REPUBLIC OF TANZANIA</b></h6>
                        <h6><b>MINISTRY OF INDUSTRY AND TRADE</b></h6>
                        <h6>WEIGHTS AND MEASURES AGENCY</h6>
                    </div>
                </div>
                <div class="col-md-3 col-xs-2 mb-2">
                    <div class="logo">
                        <img src="<?= getImage('assets/images/wma1.png')  ?>" alt="Logo" style="max-width: 100px;" />
                    </div>
                </div>
            </div>
        </div>
        
        <hr>
    </section>
    <section>
        <div class="container">
            <div class="center-content">
                <div>
                    <img src="<?= getImage('assets/images/expired.png')  ?>" width="150" alt="Invalid Certificate" class="img-fluid mb-4" />
                    <h5 class="text-center"><?=$message ?></h5>
                </div>
            </div>


        </div>
    </section>





</body>

</html>