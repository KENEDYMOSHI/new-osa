<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Print View - <?= $application->control_number ?? 'Details' ?></title>
    <style>
        /* Base styles from LicenseApplicationPdf.php */
        * {
            box-sizing: border-box;
            font-family: sans-serif;
        }

        @media print {
            @page {
                margin: 0;
            }
            body {
                margin: 1.6cm;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .no-print {
                display: none !important;
            }
        }

        body {
            background-color: white; /* Changed to white to match PDF view */
            margin: 0;
            padding: 20px;
        }

        /* Header Layout matching License PDF */
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333; /* Added separator */
            padding-bottom: 10px;
        }

        section {
            /* float: left; width: 30%; - replaced with flex */
        }

        .left, .right {
            width: 20%;
            text-align: center;
        }
        
        .left img, .right img {
            width: 80px; /* Adjust size */
            height: auto;
        }

        .middle {
            width: 50%;
            text-align: center;
        }

        .headings h5 {
            margin: 5px;
            font-size: 14px; /* Reduced to match typical PDF */
            font-weight: bold;
            color: #000;
        }

        /* Table styles from LicenseApplicationPdf.php */
        table {
            width: 100%;
            border-collapse: collapse;
            color: #222;
            font-size: 12px;
            margin-bottom: 15px;
        }

        table td, table th {
            padding: 8px; /* Slightly more padding */
            text-align: left;
            border: 1px solid #333; /* Full borders */
        }

        .shade, th {
            background: #e6e6e6 !important; /* Light gray header */
            color: #000 !important;
            font-weight: bold;
            -webkit-print-color-adjust: exact;
        }

        /* Simplified Section Headers */
        .section-header {
            background: #555;
            color: #fff;
            padding: 5px 10px;
            font-weight: bold;
            font-size: 13px;
            margin-top: 20px;
            -webkit-print-color-adjust: exact;
        }

        /* License Details Specific */
        .license-summary {
            margin-top: 20px;
            border: 1px solid #333;
            padding: 10px;
        }

         /* Helpers */
         .text-center { text-align: center; }
         .mb-4 { margin-bottom: 1.5rem; }
    </style>
</head>
<body>

    <!-- Header matching License PDF -->
    <header>
        <section class="left">
           <!-- Placeholder for Coat of Arms if not using helper -->
           <img src="<?= base_url('assets/img/tz-coat.png') ?>" alt="Coat of Arms" style="height: 80px;">
        </section>

        <section class="middle">
            <div class="headings">
                <h5>THE UNITED REPUBLIC OF TANZANIA</h5>
                <h5>MINISTRY OF INDUSTRY AND TRADE</h5>
                <h5>WEIGHTS AND MEASURES AGENCY</h5>
                <h5>APPLICATION DETAILS</h5>
            </div>
        </section>

        <section class="right">
            <img src="<?= base_url('assets/img/wma-logo.png') ?>" alt="WMA Logo" style="height: 80px;">
        </section>
    </header>

    <div class="container">
        <!-- Personal Information -->
        <table>
            <tr class="shade">
                <td colspan="2">PERSONAL PARTICULARS</td>
            </tr>
            <tr>
                <td width="30%">Applicant Name</td>
                <td><?= strtoupper(($application->first_name ?? '') . ' ' . ($application->middle_name ?? '') . ' ' . ($application->last_name ?? '')) ?></td>
            </tr>
            <tr>
                <td>License Number</td>
                <td><?= $application->license_number ?? 'Pending' ?></td>
            </tr>
            <tr>
                 <td>Application Type</td>
                 <td><?= $application->application_type ?? 'New' ?></td>
            </tr>
             <tr>
                <td>
                    <?php 
                        if (isset($application->nationality) && stripos($application->nationality, 'Tanzania') !== false) {
                            echo 'NIDA Number';
                        } else {
                            echo 'Passport Number';
                        }
                    ?>
                </td>
                <td><?= $application->identity_number ?? 'N/A' ?></td>
            </tr>
            <tr>
                <td>Date of Birth</td>
                <td><?= !empty($application->dob) ? date('F d, Y', strtotime($application->dob)) : 'N/A' ?></td>
            </tr>
            <tr>
                <td>Gender</td>
                <td><?= ucfirst($application->gender ?? 'N/A') ?></td>
            </tr>
            <tr>
                <td>Nationality</td>
                <td><?= $application->nationality ?? 'N/A' ?></td>
            </tr>
            <tr>
                <td>Email</td>
                <td><?= $application->email ?? 'N/A' ?></td>
            </tr>
            <tr>
                <td>Phone Number</td>
                <td><?= $application->phone_number ?? 'N/A' ?></td>
            </tr>
            <tr>
                <td>Region</td>
                <td><?= $application->region ?? 'N/A' ?></td>
            </tr>
            <tr>
                <td>District</td>
                <td><?= $application->district ?? 'N/A' ?></td>
            </tr>
             <tr>
                <td>Ward</td>
                <td><?= $application->ward ?? 'N/A' ?></td>
            </tr>
            <tr>
                <td>Street</td>
                <td><?= !empty($application->street) ? $application->street : 'N/A' ?></td>
            </tr>
            <tr>
                <td>Postal Address</td>
                <td><?= !empty($application->postal_address) ? $application->postal_address : 'N/A' ?></td>
            </tr>
        </table>

        <!-- Business Details -->
        <?php if (!empty($application->company_name)): ?>
        <table>
            <tr class="shade">
                <td colspan="2">BUSINESS DETAILS</td>
            </tr>
            <tr>
                <td width="30%">Company Name</td>
                <td><?= $application->company_name ?></td>
            </tr>
            <tr>
                <td>Registration Number</td>
                <td><?= $application->registration_number ?? 'N/A' ?></td>
            </tr>
            <tr>
                <td>TIN Number</td>
                <td><?= $application->tin_number ?? 'N/A' ?></td>
            </tr>
             <tr>
                <td>Company Phone</td>
                <td><?= $application->company_phone ?? 'N/A' ?></td>
            </tr>
            <tr>
                <td>Company Email</td>
                <td><?= $application->company_email ?? 'N/A' ?></td>
            </tr>
             <tr>
                <td>Business Region</td>
                <td><?= $application->business_region ?? 'N/A' ?></td>
            </tr>
            <tr>
                <td>Business District</td>
                <td><?= $application->business_district ?? 'N/A' ?></td>
            </tr>
            <tr>
                 <td>Business Ward</td>
                <td><?= $application->business_ward ?? 'N/A' ?></td>
            </tr>
             <tr>
                <td>Business Street</td>
                <td><?= !empty($application->business_street) ? $application->business_street : 'N/A' ?></td>
            </tr>
            <tr>
                <td>Postal Code</td>
                <td><?= !empty($application->postal_code) ? $application->postal_code : 'N/A' ?></td>
            </tr>
        </table>
        <?php endif; ?>

        <!-- Required Attachments -->
         <?php if (!empty($application->attachments)): ?>
        <table>
            <tr class="shade">
                <td colspan="2">ATTACHMENTS</td>
            </tr>
            <?php foreach ($application->attachments as $doc): ?>
            <tr>
                <td width="50%"><?= $doc->document_type ?? 'Document' ?></td>
                <td><?= $doc->status ?? 'Pending' ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>

        <!-- License Details -->
        <?php if (!empty($application->license_items)): ?>
        <table>
            <tr class="shade">
                <td colspan="7">LICENSE DETAILS</td>
            </tr>
            <tr>
                <th>#</th>
                <th>License Type</th>
                <th>Application Fee</th>
                <th>App. Fee C/N</th>
                <th>License Fee</th>
                <th>Lic. Fee C/N</th>
                <th>Total</th>
            </tr>
            <?php $i = 1; foreach ($application->license_items as $item): ?>
            <tr>
                <td><?= $i++ ?></td>
                <td>
                    <?= $item->type ?? 'N/A' ?>
                     <?php if (!empty($item->selected_instruments)): ?>
                        <br><small>
                        <?php 
                            $rawInsts = $item->selected_instruments;
                            $insts = is_string($rawInsts) ? json_decode($rawInsts) : $rawInsts;
                            if (is_string($insts)) $insts = json_decode($insts);
                            if (!empty($insts) && is_array($insts)) {
                                echo implode(', ', $insts);
                            }
                        ?>
                        </small>
                    <?php endif; ?>
                </td>
                <td><?= number_format($item->application_fee ?? 0, 2) ?></td>
                <td><?= $item->control_number ?? 'Pending' ?></td>
                <td><?= number_format($item->license_bill_amount ?? $item->amount ?? $item->fee ?? 0, 2) ?></td>
                <td><?= $item->license_fee_control_number ?? 'Pending' ?></td>
                <td><strong><?= number_format(($item->license_bill_amount ?? $item->fee ?? 0) + ($item->application_fee ?? 0), 2) ?></strong></td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>

         <!-- Tools & Qualifications -->
        <?php if (!empty($application->tools_list)): ?>
        <table>
             <tr class="shade">
                <td>TOOLS/EQUIPMENTS</td>
            </tr>
             <?php foreach ($application->tools_list as $tool): ?>
            <tr>
                <td><?= htmlspecialchars(is_object($tool) ? ($tool->name ?? 'N/A') : $tool) ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
        
        <?php if (!empty($application->qualifications_list)): ?>
         <table>
             <tr class="shade">
                <td>TECHNICAL QUALIFICATIONS</td>
            </tr>
             <?php foreach ($application->qualifications_list as $qual): ?>
            <tr>
                <td><?= htmlspecialchars(is_object($qual) ? ($qual->name ?? 'N/A') : $qual) ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>

    </div>

    <!-- Auto Print Script -->
    <script>
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        };
    </script>
</body>
</html>
