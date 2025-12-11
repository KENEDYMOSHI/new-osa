<?php $this->extend('layouts/auth'); ?>
<?php $this->section('content'); ?>
<?php
$pageSession = \CodeIgniter\Config\Services::session();
?>

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


<div>
    <div>
        <div class="card-body register-card-body rounded ">
            <p class="login-box-msg">Log In</p>

            <?= form_open() ?>


            <div class="input-group mt-3">
                <input type="text" name="email" class="form-control" placeholder="Email" value="<?= set_value('email') ?>">
                <div class="input-group-append">
                    <div class="input-group-text">
                        <span class="fal fa-envelope"></span>
                    </div>
                </div>

            </div>
            <span class="text-danger"><?= displayError($validation, 'email') ?></span>
            <div class="input-group mt-3">
                <input type="password" name="password" class="form-control" placeholder="Password" value="<?= set_value('password') ?>">
                <div class="input-group-append">
                    <div class="input-group-text">
                        <span class="fal fa-lock"></span>
                    </div>
                </div>
            </div>
            <span class="text-danger"><?= displayError($validation, 'password') ?></span>

            <div class="row">

                <!-- /.col -->
                <div class="col-12">
                    <button type="submit" style="background: #db611e;" class="button">Login</button>
                </div>
                <!-- /.col -->
            </div>
            </form>



        </div>
        <!-- /.form-box -->
    </div>
</div>

</div>


<?php $this->endSection(); ?>