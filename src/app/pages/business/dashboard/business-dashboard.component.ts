import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { AuthService } from '../../../core/services/auth.service';

@Component({
  selector: 'app-business-dashboard',
  standalone: true,
  imports: [CommonModule, RouterModule],
  templateUrl: './business-dashboard.component.html',
})
export class BusinessDashboardComponent implements OnInit {
  user: any = null;
  stats = {
    registeredEquipment: 12,
    pendingVerifications: 3,
    activeCertificates: 8,
    upcomingRenewals: 2,
    totalApplications: 5,
    pendingInspections: 1
  };

  recentActivities = [
    { date: '2026-03-15', description: 'Fuel Pump FP-001 verification completed', status: 'Completed', statusColor: 'green' },
    { date: '2026-03-12', description: 'Storage Tank ST-003 inspection requested', status: 'Pending', statusColor: 'yellow' },
    { date: '2026-03-10', description: 'Weighbridge WB-001 certificate renewed', status: 'Approved', statusColor: 'green' },
    { date: '2026-03-08', description: 'Flow Meter FM-002 registration submitted', status: 'Under Review', statusColor: 'blue' },
    { date: '2026-03-05', description: 'Certificate payment for Q1 2026 processed', status: 'Paid', statusColor: 'green' },
  ];

  equipmentSummary = [
    { type: 'Fuel Pumps', count: 6, verified: 5, pending: 1 },
    { type: 'Storage Tanks', count: 3, verified: 2, pending: 1 },
    { type: 'Weighbridges', count: 1, verified: 1, pending: 0 },
    { type: 'Flow Meters', count: 2, verified: 1, pending: 1 },
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
  }
}
