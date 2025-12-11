import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { AuthService } from '../../core/services/auth.service';

@Component({
  selector: 'app-dashboard',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './dashboard.component.html',
})
export class DashboardComponent {
  // Mock data for summary cards
  summaryCards = [
    {
      title: 'New Applications',
      count: 1,
      icon: 'file-plus', // Placeholder for icon name
      color: 'bg-orange-500',
      textColor: 'text-orange-500',
      bgColor: 'bg-orange-50'
    },
    {
      title: 'Applications In Progress',
      count: 1,
      icon: 'file-text',
      color: 'bg-yellow-400',
      textColor: 'text-yellow-400',
      bgColor: 'bg-yellow-50'
    },
    {
      title: 'Approved Applications',
      count: 1,
      icon: 'check-circle',
      color: 'bg-green-500',
      textColor: 'text-green-500',
      bgColor: 'bg-green-50'
    },
    {
      title: 'Completed Jobs',
      count: 4,
      icon: 'briefcase',
      color: 'bg-blue-500',
      textColor: 'text-blue-500',
      bgColor: 'bg-blue-50'
    },
    {
      title: 'New Jobs',
      count: 1,
      icon: 'users',
      color: 'bg-purple-500',
      textColor: 'text-purple-500',
      bgColor: 'bg-purple-50'
    }
  ];

  // Mock data for recent activities
  recentActivities = [
    {
      companyName: 'ABC Corporation',
      activity: 'Fuel tank calibration',
      region: 'Dar es Salaam',
      sealNumber: 'WMA-2025-001',
      startingDate: 'May 20, 2025',
      finishDate: 'May 22, 2025',
      time: '08:00 AM'
    },
    {
      companyName: 'XYZ Industries',
      activity: 'Scale verification',
      region: 'Arusha',
      sealNumber: 'WMA-2025-002',
      startingDate: 'May 15, 2025',
      finishDate: 'May 16, 2025',
      time: '10:30 AM'
    },
    {
      companyName: 'Global Traders',
      activity: 'Pump inspection',
      region: 'Mwanza',
      sealNumber: 'WMA-2025-003',
      startingDate: 'May 10, 2025',
      finishDate: 'May 12, 2025',
      time: '09:15 AM'
    }
  ];

  userName: string = 'User';

  constructor(private authService: AuthService) {}

  ngOnInit(): void {
    // Subscribe to global user state
    this.authService.currentUser$.subscribe(data => {
        if (data && data.user) {
            this.userName = data.user.username;
        } else if (data && data.personalInfo) {
             this.userName = `${data.personalInfo.first_name} ${data.personalInfo.last_name}`;
        }
    });

    // Initial fetch if needed (AuthService might already have it)
    this.authService.getProfile().subscribe();
  }
}
