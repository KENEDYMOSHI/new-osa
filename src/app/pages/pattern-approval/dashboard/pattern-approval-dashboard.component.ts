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

  requiredDocs: string[] = [
    'Pattern Approval Certificate from Country of Origin',
    'Operation Manual of the Device',
    'Calibration Manual of the Instrument',
    'Technical Specifications of the Instrument',
    'Information on the Means of Sealing',
    'Drawing/Photograph of the Instrument',
    'Test Report from Accredited Laboratory',
    'Business Registration Certificate (TIN)',
  ];

  constructor(private authService: AuthService) {}

  ngOnInit() {
    this.authService.getProfile().subscribe({
      next: (response: any) => {
        this.user = response.user;
      },
      error: (error) => {
        console.error('Error fetching user profile:', error);
      }
    });
    // TODO: Fetch real Pattern Approval application stats from API
  }
}
