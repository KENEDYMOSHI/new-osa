import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { HttpClient } from '@angular/common/http';

interface PatternItem {
  brand: string;
  model: string;
  serialNumber: string;
  patternType: string;
  category: string;
  manufacturer: string;
  countryOfOrigin: string;
  sealingMethod: string;
  certificateNumber: string;
  issuedDate: string;
  status: string;
  controlNumber: string;
  amount: number;
  paymentStatus: string;
}

@Component({
  selector: 'app-pattern-details',
  standalone: true,
  imports: [CommonModule, RouterModule],
  templateUrl: './pattern-details.component.html',
  styleUrl: './pattern-details.component.css'
})
export class PatternDetailsComponent implements OnInit {

  viewDetails = false;
  selectedPattern: PatternItem | null = null;

  // Simulated patterns list — replace with API data
  patterns: PatternItem[] = [
    {
      brand: 'OHAUS',
      model: 'CS200',
      serialNumber: 'OH-2024-00781',
      patternType: 'Weighing Instrument',
      category: 'Counter Scale',
      manufacturer: 'OHAUS Corporation',
      countryOfOrigin: 'United States',
      sealingMethod: 'Lead Seal',
      certificateNumber: 'PA/2024/WI/001',
      issuedDate: 'Feb 26, 2026',
      status: 'Approved',
      controlNumber: 'CTL/2024/WI/001',
      amount: 150000,
      paymentStatus: 'Paid',
    },
    {
      brand: 'Gilbarco',
      model: 'Encore 700',
      serialNumber: 'GIL-2025-00043',
      patternType: 'Fuel Pump',
      category: 'Standard Fuel Pump',
      manufacturer: 'Gilbarco Veeder-Root',
      countryOfOrigin: 'Germany',
      sealingMethod: 'Electronic Seal',
      certificateNumber: 'PA/2025/FP/002',
      issuedDate: 'Jan 10, 2026',
      status: 'Approved',
      controlNumber: 'CTL/2025/FP/002',
      amount: 200000,
      paymentStatus: 'Paid',
    },
  ];

  technicalSpecs = [
    { label: 'Maximum Capacity', value: '200 kg' },
    { label: 'Minimum Capacity', value: '0.5 kg' },
    { label: 'Scale Interval (d)', value: '0.05 kg' },
    { label: 'Verification Scale Interval (e)', value: '0.05 kg' },
    { label: 'Operating Temperature', value: '-10°C to +40°C' },
    { label: 'Power Supply', value: 'AC 220V / DC Battery' },
    { label: 'Display', value: 'LCD Digital Display' },
    { label: 'Accuracy Class', value: 'OIML Class III' },
  ];

  documents = [
    { name: 'Pattern Approval Certificate', file: 'PA_Certificate_OH-2024-00781.pdf', status: 'Issued' },
    { name: 'Evaluation Report', file: 'Evaluation_Report_OH-2024-00781.pdf', status: 'Issued' },
    { name: 'Technical Specifications', file: 'Tech_Specs_OHAUS_CS200.pdf', status: 'Submitted' },
    { name: 'Operation Manual', file: 'Operation_Manual_CS200.pdf', status: 'Submitted' },
  ];

  constructor(private http: HttpClient) {}

  ngOnInit(): void {
    // TODO: Load real patterns from API
    // this.http.get('/api/pattern-approval/details').subscribe(...)
  }

  openDetails(pattern: PatternItem): void {
    this.selectedPattern = pattern;
    this.viewDetails = true;
  }
}
