import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { TranslateModule } from '@ngx-translate/core';

@Component({
  selector: 'app-business-certificates',
  standalone: true,
  imports: [CommonModule, RouterModule, TranslateModule],
  templateUrl: './business-certificates.component.html',
})
export class BusinessCertificatesComponent {
  activeTab: 'certificates' | 'stickers' = 'certificates';

  certificates = [
    {
      id: 'CERT-2026-001',
      instrument: 'Weighbridge (60,000kg)',
      issueDate: '2026-01-15',
      expiryDate: '2027-01-14',
      status: 'Active',
      type: 'Verification Certificate'
    },
    {
      id: 'CERT-2026-002',
      instrument: 'Platform Scale (500kg)',
      issueDate: '2026-02-10',
      expiryDate: '2027-02-09',
      status: 'Active',
      type: 'Verification Certificate'
    },
    {
      id: 'CERT-2025-089',
      instrument: 'Fuel Dispenser (Double Nozzle)',
      issueDate: '2025-06-20',
      expiryDate: '2026-06-19',
      status: 'Expiring Soon',
      type: 'Verification Certificate'
    }
  ];

  stickers = [
    {
      instrument: 'PS : 250kg',
      verificationDate: '31-03-2026',
      nextVerification: '31-03-2027',
      stickerNumber: 'PS:ILA-1096-2026'
    },
    {
      instrument: 'Weighbridge : 60t',
      verificationDate: '15-01-2026',
      nextVerification: '15-01-2027',
      stickerNumber: 'WB:ILA-2045-2026'
    },
    {
      instrument: 'Fuel Dispenser : Petrol',
      verificationDate: '20-06-2025',
      nextVerification: '20-06-2026',
      stickerNumber: 'FD:ILA-8832-2025'
    },
    {
      instrument: 'Counter Scale : 15kg',
      verificationDate: '05-04-2026',
      nextVerification: '05-04-2027',
      stickerNumber: 'CS:ILA-1120-2026'
    },
    {
      instrument: 'Platform Scale : 1000kg',
      verificationDate: '10-02-2026',
      nextVerification: '10-02-2027',
      stickerNumber: 'PS:ILA-0542-2026'
    }
  ];

  setTab(tab: 'certificates' | 'stickers') {
    this.activeTab = tab;
  }
}
