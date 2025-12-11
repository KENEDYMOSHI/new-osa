<?php

use App\Libraries\CommonTasksLibrary;

$lib = new CommonTasksLibrary();
$centers = $lib->collectionCenters();
$groups = setting('AuthGroups.groups');

?>
<?= $this->extend(config('Auth')->views['layout']) ?>

<?= $this->section('title') ?><?= lang('Auth.register') ?> <?= $this->endSection() ?>

<?= $this->section('main') ?>

<div class="container d-flex justify-content-center p-5">
    <div class="card col-12 col-md-5 shadow-sm">
        <div class="card-body">
            <h5 class="card-title mb-5"><?= lang('Auth.register') ?></h5>

            <?php if (session('error') !== null) : ?>
                <div class="alert alert-danger" role="alert"><?= session('error') ?></div>
            <?php elseif (session('errors') !== null) : ?>
                <div class="alert alert-danger" role="alert">
                    <?php if (is_array(session('errors'))) : ?>
                        <?php foreach (session('errors') as $error) : ?>
                            <?= $error ?>
                            <br>
                        <?php endforeach ?>
                    <?php else : ?>
                        <?= session('errors') ?>
                    <?php endif ?>
                </div>
            <?php endif ?>

            <form action="<?= url_to('register') ?>" method="post">
                <?= csrf_field() ?>

                <!-- Email -->
                <div class="mb-2">
                    <label for="">First Name</label>
                    <input type="text" class="form-control" name="first_name" placeholder="Firs tname" value="<?= old('first_name') ?>" required />
                </div>

                <div class="mb-2">
                    <label for="">Last Name</label>
                    <input type="text" class="form-control" name="last_name" placeholder="Firs name" value="<?= old('last_name') ?>" required />
                </div>

                <div class="mb-2">
                    <label for="">User Email</label>
                    <input type="email" class="form-control" name="email" inputmode="email" autocomplete="email" placeholder="<?= lang('Auth.email') ?>" value="<?= old('email') ?>" required />
                </div>

                <!-- Username -->
                <div class="mb-4">
                    <label for="">User Name</label>
                    <input type="text" class="form-control" name="username" inputmode="text" autocomplete="username" placeholder="<?= lang('Auth.username') ?>" value="<?= old('username') ?>" required />
                </div>

                <!-- Password -->
                <div class="mb-2">
                    <label for="">Password</label>
                    <input type="password" class="form-control" name="password" inputmode="text" autocomplete="new-password" placeholder="<?= lang('Auth.password') ?>" required />
                </div>

                <!-- Password (Again) -->
                <div class="mb-2">
                    <label for="">Confirm Password</label>
                    <input type="password" class="form-control" name="password_confirm" inputmode="text" autocomplete="new-password" placeholder="<?= lang('Auth.passwordConfirm') ?>" required />
                </div>
                <div class="form-group mb-2">
                    <label for="">Collection Center</label>
                    <select class="form-control select2bs4" name="collection_center" required>
                        <option disabled selected>Select Center</option>
                        <?php foreach ($centers as $center) : ?>
                            <option value="<?= $center->centerNumber ?>"><?= $center->centerName ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-groupmb-2 ">
                    <label for="">User Group</label>
                    <select class="form-control select2bs4" name="userGroup" required>
                        <option disabled selected>Select User Group</option>
                        <?php foreach ($groups as $group => $title) : ?>
                            <option value="<?= $group ?>"><?= $title['title'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group mb-2">
                    <label for="">User Role</label>
                    <select class="form-control" id="role" name="role" required>
                        <option selected disabled>Select Role</option>
                        <option value="7">Admin</option>
                        <option value="3">Head Of Section</option>
                        <option value="2">Manager</option>
                        <option value="1">Officer</option>
                    </select>
                </div>

                <div class="d-grid col-12 col-md-8 mx-auto m-3">
                    <button type="submit" class="btn btn-primary btn-block"><?= lang('Auth.register') ?></button>
                </div>

                <p class="text-center"><?= lang('Auth.haveAccount') ?> <a href="<?= url_to('login') ?>"><?= lang('Auth.login') ?></a></p>

            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>