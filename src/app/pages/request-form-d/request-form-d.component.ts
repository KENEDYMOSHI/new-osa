import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { RouterModule } from '@angular/router';
import { LicenseService } from '../../services/license.service';
import { LocationService, District, Ward } from '../../services/location.service';
import { AuthService } from '../../core/services/auth.service';
import Swal from 'sweetalert2';

@Component({
  selector: 'app-request-form-d',
  standalone: true,
  imports: [CommonModule, FormsModule, RouterModule],
  templateUrl: './request-form-d.component.html',
})
export class RequestFormDComponent implements OnInit {
  
  // Licenses Data
  approvedLicenses: any[] = [];
  selectedLicense: any = null;
  userProfileName: string = ''; // Store logged-in user name
  userProfilePhone: string = ''; // Store logged-in user phone
  userId: any = null; // Store user ID

  // Location Data
  regions: string[] = [];
  districts: District[] = [];
  wards: Ward[] = [];

  // Form Data
  formData: any = {
    // Company Information
    companyName: '',
    region: '',
    district: '',
    ward: '',
    street: '',
    postalCode: '',
    address: '',

    // Certification Details
    certificationAction: '', // Erected, Adjusted, Repaired
    
    // Instrument Details
    instrumentName: '',
    serialNumber: '',
    product: '',
    stickerNumber: '',
    sealNumber: '',
    typeOfInstrument: '', 
    quantity: '', 
    capacity: '', // For Tanks
    status: '', 
    inspectionReport: '',
    verificationDate: '',
    nextVerificationDate: '',

    // Inspection Schedule
    startDate: '',
    startTime: '',
    endDate: '',
    endTime: '',

    // Final Certification
    licenseNumber: '',
    accountName: '', // New field for Account Name
    certAuthNumber: '',
    userNames: '', // Kept for backward compatibility or if needed for "Name(s) of User(s)" general field
    
    declarantTime: '',
    
    declaration: false
  };

  // Dynamic Text Configuration
  textConfig: any = {
    headerSubtitle: 'Form of Certificate to be used by a Pump Mechanic after Sealing or Re-sealing (Regulation 12(d))',
    certMechanicTitle: 'pump mechanic',
    certInstrumentName: 'pump',
    certUserDescription: 'liquid measuring pump',
    certActionContext: 'sealed/re-sealed'
  };

  // Field Visibility Configuration
  fieldConfig: any = {
      showNozzles: true,
      showSealNumber: true,
      showCapacity: false,
      showSticker: true
  };

  // Time Slots for Inspection Schedule
  timeSlots: string[] = [];

  loading: boolean = false;

  constructor(
      private licenseService: LicenseService,
      private locationService: LocationService,
      private authService: AuthService
  ) { 
      this.generateTimeSlots();
  }

  generateTimeSlots() {
      const start = 8; // 8 AM
      const end = 18; // 6 PM
      for (let i = start; i <= end; i++) {
          const hour = i < 10 ? `0${i}` : `${i}`;
          this.timeSlots.push(`${hour}:00`);
          if (i !== end) {
              this.timeSlots.push(`${hour}:30`);
          }
      }
  }

  ngOnInit(): void {
    this.fetchApprovedLicenses();
    this.loadRegions();
    this.fetchUserProfile();
  }

  fetchUserProfile() {
      this.authService.getProfile().subscribe({
          next: (data) => {
              // The API returns { user: { ... } } based on UserDropdown usage
              const userProp = data.user || data; // Fallback if structure differs
              this.userId = userProp.id;
              
              // Priority: username (from dropdown) -> first+last -> other
              // The dropdown uses `user.username` which shows "KENEDY MOSHI"
              let rawName = userProp.username || 
                            `${userProp.first_name || ''} ${userProp.last_name || ''}`.trim() || 
                            userProp.full_name || 
                            userProp.name || 
                            userProp.practitioner_name || 
                            'User';
              
              // Convert to Title Case (Small Letters / Normal Case)
              this.userProfileName = rawName.toLowerCase().replace(/\b\w/g, (s: string) => s.toUpperCase());
              
              // Get Phone Number
              // Check personalInfo first (as seen in Profile Details), then user object, then businessInfo
              const personalInfo = data.personalInfo || {};
              const businessInfo = data.businessInfo || {};
              
              this.userProfilePhone = personalInfo.phone || 
                                      userProp.phone || 
                                      userProp.phone_number || 
                                      userProp.mobile || 
                                      businessInfo.company_phone ||
                                      '---';
              
              console.log('User Profile Loaded:', this.userProfileName, this.userProfilePhone);
              
              if (this.selectedLicense) {
                  // Update current form data if license is already selected
                  this.formData.accountName = this.userProfileName;
              }
          },
          error: (err) => console.error('Failed to load user profile', err)
      });
  }

  loadRegions() {
      this.locationService.getRegions().subscribe({
          next: (data) => this.regions = data,
          error: (err) => console.error('Failed to load regions', err)
      });
  }

  onRegionChange() {
      this.formData.district = '';
      this.formData.ward = '';
      this.districts = [];
      this.wards = [];
      
      if (this.formData.region) {
          this.locationService.getDistricts(this.formData.region).subscribe({
              next: (data) => this.districts = data,
              error: (err) => console.error('Failed to load districts', err)
          });
      }
  }

  onDistrictChange() {
      this.formData.ward = '';
      this.wards = [];
      
      if (this.formData.district) {
          this.locationService.getWards(this.formData.district).subscribe({
              next: (data) => this.wards = data,
              error: (err) => console.error('Failed to load wards', err)
          });
      }
  }
  
  selectLicense(license: any) {
      if (this.selectedLicense === license) {
          // Deselect
          this.selectedLicense = null;
          this.formData.licenseNumber = '';
      } else {
          // Select
          this.selectedLicense = license;
          // Auto-fill form data where possible
          this.formData.licenseNumber = license.license_number;
          
          // Set Account Name to Profile Name (as requested: "jina la mwenye profile")
          this.formData.accountName = this.userProfileName || license.applicant_name || license.company_name || 'N/A';
          
          // Update text and fields based on license type
          this.updateConfig(license.license_type);
      }
  }

  updateConfig(licenseType: string) {
      const type = licenseType ? licenseType.toLowerCase() : '';

      if (type.includes('mechanic') || type.includes('pump') || type.includes('class d') || type.includes('measure')) {
          // Default Pump Mechanic (Class D)
          this.textConfig = {
              headerSubtitle: 'Form of Certificate to be used by a Pump Mechanic after Sealing or Re-sealing (Regulation 12(d))',
              certMechanicTitle: 'pump mechanic',
              certInstrumentName: 'pump',
              certUserDescription: 'liquid measuring pump',
              certActionContext: 'sealed/re-sealed'
          };
          this.fieldConfig = {
              showNozzles: true,
              showSealNumber: true,
              showCapacity: false,
              showSticker: true
          };
      } else if (type.includes('tank') || type.includes('construction') || type.includes('calibration')) {
          // Tank Constructor
          this.textConfig = {
              headerSubtitle: 'Form of Certificate to be used by a Tank Constructor after Calibration (Regulation 12(d))',
              certMechanicTitle: 'tank constructor',
              certInstrumentName: 'tank',
              certUserDescription: 'storage tank',
              certActionContext: 'calibrated'
          };
          this.fieldConfig = {
              showNozzles: false,
              showSealNumber: false, // Tanks might not use seal numbers in the same way, or user didn't specify
              showCapacity: true,
              showSticker: true // Keeping sticker for now unless specified
          };
      } else {
          // Fallback
          this.textConfig = {
              headerSubtitle: 'Form of Certificate to be used by a Certified Mechanic (Regulation 12(d))',
              certMechanicTitle: 'mechanic',
              certInstrumentName: 'instrument',
              certUserDescription: 'measuring instrument',
              certActionContext: 'sealed/verified'
          };
          this.fieldConfig = {
              showNozzles: true,
              showSealNumber: true,
              showCapacity: true,
              showSticker: true
          };
      }
  }

  get isFormEnabled(): boolean {
      return !!this.selectedLicense;
  }

  fetchApprovedLicenses() {
    this.licenseService.getApprovedLicenses().subscribe({
      next: (data) => {
        // Filter only approved licenses (status = 'Approved_CEO', 'License_Generated', or 'Approved')
        this.approvedLicenses = data.filter((lic: any) => 
            ['Approved_CEO', 'License_Generated', 'Approved'].includes(lic.status)
        );
        console.log('Approved Licenses:', this.approvedLicenses);
      },
      error: (err) => {
        console.error('Failed to fetch approved licenses', err);
      }
    });
  }

  validateForm(): boolean {
    const requiredFields = [
      'companyName', 'region', 'district', 'ward', 'street', 
      'instrumentName', 'serialNumber', 'product', 'typeOfInstrument', 'status',
      'verificationDate', 'nextVerificationDate', 'inspectionReport', 
      'certAuthNumber', 'declarantName', 'certificationAction'
    ];

    for (const field of requiredFields) {
      if (!this.formData[field]) {
        return false;
      }
    }
    
    // Conditional checks
    if (this.fieldConfig.showSticker && !this.formData.stickerNumber) return false;
    if (this.fieldConfig.showSealNumber && !this.formData.sealNumber) return false;
    if (this.fieldConfig.showNozzles && !this.formData.quantity) return false;
    if (this.fieldConfig.showCapacity && !this.formData.capacity) return false;
    
    // Validate Phone Number Length (Must be exactly 10 digits)
    if (this.formData.declarantPhone && this.formData.declarantPhone.length !== 10) {
        Swal.fire({
            title: 'Invalid Phone Number',
            text: 'Phone number must be exactly 10 digits.',
            icon: 'warning',
            confirmButtonColor: '#F59E0B'
        });
        return false;
    }

    return true;
  }

  submitForm() {
    if (!this.validateForm()) {
        Swal.fire({
            title: 'Incomplete Form',
            text: 'Please fill in all required fields before submitting.',
            icon: 'warning',
            confirmButtonColor: '#F59E0B'
        });
        return;
    }

    this.loading = true;
    
    // Prepare payload with snake_case mapping for DB
    const payload = {
        user_id: this.userId,
        license_number: this.formData.licenseNumber,
        practitioner_name: this.formData.accountName,
        practitioner_phone: this.userProfilePhone,
        cert_auth_number: this.formData.certAuthNumber,
        company_name: this.formData.companyName,
        region: this.formData.region,
        district: this.formData.district,
        ward: this.formData.ward,
        street: this.formData.street,
        postal_code: this.formData.postalCode,
        address: this.formData.address,
        certification_action: this.formData.certificationAction,
        instrument_name: this.formData.instrumentName,
        serial_number: this.formData.serialNumber,
        product: this.formData.product,
        sticker_number: this.formData.stickerNumber,
        seal_number: this.formData.sealNumber,
        type_of_instrument: this.formData.typeOfInstrument,
        quantity: this.formData.quantity,
        capacity: this.formData.capacity,
        status: this.formData.status,
        verification_date: this.formData.verificationDate,
        next_verification_date: this.formData.nextVerificationDate,
        inspection_report: this.formData.inspectionReport,
        start_date: this.formData.startDate,
        start_time: this.formData.startTime,
        end_date: this.formData.endDate,
        end_time: this.formData.endTime,
        declarant_name: this.formData.declarantName,
        declarant_date: this.formData.declarantDate,
        declarant_designation: this.formData.declarantDesignation,
        declarant_phone: this.formData.declarantPhone
    };

    console.log('Submitting Form D:', payload);

    this.licenseService.submitFormD(payload).subscribe({
        next: (response) => {
            this.loading = false;
            Swal.fire({
                title: 'Success!',
                text: 'Form D Request Submitted Successfully.',
                icon: 'success',
                confirmButtonColor: '#F59E0B'
            }).then(() => {
                // Refresh the page as requested
                window.location.reload();
            });
        },
        error: (err) => {
            this.loading = false;
            console.error('Submission Error:', err);
            Swal.fire({
                title: 'Error',
                text: 'Failed to submit request. Please try again.',
                icon: 'error',
                confirmButtonColor: '#F59E0B'
            });
        }
    });
  }

  printForm() {
    window.print();
  }
}
