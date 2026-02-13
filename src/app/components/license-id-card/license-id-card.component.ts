import { Component, Input } from '@angular/core';
import { CommonModule } from '@angular/common';

interface LicenseCardData {
  licenseNumber: string;
  applicantName: string;
  licenseType: string;
  issueDate: string;
  expiryDate: string;
  profilePicture?: string;
  userInitial?: string;
  companyName?: string;
  address?: string;
  phoneNumber?: string;
  region?: string;
  instruments?: any[];
  position?: string; // e.g., "CALIBRATION INSPECTOR"
}

@Component({
  selector: 'app-license-id-card',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './license-id-card.component.html',
  styleUrls: ['./license-id-card.component.css']
})
export class LicenseIdCardComponent {
  @Input() licenseData?: LicenseCardData;
  @Input() showBack: boolean = false;

  get qrCodeData(): string {
    return `https://wma.go.tz/verify/${this.licenseData?.licenseNumber}`;
  }

  toggleCard() {
    this.showBack = !this.showBack;
  }

  printCard() {
    window.print();
  }
}
