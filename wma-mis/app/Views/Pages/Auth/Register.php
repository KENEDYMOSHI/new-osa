<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="<?=base_url()?>/assets/images/WMA.png" type="image/x-icon">
    <link rel="stylesheet" href="<?=base_url()?>/dist/css/adminlte.min">
    <link rel="stylesheet" href="<?=base_url()?>/dist/css/custom.css">
    <link rel="stylesheet" href="<?=base_url()?>/dist/css/bootstrap.css">
    <link rel="stylesheet" href="<?=base_url()?>/plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="<?=base_url()?>/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
    <script src="<?=base_url()?>/plugins/jquery/jquery.min.js"></script>
    <script src="<?=base_url()?>/dist/js/bootstrap.js"></script>
    <title><?=$page['title']?></title>
</head>

<body>
    <main class="login">
        <!-- Carousel -->

        <!-- Login -->
        <section class="login__right">
            <div class="panel">
                <article class="panel__header">
                    <div class="header__brand">
                        <div class="header__logo">
                            <img src="<?=base_url()?>/assets/images/wma1.png" alt="">
                        </div>

                    </div>
                </article>
                <?php
$pageSession = \CodeIgniter\Config\Services::session();
?>

                <?php if ($pageSession->getFlashdata('Success')): ?>
                <div id="message" class="alert alert-success text-center" role="alert">
                    <?=$pageSession->getFlashdata('Success');?>
                </div>
                <?php endif;?>
                <?php if ($pageSession->getFlashdata('error')): ?>
                <div id="message" class="alert alert-danger text-center" role="alert">
                    <?=$pageSession->getFlashdata('error');?>
                </div>
                <?php endif;?>
                <?=form_open()?>
                <div class="sign text-center">
                    <p class="sign__input">Create an account</p>

                    <div class="row">
                        <div class="form-group col-md-6">
                            <input type="text" name="firstname" class="form-control" placeholder="First Name"
                                value="<?=set_value('firstname')?>">
                            <span class="text-danger"><?=displayError($validation, 'firstname')?></span>
                        </div>
                        <div class="form-group col-md-6">
                            <input type="text" name="lastname" class="form-control" placeholder="Last Name"
                                value="<?=set_value('lastname')?>">
                            <span class="text-danger"><?=displayError($validation, 'lastname')?></span>
                        </div>

                        <div class="form-group col-md-6">
                            <input type="text" name="email" class="form-control" placeholder="Email Address"
                                value="<?=set_value('email')?>">
                            <span class="text-danger"><?=displayError($validation, 'email')?></span>
                        </div>
                        <div class="form-group col-md-6">
                            <select id="region" class="form-control select2bs4" name="city">
                                <option disabled>Select Region</option>
                                <?php foreach (renderRegions() as $region): ?>
                                <option value="<?=$region['region']?>"><?=$region['region']?></option>
                                <?php endforeach;?>
                                <span class="text-danger"><?=displayError($validation, 'city')?></span>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <input type="password" name="password" class="form-control" placeholder="Password"
                            value="<?=set_value('password')?>">
                        <span class="text-danger"><?=displayError($validation, 'password')?></span>
                    </div>
                    <div class="form-group">
                        <input type="password" name="confirmpassword" class="form-control"
                            placeholder="Confirm Password" value="<?=set_value('confirmpassword')?>">
                        <span class="text-danger"><?=displayError($validation, 'confirmpassword')?></span>
                    </div>

                </div>

                <div class="option">
                    <div class="option__item">
                        <button type="submit" class="button">Register</button>
                    </div>

                </div>
                <div class="account text-center">
                    <p> I already have an account</p>
                    <a href="<?=base_url()?>/login" class="link-text">Login</a>
                </div>
                </form>

            </div>
        </section>
    </main>
    <script src="<?=base_url()?>/plugins/select2/js/select2.full.min.js"></script>
    <script>
    setInterval(function() {
        $('#message').fadeOut(7000)
    });

    //Initialize Select2 Elements
    $('.select2').select2()

    //Initialize Select2 Elements
    $('.select2bs4').select2({
        theme: 'bootstrap4'
    });
    </script>

</body>

</html>