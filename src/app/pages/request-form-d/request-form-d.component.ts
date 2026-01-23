import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { RouterModule } from '@angular/router';
import { LicenseService } from '../../services/license.service';
import { LocationService, District, Ward } from '../../services/location.service';

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
    certAuthNumber: '',
    userNames: '',
    
    declaration: false
  };

  loading: boolean = false;

  constructor(
      private licenseService: LicenseService,
      private locationService: LocationService
  ) { }

  ngOnInit(): void {
    this.fetchApprovedLicenses();
    this.loadRegions();
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
          // You could also auto-fill other fields if available in the license object
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

  submitForm() {
    this.loading = true;
    console.log('Form D Data:', this.formData);
    
    // Simulate API call
    setTimeout(() => {
        this.loading = false;
        alert('Form D Request Submitted Successfully!');
    }, 1500);
  }

  printForm() {
    window.print();
  }
}
