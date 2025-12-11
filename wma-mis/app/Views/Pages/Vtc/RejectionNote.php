<?= $this->extend('Layouts/pdfLayout'); ?>
<?= $this->section('content'); ?>
<h5 class="text-center">REJECTION NOTE</h5>
<table class=" detailsTable">

    <tr class="row">
        <td class="col-ms-8">
            <table class="table" style="width:60%">
                <tr>
                    <td>NAME</td>
                    <td class="bold"><?= $name ?></td>
                </tr>
                <tr>
                    <td>ADDRESS</td>
                    <td class="bold"><?= $address ?></td>
                </tr>
                <tr>
                    <td>ACTIVITY:</td>
                    <td class="bold"><?= $activity ?></td>
                </tr>
                <tr>
                    <td>INSTRUMENT:</td>
                    <td class="bold"><?= $instrument ?></td>
                </tr>

            </table>


    </tr>

</table>

<br>
<p>
    The under mentioned weighing and measuring instruments have been examined and rejected for repairs. These instruments must be repaired and resubmitted to the weights and measures inspector on or before the <strong><?= dateFormatter($deadline) ?></strong>. If they will be found in use for trade after this date, they will be seized and proceedings instituted against the user or owner
</p>
<br>

<table>
    <tr>
        <td>Date  <?= dateFormatter(date('Y-m-d')) ?></td>
        <td>
            <div>
                ........................................................................ <br>
                Inspector of weights and measures
            </div>
        </td>
    </tr>
</table>
<br>
<br>

....................................................................
<br>
<p>Owner's/User's Signature </p>






<?= $this->endSection(); ?>