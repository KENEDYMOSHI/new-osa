<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap 4.6 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <title><?= $title ?? 'License Approval' ?></title>
    
    <style>
        body {
            background-color: #f3f4f6;
            font-family: 'Inter', sans-serif;
            color: #1f2937;
        }
        
        /* Navbar / Top Header if needed, but tabs act as header in image */
        
        /* Tabs Container */
        .tabs-container {
            background: white;
            padding: 1rem 2rem 0;
            border-bottom: 1px solid #e5e7eb;
            margin-bottom: 2rem;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }
        
        .nav-tabs {
            border-bottom: none;
        }
        
        .nav-link {
            border: none;
            color: #6b7280;
            font-weight: 500;
            padding: 1rem 1.5rem;
            margin-bottom: -1px; /* Align with border */
        }
        
        .nav-link:hover {
            border: none;
            color: #111827;
        }
        
        .nav-link.active {
            color: #111827;
            border: none;
            border-bottom: 2px solid #111827; /* Dark underline for active tab */
            background: transparent;
        }
        
        /* Content Card */
        .content-card {
            background: white;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            border: 1px solid #e5e7eb;
            overflow: hidden;
            margin-bottom: 2rem;
        }
        
        .card-header-custom {
            padding: 1.5rem 2rem;
            border-bottom: 1px solid #f3f4f6;
        }
        
        .card-header-custom h2 {
            font-size: 1.25rem;
            font-weight: 700;
            color: #111827;
            margin: 0;
        }
        
        .card-header-custom p {
            color: #6b7280;
            margin-top: 0.25rem;
            margin-bottom: 0;
            font-size: 0.875rem;
        }
        
        /* Table */
        .table thead th {
            border-top: none;
            border-bottom: 1px solid #e5e7eb;
            color: #6b7280;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-weight: 600;
            padding: 1rem 1.5rem;
            background-color: #f9fafb;
        }
        
        .table td {
            vertical-align: middle;
            padding: 1rem 1.5rem;
            color: #111827;
            font-size: 0.875rem;
            border-top: 1px solid #e5e7eb;
        }
        
        .avatar-circle {
            width: 40px;
            height: 40px;
            background-color: #e5e7eb;
            color: #4b5563;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.875rem;
            margin-right: 1rem;
        }
        
        .applicant-info h6 {
            margin: 0;
            font-weight: 600;
            color: #111827;
        }
        
        .applicant-info small {
            color: #6b7280;
        }
        
        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
        }
        
        .status-submitted { background-color: #fef3c7; color: #92400e; } /* Yellow */
        .status-approved { background-color: #d1fae5; color: #065f46; } /* Green */
        .status-rejected { background-color: #fee2e2; color: #991b1b; } /* Red */
        
        .action-link {
            font-weight: 600;
            font-size: 0.875rem;
            margin-right: 1rem;
            cursor: pointer;
            text-decoration: none !important;
        }
        
        .link-view { color: #2563eb; }
        .link-approve { color: #059669; }
        
    </style>
</head>
<body>

    <!-- Tabs Header -->
    <div class="tabs-container">
        <ul class="nav nav-tabs" id="approvalTabs" role="tablist">
            <!-- Ignored "Applications" tab as per request, starting with Manager -->
            <li class="nav-item">
                <a class="nav-link active" id="manager-tab" data-toggle="tab" href="#manager" role="tab" aria-controls="manager" aria-selected="true">Manager</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="surveillance-tab" data-toggle="tab" href="#surveillance" role="tab" aria-controls="surveillance" aria-selected="false">Surveillance</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="dts-tab" data-toggle="tab" href="#dts" role="tab" aria-controls="dts" aria-selected="false">DTS</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="ceo-tab" data-toggle="tab" href="#ceo" role="tab" aria-controls="ceo" aria-selected="false">CEO</a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Settings</a>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="/approval/settings">License Settings</a>
                </div>
            </li>
        </ul>
        <a href="/approval/logout" class="btn btn-outline-danger btn-sm float-right" style="margin-top: -3.5rem;">
            <i class="fas fa-sign-out-alt mr-1"></i> Logout
        </a>
    </div>

    <div class="container-fluid px-5"> <!-- Wide container -->
        <div class="tab-content" id="approvalTabContent">
            
            <!-- Manager View -->
            <div class="tab-pane fade show active" id="manager" role="tabpanel" aria-labelledby="manager-tab">
                <div class="content-card">
                    <div class="card-header-custom">
                        <h2>Manager View</h2>
                        <p>Viewing data for Manager.</p>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="table-manager">
                            <thead>
                                <tr>
                                    <th>Applicant Name</th>
                                    <th>Application Type</th>
                                    <th>Control Number</th>
                                    <th>Attachments</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="tbody-manager">
                                <tr><td colspan="7" class="text-center py-4">Loading...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Surveillance View -->
            <div class="tab-pane fade" id="surveillance" role="tabpanel" aria-labelledby="surveillance-tab">
                <div class="content-card">
                    <div class="card-header-custom">
                        <h2>Surveillance View</h2>
                        <p>Viewing data for Surveillance.</p>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="table-surveillance">
                            <thead>
                                <tr>
                                    <th>Applicant Name</th>
                                    <th>Application Type</th>
                                    <th>Control Number</th>
                                    <th>Attachments</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="tbody-surveillance">
                                <tr><td colspan="7" class="text-center py-4">Loading...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
             <!-- DTS View -->
             <div class="tab-pane fade" id="dts" role="tabpanel" aria-labelledby="dts-tab">
                <div class="content-card">
                    <div class="card-header-custom">
                        <h2>DTS View</h2>
                        <p>Viewing data for DTS.</p>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="table-dts">
                            <thead>
                                <tr>
                                    <th>Applicant Name</th>
                                    <th>Application Type</th>
                                    <th>Control Number</th>
                                    <th>Attachments</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="tbody-dts">
                                <tr><td colspan="7" class="text-center py-4">Loading...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
             <!-- CEO View -->
             <div class="tab-pane fade" id="ceo" role="tabpanel" aria-labelledby="ceo-tab">
                <div class="content-card">
                    <div class="card-header-custom">
                        <h2>CEO View</h2>
                        <p>Viewing data for CEO.</p>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="table-ceo">
                            <thead>
                                <tr>
                                    <th>Applicant Name</th>
                                    <th>Application Type</th>
                                    <th>Control Number</th>
                                    <th>Attachments</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="tbody-ceo">
                                <tr><td colspan="7" class="text-center py-4">Loading...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        const API_KEY = '<?= $apiKey ?? "" ?>';
        const API_URL = '/api/approval/applications';
        const UPDATE_STATUS_URL = '/api/approval/update-status'; // Updated endpoint

        // Fetch data on load
        document.addEventListener('DOMContentLoaded', () => {
             fetchApplications();
        });

        async function fetchApplications() {
            try {
                const response = await fetch(API_URL, {
                    headers: {
                        'X-API-KEY': API_KEY,
                        'Content-Type': 'application/json'
                    }
                });
                
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                
                const data = await response.json();
                renderTables(data);
                
            } catch (error) {
                console.error('Error fetching applications:', error);
                document.querySelectorAll('tbody').forEach(el => {
                    el.innerHTML = `<tr><td colspan="7" class="text-center py-4 text-danger">Error loading data. Please try again.</td></tr>`;
                });
            }
        }

        function renderTables(items) {
            const tbodyManager = document.getElementById('tbody-manager');
            const tbodySurveillance = document.getElementById('tbody-surveillance');
            const tbodyDts = document.getElementById('tbody-dts');
            const tbodyCeo = document.getElementById('tbody-ceo');
            
            // Clear loading
            [tbodyManager, tbodySurveillance, tbodyDts, tbodyCeo].forEach(tbody => tbody.innerHTML = '');
            
            if (items.length === 0) {
                 [tbodyManager, tbodySurveillance, tbodyDts, tbodyCeo].forEach(tbody => {
                    tbody.innerHTML = `<tr><td colspan="7" class="text-center py-4">No applications found.</td></tr>`;
                 });
                 return;
            }

            // Helper to get stage rank
            const getStageRank = (stage) => {
                if (!stage || stage === 'Manager') return 1;
                if (stage === 'Surveillance') return 2;
                if (stage === 'DTS') return 3;
                if (stage === 'CEO') return 4;
                if (stage === 'Completed') return 5;
                return 0;
            };

            // Filter items by stage (Cumulative)
            const managerItems = items.filter(i => getStageRank(i.approval_stage) >= 1);
            const surveillanceItems = items.filter(i => getStageRank(i.approval_stage) >= 2);
            const dtsItems = items.filter(i => getStageRank(i.approval_stage) >= 3);
            const ceoItems = items.filter(i => getStageRank(i.approval_stage) >= 4);

            tbodyManager.innerHTML = managerItems.length ? managerItems.map(i => createAppRow(i)).join('') : emptyRow();
            tbodySurveillance.innerHTML = surveillanceItems.length ? surveillanceItems.map(i => createAppRow(i)).join('') : emptyRow();
            tbodyDts.innerHTML = dtsItems.length ? dtsItems.map(i => createAppRow(i)).join('') : emptyRow();
            tbodyCeo.innerHTML = ceoItems.length ? ceoItems.map(i => createAppRow(i)).join('') : emptyRow();
        }
        
        function emptyRow() {
            return `<tr><td colspan="7" class="text-center py-4 text-muted">No pending items in this stage.</td></tr>`;
        }

        function createAppRow(item) {
            const initials = getInitials(item.applicant_name || 'Unknown');
            const statusClass = getStatusClass(item.status);
            const attachmentsCount = item.attachment_count || 0;
            const licenseName = item.license_type || 'Unknown License';
            
            // Show item fee, not total app amount
            const fee = item.fee || 0;
            
            const isCompleted = item.approval_stage === 'Completed';

            // Generate History HTML
            let historyHtml = '';
            
            const getStatusColor = (status) => {
                if (status === 'Pending') return 'text-warning';
                if (status === 'Rejected') return 'text-danger';
                return 'text-success';
            };
            
            const getStatusIcon = (status) => {
               if (status === 'Pending') return 'fa-clock';
               if (status === 'Rejected') return 'fa-times-circle';
               return 'fa-check-circle';
            };

            if (item.approver_stage_1) {
                const s = item.status_stage_1 || 'Approved';
                historyHtml += `<div class="small text-muted mb-0"><i class="fas ${getStatusIcon(s)} ${getStatusColor(s)} mr-1" style="font-size: 0.7em;"></i> Mgr: ${item.approver_stage_1} <span class="${getStatusColor(s)}">(${s})</span></div>`;
            }
            if (item.approver_stage_2) {
                const s = item.status_stage_2 || 'Approved';
                historyHtml += `<div class="small text-muted mb-0"><i class="fas ${getStatusIcon(s)} ${getStatusColor(s)} mr-1" style="font-size: 0.7em;"></i> Surv: ${item.approver_stage_2} <span class="${getStatusColor(s)}">(${s})</span></div>`;
            }
            if (item.approver_stage_3) {
                const s = item.status_stage_3 || 'Approved';
                historyHtml += `<div class="small text-muted mb-0"><i class="fas ${getStatusIcon(s)} ${getStatusColor(s)} mr-1" style="font-size: 0.7em;"></i> DTS: ${item.approver_stage_3} <span class="${getStatusColor(s)}">(${s})</span></div>`;
            }
            if (item.approver_stage_4) {
                const s = item.status_stage_4 || 'Approved';
                historyHtml += `<div class="small text-muted mb-0"><i class="fas ${getStatusIcon(s)} ${getStatusColor(s)} mr-1" style="font-size: 0.7em;"></i> CEO: ${item.approver_stage_4} <span class="${getStatusColor(s)}">(${s})</span></div>`;
            }
            
            // Dropdown Action Menu
            const actionDropdown = `
                <div class="dropdown">
                    <button class="btn btn-sm btn-light dropdown-toggle font-weight-bold" type="button" data-toggle="dropdown" aria-expanded="false">
                        Action
                    </button>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a class="dropdown-item text-success" href="#" onclick="updateStatus('${item.id}', 'Approve')"><i class="fas fa-check mr-2"></i> Approve</a>
                        <a class="dropdown-item text-danger" href="#" onclick="updateStatus('${item.id}', 'Reject')"><i class="fas fa-times mr-2"></i> Reject</a>
                        <a class="dropdown-item text-warning" href="#" onclick="updateStatus('${item.id}', 'Pending')"><i class="fas fa-clock mr-2"></i> Pending</a>
                        <div class="dropdown-divider"></div>
                    </div>
                </div>
                <button class="btn btn-sm btn-outline-primary ml-1" onclick="viewApplication('${item.id}')" title="View Application">
                    <i class="fas fa-eye"></i>
                </button>
            `;

            return `
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="avatar-circle">${initials}</div>
                            <div class="applicant-info">
                                <h6>${item.applicant_name}</h6>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div><strong>${licenseName}</strong></div>
                        <small class="text-muted">${item.application_type || 'New'}</small>
                    </td>
                    <td>
                        <div><strong>${item.control_number || 'N/A'}</strong></div>
                        <small class="text-muted">Item Fee: TZS ${formatCurrency(fee)}</small>
                    </td>
                    <td>
                        <strong>${attachmentsCount} Files</strong> 
                        <a href="#" class="ml-2 btn btn-sm btn-light text-primary" onclick="viewAttachments('${item.id}'); return false;" title="View Attachments"><i class="fas fa-eye"></i></a>
                    </td>
                    <td>
                        <span class="status-badge ${statusClass}">${item.status}</span>
                        <div class="mt-2" style="font-size: 0.75rem;">
                            ${historyHtml}
                        </div>
                    </td>
                    <td>${item.created_at_formatted}</td>
                    <td>
                        ${!isCompleted ? actionDropdown : '<span class="text-success small font-weight-bold"><i class="fas fa-check-circle"></i> Completed</span>'}
                    </td>
                </tr>
            `;
        }

        function getInitials(name) {
            return name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase();
        }

        function getStatusClass(status) {
            if (status === 'Submitted' || status === 'Pending') return 'status-submitted';
            if (status && status.includes('Approved')) return 'status-approved';
            if (status === 'Rejected') return 'status-rejected';
            return 'status-submitted';
        }

        function formatCurrency(amount) {
            if (!amount) return '0.00';
            return parseFloat(amount).toLocaleString('en-US', { minimumFractionDigits: 2 });
        }
        
        async function updateStatus(id, action) {
            const confirmMsg = `Are you sure you want to mark this application as '${action}'?`;
            if(!confirm(confirmMsg)) return;
            
            try {
                const response = await fetch(UPDATE_STATUS_URL, {
                    method: 'POST',
                    headers: {
                        'X-API-KEY': API_KEY,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ appId: id, action: action })
                });
                
                if (response.ok) {
                    // alert('Status updated successfully!'); // Optional alert, or just refresh to show movement
                    fetchApplications(); // Refresh table to reflect changes (item might move stage)
                } else {
                    alert('Failed to update status.');
                }
            } catch (e) {
                console.error(e);
                alert('Error processing request.');
            }
        }
        
        function viewApplication(id) {
            window.location.href = '/viewApplication/' + id;
        }

        function viewAttachments(id) {
            alert('View attachments for Application ID: ' + id);
        }
    </script>
</body>
</html>