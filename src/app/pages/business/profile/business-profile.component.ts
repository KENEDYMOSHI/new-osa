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

  // Edit Owner
  showEditOwnerModal = false;
  editOwnerForm = {
    first_name: '',
    second_name: '',
    last_name: '',
    phone_number: '',
    email: '',
    postal_address: '',
  };

  // Edit Contact
  showEditContactModal = false;
  editContactForm = {
    first_name: '',
    second_name: '',
    last_name: '',
    designation: '',
    phone_number: '',
    alternative_phone: '',
    email: '',
  };

  constructor(private authService: AuthService, private businessService: BusinessRegistrationService) {}

  ngOnInit() {
    this.loadUserProfile();
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
    if (files && files.length > 0) {
      this.handleFile(files[0]);
    }
  }

  onFileSelected(event: Event) {
    const input = event.target as HTMLInputElement;
    if (input.files && input.files.length > 0) {
      this.handleFile(input.files[0]);
    }
  }

  handleFile(file: File) {
    const allowed = ['image/png', 'image/jpeg', 'image/webp'];
    if (!allowed.includes(file.type)) return;
    if (file.size > 1 * 1024 * 1024) return; // 1MB limit
    this.selectedLogoFile = file;
    const reader = new FileReader();
    reader.onload = () => {
      this.logoPreview = reader.result as string;
    };
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

  // --- Edit Owner ---
  openEditOwnerModal() {
    this.editOwnerForm = {
      first_name: this.businessInfo?.owner_first_name || '',
      second_name: this.businessInfo?.owner_second_name || '',
      last_name: this.businessInfo?.owner_last_name || '',
      phone_number: this.businessInfo?.owner_phone_number || '',
      email: this.businessInfo?.owner_email_address || '',
      postal_address: this.businessInfo?.owner_postal_address || '',
    };
    this.showEditOwnerModal = true;
  }

  closeEditOwnerModal() {
    this.showEditOwnerModal = false;
  }

  saveOwner() {
    // TODO: API call to save owner info
    console.log('Save owner:', this.editOwnerForm);
    this.closeEditOwnerModal();
  }

  // --- Edit Contact ---
  openEditContactModal() {
    this.editContactForm = {
      first_name: this.contactInfo?.first_name || '',
      second_name: this.contactInfo?.second_name || '',
      last_name: this.contactInfo?.last_name || '',
      designation: this.contactInfo?.designation || '',
      phone_number: this.contactInfo?.phone_number || '',
      alternative_phone: this.contactInfo?.alternative_phone_number || '',
      email: this.contactInfo?.email_address || '',
    };
    this.showEditContactModal = true;
  }

  closeEditContactModal() {
    this.showEditContactModal = false;
  }

  saveContact() {
    // TODO: API call to save contact info
    console.log('Save contact:', this.editContactForm);
    this.closeEditContactModal();
  }
}
