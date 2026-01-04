import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { Router, RouterModule } from '@angular/router';
import { DomSanitizer, SafeResourceUrl } from '@angular/platform-browser';
import { AuthService } from '../../core/services/auth.service';
import { LicenseService } from '../../services/license.service';
import Swal from 'sweetalert2';

import { ModalComponent } from '../../shared/components/ui/modal/modal.component';

@Component({
  selector: 'app-license-application',
  standalone: true,
  imports: [CommonModule, FormsModule, RouterModule, ModalComponent],
  templateUrl: './license-application.component.html',
  styleUrls: ['./license-application.component.css']
})
export class LicenseApplicationComponent implements OnInit {
  personalInfo: any = {};
  companyInfo: any = {};
  applicationType: string = 'New'; // Default
  applicationFees: any[] = [];
  
  // Dynamic Form Data
  previousLicenses: any[] = [];
  qualifications: any[] = [];
  experiences: any[] = [];
  tools: any[] = [];

  // Helper methods for dynamic forms
  addPreviousLicense() {
    this.previousLicenses.push({ licenseNumber: '', dateIssued: '', class: '' });
  }

  removePreviousLicense(index: number) {
    this.previousLicenses.splice(index, 1);
  }

  addQualification() {
    this.qualifications.push({ institution: '', award: '', year: '' });
  }

  removeQualification(index: number) {
    this.qualifications.splice(index, 1);
  }

  addExperience() {
    this.experiences.push({ company: '', position: '', years: '' });
  }

  removeExperience(index: number) {
    this.experiences.splice(index, 1);
  }

  addTool() {
    this.tools.push({ name: '', serialNumber: '', capacity: '' });
  }

  removeTool(index: number) {
    this.tools.splice(index, 1);
  }
  
  // ... licenseTypes ...



  licenseTypes: { 
    id: string; 
    name: string; 
    fee: number; 
    description: string; 
    selected: boolean; 
    submitted?: boolean; 
    restrictionReason?: string; 
    controlNumber?: string; 
    paymentStatus?: string; 
    billAmount?: number; 
    applicationFee?: number; 
    disabled?: boolean;
    availableInstruments?: string[]; 
    criteria?: { min?: number, max?: number };
    userSelectedInstruments?: string[];
    // Restriction properties
    isRestricted?: boolean;
    alreadyApplied?: boolean;
    daysRemaining?: number;
    availableDate?: string;
    ceoApprovedAt?: string;
  }[] = [];

  // Approved licenses with restriction info
  approvedLicenses: any[] = [];
  restrictedLicenseTypes: Set<string> = new Set();

  totalAmount = 0;
  declarationAccepted = false;
  isSubmitting = false;
  applicationId: string | null = null;

  // Interview Modal
  showInterviewModal: boolean = false;
  selectedInterview: any = null;

  // Mock interview data for testing (remove this in production)
  mockInterview = {
    result: 'PASS',
    date: new Date(),
    panel: 'John Doe, Jane Smith, Robert Johnson',
    theory: 85,
    practical: 90,
    total: 175,
    comments: 'Excellent performance in both theory and practical assessments. Candidate demonstrated strong understanding of weights and measures principles.'
  };


  baseDocuments: { id: string; name: string; date: string; status: 'Not Uploaded' | 'Uploaded' | 'Returned' | 'Resubmitted' | 'Pending'; fileName?: string | null; dbId?: string; submitted?: boolean; rejectionReason?: string; viewed?: boolean; pendingFile?: File }[] = [
    { id: 'tin', name: 'Tax Payer Identification Number (TIN)', date: '-', status: 'Not Uploaded', viewed: false },
    { id: 'businessLicense', name: 'Business License', date: '-', status: 'Not Uploaded', viewed: false },
    { id: 'taxClearance', name: 'Certificate Of Tax Clearance', date: '-', status: 'Not Uploaded', viewed: false },
    { id: 'brela', name: 'Certificate of Registration/Incorporation from BRELA', date: '-', status: 'Not Uploaded', viewed: false },
    { id: 'identity', name: 'Identity Card (National ID / Driver\'s License / Voter ID)', date: '-', status: 'Not Uploaded', viewed: false }
  ];

  renewalDocuments: { id: string; name: string; date: string; status: 'Not Uploaded' | 'Uploaded' | 'Returned' | 'Resubmitted' | 'Pending'; fileName?: string | null; dbId?: string; submitted?: boolean; rejectionReason?: string; viewed?: boolean; pendingFile?: File }[] = [
    { id: 'correctness', name: 'Certificate of Correctness', date: '-', status: 'Not Uploaded', viewed: false },
    { id: 'previousLicense', name: 'Previous License', date: '-', status: 'Not Uploaded', viewed: false }
  ];

  qualificationDocuments: { id: string; name: string; date: string; status: 'Not Uploaded' | 'Uploaded' | 'Returned' | 'Resubmitted' | 'Pending'; fileName?: string | null; dbId?: string; submitted?: boolean; rejectionReason?: string; viewed?: boolean; pendingFile?: File }[] = [
    { id: 'psle', name: 'Primary School Leaving Certificate (PSLE)', date: '-', status: 'Not Uploaded', viewed: false },
    { id: 'csee', name: 'Certificate of Secondary Education Examination (CSEE)', date: '-', status: 'Not Uploaded', viewed: false },
    { id: 'acsee', name: 'Advanced Certificate of Secondary Education Examination (ACSEE)', date: '-', status: 'Not Uploaded', viewed: false },
    { id: 'veta', name: 'Basic Certificate - Vocational Education and Training Authority (VETA)', date: '-', status: 'Not Uploaded', viewed: false },
    { id: 'nta4', name: 'Basic Certificate (NTA Level 4)', date: '-', status: 'Not Uploaded', viewed: false },
    { id: 'nta5', name: 'Technician Certificate (NTA Level 5)', date: '-', status: 'Not Uploaded', viewed: false },
    { id: 'nta6', name: 'Ordinary Diploma (NTA Level 6)', date: '-', status: 'Not Uploaded', viewed: false },
    { id: 'specialized', name: 'Other Specialized Certificates', date: '-', status: 'Not Uploaded', viewed: false },
    { id: 'bachelor', name: 'Bachelor\'s Degree', date: '-', status: 'Not Uploaded', viewed: false }
  ];

  requiredDocuments: { id: string; name: string; date: string; status: 'Not Uploaded' | 'Uploaded' | 'Returned' | 'Resubmitted' | 'Pending'; fileName?: string | null; dbId?: string; submitted?: boolean; rejectionReason?: string; viewed?: boolean; pendingFile?: File }[] = [];

  selectedDoc: any = null;

  today: Date = new Date();

  constructor(
    public router: Router,
    private authService: AuthService,
    private licenseService: LicenseService,
    private sanitizer: DomSanitizer
  ) {}

  ngOnInit(): void {
    // this.updateRequiredDocuments(); // Moved to loadLicenseTypes to ensure types are loaded first
    this.loadProfileData();
    this.loadLicenseTypes();
    this.loadApplicationFees();
    this.loadApprovedLicenses(); // NEW: Load approved licenses
    // this.loadUserDocuments(); // Removed to avoid double call (called in updateRequiredDocuments)
  }

  // Restoring missing properties and methods
  flag: string = '🇹🇿'; // Default
  isApplicationSubmitted = false;

  updateRequiredDocuments() {
    // Clone base documents to avoid reference issues
    let docs = JSON.parse(JSON.stringify(this.baseDocuments));
    
    if (this.applicationType === 'Renewal') {
      const renewalDocs = JSON.parse(JSON.stringify(this.renewalDocuments));
      docs = [...docs, ...renewalDocs];
    }
    
    // Preserve status of existing documents if switching types
    if (this.requiredDocuments.length > 0) {
        docs.forEach((newDoc: any) => {
            const existing = this.requiredDocuments.find(d => d.id === newDoc.id);
            if (existing && existing.status === 'Uploaded') {
                newDoc.status = existing.status;
                newDoc.date = existing.date;
                newDoc.fileName = existing.fileName;
                newDoc.dbId = existing.dbId;
            }
        });
    }

    this.requiredDocuments = docs;
    // Reload documents from backend to ensure status is up to date
    this.loadUserDocuments(); 
  }

  loadProfileData() {
    this.authService.getProfile().subscribe({
      next: (data: any) => {
        if (data.personalInfo) {
          this.personalInfo = { ...data.personalInfo };
          if (!this.personalInfo.email && data.user) {
              this.personalInfo.email = data.user.email;
          }
          
          // Determine citizenship and flag
          const nationality = this.personalInfo.nationality || 'Tanzania'; // Default to Tanzania if missing
          this.citizenship = nationality.toLowerCase() === 'tanzania' ? 'Citizen' : 'Non-Citizen';
          this.flag = this.getCountryFlag(nationality);
          this.calculateTotal();
        }
  
        if (data.businessInfo) {
          this.companyInfo = { ...data.businessInfo };
        }
      },
      error: (err: any) => {
        console.error('Error fetching profile:', err);
        const errorMessage = err?.error?.message || err?.message || 'Failed to load profile data.';
        Swal.fire('Error', errorMessage, 'error');
      }
    });
  }

  getCountryFlag(country: string): string {
    // Basic mapping for common countries, fallback to generic flag or code
    const countryMap: { [key: string]: string } = {
      'tanzania': '🇹🇿',
      'kenya': '🇰🇪',
      'uganda': '🇺🇬',
      'rwanda': '🇷🇼',
      'burundi': '🇧🇮',
      'congo': '🇨🇩',
      'zambia': '🇿🇲',
      'malawi': '🇲🇼',
      'mozambique': '🇲🇿',
      'south africa': '🇿🇦',
      'nigeria': '🇳🇬',
      'ghana': '🇬🇭',
      'usa': '🇺🇸',
      'uk': '🇬🇧',
      'china': '🇨🇳',
      'india': '🇮🇳'
    };
    return countryMap[country.toLowerCase()] || '🏳️';
  }

  loadLicenseTypes() {
    this.licenseService.getLicenseTypes().subscribe({
      next: (types: any[]) => {
        this.licenseTypes = types.map((t: any) => {
          let parsedInstruments: string[] = [];
          if (t.selected_instruments) {
            try {
              if (Array.isArray(t.selected_instruments)) {
                parsedInstruments = t.selected_instruments;
              } else {
                 const parsed = JSON.parse(t.selected_instruments);
                 if (Array.isArray(parsed)) parsedInstruments = parsed;
              }
            } catch (e) {
               parsedInstruments = [];
            }
          }

          let parsedCriteria: { min?: number, max?: number } = {};
          if (t.criteria) {
             try {
                // Handle double-encoded JSON or direct object
                const rawCriteria = (typeof t.criteria === 'string') ? JSON.parse(t.criteria) : t.criteria;
                parsedCriteria = (typeof rawCriteria === 'string') ? JSON.parse(rawCriteria) : rawCriteria;
                
                // Ensure numbers
                if (parsedCriteria.min) parsedCriteria.min = Number(parsedCriteria.min);
                if (parsedCriteria.max) parsedCriteria.max = Number(parsedCriteria.max);
                
                console.log(`Parsed criteria for ${t.name}:`, parsedCriteria);
             } catch (e) {
                console.warn(`Failed to parse criteria for ${t.name}:`, t.criteria, e);
                parsedCriteria = {};
             }
          }

          return {
            id: t.id,
            name: t.name,
            fee: parseFloat(t.fee),
            description: t.description,
            selected: false,
            submitted: false,
            // New fields
            availableInstruments: parsedInstruments,
            criteria: parsedCriteria,
            userSelectedInstruments: [] as string[]
          };
        });

        // Sort alphabetically by name
        this.licenseTypes.sort((a, b) => {
            const nameA = a.name.toUpperCase();
            const nameB = b.name.toUpperCase();
            if (nameA < nameB) return -1;
            if (nameA > nameB) return 1;
            return 0;
        });

        console.log('License types loaded:', this.licenseTypes);
        this.updateRequiredDocuments(); 
      },
      error: (err: any) => {
        console.error('Failed to load license types:', err);
        Swal.fire('Error', 'Failed to load license types. Please refresh the page.', 'error');
      }
    });
  }

  // Check if max instruments reached
  isMaxReached(license: any): boolean {
    if (!license.criteria || !license.criteria.max) return false;
    return (license.userSelectedInstruments?.length || 0) >= license.criteria.max;
  }

  // Toggle instrument selection
  toggleInstrument(license: any, instrument: string, event: Event) {
    event.stopPropagation(); 
    
    if (!license.userSelectedInstruments) {
        license.userSelectedInstruments = [];
    }

    const index = license.userSelectedInstruments.indexOf(instrument);
    
    if (index === -1) {
        // Adding
        if (this.isMaxReached(license)) {
             event.preventDefault();
             return;
        }
        license.userSelectedInstruments.push(instrument);
    } else {
        // Removing
        license.userSelectedInstruments.splice(index, 1);
    }

    // Smart Auto-Selection/Deselection Logic based on Criteria Validity
    const status = this.getCriteriaStatus(license);
    
    // Logic: If selection is VALID (Min requirements met) AND count > 0, Select the license.
    // Otherwise, Deselect.
    const shouldSelect = status.valid && (license.userSelectedInstruments.length > 0);
    
    if (license.selected !== shouldSelect) {
        license.selected = shouldSelect;
        this.calculateTotal();
    }
  }
  
  // Helper to check criteria status for UI feedback
  getCriteriaStatus(license: any): { valid: boolean, message: string } {
      if (!license.availableInstruments || license.availableInstruments.length === 0) {
          return { valid: true, message: '' };
      }
      
      const count = license.userSelectedInstruments ? license.userSelectedInstruments.length : 0;
      const min = license.criteria?.min || 0;
      const max = license.criteria?.max;

      if (min > 0 && count < min) {
          return { valid: false, message: `Select at least ${min} instrument(s)` };
      }
      
      // Max is handled on toggle, but good to check
      if (max && count > max) {
          return { valid: false, message: `Max ${max} instruments allowed` };
      }

      return { valid: true, message: 'Criteria met' };
  }

  loadApplicationFees() {
    this.licenseService.getApplicationFees().subscribe({
      next: (fees: any[]) => {
        this.applicationFees = fees;
        console.log('Application fees loaded:', this.applicationFees);
        this.calculateTotal(); // Recalculate once fees are loaded
      },
      error: (err: any) => {
        console.error('Failed to load application fees:', err);
      }
    });
  }

  loadUserDocuments() {
    console.log('Loading user documents...');
    this.licenseService.getUserDocuments().subscribe({
      next: (response: any) => {
        console.log('getUserDocuments response:', response);
        const docs = response.documents || [];
        const status = response.applicationStatus;
        this.applicationId = response.applicationId;

        // 1. Update Application Status & Current Application Licenses
        if (status && status !== 'Draft') {
            this.isApplicationSubmitted = true;
            // Handle active application licenses
            const licenseItems = response.licenseItems || [];
            if (licenseItems.length > 0) {
                this.licenseTypes.forEach((l: any) => {
                    l.selected = false;
                    l.submitted = false;
                });
                licenseItems.forEach((item: any) => {
                    const match = this.licenseTypes.find(l => l.name === item.license_type);
                    if (match) (match as any).submitted = true;
                });
                this.calculateTotal();
            }
        } else {
            this.isApplicationSubmitted = false;
            this.licenseTypes.forEach((l: any) => l.submitted = false);
        }

        // 2. Handle Global Submitted/Approved Licenses (From history)
        if (response.availableLicenseTypes && Array.isArray(response.availableLicenseTypes)) {
             this.licenseTypes.forEach(license => {
                  const available = response.availableLicenseTypes.find((a: any) => a.name === license.name);
                  if (available) {
                      (license as any).applicationType = available.type; // 'New' or 'Renewal'
                      // Auto-select if it is a Renewal (User needs to renew)
                      if (available.type === 'Renewal') {
                          license.selected = true;
                          // If there are instruments, we might need to select them? 
                          // Currently logic requires instruments to select license. 
                          // But user request says "itaweka kua selected". 
                          // If there are instruments, we can't select specific ones automatically unless we know history.
                          // So we select the license, but validation will force user to pick instruments.
                          // However, our `toggleLicense` prevents manual selection without instruments.
                          // Forced selection here bypasses `toggleLicense` check.
                      }
                  }
             });
        }
        
        // Calculate total after auto-selections
        this.calculateTotal();
        if (response.submittedLicenseTypes && Array.isArray(response.submittedLicenseTypes)) {
            this.licenseTypes.forEach(license => {
                const submitted = response.submittedLicenseTypes.find((s: any) => s.license_type === license.name);
                if (submitted) {
                     (license as any).submitted = true;
                     (license as any).restrictionReason = submitted.status;
                     (license as any).control_number = submitted.control_number;
                     (license as any).paymentStatus = submitted.payment_status;
                     (license as any).billAmount = submitted.bill_amount;
                     (license as any).applicationFee = submitted.application_fee;
                     (license as any).disabled = true; // Disable if already submitted/approved
                     license.selected = false;
                }
            });
        }

        // 3. Map Documents
        docs.forEach((doc: any) => {
          const docType = (doc.document_type || '').toLowerCase();
          
          // Check required documents
          const reqDoc = this.requiredDocuments.find(d => 
              d.id.toLowerCase() === docType || d.name.toLowerCase() === docType
          );
          if (reqDoc) {
            reqDoc.status = doc.status === 'Returned' ? 'Returned' : 'Uploaded';
            reqDoc.date = new Date(doc.created_at || Date.now()).toLocaleDateString('en-GB');
            reqDoc.fileName = doc.original_name;
            reqDoc.dbId = doc.id;
            reqDoc.submitted = (this.isApplicationSubmitted && doc.application_id === this.applicationId);
            (reqDoc as any).category = doc.category || 'attachment'; // PRESERVE category from database
            if (doc.status === 'Returned') {
                reqDoc.rejectionReason = doc.rejection_reason;
                reqDoc.viewed = false; // Initialize as not viewed
            }
          }

          // Check qualification documents
          const qualDoc = this.qualificationDocuments.find(d => 
              d.id.toLowerCase() === docType || d.name.toLowerCase() === docType
          );
          if (qualDoc) {
            qualDoc.status = doc.status === 'Returned' ? 'Returned' : 'Uploaded';
            qualDoc.date = new Date(doc.created_at || Date.now()).toLocaleDateString('en-GB');
            qualDoc.fileName = doc.original_name;
            qualDoc.dbId = doc.id;
            qualDoc.submitted = (this.isApplicationSubmitted && doc.application_id === this.applicationId);
            (qualDoc as any).category = doc.category || 'qualification'; // PRESERVE category from database
            if (doc.status === 'Returned') {
                qualDoc.rejectionReason = doc.rejection_reason;
                qualDoc.viewed = false; // Initialize as not viewed
            }
          }
        });
      },
      error: (err: any) => {
        console.error('Failed to load documents', err);
      }
    });
  }

  triggerFileUpload(doc: any) {
    this.selectedDoc = doc;
    const fileInput = document.getElementById('fileInput-' + doc.id) as HTMLInputElement;
    if (fileInput) {
      fileInput.click();
    } else {
      console.error('File input not found for:', doc.id);
    }
  }


  viewDocument(doc: any) {
    // Mark returned document as viewed
    if (doc.status === 'Returned' && !doc.viewed) {
      doc.viewed = true;
    }
    
    if (doc.dbId) {
      this.router.navigate(['/document-view', doc.dbId], { queryParams: { name: doc.name } });
    } else {
      Swal.fire({
        title: 'View Document',
        text: `Viewing ${doc.name} (Mock Action - Not Uploaded)`,
        icon: 'info'
      });
    }
  }

  onFileSelected(event: any, documentType: string) {
    const file: File = event.target.files[0];
    if (file) {
      // Validation: PDF only
      if (file.type !== 'application/pdf') {
        Swal.fire('Error', 'Only PDF documents are allowed.', 'error');
        event.target.value = ''; // Clear the file input
        return;
      }

      // Validation: Max 2MB
      if (file.size > 2 * 1024 * 1024) {
        Swal.fire('Error', 'File size must be less than 2MB.', 'error');
        event.target.value = ''; // Clear the file input
        return;
      }

      // Get the document reference
      const targetDoc = this.selectedDoc;
      
      // NEW LOGIC: If application is submitted and doc was not uploaded, set pending state
      if (this.isApplicationSubmitted && targetDoc && targetDoc.status === 'Not Uploaded') {
        // Store the file temporarily and show Edit/Save buttons
        targetDoc.pendingFile = file;
        targetDoc.fileName = file.name;
        targetDoc.status = 'Pending'; // Temporary status to show Edit/Save buttons
        event.target.value = ''; // Clear the file input
        this.selectedDoc = null;
        
        Swal.fire({
          icon: 'info',
          title: 'Document Selected',
          text: 'You can now edit or save this document.',
          timer: 2000,
          showConfirmButton: false
        });
        return;
      }
      
      // DIRECT UPDATE Logic: Do not delete old document.
      // The backend DocumentService will handle updating the existing record to preserve the ID.
      
      Swal.fire({
        title: 'Uploading...',
        text: 'Please wait while your document is being uploaded.',
        allowOutsideClick: false,
        didOpen: () => {
          Swal.showLoading();
        }
      });
      
      this.proceedWithUpload(file, documentType, event, targetDoc);
    }
  }

  // Helper method to handle the actual upload process
  private proceedWithUpload(file: File, documentType: string, event: any, targetDoc: any) {
    // Determine application ID to pass
    const appId = this.isApplicationSubmitted ? (this.applicationId || undefined) : undefined;

    // Determine Category - Strict Logic: Prioritize list membership over DB value to fix corruption
    let category = 'attachment'; // Default
    
    const isQual = this.qualificationDocuments.some(d => d.id === documentType || d.name === documentType);
    if (isQual) {
      category = 'qualification';
    } else if (targetDoc && targetDoc.category && targetDoc.category !== 'null') {
      // Fallback to existing category only if not explicitly identified as generic attachment
      // and not in qualification list
      category = targetDoc.category;
    }

    this.licenseService.uploadDocument(file, documentType, appId, category).subscribe({
      next: (response: any) => {
        // Update the target doc
        targetDoc.status = response.status || 'Uploaded';
        targetDoc.date = new Date(response.created_at || Date.now()).toLocaleDateString('en-GB');
        targetDoc.fileName = response.original_name;
        targetDoc.dbId = response.id;
        
        if (this.isApplicationSubmitted && response.id) {
           // Auto-submit the document since the application is already active
           this.licenseService.submitDocument(response.id).subscribe({
             next: (res: any) => {
               targetDoc.submitted = true;
               if (res && res.status) {
                 targetDoc.status = res.status;
               }
               
               Swal.fire({
                 icon: 'success',
                 title: 'Uploaded & Submitted',
                 text: `${targetDoc.name} has been successfully uploaded and submitted.`,
                 timer: 2000,
                 showConfirmButton: false
               });
               
               // Reset file input
               event.target.value = '';
               this.selectedDoc = null;
             },
             error: (subErr) => {
               console.error('Auto-submit failed', subErr);
               targetDoc.submitted = false;
               
               Swal.fire({
                 icon: 'warning',
                 title: 'Uploaded Only',
                 text: `Document uploaded but failed to auto-submit. Please click the Submit button manually.`,
                 confirmButtonText: 'OK'
               });
               
               // Reset file input
               event.target.value = '';
               this.selectedDoc = null;
             }
           });
        } else {
           // Standard upload (Draft mode)
            if (this.isApplicationSubmitted) {
               targetDoc.submitted = false;
            }
            
            Swal.fire({
              icon: 'success',
              title: 'Upload Successful',
              text: `${targetDoc.name} has been successfully uploaded.`,
              timer: 2000,
              showConfirmButton: false
            });
            
            // Reset file input
            event.target.value = '';
            this.selectedDoc = null;
        }
      },
      error: (err: any) => {
        console.error('Upload failed', err);
        let msg = 'There was an error uploading your document. Please try again.';
        
        if (err?.error?.messages) {
          if (typeof err.error.messages === 'object') {
            msg = err.error.messages.file || JSON.stringify(err.error.messages);
          } else {
            msg = err.error.messages;
          }
        } else if (err?.error?.message) {
          msg = err.error.message;
        } else if (err?.error?.error) {
          msg = err.error.error;
        }

        Swal.fire({
          icon: 'error',
          title: 'Upload Failed',
          text: msg,
        });
        event.target.value = '';
        this.selectedDoc = null;
      }
    });
  }

  deleteDocument(doc: any) {
    Swal.fire({
      title: 'Are you sure?',
      text: "You are about to remove this document.",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
      confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
      if (result.isConfirmed) {
        if (doc.dbId) {
          this.licenseService.deleteDocument(doc.dbId).subscribe({
            next: () => {
              doc.status = 'Not Uploaded';
              doc.date = '-';
              doc.fileName = null;
              doc.dbId = null;
              Swal.fire(
                'Deleted!',
                'Your document has been removed.',
                'success'
              );
            },
            error: (err: any) => {
              console.error('Delete failed', err);
              Swal.fire('Error', 'Failed to delete document', 'error');
            }
          });
        } else {
           // Fallback if no DB ID (shouldn't happen if uploaded correctly)
           doc.status = 'Not Uploaded';
           doc.date = '-';
           doc.fileName = null;
        }
      }
    });
  }

  // New method to save a pending document
  saveDocument(doc: any) {
    if (!doc.pendingFile) {
      Swal.fire('Error', 'No file selected for upload.', 'error');
      return;
    }

    Swal.fire({
      title: 'Uploading...',
      text: 'Please wait while your document is being uploaded.',
      allowOutsideClick: false,
      didOpen: () => {
        Swal.showLoading();
      }
    });

    // Determine application ID
    const appId = this.isApplicationSubmitted ? (this.applicationId || undefined) : undefined;

    // Determine Category
    let category = 'attachment'; // Default
    const isQual = this.qualificationDocuments.some(d => d.id === doc.id || d.name === doc.name);
    if (isQual) {
      category = 'qualification';
    } else if (doc.category && doc.category !== 'null') {
      category = doc.category;
    }

    this.licenseService.uploadDocument(doc.pendingFile, doc.id, appId, category).subscribe({
      next: (response: any) => {
        // Update the document
        doc.status = response.status || 'Uploaded';
        doc.date = new Date(response.created_at || Date.now()).toLocaleDateString('en-GB');
        doc.fileName = response.original_name;
        doc.dbId = response.id;
        doc.pendingFile = null; // Clear pending file
        
        Swal.fire({
          icon: 'success',
          title: 'Upload Successful',
          text: `${doc.name} has been successfully uploaded.`,
          timer: 2000,
          showConfirmButton: false
        });
      },
      error: (err: any) => {
        console.error('Upload failed', err);
        const errorMessage = err?.error?.message || err?.message || 'Failed to upload document.';
        Swal.fire({
          icon: 'error',
          title: 'Upload Failed',
          text: errorMessage,
        });
        // Reset to Not Uploaded on error
        doc.status = 'Not Uploaded';
        doc.fileName = null;
        doc.pendingFile = null;
      }
    });
  }

  billData: any = null;
  showBillModal: boolean = false;

  citizenship: 'Citizen' | 'Non-Citizen' = 'Citizen';

  calculateTotal() {
    const appFee = this.applicationFee;
    this.totalAmount = this.selectedLicenses.length * appFee;
  }

  get selectedLicenses() {
    return this.licenseTypes.filter(l => l.selected);
  }

  get applicationFee() {
    if (this.applicationFees.length === 0) {
        // Fallback if fees not loaded yet or failed
        return this.citizenship === 'Citizen' ? 50000 : 200000;
    }

    // Map frontend types to DB types
    const dbType = this.applicationType === 'New' ? 'New License' : 'Renew License';
    
    const feeRecord = this.applicationFees.find(f => 
        f.application_type === dbType && 
        f.nationality === this.citizenship
    );

    if (feeRecord) {
        return parseFloat(feeRecord.amount);
    }
    
    // Fallback if specifically not found
    return this.citizenship === 'Citizen' ? 50000 : 200000;
  }

  toggleLicense(license: any) {
    // Prevent manual toggle if instruments are available (Auto-selection logic driven by instruments)
    if (license.availableInstruments && license.availableInstruments.length > 0) {
        if (!license.selected) {
             Swal.fire('Select Instruments', 'Please select the required instruments to apply for this license.', 'info');
        }
        return;
    }
    // Prevent selection if restricted
    if ((license as any).isRestricted) {
      Swal.fire({
        icon: 'warning',
        title: 'License Restricted',
        html: `You cannot re-apply for <strong>${license.name}</strong> until <strong>${this.formatDate((license as any).availableDate)}</strong>.<br><br>` +
              `This license was approved on ${this.formatDate((license as any).ceoApprovedAt)}. ` +
              `You must wait 1 full year before re-applying.<br><br>` +
              `<strong>${(license as any).daysRemaining} days remaining</strong>`,
        confirmButtonColor: '#F59E0B'
      });
      return;
    }
    
    // Prevent selection if already submitted (but not restricted)
    if (license.submitted) {
      return;
    }
    
    // Existing toggle logic
    license.selected = !license.selected;
    this.calculateTotal();
  }

  loadApprovedLicenses() {
    this.licenseService.getApprovedLicenses().subscribe({
      next: (licenses: any[]) => {
        this.approvedLicenses = licenses;
        
        // Build set of restricted license types
        this.restrictedLicenseTypes.clear();
        licenses.forEach(license => {
          if (license.is_restricted) {
            this.restrictedLicenseTypes.add(license.license_type);
          }
        });
        
        // Update license types with restriction info
        this.updateLicenseRestrictions();
      },
      error: (err: any) => {
        console.error('Error loading approved licenses:', err);
      }
    });
  }

  updateLicenseRestrictions() {
    this.licenseTypes.forEach(license => {
      const approvedLicense = this.approvedLicenses.find(
        al => al.license_type === license.name
      );
      
      if (approvedLicense) {
        (license as any).alreadyApplied = true;
        (license as any).isRestricted = approvedLicense.is_restricted;
        (license as any).daysRemaining = approvedLicense.days_remaining;
        (license as any).availableDate = approvedLicense.available_date;
        (license as any).ceoApprovedAt = approvedLicense.ceo_approved_at;
      }
    });
  }

  formatDate(dateString: string): string {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
  }

  submitDocument(doc: any) {
    console.log('submitDocument called with doc:', doc);
     // ... existing code ...
    console.log('Document dbId:', doc.dbId);
    
    if (!doc.dbId) {
      Swal.fire('Error', 'Document ID is missing. Please upload the document first.', 'error');
      return;
    }
    
    Swal.fire({
      title: 'Submit Document?',
      text: `Are you sure you want to submit ${doc.name}?`,
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'Yes, Submit',
      cancelButtonText: 'Cancel'
    }).then((result) => {
      if (result.isConfirmed) {
        console.log('Calling licenseService.submitDocument with ID:', doc.dbId);
        this.licenseService.submitDocument(doc.dbId).subscribe({
          next: (response) => {
            console.log('Submit response:', response);
            doc.submitted = true; // Mark as submitted locally
            doc.status = 'Uploaded'; // Update status
            Swal.fire('Submitted!', 'Document has been submitted successfully.', 'success');
          },
          error: (err) => {
            console.error('Submission failed', err);
            Swal.fire('Error', err.error?.message || 'Failed to submit document.', 'error');
          }
        });
      }
    });
  }

  navigateToProfile() {
    this.router.navigate(['/profile']);
  }

  submitApplication() {
    if (!this.declarationAccepted) {
      Swal.fire('Error', 'You must accept the declaration.', 'error');
      return;
    }

    const selectedLicenses = this.licenseTypes.filter(l => l.selected);
    if (selectedLicenses.length === 0) {
      Swal.fire('Error', 'Please select at least one license type.', 'error');
      return;
    }

    // Validate Criteria for Selected Licenses
    for (const license of selectedLicenses) {
        if (license.availableInstruments && license.availableInstruments.length > 0) {
            const status = this.getCriteriaStatus(license);
            if (!status.valid) {
                 Swal.fire({
                    icon: 'error',
                    title: 'Incomplete Selection',
                    text: `For ${license.name}: ${status.message}`
                 });
                 return;
            }
        }
    }

    // Validate Required Documents
    const missingDocs = this.requiredDocuments.filter(doc => doc.status !== 'Uploaded');
    if (missingDocs.length > 0) {
        const missingNames = missingDocs.map(d => d.name).join('<br>');
        Swal.fire({
            icon: 'error',
            title: 'Missing Documents',
            html: `Please upload the following required documents before submitting:<br><br><b>${missingNames}</b>`
        });
        return;
    }

    this.isSubmitting = true;

    // Collect all valid attachment IDs (Uploaded status)
    const attachmentIds: string[] = [];
    [...this.requiredDocuments, ...this.qualificationDocuments].forEach(doc => {
        if (doc.status === 'Uploaded' && doc.dbId) {
            attachmentIds.push(doc.dbId);
        }
    });

    // Prepare license data with selected instruments
    const licensesToSubmit = selectedLicenses.map(l => ({
        ...l,
        selected_instruments: l.userSelectedInstruments // Explicitly mapping for backend clarity
    }));

    const applicationData = {
      applicationType: this.applicationType,
      totalAmount: this.totalAmount,
      declaration: this.declarationAccepted,
      licenseTypes: JSON.stringify(licensesToSubmit),
      previousLicenses: this.previousLicenses,
      qualifications: this.qualifications,
      experiences: this.experiences,
      tools: this.tools,
      attachments: attachmentIds // Send explicitly included attachments
    };

    Swal.fire({
      title: 'Processing Application...',
      text: 'Please wait while we generate your bill.',
      allowOutsideClick: false,
      didOpen: () => {
        Swal.showLoading();
      }
    });

    this.licenseService.submitApplication(applicationData).subscribe({
      next: (response: any) => {
        this.isSubmitting = false;
        Swal.close();
        
        if (response && response.billData) {
            this.billData = response.billData;
            this.showBillModal = true;
        } else {
            Swal.fire({
              icon: 'success',
              title: 'Application Submitted!',
              text: 'Your license application has been submitted successfully.',
              confirmButtonColor: '#F59E0B'
            }).then(() => {
                this.router.navigate(['/dashboard']);
            });
        }
      },
      error: (err: any) => {
        console.error('Submission error:', err);
        this.isSubmitting = false;
        Swal.close();
        const errorMessage = err.error?.messages?.error || err.error?.message || err.message || 'Failed to submit application. Please try again.';
        Swal.fire('Error', errorMessage, 'error');
      }
    });
  }

  openInterviewModal(interview: any) {
    this.selectedInterview = interview;
    this.showInterviewModal = true;
  }

  closeInterviewModal() {
    this.showInterviewModal = false;
    this.selectedInterview = null;
  }
}
