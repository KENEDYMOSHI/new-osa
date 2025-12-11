<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap 4.6 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <title><?= $title ?? 'Application Details' ?></title>
    
    <style>
        body {
            background-color: #f3f4f6;
            font-family: 'Inter', sans-serif;
            color: #1f2937;
        }
        
        .header-section {
            background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
            color: white;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .back-button {
            color: white;
            text-decoration: none;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1rem;
            transition: opacity 0.2s;
        }
        
        .back-button:hover {
            color: white;
            opacity: 0.8;
            text-decoration: none;
        }
        
        .info-card {
            background: white;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            border: 1px solid #e5e7eb;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        
        .info-card h3 {
            font-size: 1.25rem;
            font-weight: 700;
            color: #111827;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .info-card h3 i {
            color: #f97316;
        }
        
        .info-item {
            margin-bottom: 1rem;
        }
        
        .info-label {
            font-size: 0.875rem;
            font-weight: 500;
            color: #6b7280;
            margin-bottom: 0.25rem;
        }
        
        .info-value {
            color: #111827;
            font-weight: 500;
        }
        
        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 500;
            display: inline-block;
        }
        
        .status-approved { background-color: #d1fae5; color: #065f46; }
        .status-pending { background-color: #fef3c7; color: #92400e; }
        .status-rejected { background-color: #fee2e2; color: #991b1b; }
        
        .license-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem;
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 0.375rem;
            margin-bottom: 0.5rem;
        }
        
        .license-item:hover {
            background: #f9fafb;
        }
        
        .qualification-card {
            display: flex;
            gap: 0.75rem;
            padding: 0.75rem;
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 0.375rem;
            margin-bottom: 0.75rem;
        }
        
        .qualification-icon {
            width: 40px;
            height: 40px;
            background: #fed7aa;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        
        .qualification-icon i {
            color: #ea580c;
        }
        
        .attachment-card {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            margin-bottom: 0.75rem;
            cursor: pointer;
            transition: all 0.2s;
            background: white;
        }
        
        .attachment-card:hover {
            background: #f9fafb;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .loading-spinner {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 3rem;
        }
        
        .spinner-border {
            width: 3rem;
            height: 3rem;
            border-width: 0.3rem;
            border-color: #f97316;
            border-right-color: transparent;
        }
    </style>
</head>
<body>
    <div class="header-section">
        <div class="container">
            <a href="/" class="back-button">
                <i class="fas fa-arrow-left"></i>
                <span>Back to Applications</span>
            </a>
            <h1 class="mb-2" style="font-size: 2rem; font-weight: 700;">Application Details</h1>
            <p class="mb-0" style="color: rgba(255,255,255,0.9);" id="applicant-name-header">Loading...</p>
        </div>
    </div>

    <div class="container pb-5">
        <!-- Loading State -->
        <div id="loading-state" class="loading-spinner">
            <div class="spinner-border" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>

        <!-- Content (Hidden initially) -->
        <div id="content-area" style="display: none;">
            <div class="row">
                <!-- Applicant Information -->
                <div class="col-md-6">
                    <div class="info-card">
                        <h3><i class="fas fa-user"></i> Applicant Information</h3>
                        <div id="personal-info-content"></div>
                    </div>
                </div>

                <!-- Application Status -->
                <div class="col-md-6">
                    <div class="info-card">
                        <h3><i class="fas fa-clipboard-list"></i> Application Status</h3>
                        <div id="application-info-content"></div>
                    </div>
                </div>
            </div>

            <!-- License Items -->
            <div class="info-card">
                <h3><i class="fas fa-file-contract"></i> License Items</h3>
                <div id="license-items-content"></div>
            </div>

            <!-- Qualifications -->
            <div class="info-card">
                <h3><i class="fas fa-graduation-cap"></i> Qualifications</h3>
                <div id="qualifications-content"></div>
            </div>

            <!-- Attachments -->
            <div class="info-card">
                <h3><i class="fas fa-paperclip"></i> Attachments</h3>
                <div class="row" id="attachments-content"></div>
            </div>
        </div>

        <!-- Error State -->
        <div id="error-state" style="display: none;">
            <div class="info-card text-center py-5">
                <i class="fas fa-exclamation-circle text-muted mb-3" style="font-size: 4rem;"></i>
                <h2 class="text-muted mb-2">Application Not Found</h2>
                <p class="text-muted mb-4">The application you're looking for could not be found.</p>
                <a href="/" class="btn btn-primary">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Applications
                </a>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        const API_KEY = '<?= $apiKey ?? "" ?>';
        const APPLICATION_ID = '<?= $applicationId ?? "" ?>';
        const API_URL = `/api/approval/application/${APPLICATION_ID}`;

        document.addEventListener('DOMContentLoaded', () => {
            fetchApplicationDetails();
        });

        async function fetchApplicationDetails() {
            try {
                const response = await fetch(API_URL, {
                    headers: {
                        'X-API-KEY': API_KEY,
                        'Content-Type': 'application/json'
                    }
                });
                
                if (!response.ok) {
                    throw new Error('Application not found');
                }
                
                const data = await response.json();
                renderApplicationDetails(data);
                
            } catch (error) {
                console.error('Error fetching application details:', error);
                document.getElementById('loading-state').style.display = 'none';
                document.getElementById('error-state').style.display = 'block';
            }
        }

        function renderApplicationDetails(data) {
            // Hide loading, show content
            document.getElementById('loading-state').style.display = 'none';
            document.getElementById('content-area').style.display = 'block';

            // Update header
            const fullName = data.personal_info 
                ? `${data.personal_info.first_name || ''} ${data.personal_info.middle_name || ''} ${data.personal_info.last_name || ''}`.trim()
                : 'Unknown Applicant';
            document.getElementById('applicant-name-header').textContent = fullName;

            // Render Personal Info
            renderPersonalInfo(data.personal_info);

            // Render Application Info
            renderApplicationInfo(data.application_info);

            // Render License Items
            renderLicenseItems(data.license_items || []);

            // Render Qualifications
            renderQualifications(data.qualifications || []);

            // Render Attachments
            renderAttachments([...(data.required_attachments || []), ...(data.qualification_documents || [])]);
        }

        function renderPersonalInfo(info) {
            if (!info) {
                document.getElementById('personal-info-content').innerHTML = '<p class="text-muted">No personal information available.</p>';
                return;
            }

            const html = `
                <div class="info-item">
                    <div class="info-label">Full Name</div>
                    <div class="info-value">${info.first_name || ''} ${info.middle_name || ''} ${info.last_name || ''}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Gender</div>
                    <div class="info-value">${info.gender || 'N/A'}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Date of Birth</div>
                    <div class="info-value">${info.dob ? new Date(info.dob).toLocaleDateString() : 'N/A'}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Phone</div>
                    <div class="info-value">${info.phone_number || 'N/A'}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Email</div>
                    <div class="info-value">${info.email || 'N/A'}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Identity Number</div>
                    <div class="info-value">${info.nida || info.passport_number || 'N/A'}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Nationality</div>
                    <div class="info-value">${info.nationality || 'N/A'}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Address</div>
                    <div class="info-value">${info.street || ''}, ${info.ward || ''}, ${info.district || ''}, ${info.region || ''}</div>
                </div>
            `;
            document.getElementById('personal-info-content').innerHTML = html;
        }

        function renderApplicationInfo(info) {
            if (!info) {
                document.getElementById('application-info-content').innerHTML = '<p class="text-muted">No application information available.</p>';
                return;
            }

            const statusClass = info.status === 'Approved' ? 'status-approved' : 
                               info.status === 'Rejected' ? 'status-rejected' : 'status-pending';

            const html = `
                <div class="info-item">
                    <div class="info-label">Application Type</div>
                    <div class="info-value">${info.application_type || 'New'}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Status</div>
                    <div><span class="status-badge ${statusClass}">${info.status || 'Pending'}</span></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Control Number</div>
                    <div class="info-value" style="font-family: monospace;">${info.control_number || 'N/A'}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Submission Date</div>
                    <div class="info-value">${info.created_at ? new Date(info.created_at).toLocaleString() : 'N/A'}</div>
                </div>
            `;
            document.getElementById('application-info-content').innerHTML = html;
        }

        function renderLicenseItems(items) {
            if (!items || items.length === 0) {
                document.getElementById('license-items-content').innerHTML = '<p class="text-muted">No license items found.</p>';
                return;
            }

            const html = items.map(item => `
                <div class="license-item">
                    <span class="font-weight-bold">${item.license_type || 'Unknown License'}</span>
                    <span class="text-primary font-weight-bold">TZS ${formatCurrency(item.fee || 0)}</span>
                </div>
            `).join('');

            document.getElementById('license-items-content').innerHTML = html;
        }

        function renderQualifications(qualifications) {
            if (!qualifications || qualifications.length === 0) {
                document.getElementById('qualifications-content').innerHTML = '<p class="text-muted">No qualifications listed.</p>';
                return;
            }

            const html = qualifications.map(qual => `
                <div class="qualification-card">
                    <div class="qualification-icon">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="font-weight-bold">${qual.level || qual.qualification_level || 'N/A'}</div>
                        <div class="text-muted">${qual.course || qual.field_of_study || 'N/A'}</div>
                        <div class="small text-muted">${qual.institution || 'N/A'} - ${qual.year || qual.graduation_year || 'N/A'}</div>
                    </div>
                </div>
            `).join('');

            document.getElementById('qualifications-content').innerHTML = html;
        }

        function renderAttachments(attachments) {
            if (!attachments || attachments.length === 0) {
                document.getElementById('attachments-content').innerHTML = '<div class="col-12"><p class="text-muted">No attachments found.</p></div>';
                return;
            }

            const html = attachments.map(doc => `
                <div class="col-md-6">
                    <div class="attachment-card" onclick="viewDocument('${doc.id}')">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-file-pdf text-danger mr-3" style="font-size: 2rem;"></i>
                            <div>
                                <div class="font-weight-bold">${doc.document_type || 'Document'}</div>
                                <div class="small text-muted">${doc.original_name || 'file.pdf'}</div>
                            </div>
                        </div>
                        <i class="fas fa-eye text-primary"></i>
                    </div>
                </div>
            `).join('');

            document.getElementById('attachments-content').innerHTML = html;
        }

        function formatCurrency(amount) {
            if (!amount) return '0.00';
            return parseFloat(amount).toLocaleString('en-US', { minimumFractionDigits: 2 });
        }

        function viewDocument(docId) {
            // Open document in new tab
            window.open(`/api/admin/document/${docId}/view`, '_blank');
        }
    </script>
</body>
</html>
