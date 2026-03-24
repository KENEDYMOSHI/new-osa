import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { AuthService } from '../../../core/services/auth.service';
import { BusinessRegistrationService } from '../../../services/business-registration.service';

@Component({
  selector: 'app-business-profile',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './business-profile.component.html',
})
export class BusinessProfileComponent implements OnInit {
  user: any = null;
  businessInfo: any = null;
  contactInfo: any = null;
  isLoading = true;

  // Logo
  logoUrl: string | null = null;
  showLogoModal = false;
  logoPreview: string | null = null;
  selectedLogoFile: File | null = null;
  isDragging = false;
  isUploading = false;

  // Edit Business Info
  showEditBusinessModal = false;
  isSavingBusiness = false;
  editBusinessForm = {
    companyName: '',
    businessType: '',
    tin: '',
    businessLicenseNumber: '',
    officePhoneNumber: '',
    postalAddress: '',
    region: '',
    district: '',
    ward: '',
    postalCode: '',
  };

  // Edit Owner
  showEditOwnerModal = false;
  isSavingOwner = false;
  editOwnerForm = {
    ownerFirstName: '',
    ownerSecondName: '',
    ownerLastName: '',
    ownerPhoneNumber: '',
    ownerEmailAddress: '',
    ownerPostalAddress: '',
  };

  // Edit Contact
  showEditContactModal = false;
  isSavingContact = false;
  editContactForm = {
    contactFirstName: '',
    contactSecondName: '',
    contactLastName: '',
    contactDesignation: '',
    contactPhone: '',
    contactAlternativePhone: '',
    contactEmail: '',
  };

  constructor(private authService: AuthService, private businessService: BusinessRegistrationService) {}

  ngOnInit() {
    this.loadUserProfile();
  }

  get isPending(): boolean {
    return !this.businessInfo?.verification_status || this.businessInfo.verification_status === 'pending';
  }

  getOwnerInitials(): string {
    const first = this.businessInfo?.owner_first_name?.[0] || '';
    const last = this.businessInfo?.owner_last_name?.[0] || '';
    return (first + last).toUpperCase() || '?';
  }

  getContactInitials(): string {
    const first = this.contactInfo?.first_name?.[0] || '';
    const last = this.contactInfo?.last_name?.[0] || '';
    return (first + last).toUpperCase() || '?';
  }

  loadUserProfile() {
    this.isLoading = true;
    this.businessService.getProfile().subscribe({
      next: (response: any) => {
        this.user = response.user;
        this.businessInfo = response.businessOwnerInfo;
        this.contactInfo = response.businessContactInfo;
        this.logoUrl = response.businessOwnerInfo?.business_logo || null;
        this.isLoading = false;
      },
      error: (error) => {
        console.error('Error loading profile:', error);
        this.isLoading = false;
      }
    });
  }

  // --- Logo ---
  openLogoModal() {
    this.showLogoModal = true;
    this.logoPreview = null;
    this.selectedLogoFile = null;
  }

  closeLogoModal() {
    this.showLogoModal = false;
    this.logoPreview = null;
    this.selectedLogoFile = null;
    this.isDragging = false;
  }

  onDragOver(event: DragEvent) {
    event.preventDefault();
    event.stopPropagation();
    this.isDragging = true;
  }

  onDragLeave(event: DragEvent) {
    event.preventDefault();
    event.stopPropagation();
    this.isDragging = false;
  }

  onDrop(event: DragEvent) {
    event.preventDefault();
    event.stopPropagation();
    this.isDragging = false;
    const files = event.dataTransfer?.files;
    if (files && files.length > 0) this.handleFile(files[0]);
  }

  onFileSelected(event: Event) {
    const input = event.target as HTMLInputElement;
    if (input.files && input.files.length > 0) this.handleFile(input.files[0]);
  }

  handleFile(file: File) {
    const allowed = ['image/png', 'image/jpeg', 'image/webp'];
    if (!allowed.includes(file.type)) return;
    if (file.size > 1 * 1024 * 1024) return;
    this.selectedLogoFile = file;
    const reader = new FileReader();
    reader.onload = () => { this.logoPreview = reader.result as string; };
    reader.readAsDataURL(file);
  }

  uploadLogo() {
    if (!this.selectedLogoFile) return;
    this.isUploading = true;
    const formData = new FormData();
    formData.append('logo', this.selectedLogoFile);
    this.businessService.uploadLogo(formData).subscribe({
      next: (res: any) => {
        this.logoUrl = res.logo_url;
        this.isUploading = false;
        this.closeLogoModal();
      },
      error: (err) => {
        console.error('Logo upload failed:', err);
        this.isUploading = false;
      }
    });
  }

  removeLogo() {
    this.businessService.removeLogo().subscribe({
      next: () => {
        this.logoUrl = null;
        this.logoPreview = null;
        this.selectedLogoFile = null;
      },
      error: (err) => console.error('Logo removal failed:', err)
    });
  }

  // --- Edit Business Info ---
  openEditBusinessModal() {
    if (!this.isPending) return;
    this.editBusinessForm = {
      companyName:           this.businessInfo?.company_name            || '',
      businessType:          this.businessInfo?.business_type           || '',
      tin:                   this.businessInfo?.tin                     || '',
      businessLicenseNumber: this.businessInfo?.business_license_number || '',
      officePhoneNumber:     this.businessInfo?.office_phone_number     || '',
      postalAddress:         this.businessInfo?.postal_address          || '',
      region:                this.businessInfo?.region                  || '',
      district:              this.businessInfo?.district                || '',
      ward:                  this.businessInfo?.ward                    || '',
      postalCode:            this.businessInfo?.postal_code             || '',
    };
    this.showEditBusinessModal = true;
  }

  closeEditBusinessModal() {
    this.showEditBusinessModal = false;
  }

  saveBusinessInfo() {
    this.isSavingBusiness = true;
    this.businessService.updateBusinessInfo(this.editBusinessForm).subscribe({
      next: () => {
        this.isSavingBusiness = false;
        this.closeEditBusinessModal();
        this.loadUserProfile();
      },
      error: (err) => {
        console.error('Save business info failed:', err);
        this.isSavingBusiness = false;
      }
    });
  }

  // --- Edit Owner ---
  openEditOwnerModal() {
    if (!this.isPending) return;
    this.editOwnerForm = {
      ownerFirstName:     this.businessInfo?.owner_first_name    || '',
      ownerSecondName:    this.businessInfo?.owner_second_name   || '',
      ownerLastName:      this.businessInfo?.owner_last_name     || '',
      ownerPhoneNumber:   this.businessInfo?.owner_phone_number  || '',
      ownerEmailAddress:  this.businessInfo?.owner_email_address || '',
      ownerPostalAddress: this.businessInfo?.owner_postal_address|| '',
    };
    this.showEditOwnerModal = true;
  }

  closeEditOwnerModal() {
    this.showEditOwnerModal = false;
  }

  saveOwner() {
    this.isSavingOwner = true;
    this.businessService.updateBusinessInfo(this.editOwnerForm).subscribe({
      next: () => {
        this.isSavingOwner = false;
        this.closeEditOwnerModal();
        this.loadUserProfile();
      },
      error: (err) => {
        console.error('Save owner failed:', err);
        this.isSavingOwner = false;
      }
    });
  }

  // --- Edit Contact ---
  openEditContactModal() {
    if (!this.isPending) return;
    this.editContactForm = {
      contactFirstName:        this.contactInfo?.first_name              || '',
      contactSecondName:       this.contactInfo?.second_name             || '',
      contactLastName:         this.contactInfo?.last_name               || '',
      contactDesignation:      this.contactInfo?.designation             || '',
      contactPhone:            this.contactInfo?.phone_number            || '',
      contactAlternativePhone: this.contactInfo?.alternative_phone_number|| '',
      contactEmail:            this.contactInfo?.email_address           || '',
    };
    this.showEditContactModal = true;
  }

  closeEditContactModal() {
    this.showEditContactModal = false;
  }

  saveContact() {
    this.isSavingContact = true;
    this.businessService.updateBusinessInfo(this.editContactForm).subscribe({
      next: () => {
        this.isSavingContact = false;
        this.closeEditContactModal();
        this.loadUserProfile();
      },
      error: (err) => {
        console.error('Save contact failed:', err);
        this.isSavingContact = false;
      }
    });
  }
}
