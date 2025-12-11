<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sticker Verification</title>
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

        <?php if ($sticker): ?>

            <div class="list-group mt-3">
                <div class="list-group-item">
                    <i class="bi bi-person icon"></i>
                    <div class="item-content">
                        <div class="item-title">Customer Name</div>
                        <div class="item-value"><?= $sticker->customer ?></div>
                    </div>
                </div>
                <div class="list-group-item">
                    <i class="bi bi-phone icon"></i>
                    <div class="item-content">
                        <div class="item-title">Mobile Number</div>
                        <div class="item-value">+<?= $sticker->mobile ?></div>
                    </div>
                </div>
                <div class="list-group-item">
                    <i class="bi bi-geo-alt icon"></i>
                    <div class="item-content">
                        <div class="item-title">Region</div>
                        <div class="item-value"><?= $sticker->region ?></div>
                    </div>
                </div>
                <div class="list-group-item">
                    <i class="bi bi-columns-gap icon"></i>
                    <div class="item-content">
                        <div class="item-title">Activity</div>
                        <div class="item-value"><?= $sticker->activity ?></div>
                    </div>
                </div>
                <div class="list-group-item">
                    <i class="bi bi-gear icon"></i>
                    <div class="item-content">
                        <div class="item-title">Instrument</div>
                        <div class="item-value"><?= $sticker->instrument ?></div>
                    </div>
                </div>
                <div class="list-group-item">
                    <i class="bi bi-sticky icon"></i>
                    <div class="item-content">
                        <div class="item-title">Sticker Number</div>
                        <div class="item-value"><?= $sticker->stickerNumber ?></div>
                    </div>
                </div>
                <div class="list-group-item">
                    <i class="bi bi-calendar-check icon"></i>
                    <div class="item-content">
                        <div class="item-title">Verification Date</div>
                        <div class="item-value"><?= $sticker->verificationDate ?></div>
                    </div>
                </div>
                <div class="list-group-item">
                    <i class="bi bi-calendar4-week icon"></i>
                    <div class="item-content">
                        <div class="item-title">Next Verification</div>
                        <div class="item-value"><?= $sticker->nextVerification ?></div>
                    </div>
                </div>
                <div class="list-group-item">
                    <i class="bi bi-hash icon"></i>
                    <div class="item-content">
                        <div class="item-title">Control Number</div>
                        <div class="item-value"><?= $sticker->controlNumber ?></div>
                    </div>
                </div>
                <div class="list-group-item">
                    <i class="bi bi-cash-stack icon"></i>
                    <div class="item-content">
                        <div class="item-title">Amount</div>
                        <div class="item-value">Tsh <?= $sticker->amount ?></div>
                    </div>
                </div>
                <div class="list-group-item">
                    <i class="bi bi-info-square icon"></i>
                    <div class="item-content">
                        <div class="item-title">Payment Status</div>
                        <div class="item-value"><?= $sticker->paymentStatus ?></div>
                    </div>
                </div>
                <div class="list-group-item">
                    <i class="bi bi-file-earmark-medical icon"></i>
                    <div class="item-content">
                        <div class="item-title">Certificate Number</div>
                        <div class="item-value"><?= $sticker->certificateNumber ?></div>
                    </div>
                </div>
                <div class="list-group-item">
                    <i class="bi bi-person-check icon"></i>
                    <div class="item-content">
                        <div class="item-title">Verified By</div>
                        <div class="item-value"><?= $sticker->verifiedBy ?></div>
                    </div>
                </div>
            </div>
    </div>
    <?php else: ?>
    <div class="alert alert-danger">
        <strong>Sticker Not Found!</strong> The sticker you are looking for does not exist .
    </div>
    <?php endif; ?>

</div>

</body>

</html>