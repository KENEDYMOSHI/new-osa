<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form D Request - Print</title>
    <style>
        @page {
            size: A4;
            margin: 0;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            background: #fff;
            margin: 0;
            padding: 0;
            -webkit-print-color-adjust: exact;
            color: #000;
        }
        .page-container {
            width: 210mm;
            height: 296mm; /* Force single page height approx */
            padding: 15mm 20mm;
            margin: 0 auto;
            position: relative;
            box-sizing: border-box;
            background: white;
        }
        
        .form-header { text-align: center; margin-bottom: 20px; }
        .form-header h1 { font-size: 18px; font-weight: bold; margin: 0 0 5px 0; text-transform: uppercase; letter-spacing: 0.5px; color: #000; }
        .form-header p { font-size: 12px; font-weight: bold; margin: 0; color: #000; }
        .wma-logo { height: 80px; margin: 10px auto; display: block; }
        
        .form-title-section { text-align: center; margin-bottom: 25px; }
        .form-title-section h2 { font-size: 20px; font-weight: bold; text-decoration: underline; margin: 0 0 5px 0; color: #000; }
        .form-title-section p { font-size: 13px; font-weight: bold; margin: 0; text-transform: uppercase; }
        .form-subtitle { font-size: 12px; font-style: italic; font-weight: bold; margin-top: 5px; }

        .form-row { display: flex; align-items: baseline; margin-bottom: 10px; font-size: 14px; flex-wrap: wrap; }
        .form-label { font-weight: normal; margin-right: 10px; white-space: nowrap; }
        .form-value { 
            border-bottom: 1px dotted #000; 
            font-weight: bold; 
            padding: 0 5px; 
            flex-grow: 1; 
            min-width: 50px; 
        }
        .form-value-fixed {
            border-bottom: 1px dotted #000;
            font-weight: bold;
            padding: 0 5px;
            display: inline-block;
        }

        .certify-text { margin: 20px 0; font-size: 14px; }
        
        .actions-group { display: flex; justify-content: center; gap: 40px; margin: 15px 0; font-weight: bold; font-size: 14px; }
        .action-check { display: flex; align-items: center; }
        .tick-mark { font-size: 20px; margin-right: 5px; min-width: 20px; line-height: 1; }

        .footer-certify-text { margin: 20px 0; font-size: 13px; text-align: justify; line-height: 1.4; }

        .auth-section { margin-top: 25px; border-top: 1px dotted #ccc; padding-top: 15px; }
        .auth-header { font-size: 12px; font-weight: bold; text-transform: uppercase; color: #333; margin-bottom: 10px; }
        
        .inspection-report { 
            background: #fdfdfd; 
            padding: 10px; 
            margin-top: 20px; 
            border: 1px solid #eee; 
            font-size: 13px;
            page-break-inside: avoid;
        }
        .inspection-report h5 { font-size: 12px; font-weight: bold; color: #000; margin: 0 0 5px 0; }

        @media print {
            .page-container {
                width: 100%;
                height: auto;
                padding: 10mm;
                margin: 0;
            }
            body { background: white; }
        }
        
        /* Utility for grid */
        .row { display: flex; flex-wrap: wrap; margin-right: -15px; margin-left: -15px; }
        .col-8 { flex: 0 0 66.66667%; max-width: 66.66667%; padding: 0 15px; }
        .col-4 { flex: 0 0 33.33333%; max-width: 33.33333%; padding: 0 15px; text-align: right; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
    <div class="page-container">
        <!-- Header -->
        <div class="form-header">
            <h1>WEIGHTS AND MEASURES AGENCY</h1>
            <p>P.O BOX 313 DAR ES SALAAM</p>
            <img src="<?= base_url('assets/images/wma1.png') ?>" alt="WMA Logo" class="wma-logo">
        </div>

        <!-- Form Title -->
        <div class="form-title-section">
            <h2>FORM D</h2>
            <p>FORM OF CERTIFICATE TO BE USED BY A PUMP MECHANIC</p>
            <p>AFTER SEALED/RE-SEALED</p>
            <div class="form-subtitle">(Made under Regulation 12(d))</div>
        </div>

        <!-- Practitioner Info -->
        <div class="form-row">
            <span class="form-label">Company employing mechanic:</span>
            <span class="form-value"><?= strtoupper($request['company_name'] ?? '') ?></span>
        </div>
        <div class="form-row">
            <span class="form-label">License No:</span>
            <span class="form-value-fixed" style="width: 200px; margin-right: 20px;"><?= $request['license_number'] ?? '' ?></span>
            
            <span class="form-label">Phone:</span>
            <span class="form-value"><?= $request['practitioner_phone'] ?? '' ?></span>
        </div>

        <div class="certify-text">
            I hereby certify that the under- mentioned liquid measuring pump has been
        </div>

        <!-- Actions -->
        <div class="actions-group">
            <div class="action-check">
                <span class="tick-mark"><?= ($request['certification_action'] ?? '') == 'Erected' ? '&#10004;' : '&nbsp;&nbsp;' ?></span> *Erected
            </div>
            <div class="action-check">
                <span class="tick-mark"><?= ($request['certification_action'] ?? '') == 'Adjusted' ? '&#10004;' : '&nbsp;&nbsp;' ?></span> Adjusted
            </div>
            <div class="action-check">
                <span class="tick-mark"><?= ($request['certification_action'] ?? '') == 'Repaired' ? '&#10004;' : '&nbsp;&nbsp;' ?></span>  Repaired
            </div>
        </div>
        <div style="text-align: center; font-style: italic; font-size: 10px; margin-bottom: 15px;">(*Delete where not applicable)</div>

        <!-- Details -->
        <div class="form-row">
            <span class="form-label">By me and sealed with my seal No:</span>
            <span class="form-value" style="flex-grow: 0; min-width: 100px;"><?= $request['seal_number'] ?? '' ?></span>
        </div>
        
        <div class="form-row">
            <span class="form-label">Name of user of pump:</span>
            <span class="form-value"><?= strtoupper($request['company_name'] ?? '') ?></span>
        </div>

        <div class="form-row">
            <span class="form-label">Location:</span>
            <?php $loc = implode(', ', array_filter([$request['region'] ?? '', $request['district'] ?? '', $request['ward'] ?? ''])); ?>
            <span class="form-value"><?= $loc ?></span>
        </div>

        <div class="form-row">
            <span class="form-label">Make and type of pump:</span>
            <span class="form-value"><?= $request['instrument_name'] ?? '' ?> (<?= $request['type_of_instrument'] ?? '' ?>)</span>
        </div>

        <div class="form-row">
            <span class="form-label" style="width: 80px;">Product:</span>
            <span class="form-value" style="margin-right: 20px;"><?= $request['product'] ?? 'N/A' ?></span>
            
            <span class="form-label">Capacity/Nozzles:</span>
            <span class="form-value"><?= ($request['capacity'] ?? '') . ' ' . ($request['capacity_unit'] ?? '') ?></span>
        </div>

        <div class="form-row">
            <span class="form-label" style="width: 80px;">Serial No:</span>
            <span class="form-value" style="margin-right: 20px;"><?= $request['serial_number'] ?? 'N/A' ?></span>
            
            <span class="form-label">Sticker No:</span>
            <span class="form-value"><?= $request['sticker_number'] ?? 'N/A' ?></span>
        </div>

        <div class="form-row">
            <span class="form-label" style="width: 120px;">Date of sealing:</span>
            <span class="form-value"><?= isset($request['verification_date']) ? date('F d, Y', strtotime($request['verification_date'])) : '' ?></span>
        </div>
        
        <div class="form-row">
            <span class="form-label" style="width: 150px;">Next Verification Date:</span>
            <span class="form-value"><?= isset($request['next_verification_date']) ? date('F d, Y', strtotime($request['next_verification_date'])) : '' ?></span>
        </div>

        <div class="footer-certify-text">
            I further certify that the above pump was fully tested against approved stamped measures and found correct within the permitted limits of error before sealed.
        </div>

        <div class="form-row" style="justify-content: flex-end;">
            <span class="form-label">Certificate of Authorization No:</span>
            <span class="form-value-fixed" style="width: 200px; text-align: center;"><?= $request['cert_auth_number'] ?? 'Quisquam ducimus te' ?></span> 
        </div>

        <!-- Declarant -->
        <div class="form-row" style="margin-top: 25px;">
            <span class="form-label">I / We</span>
            <span class="form-value-fixed" style="width: 250px;"><?= $request['declarant_name'] ?? 'Veniam nulla minim' ?></span>
        </div>
        <div class="form-row">
            <span class="form-label">Designation:</span>
            <span class="form-value" style="margin-right: 20px;"></span>
            <span class="form-label">Phone Number:</span>
            <span class="form-value">---</span>
        </div>
        <div style="font-size: 11px; margin-top: 5px; margin-bottom: 20px; line-height: 1.3;">
            Being the user(s) for trade purposes of the liquid measuring pump described above, which has been sealed/re-sealed by the pump mechanic, request the Inspector of Weights and Measures that arrangements may be made for its verification.
        </div>

        <!-- Official Verification (Bottom) -->
        <div class="auth-section">
            <div class="row">
                <div class="col-8">
                    <div class="auth-header">VERIFIED / APPROVED BY:</div>
                    <div style="font-weight: bold; font-size: 16px; text-transform: uppercase;">
                        <?php if (($request['status'] ?? '') === 'Approved'): ?>
                            <?= !empty($request['inspector_first_name']) ? esc($request['inspector_first_name'] . ' ' . $request['inspector_last_name']) : ($request['inspector_name'] ?? 'MUSHI KASSIM') ?>
                        <?php else: ?>
                            ______________________
                        <?php endif; ?>
                    </div>
                    <div style="font-size: 12px; color: #000;">Inspector of Weights and Measures</div>
                    
                    <div class="form-row" style="margin-top: 10px;">
                        <span class="form-label">Date:</span>
                        <span class="form-value-fixed" style="width: 150px;">
                            <?= (($request['status'] ?? '') === 'Approved' && isset($request['verification_date'])) ? date('d/m/Y', strtotime($request['verification_date'])) : '________________' ?>
                        </span>
                    </div>
                </div>
                <div class="col-4 text-right">
                    <div class="auth-header">DATE:</div>
                    <div style="font-weight: bold; font-size: 16px;"><?= date('d M Y') ?></div>
                </div>
            </div>
        </div>
        
        <!-- Additional Inspection Details -->
        <?php if(!empty($request['assignment_notes'])): ?>
        <div class="inspection-report">
            <div class="auth-header" style="color: #000;">ADDITIONAL INSPECTION DETAILS</div>
            <h5>Inspection Report:</h5>
            <div style="font-style: italic;">
                <?= nl2br(esc($request['assignment_notes'])) ?>
            </div>
        </div>
        <?php endif; ?>

    </div>
    
    <script>
        window.onload = function() {
            setTimeout(function(){
                window.print();
            }, 500); 
        };
    </script>
</body>
</html>
