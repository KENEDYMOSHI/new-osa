 <?= $this->include('Layouts/mainHeader.php'); ?>
<!-- /.navbar -->
<!-- Main Side menu Container -->
<?= $this->include('Layouts/sideMenu.php'); ?>


<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <?= $this->renderSection('content'); ?>
</div>
<!-- Main Content end Here -->
<!-- footer -->
<?= $this->include('Layouts/mainFooter.php'); ?>