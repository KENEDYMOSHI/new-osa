<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate Verification</title>
    <link rel="shortcut icon" href="<?=getImage('assets/images/wma1.png')  ?>" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css" integrity="sha512-jnSuA4Ss2PkkikSOLtYs8BlYIeeIK1h99ty4YfvRPAlzr377vr3CXDb7sb7eEEBYjDtcYj+AjBH3FLv5uSJuXg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        body,
        td,
        ul,
        li {
            background: #FFF8E8 !important;
            font-size: small;

        }

        td {
            width: 50%;
        }
    </style>
</head>

<body>
    <section class="mb-4">
        <div class="container">
            <div class="row mt-5 justify-content-center text-center">
                <div class="col-md-12 mb-2">
                    <div class="logo">
                        <img src="<?=getImage('assets/images/wma1.png')  ?>" alt="Logo" style="max-width: 100px;" />
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="heading">
                        <h6><b>THE UNITED REPUBLIC OF TANZANIA</b></h6>
                        <h6><b>MINISTRY OF INDUSTRY AND TRADE</b></h6>
                        <h6>WEIGHTS AND MEASURES AGENCY</h6>
                    </div>
                </div>
                <hr>
            </div>
        </div>

    </section>

  



    <section>
        <div class="container">
            <h6 class="text-center">CERTIFICATE OF <?=$type ?></h6>
            <div class="row">
                <div class="col-md-12 mb-3">
                    <table class="table table-sm table-hover table-bordered align-middle">
                        <tbody>
                            <tr>
                                <td><b>CUSTOMER NAME:</b></td>
                                <td><?= strtoupper($certificate->customer) ?></td>
                            </tr>
                            <tr>
                                <td><b>PHONE NUMBER:</b></td>
                                <td>+<?= $certificate->mobile ?></td>
                            </tr>
                            <tr>
                                <td><b>REGION:</b></td>
                                <td><?= strtoupper(str_replace('Wakala Wa Vipimo', '', $region)) ?></td>
                            </tr>
                            <tr>
                                <td><b>CERTIFICATE NUMBER </b></td>
                                <td><?= $certificate->certificateNumber ?></td>
                            </tr>
                            <tr>
                                <td><b>CONTROL NUMBER </b></td>
                                <td><?= $certificate->controlNumber ?></td>
                            </tr>
                            <tr>
                                <td><b>AMOUNT(TZS) </b></td>
                                <td><?= number_format($amount) ?></td>
                            </tr>
                            <tr>
                                <td><b>VERIFIED BY :</b></td>
                                <td><?=strtoupper($officer)?></td>
                            </tr>
                            <tr>
                                <td><b>DATE:</b></td>
                                <td><?= dateFormatter(strtoupper($certificate->createdAt)) ?></td>
                            </tr>
                            <tr>
                                <td><b>VALID UNTIL:</b></td>
                                <td><?= dateFormatter(strtoupper(date('Y-m-d', strtotime('+1 year', strtotime($certificate->createdAt))))) ?></td>
                            </tr>
                        </tbody>
                        <tfoot>

                        </tfoot>
                    </table>
                </div>
                <div class="col-md-12 mb-3">
                    <h6 class="text-center">VERIFIED ITEMS </h6>
                    <ul class="list-group list-group-numbered">
                        <?php foreach ($items as $item): ?>
                            <li class="list-group-item"><?= ucwords($item) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <p class="text-center mb-3">Weights And Measures Agency &copy; <?=date('Y') ?></p>


            </div>
        </div>

    </section>
</body>

</html>