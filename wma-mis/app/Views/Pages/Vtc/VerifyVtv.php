<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="<?= base_url('assets/images/wma1.png') ?>" type="image/x-icon">
    <title>vehicle Verification</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            color: #333;
        }

        .header {
            text-align: center;
            margin-top: 20px;
            margin-bottom: 10px;
        }

        .logo {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #f07f19;
        }

        img {
            max-width: 100%;
            height: auto;
            padding: 4px;
        }

        .title {
            font-weight: bold;
            color: #f07f19;
        }

        .list-group-item {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .item-content {
            flex: 1;
        }

        .item-title {
            font-weight: bold;
        }

        .icon {
            font-size: 1.5rem;
            color: #f07f19;
        }
    </style>
</head>

<body>

    <div class="container py-4">

        <div class="header text-center">
            <img src="<?= base_url('assets/images/wma1.png') ?>" alt="Logo" class="logo mb-3">
            <h6>THE UNITED REPUBLIC OF TANZANIA</h6>
            <h5>WEIGHTS AND MEASURES AGENCY</h5>
            <h5>CALIBRATION CHART VERIFICATION</h5>
        </div>

        <!-- Flashdata Error Messages -->
        <?php if (session()->getFlashdata('errors')): ?>
            <div class="alert alert-danger mt-3">
                <ul class="list-group">
                    <?php foreach (session()->getFlashdata('errors') as $error): ?>
                        <li class="list-group-item list-group-item-danger"><?= esc($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if ($vehicle): ?>

            <div class="list-group mt-3">
                <div class="list-group-item">
                    <i class="bi bi-person icon"></i>
                    <div class="item-content">
                        <div class="item-title">Customer Name</div>
                        <div class="item-value"><?= ucwords(strtolower($vehicle->name)) ?></div>
                    </div>
                </div>
                <div class="list-group-item">
                    <i class="bi bi-phone icon"></i>
                    <div class="item-content">
                        <div class="item-title">Mobile Number</div>
                        <div class="item-value">+<?= $vehicle->phone_number ?></div>
                    </div>
                </div>
                <div class="list-group-item">
                    <!-- <i class="bi bi-columns-gap icon"></i> -->
                    <i class="bi bi-house-check icon"></i>
                    <div class="item-content">
                        <div class="item-title">Address</div>
                        <div class="item-value"><?= $vehicle->postal_address   ?></div>
                    </div>
                </div>
                <div class="list-group-item">
                    <i class="bi bi-geo-alt icon"></i>
                    <div class="item-content">
                        <div class="item-title">Region</div>
                        <div class="item-value"><?= str_replace('Wakala Wa Vipimo', '', wmaCenter($vehicle->region)->centerName) ?></div>
                    </div>
                </div>

                <div class="list-group-item">
                    <i class="bi bi-truck icon"></i>
                    <div class="item-content">
                        <div class="item-title">Vehicle Brand</div>
                        <div class="item-value"><?= $vehicle->vehicle_brand ?></div>
                    </div>
                </div>
                <div class="list-group-item">
                    <i class="bi bi-view-stacked icon"></i>
                    <div class="item-content">
                        <div class="item-title">Compartments</div>
                        <div class="item-value"><?= $vehicle->compartments ?></div>
                    </div>
                </div>
                <div class="list-group-item">
                    <i class="bi bi-database-down icon"></i>
                    <div class="item-content">
                        <div class="item-title">Vehicle Capacity</div>
                        <div class="item-value"><?= $isVerified ? number_format($verification->capacity) : number_format($vehicle->capacity) ?> Litres</div>
                    </div>
                </div>
                <div class="list-group-item">
                    <i class="bi bi-credit-card-2-back icon"></i>
                    <div class="item-content">
                        <div class="item-title"> Hose Plate Number</div>
                        <div class="item-value"><?= $vehicle->hose_plate_number ?></div>
                    </div>
                </div>
                <div class="list-group-item">
                    <i class="bi bi-credit-card-2-back icon"></i>
                    <div class="item-content">
                        <div class="item-title"> Trailer Plate Number</div>
                        <div class="item-value"><?= $vehicle->trailer_plate_number ?></div>
                    </div>
                </div>
                <div class="list-group-item">
                    <i class="bi bi-calendar-check icon"></i>
                    <div class="item-content">
                        <div class="item-title">Verification Date</div>
                        <div class="item-value"><?= dateFormatter($vehicle->registration_date) ?></div>
                    </div>
                </div>
                <div class="list-group-item">
                    <i class="bi bi-calendar4-week icon"></i>
                    <div class="item-content">
                        <div class="item-title">Next Verification</div>
                        <div class="item-value"><?= dateFormatter($vehicle->next_calibration) ?></div>
                    </div>
                </div>
                <div class="list-group-item">
                    <i class="bi bi-file-earmark-medical icon"></i>
                    <div class="item-content">
                        <div class="item-title">Verification Status</div>
                        <div class="item-value"><?= $isVerified ? $verification->calibrationStatus : '' ?></div>
                    </div>
                </div>


                <div class="list-group-item">
                    <!-- <i class="bi bi-hash icon"></i> -->
                    <i class="bi bi-c-circle icon"></i>
                    <div class="item-content">
                        <div class="item-title">Control Number</div>
                        <div class="item-value"><?= $isVerified ? $verification->PayCntrNum : '' ?></div>
                    </div>
                </div>
                <div class="list-group-item">
                    <i class="bi bi-cash-stack icon"></i>
                    <div class="item-content">
                        <div class="item-title">Amount</div>
                        <div class="item-value">Tsh <?= $isVerified ? number_format($verification->amount) : '' ?></div>
                    </div>
                </div>
                <div class="list-group-item">
                    <i class="bi bi-info-square icon"></i>
                    <div class="item-content">
                        <div class="item-title">Payment Status</div>
                        <div class="item-value"><?= $isVerified ? $verification->PaymentStatus : '' ?></div>
                    </div>
                </div>

                <div class="list-group-item">
                    <i class="bi bi-person-check icon"></i>
                    <div class="item-content">
                        <div class="item-title">Verified By</div>
                        <div class="item-value"><?= $officer ?></div>
                    </div>
                </div>
            </div>
            <p class="text-center mt-2">Weights And Measures Agency &copy;<?= date('Y') ?></p>

    </div>
<?php else: ?>
    <div class="alert alert-danger">
        <strong>vehicle Not Found!</strong> The vehicle you are looking for does not exist .
    </div>
<?php endif; ?>

</div>

</body>

</html>