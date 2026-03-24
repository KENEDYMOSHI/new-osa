import { CommonModule } from '@angular/common';
import { Component } from '@angular/core';
import { RouterModule } from '@angular/router';

type ApplicationStatus = 'all' | 'pending' | 'approved' | 'rejected' | 'in_review';

type ApplicationType = 'station_registration' | 'technical_inspection' | 'certificate_renewal';

type Application = {
  referenceNumber: string;
  applicationType: ApplicationType;
  serviceType: string;
  submittedDate: string;
  status: Exclude<ApplicationStatus, 'all'>;
  remarks: string;
};

type StatusFilterOption = {
  label: string;
  value: ApplicationStatus;
};

@Component({
  selector: 'app-business-applications',
  standalone: true,
  imports: [CommonModule, RouterModule],
  templateUrl: './business-applications.component.html',
})
export class BusinessApplicationsComponent {
  readonly statusFilters: StatusFilterOption[] = [
    { label: 'All', value: 'all' },
    { label: 'Pending', value: 'pending' },
    { label: 'In Review', value: 'in_review' },
    { label: 'Approved', value: 'approved' },
    { label: 'Rejected', value: 'rejected' },
  ];

  readonly serviceTypes: string[] = [
    'Vehicle Tank Verification (VTV)',
    'Weighbridge',
    'Fixed Storage Tank',
    'Bulk Storage Tank (BST)',
    'Pre Packaging',
    'Wagon Tank',
    'Fuel pump',
    'Flow Meter',
    'Check pump',
    'Pressure gauges',
    'Proving Tank',
    'Taximeter',
    'Metre Rule',
    'Tape Measure',
    'Brim Measure system',
    'Suspended Digital Ware',
    'Counter scale',
    'Platform scale',
    'Spring Balance',
    'Weigher',
    'Automatic Weigher',
    'Beam Scale',
    'Sandy & Ballast lorry (SBL)',
    'Other Measuring Instrument',
    'Other Measures of Length',
    'Domestic gas meter',
    'Weights',
  ];

  applications: Application[] = [
    {
      referenceNumber: 'APP-2026-0041',
      applicationType: 'station_registration',
      serviceType: 'Fuel pump',
      submittedDate: '2026-03-20',
      status: 'pending',
      remarks: 'Awaiting document review',
    },
    {
      referenceNumber: 'APP-2026-0038',
      applicationType: 'technical_inspection',
      serviceType: 'Weighbridge',
      submittedDate: '2026-03-17',
      status: 'in_review',
      remarks: 'Inspector assigned',
    },
    {
      referenceNumber: 'APP-2026-0035',
      applicationType: 'certificate_renewal',
      serviceType: 'Fixed Storage Tank',
      submittedDate: '2026-03-14',
      status: 'approved',
      remarks: 'Certificate issued',
    },
    {
      referenceNumber: 'APP-2026-0030',
      applicationType: 'station_registration',
      serviceType: 'Flow Meter',
      submittedDate: '2026-03-10',
      status: 'approved',
      remarks: 'Registration complete',
    },
    {
      referenceNumber: 'APP-2026-0027',
      applicationType: 'technical_inspection',
      serviceType: 'Pressure gauges',
      submittedDate: '2026-03-07',
      status: 'rejected',
      remarks: 'Incomplete documentation',
    },
    {
      referenceNumber: 'APP-2026-0022',
      applicationType: 'certificate_renewal',
      serviceType: 'Platform scale',
      submittedDate: '2026-03-03',
      status: 'approved',
      remarks: 'Certificate renewed',
    },
    {
      referenceNumber: 'APP-2026-0018',
      applicationType: 'station_registration',
      serviceType: 'Vehicle Tank Verification (VTV)',
      submittedDate: '2026-02-28',
      status: 'pending',
      remarks: 'Pending payment verification',
    },
    {
      referenceNumber: 'APP-2026-0015',
      applicationType: 'technical_inspection',
      serviceType: 'Counter scale',
      submittedDate: '2026-02-24',
      status: 'in_review',
      remarks: 'Scheduled for inspection',
    },
    {
      referenceNumber: 'APP-2026-0010',
      applicationType: 'certificate_renewal',
      serviceType: 'Domestic gas meter',
      submittedDate: '2026-02-20',
      status: 'rejected',
      remarks: 'Equipment failed inspection',
    },
  ];

  selectedStatus: ApplicationStatus = 'all';
  searchTerm = '';
  showNewApplicationModal = false;

  readonly pageSize = 7;
  currentPage = 1;

  get filteredApplications(): Application[] {
    return this.applications.filter((app) => {
      const matchesStatus =
        this.selectedStatus === 'all' || app.status === this.selectedStatus;
      const term = this.searchTerm.trim().toLowerCase();
      const matchesSearch =
        term.length === 0 ||
        app.referenceNumber.toLowerCase().includes(term) ||
        app.serviceType.toLowerCase().includes(term);
      return matchesStatus && matchesSearch;
    });
  }

  get pagedApplications(): Application[] {
    const start = (this.currentPage - 1) * this.pageSize;
    return this.filteredApplications.slice(start, start + this.pageSize);
  }

  get totalPages(): number {
    return Math.max(1, Math.ceil(this.filteredApplications.length / this.pageSize));
  }

  get pageNumbers(): number[] {
    const total = this.totalPages;
    const current = this.currentPage;
    if (total <= 7) return Array.from({ length: total }, (_, i) => i + 1);
    if (current <= 4) return [1, 2, 3, 4, 5, -1, total];
    if (current >= total - 3) return [1, -1, total - 4, total - 3, total - 2, total - 1, total];
    return [1, -1, current - 1, current, current + 1, -1, total];
  }

  get paginationFrom(): number {
    return this.filteredApplications.length === 0 ? 0 : (this.currentPage - 1) * this.pageSize + 1;
  }

  get paginationTo(): number {
    return Math.min(this.currentPage * this.pageSize, this.filteredApplications.length);
  }

  get totalApplications(): number {
    return this.applications.length;
  }

  setStatusFilter(status: ApplicationStatus): void {
    this.selectedStatus = status;
    this.currentPage = 1;
  }

  setSearchTerm(value: string): void {
    this.searchTerm = value;
    this.currentPage = 1;
  }

  goToPage(page: number): void {
    if (page >= 1 && page <= this.totalPages) this.currentPage = page;
  }

  getFilterCount(status: ApplicationStatus): number {
    if (status === 'all') return this.totalApplications;
    return this.applications.filter((a) => a.status === status).length;
  }

  isActiveFilter(status: ApplicationStatus): boolean {
    return this.selectedStatus === status;
  }

  getApplicationTypeLabel(type: ApplicationType): string {
    const labels: Record<ApplicationType, string> = {
      station_registration: 'Station Registration',
      technical_inspection: 'Technical Inspection',
      certificate_renewal: 'Certificate Renewal',
    };
    return labels[type];
  }

  getStatusClasses(status: Application['status']): string {
    const classes: Record<Application['status'], string> = {
      pending: 'bg-amber-50 text-amber-700 dark:bg-amber-500/10 dark:text-amber-300',
      in_review: 'bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-300',
      approved: 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300',
      rejected: 'bg-red-50 text-red-700 dark:bg-red-500/10 dark:text-red-300',
    };
    return classes[status];
  }

  getStatusLabel(status: Application['status']): string {
    const labels: Record<Application['status'], string> = {
      pending: 'Pending',
      in_review: 'In Review',
      approved: 'Approved',
      rejected: 'Rejected',
    };
    return labels[status];
  }

  openNewApplicationModal(): void {
    this.showNewApplicationModal = true;
  }

  closeNewApplicationModal(): void {
    this.showNewApplicationModal = false;
  }
}
