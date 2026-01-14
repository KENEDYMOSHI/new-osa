import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { AuthService } from '../../core/services/auth.service';
import { LicenseService } from '../../services/license.service';
import { AppModalComponent } from '../../components/app-modal/app-modal.component';
import { ActivatedRoute } from '@angular/router';
import { firstValueFrom } from 'rxjs';
import Swal from 'sweetalert2';

@Component({
  selector: 'app-profile',
  standalone: true,
  imports: [CommonModule, FormsModule, ReactiveFormsModule, AppModalComponent],
  templateUrl: './profile.component.html',
})
export class ProfileComponent implements OnInit {
  activeTab = 'personal';
  activeView = 'profile'; // 'profile' or 'password'
  isEditing = false;
  isLoading = true;

  editingSection: string | null = null;
  showModal = false;
  showCompanyModal = false;
  backupData: any = {};

  userInfo = {
    name: '',
    email: '',
    phone: ''
  };

  personalInfo: any = {};
  companyInfo: any = {};

  securityInfo = {
    currentPassword: '',
    newPassword: '',
    confirmPassword: ''
  };

  showCurrentPassword = false;
  showNewPassword = false;
  showConfirmPassword = false;

  licenseData = [
    {
      type: 'Verification Officer License',
      number: 'VO-2023-12345',
      issueDate: 'Jan 15, 2023',
      expiryDate: 'Jan 14, 2024',
      status: 'Active'
    },
    {
      type: 'Scale Repair License',
      number: 'SR-2022-98765',
      issueDate: 'Jun 1, 2022',
      expiryDate: 'May 31, 2023',
      status: 'Expired'
    }
  ];

  socialLinks = {
    facebook: '',
    twitter: '',
    linkedin: '',
    instagram: ''
  };

  constructor(
    private authService: AuthService,
    private route: ActivatedRoute,
    private licenseService: LicenseService
  ) {}

  ngOnInit() {
    this.route.queryParams.subscribe(params => {
        this.activeView = params['view'] || 'profile';
    });
    this.fetchProfile();
  }

  async fetchProfile() {
    this.isLoading = true;
    try {
      const data = await firstValueFrom(this.authService.getProfile());
      console.log('Profile Data Fetched:', data);
      
      if (data.user) {
        this.userInfo.name = data.user.username;
        this.userInfo.email = data.user.email;
      }

      if (data.personalInfo) {
        this.personalInfo = {
          nationality: data.personalInfo.nationality,
          identityNumber: data.personalInfo.identity_number,
          firstName: data.personalInfo.first_name,
          secondName: data.personalInfo.second_name,
          lastName: data.personalInfo.last_name,
          gender: data.personalInfo.gender,
          dateOfBirth: data.personalInfo.dob,
          region: data.personalInfo.region,
          district: data.personalInfo.district,
          town: data.personalInfo.town,
          street: data.personalInfo.street,
          phone: data.personalInfo.phone || '',
          email: data.user.email // Ensure email is available here
        };
      }

      if (data.businessInfo) {
        this.companyInfo = {
          tin: data.businessInfo.tin,
          companyName: data.businessInfo.company_name,
          companyEmail: data.businessInfo.company_email,
          companyPhone: data.businessInfo.company_phone,
          brelaNumber: data.businessInfo.brela_number,
          region: data.businessInfo.bus_region,
          district: data.businessInfo.bus_district,
          town: data.businessInfo.bus_town,
          postalCode: data.businessInfo.postal_code,
          street: data.businessInfo.bus_street
        };
      }

      // Initialize backup data
      this.backupData = JSON.parse(JSON.stringify({
        personal: this.personalInfo,
        company: this.companyInfo,
        social: this.socialLinks
      }));

       // Populate License Data
        if (data.licenses && Array.isArray(data.licenses)) {
         this.licenseData = data.licenses.map((lic: any) => ({
             id: lic.app_id || lic.id,
             original_id: lic.app_id || lic.application_id || lic.original_id,
             type: lic.license_type || 'Unknown License',
             number: lic.license_number || 'Pending',
             // Use issue_date from licenses table, fallback to valid_from
             issueDate: lic.issue_date ? new Date(lic.issue_date).toLocaleDateString() : (lic.valid_from ? new Date(lic.valid_from).toLocaleDateString() : 'N/A'),
             // Use license_expiry_date from licenses table, fallback to valid_to
             expiryDate: lic.license_expiry_date ? new Date(lic.license_expiry_date).toLocaleDateString() : (lic.valid_to ? new Date(lic.valid_to).toLocaleDateString() : 'N/A'),
             status: lic.status || 'Submitted',
             controlNumber: lic.control_number || 'N/A'
         }));
      } else {
        this.licenseData = [];
      }

    } catch (error) {
      console.error('Failed to fetch profile:', error);
    } finally {
      this.isLoading = false;
    }
  }

  openEditModal() {
    this.showModal = true;
    // Backup current state
    this.backupData = JSON.parse(JSON.stringify({
        personal: this.personalInfo,
        company: this.companyInfo,
        social: this.socialLinks
    }));
  }

  closeModal() {
    this.showModal = false;
    // Restore data on cancel
    this.personalInfo = { ...this.backupData.personal };
    this.companyInfo = { ...this.backupData.company };
    this.socialLinks = { ...this.backupData.social };
  }

  async saveProfile() {
    this.isLoading = true;
    try {
        // Update Personal Info
        await firstValueFrom(this.authService.updatePersonalProfile(this.personalInfo));
        
        // Update local user info name if changed
        if (this.personalInfo.firstName && this.personalInfo.lastName) {
            this.userInfo.name = `${this.personalInfo.firstName} ${this.personalInfo.lastName}`;
        }
        
        await Swal.fire({
            title: 'Success!',
            text: 'Profile updated successfully.',
            icon: 'success',
            timer: 1500,
            showConfirmButton: false
        });
      
      this.showModal = false;
      
      // Refresh global state
      this.authService.getProfile().subscribe();

    } catch (error) {
      console.error('Error saving profile:', error);
      Swal.fire({
        title: 'Error!',
        text: 'Failed to update profile. Please try again.',
        icon: 'error',
        confirmButtonText: 'OK',
        confirmButtonColor: '#F59E0B'
      });
    } finally {
      this.isLoading = false;
    }
  }

  openCompanyEditModal() {
    this.showCompanyModal = true;
    // Backup current state
    this.backupData = JSON.parse(JSON.stringify({
        personal: this.personalInfo,
        company: this.companyInfo,
        social: this.socialLinks
    }));
  }

  closeCompanyModal() {
    this.showCompanyModal = false;
    // Restore data on cancel
    this.companyInfo = { ...this.backupData.company };
  }

  async saveCompanyProfile() {
    this.isLoading = true;
    try {
        await firstValueFrom(this.authService.updateBusinessProfile(this.companyInfo));
        
        await Swal.fire({
            title: 'Success!',
            text: 'Company information updated successfully.',
            icon: 'success',
            timer: 1500,
            showConfirmButton: false
        });
      
      this.showCompanyModal = false;
      
      // Refresh global state
      this.authService.getProfile().subscribe();

    } catch (error) {
      console.error('Error saving company profile:', error);
      Swal.fire({
        title: 'Error!',
        text: 'Failed to update company information. Please try again.',
        icon: 'error',
        confirmButtonText: 'OK',
        confirmButtonColor: '#F59E0B'
      });
    } finally {
      this.isLoading = false;
    }
  }

  passwordErrors = {
    currentPassword: '',
    newPassword: '',
    confirmPassword: '',
    general: ''
  };

  validatePasswordForm(): boolean {
    let isValid = true;
    this.passwordErrors = {
      currentPassword: '',
      newPassword: '',
      confirmPassword: '',
      general: ''
    };

    if (!this.securityInfo.currentPassword) {
      this.passwordErrors.currentPassword = 'Please fill out this field.';
      isValid = false;
    }

    if (!this.securityInfo.newPassword) {
      this.passwordErrors.newPassword = 'Please fill out this field.';
      isValid = false;
    } else if (this.securityInfo.newPassword.length < 8) {
      this.passwordErrors.newPassword = 'Password must be at least 8 characters.';
      isValid = false;
    }

    if (!this.securityInfo.confirmPassword) {
      this.passwordErrors.confirmPassword = 'Please fill out this field.';
      isValid = false;
    } else if (this.securityInfo.newPassword !== this.securityInfo.confirmPassword) {
      this.passwordErrors.confirmPassword = 'New password and Confirm password do not match.';
      isValid = false;
    }

    if (!isValid) {
      this.passwordErrors.general = 'Please complete the highlighted fields before saving.';
    }

    return isValid;
  }

  async updatePassword() {
    if (!this.validatePasswordForm()) {
        return;
    }

    this.isLoading = true;
    try {
        await firstValueFrom(this.authService.changePassword(this.securityInfo));
        
        await Swal.fire({
            title: 'Success!',
            text: 'Password changed successfully.',
            icon: 'success',
            timer: 1500,
            showConfirmButton: false
        });

        // Reset form
        this.securityInfo = {
            currentPassword: '',
            newPassword: '',
            confirmPassword: ''
        };
    } catch (error: any) {
        console.error('Failed to change password:', error);
        const msg = error.error?.messages?.currentPassword || 'Failed to change password. Please check your current password.';
        Swal.fire({
            title: 'Error!',
            text: msg,
            icon: 'error',
            confirmButtonText: 'OK',
            confirmButtonColor: '#F59E0B'
        });
    } finally {
        this.isLoading = false;
    }
  }
  // License View Modal
  showLicenseModal: boolean = false;
  licenseModalUrl: string | null = null;
  licenseModalNumber: string | null = null;

  closeLicenseModal() {
    this.showLicenseModal = false;
    this.licenseModalUrl = null;
    this.licenseModalNumber = null;
  }

  viewLicense(license: any) {
    const appId = license.original_id || license.id; // Use application ID if possible
    if (!appId) {
        Swal.fire('Error', 'Application ID missing', 'error');
        return;
    }

    // Show loading
    Swal.fire({
      title: 'Loading License...',
      allowOutsideClick: false,
      didOpen: () => {
        Swal.showLoading();
      }
    });

    this.licenseService.viewLicense(appId).subscribe({
      next: (response) => {
        Swal.close();
        if (response.license_url) {
          // Open license in modal
          this.licenseModalUrl = response.license_url;
          this.licenseModalNumber = response.license_number || license.number || 'License Preview';
          this.showLicenseModal = true;
        } else {
          Swal.fire('Error', 'License is ready but no URL returned.', 'error');
        }
      },
      error: (err) => {
        Swal.close();
        console.error('Failed to view license', err);
        Swal.fire('Error', 'Failed to load license preview: ' + (err.error?.message || err.message), 'error');
      }
    });
  }

  // Helper method to check if license is expired
  isLicenseExpired(license: any): boolean {
    if (!license.expiryDate || license.expiryDate === 'N/A') {
      return false;
    }
    
    // Parse the expiry date and compare with current date
    const expiryDate = new Date(license.expiryDate);
    const today = new Date();
    today.setHours(0, 0, 0, 0); // Reset time to start of day for accurate comparison
    
    return expiryDate < today;
  }
}
