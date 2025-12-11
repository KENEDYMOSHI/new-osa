<?= $this->extend('Layouts/coreLayout'); ?>
<?= $this->section('profileDetails'); ?>
<li class="nav-item dropdown d-sm-inline-block mr-4">
    <a href="#" data-toggle="dropdown">
        <?php if ( auth()->user()->avatar != '') : ?>
            <img class="avatar img-circle elevation-3" src="<?=  auth()->user()->avatar ?>" alt="User profile picture">
        <?php else : ?>
            <img class="avatar img-circle elevation-3" src="<?= base_url() ?>/assets/images/avatar.png" alt="User profile picture">
        <?php endif; ?>
    </a>
    <div class="dropdown-menu dropdown-menu-md dropdown-menu-right mt-2">
        <a href="#" class="dropdown-item">
            <span> <?= auth()->user()->username ?></span>

        </a>
        <div class="dropdown-divider"></div>
        <a href="<?= base_url() ?>/profile" class="dropdown-item">
            <i class="far fa-user mr-2 mb-1 "></i>My Profile
        </a>
        <div class="dropdown-divider"></div>
        <a href="<?= base_url() ?>/logout" class="dropdown-item">
            <i class="far fa-power-off mr-2 mb-1 "></i>Log Out

        </a>

    </div>
</li>
<?= $this->endSection(); ?>



<?= $this->extend('Layouts/coreLayout'); ?>
<?= $this->section('content'); ?>
<!-- ==================== -->


<?php
$pageSession = \CodeIgniter\Config\Services::session();
?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark"><?= $page['heading'] ?></h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('profile') ?>">Profile</a></li>
                    <li class="breadcrumb-item active"><?= $page['heading'] ?></li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->

<!-- Main content -->


<section class="content body">
    <div class="container-fluid">
        <?= form_open() ?>
        <div class="row card col-md-6 p-4">
            <?php if ($pageSession->getFlashdata('Success')) : ?>
                <div id="message" class="alert alert-success text-center" role="alert">
                    <?= $pageSession->getFlashdata('Success'); ?>
                </div>
            <?php endif; ?>
            <?php if ($pageSession->getFlashdata('error')) : ?>
                <div id="message" class="alert alert-success text-center" role="alert">
                    <?= $pageSession->getFlashdata('error'); ?>
                </div>
            <?php endif; ?>
            <div class="form-group">
                <label for="my-input">Old Password</label>
                <input id="my-input" class="form-control" type="password" name="oldPassword" value="<?= set_value('oldPassword') ?>">
                <span class="text-danger"><?= displayError($validation, 'oldPassword') ?></span>
            </div>
            <div class="form-group">
                <label for="my-input">New Password</label>
                <input id="my-input" class="form-control" type="password" name="password" value="<?= set_value('password') ?>">
                <span class="text-danger"><?= displayError($validation, 'password') ?></span>
            </div>
            <div class="form-group">
                <label for="my-input">Confirm New Password</label>
                <input id="my-input" class="form-control" type="password" name="confirmNewPassword" >
                <span class="text-danger"><?= displayError($validation, 'confirmNewPassword') ?></span>
            </div>
            <div class="form-group">
                <button type="submit" style="background: #db611e;" class="btn btn-primary">Update</button>
            </div>
        </div>
        <?= form_close() ?>
    </div>
<!-- Hello@1234 -->
</section>

<?= $this->endSection(); ?>