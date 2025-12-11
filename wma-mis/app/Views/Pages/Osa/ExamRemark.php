<?= $this->extend('Layouts/coreLayout'); ?>
<?= $this->section('content'); ?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark"><?= $page['heading'] ?></h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url() ?>/dashboard">Dashboard</a></li>
                    <li class="breadcrumb-item active"><?= $page['heading'] ?></li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>

<div class="container-fluid">
    <!-- ROLES/GROUPS -->
    <!-- manager -->
    <!-- surveillance -->
    <!--  -->
    <?php if ($user->inGroup('manager')): ?>

    <?php elseif ($user->inGroup('surveillance')): ?>

    <?php else: ?>

    <?php endif; ?>


    <!-- your ui goes hoere -->

</div>
</div>


<script>
    //your logic goes here
</script>


<?= $this->endSection(); ?>