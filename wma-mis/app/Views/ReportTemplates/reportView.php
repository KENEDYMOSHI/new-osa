<?= $this->extend('Layouts/pdfLayout'); ?>
<?= $this->section('content'); ?>
<div class="report">
    <p class="text-center text-bold"><strong><?= $title ?></strong></p>
    <table class="table table-bordered">
        <?= $template->report ?>
    </table>
</div>

<div class="collectionSummary" style="width:50%; float:right">
    <?= $template->summary ?>
</div>
<?= $this->endSection(); ?>