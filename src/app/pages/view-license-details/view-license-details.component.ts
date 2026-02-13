import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { LicenseService } from '../../services/license.service';
import { Router } from '@angular/router';
import { FormsModule } from '@angular/forms';

import { AppModalComponent } from '../../components/app-modal/app-modal.component';
import { LicenseIdCardComponent } from '../../components/license-id-card/license-id-card.component';

@Component({
  selector: 'app-view-license-details',
  standalone: true,
  imports: [CommonModule, FormsModule, AppModalComponent, LicenseIdCardComponent],
  templateUrl: './view-license-details.component.html',
  styleUrls: ['./view-license-details.component.css']
})
export class ViewLicenseDetailsComponent implements OnInit {
  licenses: any[] = [];
  filteredLicenses: any[] = [];
  isLoading: boolean = true;
  searchTerm: string = '';

  // Filter properties
  filterLicenseNumber: string = '';
  filterLicenseType: string = '';
  filterYear: string = '';
  filterStatus: string = '';
  
  // Filter options
  licenseTypes: string[] = [];
  years: number[] = [];

  // License View Modal
  showLicenseModal: boolean = false;
  licenseModalUrl: string | null = null;
  licenseModalNumber: string | null = null;

  // ID View Modal
  showIDModal: boolean = false;
  idModalUrl: string | null = null;
  idModalName: string | null = null;

  // License ID Card Modal
  showLicenseCardModal: boolean = false;
  selectedLicenseCardData: any = null;

  constructor(
    private licenseService: LicenseService,
    private router: Router
  ) {}

  ngOnInit(): void {
    // Fetch licenses on init
    this.fetchLicenses();
  }

  fetchLicenses() {
    this.isLoading = true;
    this.licenseService.getApprovedLicenses().subscribe(
      (res: any) => {
        // Filter to show only licenses that have actually been generated (have a license number)
        this.licenses = res.filter((lic: any) => lic.license_number);
        this.filteredLicenses = [...this.licenses];
        this.extractFilterOptions();
        this.isLoading = false;
      },
      (err) => {
        console.error('Error fetching licenses', err);
        this.isLoading = false;
      }
    );
  }

  filterLicenses() {
    if (!this.searchTerm) {
      this.filteredLicenses = [...this.licenses];
      return;
    }
    
    const term = this.searchTerm.toLowerCase();
    this.filteredLicenses = this.licenses.filter(license => 
      license.license_number?.toLowerCase().includes(term) ||
      license.license_type?.toLowerCase().includes(term)
    );
  }

  applyFilters() {
    this.filteredLicenses = this.licenses.filter(license => {
      // Filter by license number
      if (this.filterLicenseNumber && !license.license_number?.toLowerCase().includes(this.filterLicenseNumber.toLowerCase())) {
        return false;
      }
      
      // Filter by license type
      if (this.filterLicenseType && license.license_type !== this.filterLicenseType) {
        return false;
      }
      
      // Filter by year
      if (this.filterYear) {
        const issueYear = new Date(license.issue_date || license.updated_at).getFullYear().toString();
        if (issueYear !== this.filterYear) {
          return false;
        }
      }
      
      // Filter by status
      if (this.filterStatus) {
        const isExpired = this.isExpired(license.expiry_date);
        const status = isExpired ? 'Expired' : 'Active';
        if (status !== this.filterStatus) {
          return false;
        }
      }
      
      return true;
    });
  }

  clearFilters() {
    this.filterLicenseNumber = '';
    this.filterLicenseType = '';
    this.filterYear = '';
    this.filterStatus = '';
    this.filteredLicenses = [...this.licenses];
  }

  extractFilterOptions() {
    // Extract unique license types
    const types = new Set<string>();
    const yearSet = new Set<number>();
    
    this.licenses.forEach(license => {
      if (license.license_type) {
        types.add(license.license_type);
      }
      if (license.issue_date || license.updated_at) {
        const year = new Date(license.issue_date || license.updated_at).getFullYear();
        yearSet.add(year);
      }
    });
    
    this.licenseTypes = Array.from(types).sort();
    this.years = Array.from(yearSet).sort((a, b) => b - a); // Descending order
  }

  viewLicense(applicationId: string) {
     console.log('Requesting license view for App ID:', applicationId);
     this.licenseService.viewLicense(applicationId).subscribe(
      (res: any) => {
          console.log('View License Response:', res);
          
          if (res.license_url) {
             let url = res.license_url;
             // Ensure URL is absolute to point to backend (localhost:8080)
             if (url && !url.startsWith('http')) {
                 const relativePath = url.startsWith('/') ? url.substring(1) : url;
                 url = `http://localhost:8080/${relativePath}`;
             }
             
             this.licenseModalUrl = url;
             const license = this.licenses.find(l => l.id === applicationId);
             this.licenseModalNumber = res.license_number || license?.license_number || 'License Details';
             
             console.log('Opening Modal with URL:', this.licenseModalUrl);
             this.showLicenseModal = true;
          } else {
             console.warn('License URL missing in response:', res);
             alert('License data retrieved but no view URL provided.');
          }
      },
      err => {
          console.error('Failed to view license', err);
          if (err.status === 402) {
             alert(err.error?.message || 'Payment incomplete. Please check your bills.');
          } else {
             alert('Could not view license. Please try again.');
          }
      }
     )
  }

  closeLicenseModal() {
    this.showLicenseModal = false;
    this.licenseModalUrl = null;
    this.licenseModalNumber = null;
  }

  viewID(applicationId: string) {
     console.log('Requesting ID view for App ID:', applicationId);
     this.licenseService.viewID(applicationId).subscribe(
      (res: any) => {
          console.log('View ID Response:', res);
          
          if (res.id_url) {
             let url = res.id_url;
             // Ensure URL is absolute to point to backend (localhost:8080)
             if (url && !url.startsWith('http')) {
                 const relativePath = url.startsWith('/') ? url.substring(1) : url;
                 url = `http://localhost:8080/${relativePath}`;
             }
             
             this.idModalUrl = url;
             const license = this.licenses.find(l => l.id === applicationId);
             this.idModalName = res.applicant_name || license?.license_number || 'ID Document';
             
             console.log('Opening ID Modal with URL:', this.idModalUrl);
             this.showIDModal = true;
          } else {
             console.warn('ID URL missing in response:', res);
             alert('ID document not available.');
          }
      },
      (err: any) => {
          console.error('Failed to view ID', err);
          alert('Could not view ID document. Please try again.');
      }
     )
  }

  closeIDModal() {
    this.showIDModal = false;
    this.idModalUrl = null;
    this.idModalName = null;
  }

  // License ID Card Modal Methods
  openLicenseCardModal(license: any) {
    this.selectedLicenseCardData = {
      licenseNumber: license.license_number,
      applicantName: license.applicant_name,
      licenseType: license.license_type,
      issueDate: license.issue_date,
      expiryDate: license.expiry_date,
      profilePicture: this.getProfilePictureUrl(license.profile_picture),
      userInitial: license.applicant_name?.charAt(0) || 'N',
      companyName: license.company_name || '',
      address: license.address || '',
      phoneNumber: license.phone_number || '',
      region: license.region || '',
      instruments: license.instruments || [],
      position: this.getPositionFromLicenseType(license.license_type)
    };
    this.showLicenseCardModal = true;
  }

  getProfilePictureUrl(path: string | null): string | null {
    if (!path) return null;
    if (path.startsWith('http')) return path;
    const cleanPath = path.startsWith('/') ? path.substring(1) : path;
    return `http://localhost:8080/${cleanPath}`;
  }

  closeLicenseCardModal() {
    this.showLicenseCardModal = false;
    this.selectedLicenseCardData = null;
  }

  // Helper method to determine position based on license type
  getPositionFromLicenseType(licenseType: string): string {
    if (!licenseType) return 'CALIBRATION INSPECTOR';
    
    const type = licenseType.toUpperCase();
    if (type.includes('CLASS A') || type.includes('SENIOR')) {
      return 'SENIOR CALIBRATION INSPECTOR';
    } else if (type.includes('CLASS B')) {
      return 'CALIBRATION INSPECTOR';
    } else if (type.includes('CLASS C')) {
      return 'ASSISTANT CALIBRATION INSPECTOR';
    }
    return 'CALIBRATION INSPECTOR';
  }
  
  // Helper to check expiry
  isExpired(expiryDate: string): boolean {
      if (!expiryDate) return false;
      return new Date(expiryDate) < new Date();
  }
}
