import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { AuthService } from '../../../core/services/auth.service';

@Component({
  selector: 'app-pattern-approval-dashboard',
  standalone: true,
  imports: [CommonModule, RouterModule],
  templateUrl: './pattern-approval-dashboard.component.html',
})
export class PatternApprovalDashboardComponent implements OnInit {
  user: any = null;
  stats = {
    totalApplications: 0,
    pending: 0,
    approved: 0,
    rejected: 0
  };

  constructor(private authService: AuthService) {}

  ngOnInit() {
    // Get user profile
    this.authService.getProfile().subscribe({
      next: (response: any) => {
        this.user = response.user;
      },
      error: (error) => {
        console.error('Error fetching user profile:', error);
      }
    });

    // TODO: Fetch Pattern Approval applications stats
    // For now, using placeholder data
  }
}
