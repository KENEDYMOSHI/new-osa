import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { FormsModule } from '@angular/forms';
import { LicenseService } from '../../services/license.service';

@Component({
  selector: 'app-my-applications',
  standalone: true,
  imports: [CommonModule, RouterModule, FormsModule],
  templateUrl: './my-applications.component.html',
})
export class MyApplicationsComponent implements OnInit {
  
  // Filter states
  selectedCategory: string = 'All Categories';
  selectedStatus: string = 'All Statuses';
  selectedDate: string = '';

  // Data
  applications: any[] = [];
  loading: boolean = true;

  // Fee Generation Modal
  showFeeModal: boolean = false;
  feeModalData: any = null;

  constructor(private licenseService: LicenseService) { }

  ngOnInit(): void {
    this.fetchApplications();
  }

  fetchApplications() {
    this.loading = true;
    this.licenseService.getUserApplications().subscribe({
      next: (data) => {
        this.applications = data;
        this.loading = false;
      },
      error: (err) => {
        console.error('Failed to fetch applications', err);
        this.loading = false;
      }
    });
  }

  get approvedApplications() {
    return this.applications.filter(app => app.status === 'Approved' || app.status === 'Approved_CEO' || app.status === 'License_Generated');
  }

  get inProgressApplications() {
    // Return applications that are NOT approved
    return this.applications.filter(app => !(app.status === 'Approved' || app.status === 'Approved_CEO' || app.status === 'License_Generated'));
  }

  getStepClass(step: any, index: number, allSteps: any[]): string {
    // Logic to determine color/style based on status
    if (step.status === 'completed') return 'text-orange-500';
    if (step.status === 'current') return 'text-gray-800 font-bold';
    return 'text-gray-400';
  }


  // Interview Modal
  showInterviewModal: boolean = false;
  openInterviewId: string | null = null;
  selectedInterview: any = null;

  toggleInterviewResult(interview: any) {
    // Determine unique ID for this interview/app. In the loop we use 'app'. 
    // The interview object doesn't have an ID easily separate from app.
    // Let's use the object reference equality for simple check in template.
    if (this.selectedInterview === interview) {
        this.selectedInterview = null; // Close
    } else {
        this.selectedInterview = interview; // Open
    }
  }

  closeInterviewModal() {
    this.showInterviewModal = false;
    this.selectedInterview = null;
  }

  // Application Details Modal (CV Format)
  showDetailsModal: boolean = false;
  selectedApplicationDetails: any = null;
  loadingDetails: boolean = false;

  viewApplicationDetails(app: any) {
    this.loadingDetails = true;
    this.showDetailsModal = true;
    window.scrollTo(0, 0);
    
    // Check if we already have the full details or need to fetch
    // Use original_id if available, otherwise app.id
    const appId = app.original_id || app.id;
    
    this.licenseService.getApplicationDetails(appId).subscribe({
      next: (data) => {
        this.selectedApplicationDetails = data;
        this.loadingDetails = false;
      },
      error: (err) => {
        console.error('Failed to load application details', err);
        this.loadingDetails = false;
        alert('Failed to load full application details.');
        this.showDetailsModal = false;
      }
    });
  }

  closeDetailsModal() {
    this.showDetailsModal = false;
    this.selectedApplicationDetails = null;
  }

  printApplication() {
    window.print();
  }

  // License Fee and Payment Workflow
  generateFee(application: any) {
    if (confirm('Generate control number for license fee for this application?')) {
      this.licenseService.generateLicenseFee(application.original_id).subscribe({
        next: (response) => {
          // Show modal with bill details
          this.feeModalData = response.bill;
          this.showFeeModal = true;
          
          // Refresh applications to update button state
          this.fetchApplications();
        },
        error: (err) => {
          console.error('Failed to generate fee', err);
          alert(err.error?.message || 'Failed to generate license fee. Please try again.');
        }
      });
    }
  }

  closeFeeModal() {
    this.showFeeModal = false;
    this.feeModalData = null;
  }

  // Helper method to format numbers
  formatNumber(num: number): string {
    return new Intl.NumberFormat('en-US', {
      minimumFractionDigits: 2,
      maximumFractionDigits: 2
    }).format(num);
  }

  viewBill(application: any) {
    this.licenseService.checkPaymentStatus(application.original_id).subscribe({
      next: (response) => {
        if (response.has_bill) {
          const bill = response.bill;
          const message = `📄 Bill Details\n\n` +
                         `📋 Control Number: ${bill.control_number}\n` +
                         `💰 License Fee: TZS ${this.formatNumber(bill.license_fee)}\n` +
                         `💰 Application Fee: TZS ${this.formatNumber(bill.application_fee)}\n` +
                         `💵 Total Amount: TZS ${this.formatNumber(bill.total_amount)}\n` +
                         `📊 Payment Status: ${bill.payment_status}\n\n` +
                         `ℹ️ You can pay this bill in the "Billing & Payments" section.`;
          alert(message);
        } else {
          alert('No bill found for this application');
        }
      },
      error: (err) => {
        console.error('Failed to check payment status', err);
        alert('Failed to retrieve bill information');
      }
    });
  }

  viewLicense(application: any) {
    this.licenseService.viewLicense(application.original_id).subscribe({
      next: (response) => {
        if (response.license_url) {
          // Open license in new window or download
          window.open(response.license_url, '_blank');
        } else {
          alert('License is ready for viewing');
          // You can implement actual license viewing logic here
        }
      },
      error: (err) => {
        console.error('Failed to view license', err);
        if (err.status === 402) {
          // Payment required
          alert(err.error?.message || 'Payment must be completed before viewing license');
          // Optionally show bill details
          if (err.error?.bill) {
            this.viewBill(application);
          }
        } else {
          alert('Failed to view license');
        }
      }
    });
  }

  // Helper method to determine button state
  getLicenseButtonState(application: any): 'generate' | 'view-bill' | 'view-license' | 'none' {
    // This will be enhanced when we add payment status to the application object
    if (application.status === 'Approved' || application.status === 'Approved_CEO') {
      // Check if bill exists (you'll need to add this to the application data)
      if (application.bill_status === 'Paid') {
        return 'view-license';
      } else if (application.bill_status === 'Pending') {
        return 'view-bill';
      } else {
        return 'generate';
      }
    }
    return 'none';
  }
}
