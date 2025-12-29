<?= $this->extend('Layouts/coreLayout'); ?>
<?= $this->section('content'); ?>

<style>
    /* Scoped Styles for CV View - Optimized for PDF */
    .cv-container {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        max-width: 1000px;
        margin: 0 auto; /* Removed top margin for PDF */
        background: white;
        /* box-shadow removed for PDF */
        border-radius: 0; /* flattened for PDF */
        overflow: hidden;
    }
    
    .cv-container .cv-header { 
        background: #2c5f2d !important; /* Force color for PDF */
        color: white !important; 
        padding: 30px 40px; 
        -webkit-print-color-adjust: exact;
    }
    .cv-container .cv-header h1 { font-size: 24px; margin-bottom: 10px; font-weight: bold; }
    
    .cv-container .header-info { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-top: 20px; font-size: 13px; }
    .cv-container .header-info-item { background: rgba(255,255,255,0.1); padding: 10px; border-radius: 5px; }
    .cv-container .header-info-label { font-size: 10px; text-transform: uppercase; opacity: 0.8; margin-bottom: 3px; }
    .cv-container .header-info-value { font-weight: 500; }
    
    .cv-container .cv-content { padding: 40px; }
    
    .cv-container .cv-section { margin-bottom: 35px; }
    .cv-container .section-title { 
        font-size: 18px; 
        font-weight: 600; 
        color: #2c5f2d; 
        margin-bottom: 20px; 
        padding-bottom: 10px; 
        border-bottom: 2px solid #2c5f2d; 
        display: flex; 
        align-items: center; 
        gap: 10px; 
    }
    .cv-container .section-title svg { width: 20px; height: 20px; fill: #2c5f2d; }
    
    .cv-container .info-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; }
    .cv-container .info-item { padding: 15px; background: #f8f9fa; border-radius: 5px; border-left: 3px solid #2c5f2d; }
    .cv-container .info-label { font-size: 11px; color: #666; text-transform: uppercase; margin-bottom: 5px; font-weight: 600; }
    .cv-container .info-value { font-size: 14px; color: #1a1a1a; font-weight: 500; }
    
    .cv-container .doc-list { list-style: none; padding: 0; margin: 0; }
    .cv-container .doc-item { padding: 12px; background: #f8f9fa; border-radius: 5px; margin-bottom: 10px; display: flex; justify-content: space-between; align-items: center; }
    .cv-container .doc-item svg { width: 16px; height: 16px; fill: #2c5f2d; }
    .cv-container .doc-name { font-size: 14px; color: #1a1a1a; display: flex; align-items: center; gap: 10px; }
    .cv-container .doc-status { padding: 4px 12px; border-radius: 12px; font-size: 11px; font-weight: bold; }
    
    .cv-container .status-uploaded { background: #d4edda; color: #155724; }
    .cv-container .status-approved { background: #d4edda; color: #155724; }
    .cv-container .status-pending { background: #fff3cd; color: #856404; }
    .cv-container .status-returned { background: #f8d7da; color: #721c24; }
    
    .cv-container table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    .cv-container table th { background: #f5f5f5; padding: 12px; text-align: left; font-size: 12px; font-weight: 600; color: #555; border: 1px solid #ddd; }
    .cv-container table td { padding: 12px; font-size: 13px; border: 1px solid #ddd; color: #333; }
    
    .cv-container .qual-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }
    .cv-container .qual-card { background: #f8f9fa; padding: 15px; border-radius: 8px; border-left: 3px solid #2c5f2d; }
    .cv-container .qual-card h4 { font-size: 13px; color: #2c5f2d; margin-bottom: 10px; text-transform: uppercase; font-weight: 600; }
    .cv-container .qual-list { list-style: none; padding: 0; }
    .cv-container .qual-list li { padding: 5px 0; font-size: 13px; color: #555; }
    .cv-container .qual-list li:before { content: "•"; color: #2c5f2d; font-weight: bold; margin-right: 8px; }
    
    .cv-container .timeline { position: relative; padding-left: 30px; margin-top: 10px; }
    .cv-container .timeline-item { position: relative; padding-bottom: 20px; }
    .cv-container .timeline-item:before { content: ''; position: absolute; left: -22px; top: 5px; width: 12px; height: 12px; border-radius: 50%; background: #2c5f2d; border: 3px solid white; box-shadow: 0 0 0 2px #2c5f2d; }
    .cv-container .timeline-item:after { content: ''; position: absolute; left: -17px; top: 17px; bottom: -20px; width: 2px; background: #ddd; }
    .cv-container .timeline-item:last-child:after { display: none; }
    .cv-container .timeline-content { background: #f8f9fa; padding: 12px; border-radius: 5px; }
    .cv-container .timeline-header { font-weight: 600; color: #2c5f2d; margin-bottom: 5px; font-size: 14px; }
    .cv-container .timeline-meta { font-size: 11px; color: #666; margin-bottom: 5px; }
    .cv-container .timeline-comment { font-size: 12px; color: #555; margin-top: 5px; }
</style>

<!-- No Breadcrumbs or Action Bar for PDF View -->

<div class="cv-container">
    <!-- Header -->
    <div class="cv-header">
        <h1><?= strtoupper(($application->first_name ?? '') . ' ' . ($application->middle_name ?? '') . ' ' . ($application->last_name ?? '')) ?></h1>
        <div class="header-info">
            <div class="header-info-item">
                <div class="header-info-label">License Number</div>
                <div class="header-info-value"><?= $application->license_number ?? 'Pending' ?></div>
            </div>
            <div class="header-info-item">
                <div class="header-info-label">Application Type</div>
                <div class="header-info-value"><?= $application->application_type ?? 'New' ?></div>
            </div>
            <div class="header-info-item">
                <div class="header-info-label">Status</div>
                <div class="header-info-value"><?= $application->status ?? 'Pending' ?></div>
            </div>
        </div>
    </div>
    
    <!-- Content -->
    <div class="cv-content">
        <!-- Personal Information -->
        <div class="cv-section">
            <h2 class="section-title">
                <svg viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                Personal Information
            </h2>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">
                        <?php 
                            if (isset($application->nationality) && stripos($application->nationality, 'Tanzania') !== false) {
                                echo 'NIDA Number';
                            } else {
                                echo 'Passport Number';
                            }
                        ?>
                    </div>
                    <div class="info-value"><?= $application->identity_number ?? 'N/A' ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Full Name</div>
                    <div class="info-value"><?= ($application->first_name ?? '') . ' ' . ($application->middle_name ?? '') . ' ' . ($application->last_name ?? '') ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Date of Birth</div>
                    <div class="info-value"><?= !empty($application->dob) ? date('F d, Y', strtotime($application->dob)) : 'N/A' ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Gender</div>
                    <div class="info-value"><?= ucfirst($application->gender ?? 'N/A') ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Nationality</div>
                    <div class="info-value"><?= $application->nationality ?? 'N/A' ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Email</div>
                    <div class="info-value"><?= $application->email ?? 'N/A' ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Phone Number</div>
                    <div class="info-value"><?= $application->phone_number ?? 'N/A' ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Region</div>
                    <div class="info-value"><?= $application->region ?? 'N/A' ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">District</div>
                    <div class="info-value"><?= $application->district ?? 'N/A' ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Ward</div>
                    <div class="info-value"><?= $application->ward ?? 'N/A' ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Street</div>
                    <div class="info-value"><?= !empty($application->street) ? $application->street : 'N/A' ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Postal Address</div>
                    <div class="info-value"><?= !empty($application->postal_address) ? $application->postal_address : 'N/A' ?></div>
                </div>
            </div>
        </div>
        
        <!-- Business Details -->
        <?php if (!empty($application->company_name)): ?>
        <div class="cv-section">
            <h2 class="section-title">
                <svg viewBox="0 0 24 24"><path d="M12 7V3H2v18h20V7H12zM6 19H4v-2h2v2zm0-4H4v-2h2v2zm0-4H4V9h2v2zm0-4H4V5h2v2zm4 12H8v-2h2v2zm0-4H8v-2h2v2zm0-4H8V9h2v2zm0-4H8V5h2v2zm10 12h-8v-2h2v-2h-2v-2h2v-2h-2V9h8v10zm-2-8h-2v2h2v-2zm0 4h-2v2h2v-2z"/></svg>
                Business Details
            </h2>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Company Name</div>
                    <div class="info-value"><?= $application->company_name ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Registration Number</div>
                    <div class="info-value"><?= $application->registration_number ?? 'N/A' ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">TIN Number</div>
                    <div class="info-value"><?= $application->tin_number ?? 'N/A' ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Company Phone</div>
                    <div class="info-value"><?= $application->company_phone ?? 'N/A' ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Company Email</div>
                    <div class="info-value"><?= $application->company_email ?? 'N/A' ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Business Region</div>
                    <div class="info-value"><?= $application->business_region ?? 'N/A' ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Business District</div>
                    <div class="info-value"><?= $application->business_district ?? 'N/A' ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Business Ward</div>
                    <div class="info-value"><?= $application->business_ward ?? 'N/A' ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Business Street</div>
                    <div class="info-value"><?= !empty($application->business_street) ? $application->business_street : 'N/A' ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Postal Code</div>
                    <div class="info-value"><?= !empty($application->postal_code) ? $application->postal_code : 'N/A' ?></div>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Required Attachments -->
        <?php if (!empty($application->attachments)): ?>
        <div class="cv-section">
            <h2 class="section-title">
                <svg viewBox="0 0 24 24"><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg>
                Required Attachments
            </h2>
            <ul class="doc-list">
                <?php foreach ($application->attachments as $doc): ?>
                <li class="doc-item">
                    <div class="doc-name">
                        <svg viewBox="0 0 24 24"><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg>
                        <?= $doc->document_type ?? 'Document' ?>
                    </div>
                    <span class="doc-status status-<?= strtolower($doc->status ?? 'pending') ?>">
                        <?= $doc->status ?? 'Pending' ?>
                    </span>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>
        
        <!-- License Details -->
        <?php if (!empty($application->license_items)): ?>
        <div class="cv-section">
            <h2 class="section-title">
                <svg viewBox="0 0 24 24"><path d="M9 11H7v2h2v-2zm4 0h-2v2h2v-2zm4 0h-2v2h2v-2zm2-7h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V9h14v11z"/></svg>
                License Details
            </h2>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>License Type</th>
                        <th>Application Fee</th>
                        <th>App. Fee C/N</th>
                        <th>License Fee</th>
                        <th>Lic. Fee C/N</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1; foreach ($application->license_items as $item): ?>
                    <tr>
                        <td><?= $i++ ?></td>
                        <td><?= $item->type ?? 'N/A' ?></td>
                        <td>TZS <?= number_format($item->application_fee ?? 0, 2) ?></td>
                        <td><?= $item->control_number ?? 'Pending' ?></td>
                        <td>TZS <?= number_format($item->fee ?? 0, 2) ?></td>
                        <td><?= $item->license_fee_control_number ?? 'Pending' ?></td>
                        <td><strong>TZS <?= number_format(($item->fee ?? 0) + ($item->application_fee ?? 0), 2) ?></strong></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
        
        <!-- Tools & Qualifications -->
        <?php if (!empty($application->tools_list) || !empty($application->qualifications_list) || !empty($application->experience_list)): ?>
        <div class="cv-section">
            <h2 class="section-title">
                <svg viewBox="0 0 24 24"><path d="M22.7 19l-9.1-9.1c.9-2.3.4-5-1.5-6.9-2-2-5-2.4-7.4-1.3L9 6 6 9 1.6 4.7C.4 7.1.9 10.1 2.9 12.1c1.9 1.9 4.6 2.4 6.9 1.5l9.1 9.1c.4.4 1 .4 1.4 0l2.3-2.3c.5-.4.5-1.1.1-1.4z"/></svg>
                Tools & Qualifications
            </h2>
            <div class="qual-grid">
                <?php if (!empty($application->tools_list)): ?>
                <div class="qual-card">
                    <h4>Tools & Equipment</h4>
                    <ul class="qual-list">
                        <?php foreach ($application->tools_list as $tool): ?>
                        <li><?= htmlspecialchars(is_object($tool) ? ($tool->name ?? 'N/A') : $tool) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($application->qualifications_list)): ?>
                <div class="qual-card">
                    <h4>Technical Qualifications</h4>
                    <ul class="qual-list">
                        <?php foreach ($application->qualifications_list as $qual): ?>
                        <li><?= htmlspecialchars(is_object($qual) ? ($qual->name ?? 'N/A') : $qual) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($application->experience_list)): ?>
                <div class="qual-card">
                    <h4>Work Experience</h4>
                    <ul class="qual-list">
                        <?php foreach ($application->experience_list as $exp): ?>
                        <li><?= htmlspecialchars(is_object($exp) ? (($exp->company ?? '') . ' - ' . ($exp->position ?? '')) : $exp) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Approvals -->
        <?php if (!empty($application->approval_logs)): ?>
        <div class="cv-section">
            <h2 class="section-title">
                <svg viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                Approvals
            </h2>
            <div class="timeline">
                <?php foreach ($application->approval_logs as $log): ?>
                <div class="timeline-item">
                    <div class="timeline-content">
                        <div class="timeline-header"><?= $log->stage ?? 'N/A' ?> - <?= $log->status ?? 'N/A' ?></div>
                        <div class="timeline-meta">
                            By: <?= $log->approver ?? 'N/A' ?> • 
                            <?= !empty($log->date) ? date('F d, Y H:i', strtotime($log->date)) : 'N/A' ?>
                        </div>
                        <?php if (!empty($log->comment)): ?>
                        <div class="timeline-comment"><strong>Comment:</strong> <?= htmlspecialchars($log->comment) ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection(); ?>
