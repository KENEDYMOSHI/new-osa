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

  // Modal State
  isModalOpen = false;
  previewUrl: SafeResourceUrl | null = null;
  previewDocName: string = '';

  licenseTypes: { id: string; name: string; fee: number; description: string; selected: boolean; submitted?: boolean }[] = [
    { id: 'classA', name: 'Class A', fee: 100000, description: 'To install, overhaul, service or repair all types of weighing instruments throughout Mainland Tanzania.', selected: false },
    { id: 'classB', name: 'Class B', fee: 75000, description: 'To install, overhaul, service or repair not more than six and not less than four types of weighing instruments throughout Mainland Tanzania.', selected: false },
    { id: 'classC', name: 'Class C', fee: 50000, description: 'To install, overhaul, service or repair not more than three types of weighing instruments throughout Mainland Tanzania.', selected: false },
    { id: 'classD', name: 'Class D', fee: 250000, description: 'To erect, install, overhaul, adjust, service or repair measuring of Liquid Measuring Pumps and Flow Meters throughout Mainland Tanzania.', selected: false },
    { id: 'classE', name: 'Class E', fee: 300000, description: 'To manufacture the measuring instruments or systems throughout Mainland Tanzania.', selected: false },
    { id: 'tankConst', name: 'Tank Construction', fee: 800000, description: 'To construct Tanks throughout Mainland Tanzania.', selected: false },
    { id: 'fixedTankVer', name: 'Fixed Storage Tanks Verification', fee: 400000, description: 'Verification of fixed storage tanks.', selected: false },
    { id: 'tankCal', name: 'Tank Calibration', fee: 400000, description: 'To calibrate underground storage tanks.', selected: false },
    { id: 'gasCal', name: 'Gas Measuring instrument license', fee: 2500000, description: 'To calibrate gas flow meters.', selected: false },
    { id: 'marineSurvey', name: 'Marine Measurement Survey', fee: 250000, description: 'Marine measurement survey services.', selected: false },
  ];

  totalAmount = 0;
  declarationAccepted = false;
  isSubmitting = false;
  applicationId: string | null = null;

  baseDocuments: { id: string; name: string; date: string; status: 'Not Uploaded' | 'Uploaded' | 'Returned'; fileName?: string | null; dbId?: string; submitted?: boolean; rejectionReason?: string }[] = [
    { id: 'tin', name: 'Tax Payer Identification Number (TIN)', date: '-', status: 'Not Uploaded' },
    { id: 'businessLicense', name: 'Business License', date: '-', status: 'Not Uploaded' },
    { id: 'taxClearance', name: 'Certificate Of Tax Clearance', date: '-', status: 'Not Uploaded' },
    { id: 'brela', name: 'Certificate of Registration/Incorporation from BRELA', date: '-', status: 'Not Uploaded' },
    { id: 'identity', name: 'Identity Card (National ID / Driver\'s License / Voter ID)', date: '-', status: 'Not Uploaded' }
  ];

  renewalDocuments: { id: string; name: string; date: string; status: 'Not Uploaded' | 'Uploaded' | 'Returned'; fileName?: string | null; dbId?: string; submitted?: boolean; rejectionReason?: string }[] = [
    { id: 'correctness', name: 'Certificate of Correctness', date: '-', status: 'Not Uploaded' },
    { id: 'previousLicense', name: 'Previous License', date: '-', status: 'Not Uploaded' }
  ];

  qualificationDocuments: { id: string; name: string; date: string; status: 'Not Uploaded' | 'Uploaded' | 'Returned'; fileName?: string | null; dbId?: string; submitted?: boolean; rejectionReason?: string }[] = [
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

  requiredDocuments: { id: string; name: string; date: string; status: 'Not Uploaded' | 'Uploaded' | 'Returned'; fileName?: string | null; dbId?: string; submitted?: boolean; rejectionReason?: string }[] = [];

  selectedDoc: any = null;

  today: Date = new Date();

  constructor(
    public router: Router,
    private authService: AuthService,
    private licenseService: LicenseService,
    private sanitizer: DomSanitizer
  ) {}

  ngOnInit(): void {
    this.updateRequiredDocuments();
    this.loadProfileData();
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
        if (response.submittedLicenseTypes && Array.isArray(response.submittedLicenseTypes)) {
            const submittedNames = response.submittedLicenseTypes.map((s: any) => s.license_type);
            this.licenseTypes.forEach(license => {
                if (submittedNames.includes(license.name)) {
                     (license as any).submitted = true;
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
            if (doc.status === 'Returned') {
                reqDoc.rejectionReason = doc.rejection_reason;
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
            if (doc.status === 'Returned') {
                qualDoc.rejectionReason = doc.rejection_reason;
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

      // Show loading
      Swal.fire({
        title: 'Uploading...',
        text: 'Please wait while your document is being uploaded.',
        allowOutsideClick: false,
        didOpen: () => {
          Swal.showLoading();
        }
      });

      // Determine application ID to pass
      const appId = this.isApplicationSubmitted ? (this.applicationId || undefined) : undefined;

      this.licenseService.uploadDocument(file, documentType, appId).subscribe({
        next: (response: any) => {
          // Update the selected doc (or find it in the list if selectedDoc is null)
          const docId = documentType;
          this.selectedDoc.status = 'Uploaded';
          this.selectedDoc.date = new Date(response.created_at || Date.now()).toLocaleDateString('en-GB');
          this.selectedDoc.fileName = response.original_name;
          this.selectedDoc.dbId = response.id;
          
          if (this.isApplicationSubmitted) {
             this.selectedDoc.submitted = false;
          }
          
          Swal.fire({
            icon: 'success',
            title: 'Upload Successful',
            text: `${this.selectedDoc.name} has been successfully uploaded.`,
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

  closeModal() {
    this.isModalOpen = false;
    this.previewUrl = null;
    this.previewDocName = '';
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

  billData: any = null;
  showBillModal: boolean = false;

  citizenship: 'Citizen' | 'Non-Citizen' = 'Citizen';

  calculateTotal() {
    const appFee = this.citizenship === 'Citizen' ? 50000 : 200000;
    this.totalAmount = this.selectedLicenses.length * appFee;
  }

  get selectedLicenses() {
    return this.licenseTypes.filter(l => l.selected);
  }

  get applicationFee() {
    return this.citizenship === 'Citizen' ? 50000 : 200000;
  }

  toggleLicense(license: any) {
    if (license.submitted || this.isApplicationSubmitted) return;
    license.selected = !license.selected;
    this.calculateTotal();
  }

  submitDocument(doc: any) {
    Swal.fire({
      title: 'Submit Document?',
      text: `Are you sure you want to submit ${doc.name}?`,
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'Yes, Submit',
      cancelButtonText: 'Cancel'
    }).then((result) => {
      if (result.isConfirmed) {
        this.licenseService.submitDocument(doc.dbId).subscribe({
          next: () => {
            doc.submitted = true; // Mark as submitted locally
            Swal.fire('Submitted!', 'Document has been submitted successfully.', 'success');
          },
          error: (err) => {
            console.error('Submission failed', err);
            Swal.fire('Error', 'Failed to submit document.', 'error');
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

    const applicationData = {
      applicationType: this.applicationType,
      totalAmount: this.totalAmount,
      declaration: this.declarationAccepted,
      licenseTypes: JSON.stringify(selectedLicenses),
      previousLicenses: this.previousLicenses,
      qualifications: this.qualifications,
      experiences: this.experiences,
      tools: this.tools
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
}
