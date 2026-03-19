import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { AuthService } from '../../../core/services/auth.service';

@Component({
  selector: 'app-business-profile',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './business-profile.component.html',
})
export class BusinessProfileComponent implements OnInit {
  user: any = null;
  businessInfo: any = null;
  contactInfo: any = null;
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
        this.businessInfo = response.businessOwnerInfo;
        this.contactInfo = response.businessContactInfo;
        this.isLoading = false;
      },
      error: (error) => {
        console.error('Error loading profile:', error);
        this.isLoading = false;
      }
    });
  }
}
