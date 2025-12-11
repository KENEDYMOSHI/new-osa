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
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active"><?= $page['heading'] ?></li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->
<?php

//print_r($profile);
?>
<!-- Main content -->
<section class="content body">
    <div class="container-fluid">
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

        <?php if (isset($validation)) : ?>
            <div id="message" class="alert alert-danger" role="alert">
                <?= $validation->listErrors(); ?>
            </div>
        <?php endif; ?>
        <div class="row">
            <div class="col-md-4">

                <!-- Profile Image -->
                <div class="card card-primary card-outline">
                    <div class="card-body box-profile">

                        <div class="text-center">
                            <?php if (auth()->user()->avatar != '') : ?>
                                <img class="profile-user-img img-fluid img-circle" src="<?= auth()->user()->avatar ?>" alt="User profile picture">
                            <?php else : ?>
                                <img class="profile-user-img img-fluid img-circle" src="<?= base_url() ?>/assets/images/avatar.png" alt="User profile picture">
                            <?php endif; ?>
                        </div>

                        <h3 class="profile-username text-center">
                            <?= auth()->user()->username ?>
                        </h3>

                        <p class="text-muted text-center"><?= auth()->user()->groups[0] ?></p>
                        


                        <ul class="list-group list-group-unbordered mb-3">
                            <li class="list-group-item">
                                <?= auth()->user()->email ?>
                            </li>
                            <li class="list-group-item">
                                <?= centerName() ?>
                              
                            </li>
                         
                        </ul>
                        <h5>Signature</h5>
                        <img src="<?=$sign?>" alt="" width="100" style="border: 1px solid #ccc; padding: 5px; border-radius: 5px;">


                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->

                <!-- About Me Box -->
                <!-- <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">About Me</h3>
                    </div>
                  
                    <div class="card-body">


                        <strong><i class="fas fa-map-marker-alt mr-1"></i> Location</strong>

                        <p class="text-muted">Tanzania</p>

                       


                    </div>
                  
                </div> -->
                <!-- /.card -->
            </div>
            <!-- /.col -->
            <div class="col-md-8">

                <?php
                // print_r($tasks);
                ?>
                <div class="card">
                    <div class="card-header p-2">
                        <ul class="nav nav-pills">
                            <!-- <li class="nav-item"><a class="nav-link active" href="#activity"
                                    data-toggle="tab">Activity</a></li> -->

                            <?php if ($user->inGroup('officer')) : ?>
                                <li class="nav-item"><a class="nav-link active" href="#timeline" data-toggle="tab">
                                        Activities</a>
                                </li>
                            <?php endif; ?>
                            <li class="nav-item"><a class="nav-link " href="#settings" data-toggle="tab">Edit Profile</a>
                            </li>
                            <!-- <li class="nav-item"><a class="nav-link" href="#settings" data-toggle="tab">Change
                                    Password</a>
                            </li> -->
                        </ul>
                    </div><!-- /.card-header -->
                    <div class="card-body">
                        <div class="tab-content">
                            <!-- <div class="active tab-pane" id="activity">

                            </div> -->
                            <!-- /.tab-pane -->
                            <?php if ($user->inGroup('officer')) : ?>
                                <?= $this->include('Widgets/tasks'); ?>
                            <?php else : ?>

                            <?php endif; ?>
                            <!-- /.tab-pane -->


                            <div class="<?php if ($user->inGroup('manager')) : ?>
                                tab-pane active
                            <?php else : ?>
                                tab-pane
                            <?php endif; ?>" id="settings">

                                <?= form_open_multipart() ?>
                                <div class="form-group">
                                    <!-- <label for="my-input">Upload Profile Picture</label> -->
                                    <input class="" type="file" name="avatar">
                                </div>
                                <div class="form-group row">
                                    <div class=" ">
                                        <button type="submit" class="btn btn-success btn-sm">Upload Photo</button>
                                    </div>
                                </div>

                                <?= form_close() ?>


                            </div>

                        </div>

                        <!-- /.tab-pane -->
                    </div>
                    <!-- /.tab-content -->
                </div><!-- /.card-body -->
            </div>
            <!-- /.nav-tabs-custom -->
        </div>
        <!-- /.col -->
    </div>
    </div>
</section>

<?= $this->endSection(); ?>