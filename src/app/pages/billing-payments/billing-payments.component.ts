import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterLink } from '@angular/router';
import { FormsModule } from '@angular/forms';
import { BillingInvoiceTableComponent, Invoice } from '../../shared/components/ecommerce/billing/billing-invoice-table/billing-invoice-table.component';
import { LicenseService } from '../../services/license.service';

@Component({
  selector: 'app-billing-payments',
  standalone: true,
  imports: [CommonModule, RouterLink, FormsModule, BillingInvoiceTableComponent],
  templateUrl: './billing-payments.component.html',
})
export class BillingPaymentsComponent implements OnInit {
  invoices: Invoice[] = [];
  isLoading = false;

  // Filters
  controlNumber: string = '';
  selectedLicenseType: string = '';
  selectedFeeType: string = '';
  selectedPaymentStatus: string = '';
  fromDate: string = '';
  toDate: string = '';

  licenseTypes = [
    { id: 'classA', name: 'Class A' },
    { id: 'classB', name: 'Class B' },
    { id: 'classC', name: 'Class C' },
    { id: 'classD', name: 'Class D' },
    { id: 'classE', name: 'Class E' },
    { id: 'tankConst', name: 'Tank Construction' },
    { id: 'fixedTankVer', name: 'Fixed Storage Tanks Verification' },
    { id: 'tankCal', name: 'Tank Calibration' },
    { id: 'gasCal', name: 'Gas Measuring instrument license' },
    { id: 'marineSurvey', name: 'Marine Measurement Survey' },
  ];

  constructor(private licenseService: LicenseService) {}

  ngOnInit(): void {
    this.fetchBills();
  }

  fetchBills(): void {
    this.isLoading = true;
    
    const filters = {
      controlNumber: this.controlNumber,
      licenseType: this.selectedLicenseType,
      feeType: this.selectedFeeType,
      paymentStatus: this.selectedPaymentStatus,
      fromDate: this.fromDate,
      toDate: this.toDate
    };

    this.licenseService.getUserBills(filters).subscribe({
      next: (data) => {
        this.invoices = data.map((bill: any) => ({
          id: bill.id,
          billId: bill.billId,
          controlNumber: bill.controlNumber,
          amount: bill.amount,
          paymentStatus: bill.paymentStatus,
          billDescription: bill.billDescription,
          date: new Date(bill.date).toLocaleDateString(),
          licenseType: bill.licenseType,
          billType: bill.billType // 1 or 2
        }));
        this.isLoading = false;
      },
      error: (error) => {
        console.error('Error fetching bills:', error);
        this.isLoading = false;
      }
    });
  }

  resetFilters(): void {
    this.controlNumber = '';
    this.selectedLicenseType = '';
    this.selectedFeeType = '';
    this.selectedPaymentStatus = '';
    this.fromDate = '';
    this.toDate = '';
    this.fetchBills();
  }
}
