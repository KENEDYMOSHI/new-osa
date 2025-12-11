import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterLink, Router } from '@angular/router';
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
  // selectedLicenseTypes and newSelectedLicenseType moved below to change type to any[]
  
  newPreviousLicenseNumber: string = '';

  licenseTypes = [
    'Class A - Verification',
    'Class B - Repair',
    'Class C - Manufacturing',
    'Class D - Import/Export'
  ];

  availableLicenseTypes: any[] = [];
  selectedLicenseTypes: any[] = [];
  newSelectedLicenseType: any = null;

  toggleLicenseType(type: string) {
    const index = this.selectedLicenseTypes.indexOf(type);
    if (index === -1) {
      this.selectedLicenseTypes.push(type);
    } else {
      this.selectedLicenseTypes.splice(index, 1);
    }
  }

  // Qualifications Logic
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

      this.licenseService.uploadDocument(file, docType, undefined).subscribe({
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
    private http: HttpClient, 
    private licenseService: LicenseService,
    private sanitizer: DomSanitizer
  ) {}

  async ngOnInit() {
    try {
      const data = await firstValueFrom(this.authService.getProfile());
      console.log('Profile Data for License:', data);

      if (data.user) {
        this.particulars.email = data.user.email;
        // Fallback name if personal info is missing
        this.particulars.fullName = data.user.username;
      }

      if (data.personalInfo) {
        // Map all fields for the preview card
        this.particulars.nida = data.personalInfo.identity_number || 'N/A';
        this.particulars.firstName = data.personalInfo.first_name || 'N/A';
        this.particulars.middleName = data.personalInfo.second_name || 'N/A';
        this.particulars.lastName = data.personalInfo.last_name || 'N/A';
        this.particulars.gender = data.personalInfo.gender || 'N/A';
        this.particulars.dob = data.personalInfo.dob || 'N/A';
        this.particulars.phone = data.personalInfo.phone || 'N/A';
        this.particulars.region = data.personalInfo.region || 'N/A';
        this.particulars.district = data.personalInfo.district || 'N/A';
        this.particulars.street = data.personalInfo.street || 'N/A';
        this.particulars.nationality = data.personalInfo.nationality || 'Tanzania'; // Default or fetch

        const firstName = data.personalInfo.first_name || '';
        const secondName = data.personalInfo.second_name || '';
        const lastName = data.personalInfo.last_name || '';
        
        const fullName = `${firstName} ${secondName} ${lastName}`.replace(/\s+/g, ' ').trim();
        
        this.particulars.fullName = fullName || this.particulars.fullName;
        
        // Update Welcome Card Title
        if (fullName) {
          this.welcomeCard.title = `Welcome, ${fullName}`;
        }
        
        // Construct address for legacy use if needed
        const parts = [
          data.personalInfo.street,
          data.personalInfo.town,
          data.personalInfo.district,
          data.personalInfo.region
        ].filter(part => part);
        this.particulars.address = parts.join(', ');
      }

      if (data.businessInfo) {
        this.particulars.businessName = data.businessInfo.company_name || 'N/A';
        this.particulars.tin = data.businessInfo.tin || 'N/A';
        this.particulars.brelaNumber = data.businessInfo.brela_number || 'N/A';
        this.particulars.companyEmail = data.businessInfo.company_email || 'N/A';
        this.particulars.companyPhone = data.businessInfo.company_phone || 'N/A';
        this.particulars.postalCode = data.businessInfo.postal_code || 'N/A';
        this.particulars.busRegion = data.businessInfo.bus_region || 'N/A';
        this.particulars.busDistrict = data.businessInfo.bus_district || 'N/A';
        this.particulars.busTown = data.businessInfo.bus_town || 'N/A';
        this.particulars.busStreet = data.businessInfo.bus_street || 'N/A';
      }

    } catch (error) {
      console.error('Failed to fetch profile for license:', error);
    }
    
    this.loadUserDocuments();
  }

  loadUserDocuments() {
    this.licenseService.getUserDocuments().subscribe({
      next: (response: any) => {
        const docs = response.documents || [];
        
        // Check Application Status
        const status = response.applicationStatus;
        // Consider Submitted, Approved, Pending, etc. as "Submitted" state where edits are disabled
        this.isApplicationSubmitted = status && status !== 'Draft' && status !== 'Returned'; 

        // Populate available license types from backend (already filtered)
        if (response.availableLicenseTypes && Array.isArray(response.availableLicenseTypes)) {
          this.availableLicenseTypes = response.availableLicenseTypes;
        } else {
           // Fallback if backend doesn't send it (e.g. error), simplify or empty
           this.availableLicenseTypes = this.licenseTypes.map(t => ({ name: t, type: 'New', date: null }));
        }

        docs.forEach((doc: any) => {
          // Check required documents
          const reqDoc = this.requiredDocuments.find(d => d.id === doc.document_type || d.name === doc.document_type);
          if (reqDoc) {
            reqDoc.status = 'Uploaded';
            reqDoc.date = new Date(doc.created_at || Date.now()).toLocaleDateString('en-GB');
            reqDoc.fileName = doc.original_name;
            reqDoc.dbId = doc.id;
          }

          // Check qualification documents
          const qualDoc = this.qualificationDocuments.find(d => d.id === doc.document_type || d.name === doc.document_type);
          if (qualDoc) {
            qualDoc.status = 'Uploaded';
            qualDoc.date = new Date(doc.created_at || Date.now()).toLocaleDateString('en-GB');
            qualDoc.fileName = doc.original_name;
            qualDoc.dbId = doc.id;
          }
        });
      },
      error: (err: any) => {
        console.error('Failed to load documents', err);
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
        if (this.selectedLicenseTypes.length === 0) {
          alert('Please select at least one license type.');
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
    // Show if any selected license is of type 'Renew' or 'Renewal'
    return this.selectedLicenseTypes.some(item => item.type && String(item.type).toLowerCase().includes('renew'));
  }

  addPreviousLicenseNumber() {
    if (this.newPreviousLicenseNumber.trim()) {
      this.previousLicenseNumbers.push(this.newPreviousLicenseNumber.trim());
      this.newPreviousLicenseNumber = '';
    }
  }

  removePreviousLicenseNumber(index: number) {
    this.previousLicenseNumbers.splice(index, 1);
  }

  // Modal State
  showReplaceModal: boolean = false;
  pendingLicenseType: any = null;

  addLicenseType() {
    if (this.newSelectedLicenseType) {
      // Check if a license is already selected
      if (this.selectedLicenseTypes.length > 0) {
        // Show custom modal instead of window.confirm
        this.pendingLicenseType = this.newSelectedLicenseType;
        this.showReplaceModal = true;
        return;
      }

      // Add the new license
      this.selectedLicenseTypes.push(this.newSelectedLicenseType);
      
      // Remove from available list
      this.availableLicenseTypes = this.availableLicenseTypes.filter(type => type !== this.newSelectedLicenseType);
      
      this.newSelectedLicenseType = null;
    }
  }

  confirmReplace() {
    if (this.pendingLicenseType) {
      // Return the currently selected license to the available list
      const currentLicense = this.selectedLicenseTypes[0];
      this.availableLicenseTypes.push(currentLicense);
      this.selectedLicenseTypes = []; // Clear current selection

      // Add the pending license
      this.selectedLicenseTypes.push(this.pendingLicenseType);

      // Remove pending from available list
      this.availableLicenseTypes = this.availableLicenseTypes.filter(type => type !== this.pendingLicenseType);

      // Reset state
      this.showReplaceModal = false;
      this.pendingLicenseType = null;
      this.newSelectedLicenseType = null;
    }
  }

  cancelReplace() {
    this.showReplaceModal = false;
    this.pendingLicenseType = null;
    this.newSelectedLicenseType = null;
  }

  removeLicenseType(index: number) {
    const removedType = this.selectedLicenseTypes[index];
    this.selectedLicenseTypes.splice(index, 1);
    
    // Add back to available list and sort
    this.availableLicenseTypes.push(removedType);
    this.availableLicenseTypes.sort(); 
  }

  saveParticulars() {
    console.log('Particulars saved:', this.particulars);
    console.log('Previous Licenses:', this.previousLicenseNumbers);
    console.log('Selected Types:', this.selectedLicenseTypes);
    this.goToStep(2);
  }

  // Step 2: Qualifications
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
    // No longer going to step 4, but maybe we want to validate or just stay here until they click submit
    // Actually, saveTools was the "Next" button action. 
    // Since we are merging the submit action into this step, we might not need saveTools as a separate "Next" action anymore.
    // But if the user clicks "Save & Continue" (which we will rename/remove), it should probably just do nothing or be removed.
    // However, for now, let's just log it. The actual submission will be handled by submitApplication.
  }

  // Step 4: Submit
  // Step 4: Submit
  submitApplication() {
    const applicationData = {
      applicationType: 'New', // Defaulting to New for now
      totalAmount: 100000, // Default amount, should be calculated based on license types
      declaration: this.declaration,
      particulars: this.particulars,
      previousLicenses: this.previousLicenseNumbers,
      licenseTypes: this.selectedLicenseTypes, // Sending string array
      qualifications: this.qualificationsList,
      experiences: this.experiencesList,
      tools: this.tools
    };
    
    console.log('Submitting Application:', applicationData);
    
    this.http.post('http://localhost:8080/api/license/submit', applicationData).subscribe({
      next: (response: any) => {
        console.log('Submission successful', response);
        alert('Application Submitted Successfully!');
        this.router.navigate(['/dashboard']); // Redirect to dashboard
      },
      error: (error) => {
        console.error('Submission failed', error);
        alert('Failed to submit application. Please try again.');
      }
    });
  }
}
