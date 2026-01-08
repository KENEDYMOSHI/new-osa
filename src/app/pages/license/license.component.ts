import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterLink, Router, ActivatedRoute } from '@angular/router';
import { FormsModule } from '@angular/forms';
import { AuthService } from '../../core/services/auth.service';
import { firstValueFrom } from 'rxjs';
import { HttpClient } from '@angular/common/http';
import { LicenseService } from '../../services/license.service';
import { DomSanitizer, SafeResourceUrl } from '@angular/platform-browser';
import Swal from 'sweetalert2';

@Component({
  selector: 'app-license',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './license.component.html',
  styleUrls: ['./license.component.css']
})
export class LicenseComponent {
  welcomeCard = {
    id: 0,
    title: 'Welcome, [Account Holder’s Name]',
    description: 'Thank you for continuing with the final steps of completing your WMA licensing application.',
    icon: 'info',
    isOpen: false,
    action: 'Get Started',
    variant: 'card-welcome',
    details: 'Please provide a complete list of all tools, equipment, and instruments you use in carrying out your technical duties. Make sure each item is entered with its correct name and added to the system one by one using the (+) button. This information is essential for verifying your operational capability and ensuring that you have the necessary resources to perform inspection, verification, and other technical services recognized by WMA. Enter the details accurately to support the proper evaluation of your application. Once all tools and equipment have been listed, click Save to proceed to the next step of the licensing process.'
  };

  // Wizard Logic
  currentStep = 1;
  steps = [
    { id: 1, title: 'License Details', icon: 'user' },
    { id: 2, title: 'Applicant Qualifications', icon: 'check-circle' },
    { id: 3, title: 'Tools & Equipments', icon: 'tool' },
    { id: 4, title: 'Preview & Confirm', icon: 'thumbs-up' }
  ];

  // Particulars Logic
  particulars = {
    fullName: '',
    phone: '',
    email: '',
    businessName: '',
    address: '',
    // Extended fields for preview
    nida: '',
    firstName: '',
    middleName: '',
    lastName: '',
    gender: '',
    dob: '',
    region: '',
    district: '',
    street: '',
    nationality: '',
    // Extended business fields
    tin: '',
    brelaNumber: '',
    companyEmail: '',
    companyPhone: '',
    postalCode: '',
    busRegion: '',
    busDistrict: '',
    busTown: '',
    busStreet: ''
  };

  // Particulars Lists
  previousLicenseNumbers: string[] = [];
  
  newPreviousLicenseNumber: string = '';

  licenseTypes: any[] = [];
  availableLicenseTypes: any[] = [];
  
  // Single Selection Logic
  // Single Selection Logic
  selectedLicenseType: any = null;
  tempSelectedLicenseType: any = null; // For the dropdown selection
  
  // Replace Modal Logic
  showReplaceModal: boolean = false;
  licenseToReplace: any = null;

  addLicenseType() {
    if (!this.tempSelectedLicenseType) return;

    if (this.selectedLicenseType) {
      // If a license is already selected, prompt to replace
      this.licenseToReplace = this.tempSelectedLicenseType;
      this.showReplaceModal = true;
    } else {
      // Otherwise, just select it
      this.selectedLicenseType = this.tempSelectedLicenseType;
      this.tempSelectedLicenseType = null;
      // Load documents for this application
      this.loadApplicationDocuments();
    }
  }

  loadApplicationDocuments() {
    if (!this.selectedLicenseType || !this.selectedLicenseType.id) return;
    
    // Reset to initial clean state (removes previous dynamic additions)
    this.initializeDocuments();

    // Fetch ALL user documents (Drafts + Attached) to ensure we see everything
    // This fixes the issue where documents uploaded (as drafts) or attached to other phases weren't showing
    this.licenseService.getUserDocuments().subscribe({
      next: (response: any) => {
        const documents = response.documents || [];
        console.log('Fetched documents:', documents); // Debug log
        
        // Update requiredDocuments and qualificationDocuments with uploaded files
        documents.forEach((doc: any) => {
          const docType = doc.document_type;
          const category = (doc.category || '').toLowerCase();

          // 1. Try to find in Required Docs (Match by ID or Name)
          let reqDoc = this.requiredDocuments.find(d => d.id === docType || d.name.toLowerCase() === docType.toLowerCase());
          
          if (reqDoc) {
              this.updateDocStatus(reqDoc, doc);
              return;
          } 
          
          // 2. Try to find in Qualification Docs (Match by ID or Name)
          let qualDoc = this.qualificationDocuments.find(d => d.id === docType || d.name.toLowerCase() === docType.toLowerCase());
              
          if (qualDoc) {
              this.updateDocStatus(qualDoc, doc);
              return;
          }

          // 3. No match found in predefined lists - Add dynamically
          // This ensures ALL database documents are shown
          const newDocEntry = {
              id: docType,
              name: this.formatDocName(docType), // Helper to make it readable
              date: new Date(doc.created_at || Date.now()).toLocaleDateString('en-GB'),
              status: 'Uploaded',
              fileName: doc.original_name,
              dbId: doc.id
          };
          
          if (category === 'qualification') {
              this.qualificationDocuments.push(newDocEntry);
          } else {
              // Default to required/attachment if unknown category
              this.requiredDocuments.push(newDocEntry);
          }
        });
      },
      error: (err: any) => {
        console.error('Failed to load application documents', err);
      }
    });
  }

  // Helper to reset and initialize document lists
  initializeDocuments() {
    this.requiredDocuments = [
      { id: 'tin', name: 'Tax Payer Identification Number (TIN)', date: '-', status: 'Not Uploaded' },
      { id: 'businessLicense', name: 'Business License', date: '-', status: 'Not Uploaded' },
      { id: 'taxClearance', name: 'Certificate Of Tax Clearance', date: '-', status: 'Not Uploaded' },
      { id: 'brela', name: 'Certificate of Registration/Incorporation from BRELA', date: '-', status: 'Not Uploaded' },
      { id: 'identity', name: 'Identity Card (National ID / Driver\'s License / Voter ID)', date: '-', status: 'Not Uploaded' }
    ];

    this.qualificationDocuments = [
      { id: 'psle', name: 'Primary School Leaving Certificate (PSLE)', date: '-', status: 'Not Uploaded' },
      { id: 'csee', name: 'Certificate of Secondary Education Examination (CSEE)', date: '-', status: 'Not Uploaded' },
      { id: 'acsee', name: 'Advanced Certificate of Secondary Education Examination (ACSEE)', date: '-', status: 'Not Uploaded' },
      { id: 'veta', name: 'Basic Certificate - Vocational Education and Training Authority (VETA)', date: '-', status: 'Not Uploaded' },
      { id: 'nta4', name: 'Basic Certificate (NTA Level 4)', date: '-', status: 'Not Uploaded' },
      { id: 'nta5', name: 'Technician Certificate (NTA Level 5)', date: '-', status: 'Not Uploaded' },
      { id: 'nta6', name: 'Ordinary Diploma (NTA Level 6)', date: '-', status: 'Not Uploaded' },
      { id: 'specialized', name: 'Other Specialized Certificates', date: '-', status: 'Not Uploaded' },
      { id: 'bachelor', name: 'Bachelor\'s Degree', date: '-', status: 'Not Uploaded' }
    ];
  }

  formatDocName(type: string): string {
      // Basic formatter: 'business_license' -> 'Business License'
      if (!type) return 'Unknown Document';
      return type.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
  }

  updateDocStatus(targetDoc: any, sourceDoc: any) {
      targetDoc.status = 'Uploaded';
      targetDoc.fileName = sourceDoc.original_name;
      targetDoc.dbId = sourceDoc.id;
      targetDoc.date = new Date(sourceDoc.created_at || Date.now()).toLocaleDateString('en-GB');
  }



  downloadDocument(doc: any) {
    if (doc.dbId) {
       this.licenseService.viewDocument(doc.dbId).subscribe({
        next: (blob: Blob) => {
           const url = window.URL.createObjectURL(blob);
           const a = document.createElement('a');
           a.href = url;
           a.download = doc.fileName || doc.name + '.pdf';
           document.body.appendChild(a);
           a.click();
           document.body.removeChild(a);
           window.URL.revokeObjectURL(url);
        },
        error: (err) => console.error('Download failed', err)
       });
    }
  }

  hasUploadedRequiredDocs(): boolean {
      return this.requiredDocuments.some(doc => doc.status === 'Uploaded');
  }

  hasUploadedQualificationDocs(): boolean {
      return this.qualificationDocuments.some(doc => doc.status === 'Uploaded');
  }

  confirmReplace() {
    this.selectedLicenseType = this.licenseToReplace;
    this.licenseToReplace = null;
    this.showReplaceModal = false;
    this.tempSelectedLicenseType = null; // Reset dropdown
    this.previousLicenseNumbers = [];
    // Load documents for the new selection
    this.loadApplicationDocuments();
  }

  cancelReplace() {
    this.licenseToReplace = null;
    this.showReplaceModal = false;
    // Do not reset tempSelectedLicenseType so user sees what they selected
  }

  removeLicenseType() {
    this.selectedLicenseType = null;
    this.previousLicenseNumbers = [];
  }

  // Qualifications Logic
  qualificationsList: string[] = [];
  experiencesList: string[] = [];
  
  newQualification: string = '';
  newExperience: string = '';

  // Declaration
  declaration: boolean = false;

  // Tools Logic
  tools: string[] = [];
  newTool: string = '';

  // Documents Logic
  requiredDocuments: { id: string; name: string; date: string; status: string; fileName?: string | null; dbId?: string; submitted?: boolean }[] = [
    { id: 'tin', name: 'Tax Payer Identification Number (TIN)', date: '-', status: 'Not Uploaded' },
    { id: 'businessLicense', name: 'Business License', date: '-', status: 'Not Uploaded' },
    { id: 'taxClearance', name: 'Certificate Of Tax Clearance', date: '-', status: 'Not Uploaded' },
    { id: 'brela', name: 'Certificate of Registration/Incorporation from BRELA', date: '-', status: 'Not Uploaded' },
    { id: 'identity', name: 'Identity Card (National ID / Driver\'s License / Voter ID)', date: '-', status: 'Not Uploaded' }
  ];

  qualificationDocuments: { id: string; name: string; date: string; status: string; fileName?: string | null; dbId?: string; submitted?: boolean }[] = [
    { id: 'psle', name: 'Primary School Leaving Certificate (PSLE)', date: '-', status: 'Not Uploaded' },
    { id: 'csee', name: 'Certificate of Secondary Education Examination (CSEE)', date: '-', status: 'Not Uploaded' },
    { id: 'acsee', name: 'Advanced Certificate of Secondary Education Examination (ACSEE)', date: '-', status: 'Not Uploaded' },
    { id: 'veta', name: 'Basic Certificate - Vocational Education and Training Authority (VETA)', date: '-', status: 'Not Uploaded' },
    { id: 'nta4', name: 'Basic Certificate (NTA Level 4)', date: '-', status: 'Not Uploaded' },
    { id: 'nta5', name: 'Technician Certificate (NTA Level 5)', date: '-', status: 'Not Uploaded' },
    { id: 'nta6', name: 'Ordinary Diploma (NTA Level 6)', date: '-', status: 'Not Uploaded' },
    { id: 'specialized', name: 'Other Specialized Certificates', date: '-', status: 'Not Uploaded' },
    { id: 'bachelor', name: 'Bachelor\'s Degree', date: '-', status: 'Not Uploaded' }
  ];

  // Modal State
  isModalOpen = false;
  previewUrl: SafeResourceUrl | null = null;
  previewDocName: string = '';

  // App State
  isApplicationSubmitted = false;

  // Document Actions
  selectedDoc: any = null;

  triggerUpload(doc: any) {
    this.selectedDoc = doc;
    const fileInput = document.querySelector('input[type="file"]') as HTMLInputElement;
    if (fileInput) {
      fileInput.click();
    }
  }

  onFileSelected(event: any) {
    const file: File = event.target.files[0];
    if (file) {
      // Validation: PDF only
      if (file.type !== 'application/pdf') {
        Swal.fire('Error', 'Only PDF documents are allowed.', 'error');
        event.target.value = ''; // Clear the file input
        this.selectedDoc = null;
        return;
      }

      // Validation: Max 2MB
      if (file.size > 2 * 1024 * 1024) {
        Swal.fire('Error', 'File size must be less than 2MB.', 'error');
        event.target.value = ''; // Clear the file input
        this.selectedDoc = null;
        return;
      }

      // Show loading
      Swal.fire({
        title: 'Uploading...',
        text: 'Please wait while your document is being uploaded.',
        allowOutsideClick: false,
        didOpen: () => {
          Swal.showLoading();
        }
      });

      // For initial application wizard, application ID might not be set yet or is null for drafts
      const docType = this.selectedDoc.id || this.selectedDoc.name;
      
      // Use selectedLicenseType.id if available (this IS the application ID in this context)
      const applicationId = this.selectedLicenseType ? this.selectedLicenseType.id : undefined;
      
      // Determine category based on which list the selected doc belongs to
      let category = 'attachment';
      if (this.qualificationDocuments.includes(this.selectedDoc)) {
          category = 'qualification';
      }

      this.licenseService.uploadDocument(file, docType, applicationId, category).subscribe({
        next: (response: any) => {
          // Update the selected doc status in the relevant list
          if (this.selectedDoc) {
             this.selectedDoc.status = 'Uploaded';
             this.selectedDoc.date = new Date(response.created_at || Date.now()).toLocaleDateString('en-GB');
             this.selectedDoc.fileName = response.original_name;
             this.selectedDoc.dbId = response.id;
          }
          
          Swal.fire({
            icon: 'success',
            title: 'Upload Successful',
            text: `${this.selectedDoc?.name} has been successfully uploaded.`,
            timer: 2000,
            showConfirmButton: false
          });
          
          // Reset file input
          event.target.value = '';
          this.selectedDoc = null;
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
  }

  constructor(
    private authService: AuthService, 
    private router: Router, 
    private route: ActivatedRoute,
    private http: HttpClient, 
    private licenseService: LicenseService,
    private sanitizer: DomSanitizer
  ) {}

  ngOnInit() {
    this.loadUserDocuments();
  }

  loadUserDocuments() {
    // First, load user profile data to populate particulars
    this.authService.getProfile().subscribe({
      next: (response: any) => {
        const personal = response.personalInfo || {};
        const business = response.businessInfo || {};
        
        // Populate personal information
        this.particulars.firstName = personal.first_name || '';
        this.particulars.middleName = personal.second_name || '';
        this.particulars.lastName = personal.last_name || '';
        this.particulars.fullName = `${personal.first_name || ''} ${personal.second_name || ''} ${personal.last_name || ''}`.trim();
        this.particulars.email = response.user?.email || '';
        this.particulars.phone = personal.phone || '';
        this.particulars.nida = personal.identity_number || personal.passport_number || '';
        this.particulars.gender = personal.gender || '';
        this.particulars.dob = personal.dob || '';
        this.particulars.nationality = personal.nationality || '';
        this.particulars.region = personal.region || '';
        this.particulars.district = personal.district || '';
        this.particulars.street = personal.street || '';
        
        // Populate business information
        this.particulars.businessName = business.company_name || '';
        this.particulars.tin = business.tin || '';
        this.particulars.brelaNumber = business.brela_number || '';
        this.particulars.companyEmail = business.company_email || '';
        this.particulars.companyPhone = business.company_phone || '';
        this.particulars.postalCode = business.postal_code || '';
        this.particulars.busRegion = business.bus_region || '';
        this.particulars.busDistrict = business.bus_district || '';
        this.particulars.busTown = business.bus_town || '';
        this.particulars.busStreet = business.bus_street || '';
      },
      error: (err: any) => {
        console.error('Failed to load profile', err);
      }
    });

    this.getEligibleApplications();
  }

  getEligibleApplications() {
    this.licenseService.getEligibleApplications().subscribe({
        next: (apps) => {
            this.availableLicenseTypes = apps.filter((app: any) => {
              const appType = String(app.type || '').toLowerCase();
              const isManagerApproved = String(app.manager_approval || '').toLowerCase() === 'approved';
              const isSurveillanceApproved = String(app.surveillance_approval || '').toLowerCase() === 'approved';
              const isTypeNew = appType === 'new';
              const isTypeRenew = appType === 'renew' || appType === 'renewal';
              const interviewPassed = String(app.interview_status || '').toLowerCase() === 'pass';

              if (isTypeNew) {
                  return isManagerApproved && isSurveillanceApproved && interviewPassed;
              } else if (isTypeRenew) {
                  return isManagerApproved && isSurveillanceApproved;
              }
              
              return false; 
            });

            this.licenseTypes = [...this.availableLicenseTypes]; 
            this.selectedLicenseType = null;
            
            // If no apps remain, handle redirection or message
            if (this.availableLicenseTypes.length === 0) {
               // Optional: Auto redirect if this was a refresh after submission
            }
        },
        error: (err) => {
            console.error('Failed to load eligible applications', err);
            this.availableLicenseTypes = [];
        }
    });
  }

  viewDocument(doc: any) {
    if (doc.dbId) {
      // Show loading while fetching
      Swal.fire({
        title: 'Loading Document...',
        allowOutsideClick: false,
        didOpen: () => {
          Swal.showLoading();
        }
      });

      this.licenseService.viewDocument(doc.dbId).subscribe({
        next: (blob: Blob) => {
           Swal.close();
           const pdfBlob = new Blob([blob], { type: 'application/pdf' });
           const objectUrl = window.URL.createObjectURL(pdfBlob);
           this.previewUrl = this.sanitizer.bypassSecurityTrustResourceUrl(objectUrl);
           this.previewDocName = doc.name;
           this.isModalOpen = true;
        },
        error: (err: any) => {
          Swal.close();
          console.error('View failed', err);
          Swal.fire('Error', 'Failed to load document preview', 'error');
        }
      });
    } else {
      Swal.fire({
        title: 'View Document',
        text: `Viewing ${doc.name} (Mock Action - Not Uploaded)`,
        icon: 'info'
      });
    }
  }

  deleteDocument(doc: any) {
    if (!doc.dbId) return;

    Swal.fire({
      title: 'Are you sure?',
      text: "You won't be able to revert this!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
      confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
      if (result.isConfirmed) {
        this.licenseService.deleteDocument(doc.dbId).subscribe({
          next: () => {
            Swal.fire('Deleted!', 'Your file has been deleted.', 'success');
            
            // Reset local state for this doc
            doc.status = 'Not Uploaded';
            doc.date = '-';
            doc.fileName = null;
            doc.dbId = null;
          },
          error: (err: any) => {
             console.error('Delete failed', err);
             Swal.fire('Error', 'Failed to delete document', 'error');
          }
        });
      }
    });
  }

  closeModal() {
    this.isModalOpen = false;
    this.previewUrl = null;
    this.previewDocName = '';
  }

  // Navigation Methods
  goToStep(step: number) {
    // Validation before moving forward
    if (step > this.currentStep) {
      if (this.currentStep === 1) {
        if (!this.selectedLicenseType) {
          alert('Please select a license type.');
          return;
        }
      } else if (this.currentStep === 2) {
        if (this.qualificationsList.length === 0 || this.experiencesList.length === 0) {
          alert('Please add at least one qualification AND one experience.');
          return;
        }
      } else if (this.currentStep === 3) {
        if (this.tools.length === 0) {
          alert('Please add at least one tool or equipment.');
          return;
        }
      }
    }
    this.currentStep = step;
  }

  // Step 1: Particulars
  get showPreviousLicenseSection(): boolean {
    if (!this.selectedLicenseType) return false;
    const type = String(this.selectedLicenseType.type || '').toLowerCase();
    return type.includes('renew');
  }

  addPreviousLicenseNumber() {
    if (this.previousLicenseNumbers.length >= 1) return;
    if (this.newPreviousLicenseNumber.trim()) {
      this.previousLicenseNumbers.push(this.newPreviousLicenseNumber.trim());
      this.newPreviousLicenseNumber = '';
    }
  }

  removePreviousLicenseNumber(index: number) {
    this.previousLicenseNumbers.splice(index, 1);
  }

  saveParticulars() {
    console.log('Particulars saved:', this.particulars);
    console.log('Previous Licenses:', this.previousLicenseNumbers);
    console.log('Selected Type:', this.selectedLicenseType);
    this.goToStep(2);
  }

  // Step 2: Qualifications
  addQualification() {
    if (this.newQualification.trim()) {
      this.qualificationsList.push(this.newQualification.trim());
      this.newQualification = '';
    }
  }

  addExperience() {
    if (this.newExperience.trim()) {
      this.experiencesList.push(this.newExperience.trim());
      this.newExperience = '';
    }
  }

  removeQualification(index: number) {
    this.qualificationsList.splice(index, 1);
  }

  removeExperience(index: number) {
    this.experiencesList.splice(index, 1);
  }

  saveQualifications() {
    console.log('Qualifications saved:', this.qualificationsList);
    console.log('Experiences saved:', this.experiencesList);
    this.goToStep(3);
  }

  // Step 3: Tools
  addTool() {
    if (this.newTool.trim()) {
      this.tools.push(this.newTool.trim());
      this.newTool = '';
    }
  }

  removeTool(index: number) {
    this.tools.splice(index, 1);
  }

  saveTools() {
    console.log('Tools saved:', this.tools);
  }

  // Step 4: Submit
   submitApplication() {
    if (!this.selectedLicenseType) {
        alert('No license selected.');
        return;
    }

    const applicationData = {
      applicationType: this.selectedLicenseType.type || 'New',
      // Send as array even if single, to keep backend compatibility or update backend if preferred. 
      // Backend expects array of licenses.
      licenseTypes: [this.selectedLicenseType], 
      totalAmount: parseFloat(this.selectedLicenseType.fee) || 0,
      declaration: this.declaration,
      particulars: this.particulars,
      previousLicenses: this.previousLicenseNumbers,
      qualifications: this.qualificationsList,
      experiences: this.experiencesList,
      tools: this.tools
    };
    
    console.log('Submitting Application:', applicationData);
    
    this.licenseService.submitApplication(applicationData).subscribe({
      next: (response: any) => {
        console.log('Submission successful', response);
        
        Swal.fire({
            title: 'Application Completed',
            text: 'Your license application has been successfully completed.',
            icon: 'success',
            confirmButtonText: 'OK'
        }).then(() => {
            // After alert close
            this.currentStep = 1;
            this.selectedLicenseType = null;
            this.tools = [];
            this.qualificationsList = [];
            this.experiencesList = [];
            
            // Reload list to see if any remain
            this.getEligibleApplications();
            
            // Little delay to allow getEligibleApplications to fetch
            setTimeout(() => {
                if (this.availableLicenseTypes.length === 0) {
                     this.router.navigate(['/dashboard']);
                }
            }, 1000);
        });
      },
      error: (error) => {
        console.error('Submission failed', error);
        alert('Failed to submit application. Please try again.');
        this.isApplicationSubmitted = false;
      }
    });
  }
}
