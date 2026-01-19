import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { AuthService } from '../../../core/services/auth.service';
import { PatternSelectionComponent } from '../pattern-selection/pattern-selection.component';

@Component({
  selector: 'app-pattern-approval-application',
  standalone: true,
  imports: [CommonModule, PatternSelectionComponent],
  templateUrl: './pattern-approval-application.component.html',
})
export class PatternApprovalApplicationComponent implements OnInit {
  user: any = null;
  personalInfo: any = null;
  businessInfo: any = null;
  isLoading = true;

  constructor(private authService: AuthService) {}

  ngOnInit() {
    this.loadUserProfile();
  }

  loadUserProfile() {
    this.isLoading = true;
    this.authService.getProfile().subscribe({
      next: (response: any) => {
        this.user = response.user;
        this.personalInfo = response.personalInfo;
        this.businessInfo = response.businessInfo;
        this.isLoading = false;
      },
      error: (error) => {
        console.error('Error loading profile:', error);
        this.isLoading = false;
      }
    });
  }
}
