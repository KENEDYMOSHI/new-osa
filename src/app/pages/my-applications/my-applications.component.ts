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

  getStepClass(step: any, index: number, allSteps: any[]): string {
    // Logic to determine color/style based on status
    if (step.status === 'completed') return 'text-orange-500';
    if (step.status === 'current') return 'text-gray-800 font-bold';
    return 'text-gray-400';
  }


  // Interview Modal
  showInterviewModal: boolean = false;
  selectedInterview: any = null;

  openInterviewModal(interview: any) {
    this.selectedInterview = interview;
    this.showInterviewModal = true;
  }

  closeInterviewModal() {
    this.showInterviewModal = false;
    this.selectedInterview = null;
  }
}
