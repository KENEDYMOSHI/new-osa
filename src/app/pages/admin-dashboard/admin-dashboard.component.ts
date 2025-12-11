import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { AdminService } from '../../services/admin.service';
import { DomSanitizer, SafeResourceUrl } from '@angular/platform-browser';
import { Router } from '@angular/router';
import { AuthService } from '../../core/services/auth.service';

@Component({
  selector: 'app-admin-dashboard',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './admin-dashboard.component.html',
  styleUrls: ['./admin-dashboard.component.css']
})
export class AdminDashboardComponent implements OnInit {
  applications: any[] = [];
  activeTab: string = 'Applications';
  tabs = ['Applications', 'Manager', 'Surveillance', 'DTS', 'CEO'];
  
  get filteredApplications() {
    // For testing purposes, we want to show all applications in all tabs as requested
    // "show kwa wote application ilio kua submitted"
    return this.applications;

    /* Original filtering logic
    return this.applications.filter(app => {
      const stage = parseInt(app.current_stage || '1', 10); // Default to stage 1 if missing
      
      switch (this.activeTab) {
        case 'Manager':
          return stage >= 1;
        case 'Surveillance':
          return stage >= 2;
        case 'DTS':
          return stage >= 3;
        case 'CEO':
          return stage >= 4 || app.status === 'Approved';
        default:
          return true;
      }
    });
    */
  }
  
  selectedApplication: any = null;
  showModal: boolean = false;
  previewUrl: SafeResourceUrl | null = null;
  previewLoading: boolean = false;
  
  expandedAppId: string | null = null;
  expandedAppDetails: any = null;
  loadingAttachments: boolean = false;

  constructor(
    private adminService: AdminService, 
    private sanitizer: DomSanitizer,
    private authService: AuthService,
    private router: Router
  ) {}

  ngOnInit(): void {
    this.fetchApplications();
  }

  logout() {
    this.authService.logout();
    this.router.navigate(['/admin/login']);
  }

  fetchApplications() {
    this.adminService.getApplications().subscribe({
      next: (data) => {
        this.applications = data;
      },
      error: (err) => {
        console.error('Failed to fetch applications', err);
      }
    });
  }

  toggleAttachments(id: string) {
    if (this.expandedAppId === id) {
      this.expandedAppId = null;
      this.expandedAppDetails = null;
      return;
    }

    this.expandedAppId = id;
    this.loadingAttachments = true;
    this.expandedAppDetails = null;

    this.adminService.getApplicationDetails(id).subscribe({
      next: (data) => {
        this.expandedAppDetails = data;
        this.processAttachments(data.attachments);
        this.loadingAttachments = false;
      },
      error: (err) => {
        console.error('Failed to fetch details', err);
        this.loadingAttachments = false;
      }
    });
  }

  requiredAttachments: any[] = [];
  qualificationAttachments: any[] = [];

  processAttachments(attachments: any[]) {
    this.requiredAttachments = [];
    this.qualificationAttachments = [];

    // User Request: "show all document alizo uppload applicaant"
    // We will no longer split them into categories, just show everything in the main list.
    if (attachments) {
      this.requiredAttachments = attachments;
    }
  }

  viewApplication(id: string) {
    this.router.navigate(['/viewApplication', id]);
  }

  closeModal() {
    this.showModal = false;
    this.selectedApplication = null;
    this.previewUrl = null;
  }

  parseJson(jsonString: any): any {
    if (typeof jsonString === 'string') {
      try {
        return JSON.parse(jsonString);
      } catch (e) {
        return [];
      }
    }
    return jsonString;
  }

  viewDocument(id: string) {
    // Legacy method if needed, but we use openDocument now
    this.openDocument(id);
  }
  
  openDocument(id: string) {
      this.previewLoading = true;
      const token = localStorage.getItem('token');
      fetch(this.adminService.getDocumentUrl(id), {
          headers: {
              'Authorization': `Bearer ${token}`
          }
      })
      .then(response => response.blob())
      .then(blob => {
          const objectUrl = window.URL.createObjectURL(blob);
          this.previewUrl = this.sanitizer.bypassSecurityTrustResourceUrl(objectUrl);
          this.previewLoading = false;
      })
      .catch(err => {
          console.error('Error downloading document', err);
          this.previewLoading = false;
      });
  }

  approveApplication(id: string) {
    if (confirm('Are you sure you want to approve this application?')) {
      this.adminService.approveApplication(id).subscribe({
        next: () => {
          alert('Application approved successfully');
          this.fetchApplications(); // Refresh list
          if (this.selectedApplication && this.selectedApplication.application.id === id) {
             this.closeModal(); // Close modal if open for this app
          }
        },
        error: (err) => {
          console.error('Failed to approve application', err);
          alert('Failed to approve application');
        }
      });
    }
  }

  getDocumentUrl(id: string): string {
    return this.adminService.getDocumentUrl(id);
  }

  // Document Return functionality
  returnCommentDocId: string | null = null;
  returnComment: string = '';

  toggleReturnComment(docId: string) {
    if (this.returnCommentDocId === docId) {
      this.returnCommentDocId = null;
      this.returnComment = '';
    } else {
      this.returnCommentDocId = docId;
      this.returnComment = '';
    }
  }

  cancelReturn() {
    this.returnCommentDocId = null;
    this.returnComment = '';
  }

  submitReturn(docId: string) {
    if (!this.returnComment.trim()) {
      alert('Please enter a reason for returning this document.');
      return;
    }

    this.adminService.returnDocument(docId, this.returnComment).subscribe({
      next: () => {
        alert('Document returned successfully');
        // Refresh the attachments
        if (this.expandedAppId) {
          this.toggleAttachments(this.expandedAppId);
          this.toggleAttachments(this.expandedAppId); // Toggle twice to refresh
        }
        // Reset
        this.returnCommentDocId = null;
        this.returnComment = '';
      },
      error: (err) => {
        console.error('Failed to return document', err);
        alert('Failed to return document. Please try again.');
      }
    });
  }

  setActiveTab(tab: string) {
    this.activeTab = tab;
  }
}
