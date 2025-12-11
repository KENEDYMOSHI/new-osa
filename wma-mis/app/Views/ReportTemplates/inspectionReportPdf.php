<?= $this->extend('Layouts/pdfLayout'); ?>
<?= $this->section('content'); ?>
<h5 class="text-center mt-2"> </h5>
<div class="card">

    <h6 class="text-center mb-2"><b><?= strtoupper($title) ?></b></h6>
    <div class="card-body">
        <?= $report ?>
    </div>

</div>

<?= $this->endSection(); ?>