<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form D Certificate - <?= esc($request['form_d_number'] ?? $request['id']) ?></title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 14px;
            line-height: 1.4;
            color: #000;
            background: #fff;
            padding: 30px;
            margin: 0;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 16px;
            font-weight: bold;
            margin: 0 0 5px 0;
            text-transform: uppercase;
        }

        .header p {
            font-size: 13px;
            margin: 0;
            font-weight: bold;
        }

        .logo-container {
            margin: 15px 0;
            text-align: center;
        }

        .logo-container img {
            height: 90px;
        }

        .form-title {
            text-align: center;
            margin-bottom: 30px;
        }

        .form-title h2 {
            font-size: 18px;
            font-weight: bold;
            text-decoration: underline;
            margin: 0 0 5px 0;
        }

        .form-title p {
            font-size: 13px;
            font-weight: bold;
            margin: 0;
            text-transform: uppercase;
        }
        
        .form-subtitle {
            text-align: center;
            font-size: 12px;
            font-style: italic;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .row {
            display: flex;
            align-items: baseline;
            margin-bottom: 15px;
        }

        .label {
            font-weight: bold;
            white-space: nowrap;
            margin-right: 10px;
        }

        .value {
            border-bottom: 1px dotted #000;
            flex-grow: 1;
            padding: 0 5px;
            font-weight: bold;
        }
        
        .value-inline {
            border-bottom: 1px dotted #000;
            padding: 0 5px;
            font-weight: bold;
            display: inline-block;
            min-width: 150px;
        }

        .certify-text {
            margin: 20px 0;
            font-weight: normal;
        }

        .actions {
            display: flex;
            justify-content: center;
            gap: 40px;
            margin: 20px 0;
        }

        .action-item {
            display: flex;
            align-items: center;
            font-weight: bold;
        }
        
        .check-icon {
            margin-right: 5px;
            font-size: 18px;
            font-weight: bold;
        }

        .info-grid {
            width: 100%;
            margin-bottom: 20px;
        }
        
        .info-row {
            display: flex;
            margin-bottom: 12px;
            align-items: baseline;
        }
        
        .col-half {
            width: 50%;
            display: flex;
            align-items: baseline;
        }
        
        .col-half .label {
            width: 110px; /* Fixed width for labels alignment */
        }

        .footer-certify {
            margin: 30px 0;
            font-weight: bold;
            font-size: 13px;
            text-align: justify;
        }
        
        .auth-row {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 40px;
            align-items: baseline;
        }

        .signature-section {
            margin-top: 40px;
            display: flex;
            align-items: baseline;
        }

        .signature-lines {
            display: flex;
            flex-grow: 1;
            justify-content: space-between;
            margin-left: 20px;
        }
        
        .signature-line {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 45%;
        }
        
        .dotted-line {
            border-bottom: 1px dotted #000;
            width: 100%;
            height: 20px;
            margin-bottom: 5px;
        }

        @media print {
            body { padding: 0; }
            .container { width: 100%; max-width: 100%; }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>WEIGHTS AND MEASURES AGENCY</h1>
            <p>P.O BOX 313 DAR ES SALAAM</p>
        </div>

        <!-- Logo -->
        <div class="logo-container">
            <img src="<?= base_url('assets/images/wma1.png') ?>" alt="WMA Logo">
        </div>

        <!-- Form Title -->
        <div class="form-title">
            <h2>FORM D</h2>
            <p>FORM OF CERTIFICATE TO BE USED BY A PUMP MECHANIC</p>
            <p>AFTER SEALED/RE-SEALED</p>
            <div class="form-subtitle">(Made under Regulation 12(d))</div>
        </div>

        <!-- Top Section -->
        <div class="row">
            <span class="label">Company employing mechanic:</span>
            <span class="value"><?= esc($request['company_name'] ?? '') ?></span>
        </div>

        <div class="row">
            <span class="label">License No:</span>
            <span class="value" style="flex-grow: 0; min-width: 200px; margin-right: 20px;"><?= esc($request['license_number'] ?? '') ?></span>
            
            <span class="label">Phone:</span>
            <span class="value"><?= esc($request['practitioner_phone'] ?? 'N/A') ?></span>
        </div>

        <div class="certify-text">
            I hereby certify that the under- mentioned liquid measuring pump has been
        </div>

        <!-- Actions -->
        <div class="actions">
            <div class="action-item">
                <span class="check-icon"><?= ($request['certification_action'] ?? '') == 'Erected' ? '✔' : '' ?></span> *Erected
            </div>
            <div class="action-item">
                <span class="check-icon"><?= ($request['certification_action'] ?? '') == 'Adjusted' ? '✔' : '' ?></span> Adjusted
            </div>
            <div class="action-item">
                <span class="check-icon"><?= ($request['certification_action'] ?? '') == 'Repaired' ? '✔' : '' ?></span> Repaired
            </div>
        </div>
        <div style="text-align: center; font-style: italic; font-size: 11px; margin-bottom: 20px;">(*Delete where not applicable)</div>

        <!-- Details Grid -->
        <div class="info-grid">
            <div class="info-row">
                <span class="label">By me and sealed with my seal No:</span>
                <span class="value" style="flex-grow: 0; min-width: 100px;"><?= esc($request['inspector_id'] ?? '') ?></span>
                <!-- Assuming inspector_id is seal number, or pull seal number if specific field exists -->
            </div>
            
            <div class="info-row">
                <span class="label">Name of user of pump:</span>
                <span class="value"><?= esc($request['company_name'] ?? '') ?></span> <!-- Using company name as user mainly -->
            </div>

            <div class="info-row">
                <span class="label">Location:</span>
                <?php 
                    $location = implode(', ', array_filter([$request['region'] ?? '', $request['district'] ?? '', $request['ward'] ?? '', $request['street'] ?? '']));
                ?>
                <span class="value"><?= esc($location) ?></span>
            </div>

            <div class="info-row">
                <span class="label">Make and type of pump:</span>
                <span class="value"><?= esc($request['instrument_name'] ?? '') ?> (<?= esc($request['type_of_instrument'] ?? '') ?>)</span>
            </div>

            <div class="info-row">
                <div class="col-half">
                    <span class="label" style="width: 60px;">Product:</span>
                    <span class="value" style="margin-right: 20px;"><?= esc($request['product'] ?? '') ?></span>
                </div>
                <div class="col-half">
                    <span class="label">Capacity/Nozzles:</span>
                    <span class="value"><?= esc($request['capacity'] ?? '') ?> <?= esc($request['capacity_unit'] ?? '') ?> / <?= esc($request['quantity'] ?? '') ?></span>
                </div>
            </div>

            <div class="info-row">
                <div class="col-half">
                    <span class="label" style="width: 70px;">Serial No:</span>
                    <span class="value" style="margin-right: 20px;"><?= esc($request['serial_number'] ?? '') ?></span>
                </div>
                <div class="col-half">
                    <span class="label" style="width: 80px;">Sticker No:</span>
                    <span class="value"><?= esc($request['sticker_number'] ?? '') ?></span>
                </div>
            </div>

            <div class="info-row">
                <span class="label">Date of sealing:</span>
                <span class="value" style="flex-grow: 0; min-width: 200px;"><?= esc($request['verification_date'] ?? 'N/A') ?></span>
            </div>

            <div class="info-row">
                <span class="label">Next Verification Date:</span>
                <span class="value" style="flex-grow: 0; min-width: 200px;"><?= esc($request['next_verification_date'] ?? 'N/A') ?></span>
            </div>
        </div>

        <!-- Footer Statement -->
        <div class="footer-certify">
            I further certify that the above pump was fully tested against approved stamped measures and found correct within the permitted limits of error before sealed.
        </div>

        <div class="auth-row">
            <span class="label">Certificate of Authorization No:</span>
            <span class="value" style="flex-grow: 0; min-width: 250px;"><?= esc($request['cert_auth_number'] ?? 'N/A') ?></span>
        </div>

        <!-- Signatures -->
        <div class="signature-section">
            <span class="label">I / We</span>
            <span class="value" style="width: 200px; flex-grow: 0;"><?= esc($request['declarant_name'] ?? '') ?></span>
            
            <div class="signature-lines">
                <div class="signature-line">
                    <div class="dotted-line"></div>
                    <span style="font-size: 12px;">Signature</span>
                </div>
                <!-- Date from declarant date -->
                <div class="signature-line">
                     <div class="dotted-line" style="text-align: center; border-bottom: none; font-weight: bold;"><?= esc($request['declarant_date'] ?? '') ?></div>
                     <span style="border-top: 1px dotted #000; width: 100%; text-align: center; font-size: 12px; padding-top: 2px;">Date</span>
                </div>
            </div>
        </div>

    </div>
    
</body>
</html>
