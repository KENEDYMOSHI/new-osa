<?= $this->extend('Layouts/pdfLayout'); ?>
<?= $this->section('content'); ?>
<table class=" detailsTable">

    <tr class="row">
        <td class="col-ms-8">
        <table class="table" style="width:60%">
        <tr>
            <td>VERIFICATION CHART NO</td>
            <td class="bold"><?= $chart->chartNumber ?></td>
        </tr>
        <tr>
            <td>FOR: TANK/TR</td>
            <td class="bold"><?= $chart->plateNumber ?></td>
        </tr>
        <tr>
            <td>FOR:</td>
            <td class="bold"><?= $chart->customer ?></td>
        </tr>
        <tr>
            <td>CAPACITY:</td>
            <td class="bold"><?= $chart->capacity ?> LITRE</td>
        </tr>
    </table>
        </td>
        <td class="col-ms-4">
        <div class="qrCode">
            <img class="qrImg" src="<?= $qrCode ?>" alt="qr-code">
        </div>
        </td>
    
    </tr>
   
</table>



<div class="container45">
    <table class="">


        <tbody>
            <tr class="row" id="upperChart">
                <?= $chart->upperChart ?>
            </tr>


        </tbody>



    </table>
    <span class="line"></span>


    <table class="table  table-sm">
        <tr class="row p-0 m-0">
            <td class="col-md-6">The dipstick(s)was | were marked</td>
            <td class="col-md-6">
                <p class="bold"><?= $chart->chartNumber ?></p></br>
                <p class="bold"><?= $chart->customer ?></p></br>
                <p class="bold"><?= $chart->plateNumber ?></p></br>
            </td>
        </tr>
        <br>

        <tr class="row" id="lowerChart">
            <?= $chart->lowerChart ?>

        </tr>
    </table>
    <span class="line"></span>

    <span class="text-bold text">The tank was verified &nbsp; 1. On a level plane 2 . against approved measure</><br>
        NOTE (a) the compartments should be filled in the order &nbsp; <span class="bold"><?= $chart->fillOrder ?></span> and emptied in the order &nbsp; <span class="bold"><?= $chart->emptyOrder ?></span> <br>
        (b) THIS TANK SHALL BE VERIFIED AGAIN IF SUSPECTED OF GIVING INCORRECT MEASUREMENTS BUT IN ANY CASE NOT LATER THAN <span class="bold"><?= $chart->nextVerification ?></span>
        <br>

        <div class="container">
            <div style="width: 100%; overflow: hidden;">
                <div style="float: left; width: 33.33%;">
                    <p>
                        DATE: <?= $chart->verificationDate ?> <br>
                        <span>DISTRIBUTION OF COPIES:-</span> <br>
                        <span class="bold"> 1.<?= $chart->customer ?></span> <br>
                        <span class="bold"> 2. Weights & Measures Agency,</span>
                        <br> <span class="bold">P.o Box 313 Dar Es Salaam</span>
                    </p>
                </div>
                <div style="float: left; width: 33.33%;">
                    <p style="margin-top: 80px;">
                        <span class="bold">REGIONAL MANAGER</span> <br>
                        <?= str_replace('Wakala Wa Vipimo', '', auth()->user()->centerName) ?><br>
                    </p>
                </div>
                <div style="float: left; width: 33.33%;">
                    <div class="seal-container">
                        <div class="seal">
                            WMA OFFICIAL SEAL
                        </div>
                    </div>
                </div>
            </div>
        </div>






</div>



<?= $this->endSection(); ?>