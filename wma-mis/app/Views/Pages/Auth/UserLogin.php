<!DOCTYPE html>
<html>

<!-- Added by HTTrack -->
<meta http-equiv="content-type" content="text/html;charset=UTF-8" /><!-- /Added by HTTrack -->

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Weights and Measures Agency Management Information System">
    <meta name="keywords" content="wmamis, wma,vipimo,Weights and Measures Agency">

    <meta name="viewport" content="width=device-width,
                             initial-scale=1">
    <!-- <link rel="stylesheet" href="gfg-style.css"> -->
    <title>Weights and Measures Agency Management Information System</title>
    <link rel="icon" type="image/x-icon" href="<?= base_url('assets/images/wma.ico') ?>">

    <link href="<?= base_url('authAssets/css/bootstrap.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('authAssets/font-awesome/css/font-awesome.css') ?>" rel="stylesheet">
    <link href="<?= base_url('authAssets/css/plugins/iCheck/custom.css') ?>" rel="stylesheet">

 
    <link href="<?= base_url('authAssets/css/style.css') ?>" rel="stylesheet">

    <link href="<?= base_url('authAssets/css/bootstrap.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('authAssets/font-awesome/css/font-awesome.css') ?>" rel="stylesheet">

    <link href="<?= base_url('authAssets/css/style.css') ?>" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/fontawesome.min.css" integrity="sha512-RvQxwf+3zJuNwl4e0sZjQeX7kUa3o82bDETpgVCH2RiwYSZVDdFJ7N/woNigN/ldyOOoKw8584jM4plQdt8bhA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="<?= base_url('authAssets/css/login.css') ?>" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.8.1/css/all.min.css" integrity="sha512-gMjQeDaELJ0ryCI+FtItusU9MkAifCZcGq789FrzkiM49D8lbDhoaUaIX4ASU187wofMNlgBJ4ckbrXM9sE6Pg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <style>
        .primary{
            background-color: #DB611E;
            border: #DB611E 1px;
            color: #fff;
        }
        .primary:hover{
            background-color: #c9571a;
            border: #c9571a 1px; 
            color: #fff;
        }
        .primary:focus{
            background-color: #c9571a;
            border: #c9571a 1px; 
            color: #fff;
        }
    </style>
</head>
<?php $pageSession = \CodeIgniter\Config\Services::session(); ?>

<body class="gray-bg">
    <div class="middle-box text-center loginscreen animated fadeInDown">
        <div class="login">
            <!-- <div class="col-lg-12 header" style="text-align: center;">
                <div class="col-lg-3 logo">
                    <img src="<?= base_url() ?>/authAssets/img/ngao.png" alt="">
                </div> -->
            <div>
                <h3>The United Republic of Tanzania</h3>
                <h2>Weights and Measures Agency</h2>
                <h2>Management Information System (WMA-MIS)</h2>
            </div>

        </div>
        <div class="ibox float-e-margins">
            <div class="ibox-content">
                <div class="row">
                    <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12  no-padding b-r" id="slider">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding carousel slide" id="carousel2">
                            <ol class="carousel-indicators">
                                <li data-slide-to="0" data-target="#carousel2" class="active"></li>
                                <li data-slide-to="1" data-target="#carousel2"></li>
                                <li data-slide-to="2" data-target="#carousel2"></li>
                                <!--  <li data-slide-to="3" data-target="#carousel2"></li> -->

                            </ol>
                            <div class="carousel-inner">
                                <div class="item active">
                                    <a href="#">
                                        <img alt="image" class="img-responsive" src="<?= base_url() ?>/authAssets/img/slide1.png">
                                    </a>
                                    <div class="carousel-caption">
                                        <p></p>
                                    </div>
                                </div>
                                <div class="item ">
                                    <a href="#">
                                        <img alt="image" class="img-responsive" src="<?= base_url() ?>/authAssets/img/slide2.png">
                                    </a>
                                    <div class="carousel-caption">
                                        <p></p>
                                    </div>
                                </div>

                             
                                <div class="item ">
                                    <a href="#">
                                        <img alt="image" class="img-responsive" src="<?= base_url() ?>/authAssets/img/slide3.png">
                                    </a>
                                    <div class="carousel-caption">
                                        <p></p>
                                    </div>
                                </div>
                                <div class="item ">
                                    <a href="#">
                                        <img alt="image" class="img-responsive" src="<?= base_url() ?>/authAssets/img/slide4.png">
                                    </a>
                                    <div class="carousel-caption">
                                        <p></p>
                                    </div>
                                </div>
                            </div>
                            <a data-slide="prev" href="#carousel2" class="left carousel-control">
                                <span class="icon-prev"></span>
                            </a>
                            <a data-slide="next" href="#carousel2" class="right carousel-control">
                                <span class="icon-next"></span>
                            </a>
                        </div>

                    </div>

                    <div class="col-lg-6 col-sm-12 col-xs-12 col-md-12">
                        <br><br>
                        <?php if ($pageSession->getFlashdata('Success')) : ?>
                            <div id="message" class="alert alert-success text-center" role="alert">
                                <?= $pageSession->getFlashdata('Success'); ?>
                            </div>
                        <?php endif; ?>
                        <?php if ($pageSession->getFlashdata('error')) : ?>
                            <div id="message" class="alert alert-danger text-center" role="alert">
                                <?= $pageSession->getFlashdata('error'); ?>
                            </div>
                        <?php endif; ?>

                        <h4>Login into your account</h4>
                        <br><br>




                        <?= form_open(base_url('loginAction')) ?>
                        <div class="input">
                            <div class="input-group ">
                                <span class="input-group-btn">
                                    <a class="btn btn-primary primary" ><i class="fa fa-envelope"></i></a>
                                </span>
                                <input type="text" class="form-control" placeholder="Enter your email" name="email" maxlength="35" value="<?= set_value('email') ?>">
                            </div>
                            <span class="text-danger"><?= displayError($validation, 'email') ?></span>
                        </div>

                        <div class="input-group ">
                            <span class="input-group-btn">
                                <a class="btn btn-primary primary" ><i class="fa fa-key"></i></i></a>
                            </span>
                            <input type="password" name="password" class="form-control" placeholder="Enter your password" maxlength="20" value="<?= set_value('password') ?>" autocomplete="off">
                        </div>
                        <span class="text-danger"><?= displayError($validation, 'password') ?></span>

                        <div>

                            <button type="submit" class="btn m-b primary"><i class="fa fa-right-to-bracket"></i></i> Login</button>
                            <!-- <a href="#" class="pull-left"><small>Forgot Password?</small></a> -->
                        </div>
                    </div>




                    <div class="footer-content col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="copyright">
                            <p class="text-center"><small>For any Technical inquiry, Please contact your ICT Support Team at :</small>
                                <small><a href="http://ictsupport@wma.go.tz/" target="_blank">ictsupport@wma.go.tz</a> © <?=date('Y') ?> WEIGHTS AND MEASURES AGENCY All Rights Reserved.</small>
                                <small>

                                    <a href="https://www.wma.go.tz/" target="_blank">Main Website</a> |
                                    <a href="https://www.wma.go.tz/footers/disclaimer/" target="_blank">Disclaimer</a> |
                                    <a href="https://www.wma.go.tz/footers/policy/" target="_blank">Privacy Policy</a> |
                                    <a href="https://www.wma.go.tz/sitemap/" target="_blank">Sitemap</a> |
                                    <a href="https://mail.wma.go.tz" target="_blank">Staff Mail</a> |
                                    <a data-toggle="modal" data-target="#version" title="Version 0.1">Version 0.1 |</a></small>
                                    <a  class="btn btn-xs mt-2 primary" href="public/System_Access_Request_Form.pdf" download="public/System_Access_Request_Form.pdf" title="Access Form">System Access Form</a>
                                </small>

                            </p>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    </div>
    <script>
        setInterval(function() {
            $('#message').fadeOut(7000)
        });

        setTimeout(function() {
            location.reload();
        }, 600000);
    </script>

    
    

</body>