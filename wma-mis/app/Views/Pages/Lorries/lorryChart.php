<?= $this->extend('Layouts/pdfLayout'); ?>
<?= $this->section('content'); ?>
<style>
    td {
        padding: 7px 3px;
    }

    .tag {
        width: 50%;
    }

    .prop {
        width: 50%;
    }

    .text {
        font-size: 13px;
        font-family: Arial, Helvetica, sans-serif;
        font-weight: prop;
    }

    .qr-code {
        width: 100px;


    }

    .qrBox {


        margin-top: 175px;
        margin-left: 300px;
    }
</style>
<p class="text-center">The Weights and Measures(Sand And Other Ballast) Regulations,2013</p>
<h5 class="bold" style="font-family: Arial;text-align:center">DETAILS FOR CALIBRATION OF SAND AND BALLAST LORRIES(SBL)</h5>


<br>
<table class="table table-bordered">


    <tr>
        <td class="tag">CUSTOMER NAME</td>
        <td class="prop"><?= $lorry->PyrName ?></td>
    </tr>
    <tr>
        <td class="tag">ADDRESS</td>
        <td class="prop"><?= $lorry->postal_address ?></td>
    </tr>
    <tr>
        <td>MOBILE NUMBER</td>
        <td class="prop">+<?= $lorry->phone_number ?></td>
    </tr>
    <tr>
        <td class="tag">TYPE OF LORRY</td>
        <td class="prop"><?= $lorry->type ?></td>
    </tr>
    <tr>
        <td class="tag">MODEL</td>
        <td class="prop"><?= $lorry->model ?></td>
    </tr>
    <tr>
        <td class="tag">REGISTRATION NUMBER</td>
        <td class="prop"><?= $lorry->plate_number ?></td>
    </tr>
    <tr>
        <td class="tag">MEAN LENGTH</td>
        <td class="prop"><?= $lorry->height ?>m</td>
    </tr>
    <tr>
        <td class="tag">MEAN WIDTH</td>
        <td class="prop"><?= $lorry->width ?>m</td>
    </tr>
    <tr>
        <td class="tag">MEAN DEPTH</td>
        <td class="prop"><?= $lorry->depth ?>m</td>
    </tr>
    <tr>
        <td class="tag">MAXIMUM VOLUME</td>
        <td class="prop"><?= $lorry->capacity ?>m<sup>3</sup></td>
    </tr>
    <tr>
        <td class="tag">CERTIFICATE OF CORRECTNESS NUMBER</td>
        <td class="prop"><?= $certificateOfCorrectness ?></td>
    </tr>
    <tr>
        <td class="tag">CALIBRATION DATE</td>
        <td class="prop"><?= dateFormatter($lorry->registration_date) ?></td>
    </tr>
    <tr>
        <td class="tag">NEXT CALIBRATION DATE</td>
        <td class="prop"><?= dateFormatter($lorry->next_calibration) ?></td>
    </tr>
    <tr>
        <td class="tag">CONTROL NUMBER</td>
        <td class="prop"><?= $lorry->PayCntrNum ?></td>
    </tr>
    <tr>
        <td class="tag">STICKER NUMBER</td>
        <td class="prop"><?= $lorry->stickerNumber ?></td>
    </tr>
    <tr>
        <td class="tag">FEES PAID(TSH)</td>
        <td class="prop"><?= number_format($lorry->amount) ?></td>
    </tr>



</table>

<br>
<br>
<br>
<br>

<table>
    <tr>

        <td>
            <div>
                ........................................................................ <br><br>
                OWNER'S SIGNATURE
            </div>
        </td>
        <td>
            <div>
                ........................................................................ <br><br>
                INSPECTOR'S SIGNATURE
            </div>
        </td>
    </tr>
</table>





<div class="qrBox">
    <img src="<?= $qrCode ?>" alt="" class="qr-code">
</div>



<?= $this->endSection(); ?>