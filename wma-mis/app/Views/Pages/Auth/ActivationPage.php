<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="<?= base_url() ?>/assets/images/wma1.png" type="image/x-icon">
    <link rel="stylesheet" href="<?= base_url() ?>/dist/css/adminlte.min">
    <link rel="stylesheet" href="<?= base_url() ?>/dist/css/custom.css">
    <link rel="stylesheet" href="<?= base_url() ?>/dist/css/bootstrap.css">
    <script src="<?= base_url() ?>/plugins/jquery/jquery.min.js"></script>
    <script src="<?= base_url() ?>/dist/js/bootstrap.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@200;300;400;500;600&display=swap" rel="stylesheet">
    <title><?= $page['title'] ?></title>
    <style>
        body {
            overflow: hidden;
            /* height: 100%; */
            font-family: 'Nunito', sans-serif;
        }

        h5 {
            margin: 0;
            font-size: 1rem;
            font-weight: 600;
        }





        .authentication {
            margin-top: 3.4rem;
            height: 350px;
            /* background: green; */
            box-shadow: rgba(0, 0, 0, 0.24) 0px 3px 8px;
            padding: 0;
            border-radius: 5px;
            /* width: 60vw; */
            overflow: hidden;
            /* margin-right: -1rem; */
        }

        /* #box-x {
            display: block;
            width: 500px !important;
        } */

        .slide {
            height: 350px;
            overflow: hidden;
        }

        .slide img {
            height: 350px;
            width: 100%;
            object-fit: cover;
            overflow: hidden;
        }

        .form-box {
            padding-right: 2.5rem;
            padding-top: 4rem;
            /* width: 100%; */
        }

        .mg {
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }



        @media screen and (max-width:640px) {
            .hide {
                display: none;
            }

            body {
                height: 100%;
            }

            h5 {
                margin: 0;
                font-size: 0.6rem;
            }

            .authentication {
                margin-top: 2rem;
                width: 90vw;
                /* height: 100%; */
                /* background: green; */
                /* overflow: hidden; */
                box-shadow: rgba(0, 0, 0, 0.24) 0px 3px 8px;
                padding: 0;
                margin-left: 0;

            }

            .mg {
                padding: 2rem;

            }

            .form-box {
                padding: 2.5rem;

            }

            /* .auth-nav {
                padding: 0;

            } */

            /* #login {
                padding: 2rem;
                di
            } */
        }
    </style>
</head>

<body>
    <?php $pageSession = \CodeIgniter\Config\Services::session(); ?>
    <nav class="auth-nav">
        <img class="auth-logo" src="<?= base_url() ?>/assets/images/emblem.png" alt="">
        <div class="heading text-center">
            <h5>THE UNITED REPUBLIC OF TANZANIA</h5>
            <h5>WEIGHTS AND MEASURES AGENCY</h5>
            <h5>MANAGEMENT INFORMATION SYSTEM (WMA-MIS)</h5>
        </div>
        <img class="auth-logo" src="<?= base_url() ?>/assets/images/wma1.png" alt="">
    </nav>
    <main class="login">
        <main class="login">
            <!-- Carousel -->

            <!-- Login -->
            <section class="login__right elevation-2">
                <div class="panel ">
                    <article class="panel__header">
                        <div class="header__brand">
                            <!-- <div class="header__logo">
                                <img src="<?= base_url() ?>/assets/images/wma1.png" alt="">
                            </div> -->

                        </div>
                    </article>

                    <?php if (session()->getFlashdata('error')) : ?>
                        <div class="alert alert-danger">
                            <?= session()->getFlashdata('error') ?>
                        </div>
                    <?php endif; ?>


                    <div class="auth-box">


                        <?= form_open() ?>
                        <div class="sign text-center">
                            <p class="sign__input">Update Your Account Password </p>

                            <?php if ($contact == 1) : ?>

                                <div class="form-group">
                                    <input type="tel" name="phone" class="form-control" placeholder="Enter Phone Number" value="<?= set_value('phone') ?>">
                                    <span class="text-danger"><?= displayError($validation, 'phone') ?></span>
                                </div>

                            <?php endif; ?>

                            <div class="form-group">
                                <input type="password" name="password" class="form-control" placeholder="Enter Password" value="<?= set_value('password') ?>">
                                <span class="text-danger"><?= displayError($validation, 'password') ?></span>
                            </div>
                            <div class="form-group">
                                <input type="password" name="confirm-password" class="form-control" placeholder="Confirm password" value="<?= set_value('confirm-password') ?>">
                                <span class="text-danger"><?= displayError($validation, 'confirm-password') ?></span>
                            </div>
                        </div>

                        <div class="option">
                            <div class="option__item">
                                <button style="background: #DB611E;" type="submit" class="button">Submit</button>
                            </div>

                        </div>

                        </form>
                    </div>


                </div>
            </section>
        </main>

        <script>
            setInterval(function() {
                $('#message').fadeOut(7000)
            });
        </script>

</body>

</html>