import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { Router, RouterModule } from '@angular/router';
import { AuthService } from '../../core/services/auth.service';
import { LicenseService } from '../../services/license.service';

@Component({
  selector: 'app-dashboard',
  standalone: true,
  imports: [CommonModule, RouterModule],
  templateUrl: './dashboard.component.html',
})
export class DashboardComponent implements OnInit {
  userName: string = 'User';
  canApply: boolean = false;
  eligibilityReason: string = '';

  // Statistics
  stats = {
    total: 0,
    approved: 0,
    pending: 0,
    inProgress: 0
  };

  // Recent Applications
  recentApplications: any[] = [];

  // Notifications
  notifications: any[] = [];

  // Completion Rate
  completionRate: number = 0;

  constructor(
    private authService: AuthService, 
    private licenseService: LicenseService,
    private router: Router
  ) {}

  ngOnInit(): void {
    // Get user info
    this.authService.currentUser$.subscribe(data => {
      if (data && data.user) {
        this.userName = data.user.username;
      } else if (data && data.personalInfo) {
        this.userName = `${data.personalInfo.first_name} ${data.personalInfo.last_name}`;
      }
    });

    this.authService.getProfile().subscribe();
    
    // Check eligibility
    this.checkLicenseEligibility();
    
    // Load dashboard data
    this.loadDashboardData();
  }

  checkLicenseEligibility() {
    this.licenseService.checkEligibility().subscribe(
      (res: any) => {
        this.canApply = res.canApply;
        if (!this.canApply) {
          this.eligibilityReason = res.reason;
        }
      },
      (err) => {
        console.error('Error checking eligibility', err);
        this.canApply = false;
      }
    );
  }

  loadDashboardData() {
    // Load applications to calculate stats
    this.licenseService.getUserApplications().subscribe(
      (response: any) => {
        // Handle response layout: backend returns direct array or object with data property
        const applications = Array.isArray(response) ? response : (response.data || []);
        
        // Calculate statistics
        this.stats.total = applications.length;
        this.stats.approved = applications.filter((app: any) => 
          app.status === 'Approved' || app.status === 'Approved_CEO'
        ).length;
        this.stats.pending = applications.filter((app: any) => 
          app.status === 'Pending' || app.status === 'Pending_DTS' || app.status === 'Pending_CEO'
        ).length;
        this.stats.inProgress = applications.filter((app: any) => 
          app.status !== 'Approved' && app.status !== 'Approved_CEO' && 
          app.status !== 'Pending' && app.status !== 'Pending_DTS' && app.status !== 'Pending_CEO'
        ).length;

        // Get recent applications (last 5)
        this.recentApplications = applications
          .slice(0, 5)
          .map((app: any) => ({
            id: app.id,
            licenseType: app.license_class || 'License Application', // Map correctly if needed
            controlNumber: app.control_number || `APP-${app.id?.substring(0, 8)}`,
            date: this.formatDate(app.created_at),
            status: this.getStatusLabel(app.status)
          }));

        // Calculate completion rate
        if (this.stats.total > 0) {
          this.completionRate = Math.round((this.stats.approved / this.stats.total) * 100);
        }

        // Generate notifications
        this.generateNotifications(applications);
      },
      (error) => {
        console.error('Error loading dashboard data', error);
        // Set default values
        this.recentApplications = [];
        this.notifications = [];
      }
    );
  }

  generateNotifications(applications: any[]) {
    this.notifications = [];

    // Check for pending applications
    const pending = applications.filter(app => 
      app.status === 'Pending' || app.status === 'Pending_DTS' || app.status === 'Pending_CEO'
    );
    if (pending.length > 0) {
      this.notifications.push({
        type: 'info',
        title: 'Applications Under Review',
        message: `You have ${pending.length} application(s) currently under review.`,
        time: 'Just now'
      });
    }

    // Check for approved applications
    const approved = applications.filter(app => 
      app.status === 'Approved' || app.status === 'Approved_CEO'
    );
    if (approved.length > 0) {
      this.notifications.push({
        type: 'success',
        title: 'Congratulations!',
        message: `${approved.length} of your applications have been approved.`,
        time: 'Today'
      });
    }

    // Check for incomplete applications
    const incomplete = applications.filter(app => 
      app.status === 'Applicant_Submission' || app.status === 'Draft'
    );
    if (incomplete.length > 0) {
      this.notifications.push({
        type: 'warning',
        title: 'Action Required',
        message: `You have ${incomplete.length} incomplete application(s). Please complete them.`,
        time: '2 hours ago'
      });
    }

    // Add a reminder if eligible to apply
    if (this.canApply && applications.length === 0) {
      this.notifications.push({
        type: 'reminder',
        title: 'Get Started',
        message: 'Start your first license application today!',
        time: 'Today'
      });
    }
  }

  getStatusLabel(status: string): string {
    const statusMap: { [key: string]: string } = {
      'Approved': 'Approved',
      'Approved_CEO': 'Approved',
      'Pending': 'Pending',
      'Pending_DTS': 'Under Review',
      'Pending_CEO': 'Under Review',
      'Applicant_Submission': 'In Progress',
      'Draft': 'In Progress'
    };
    return statusMap[status] || status;
  }

  formatDate(dateString: string): string {
    if (!dateString) return '';
    const date = new Date(dateString);
    const options: Intl.DateTimeFormatOptions = { 
      year: 'numeric', 
      month: 'short', 
      day: 'numeric' 
    };
    return date.toLocaleDateString('en-US', options);
  }

  viewApplication(id: number) {
    this.router.navigate(['/my-applications']);
  }
}
