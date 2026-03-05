import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { FormsModule } from '@angular/forms';
import { DateRangePickerComponent, DateRange } from '../../../shared/components/date-range-picker/date-range-picker.component';

type PaymentStatus = 'Paid' | 'Pending' | 'Overdue' | 'Waived';

interface Invoice {
  patternType: string;
  category: string;
  feeType: string;
  status: PaymentStatus;
  date: string;
  controlNumber: string;
  amount: number;
}

@Component({
  selector: 'app-billing-payments',
  standalone: true,
  imports: [CommonModule, RouterModule, FormsModule, DateRangePickerComponent],
  templateUrl: './billing-payments.component.html',
  styleUrl: './billing-payments.component.css',
})
export class BillingPaymentsComponent {
  selectedStatus = '';
  selectedPatternType = '';
  searchControlNumber = '';
  dateMode: '' | 'date' | 'range' = '';
  filterDate = '';
  rangeFrom: Date | null = null;
  rangeTo: Date | null = null;

  setDateMode(mode: '' | 'date' | 'range') {
    this.dateMode = mode;
    if (mode !== 'date') this.filterDate = '';
    if (mode !== 'range') { this.rangeFrom = null; this.rangeTo = null; }
  }

  invoices: Invoice[] = [
    {
      patternType: 'Weighing Instrument',
      category: 'Counter Scale',
      feeType: 'Application Fee',
      status: 'Paid',
      date: 'Feb 26, 2024',
      controlNumber: '994191989745',
      amount: 150000,
    },
    {
      patternType: 'Fuel Pump',
      category: 'Standard Fuel Pump',
      feeType: 'Application Fee',
      status: 'Paid',
      date: 'Jan 10, 2025',
      controlNumber: '994191989812',
      amount: 200000,
    },
    {
      patternType: 'Meter Instrument',
      category: 'Water Meter',
      feeType: 'Evaluation Fee',
      status: 'Pending',
      date: 'Mar 01, 2025',
      controlNumber: '994191989934',
      amount: 175000,
    },
    {
      patternType: 'Capacity Measures',
      category: 'Liquid Measure',
      feeType: 'Application Fee',
      status: 'Overdue',
      date: 'Nov 15, 2024',
      controlNumber: '994191989601',
      amount: 120000,
    },
  ];

  get patternTypes(): string[] {
    return [...new Set(this.invoices.map(i => i.patternType))];
  }

  get filteredInvoices(): Invoice[] {
    return this.invoices.filter((inv) => {
      const matchStatus = this.selectedStatus ? inv.status === this.selectedStatus : true;
      const matchType = this.selectedPatternType ? inv.patternType === this.selectedPatternType : true;
      const matchControl = this.searchControlNumber
        ? inv.controlNumber.toLowerCase().includes(this.searchControlNumber.toLowerCase())
        : true;
      const matchDate = this.filterDate ? inv.date === this.filterDate : true;
      let matchRange = true;
      if (this.rangeFrom || this.rangeTo) {
        const invDate = new Date(inv.date);
        if (this.rangeFrom) matchRange = matchRange && invDate >= this.rangeFrom;
        if (this.rangeTo)   matchRange = matchRange && invDate <= this.rangeTo;
      }
      return matchStatus && matchType && matchControl && matchDate && matchRange;
    });
  }

  onRangeSelected(range: DateRange) {
    this.rangeFrom = range.from;
    this.rangeTo = range.to;
  }

  clearFilters() {
    this.selectedStatus = '';
    this.selectedPatternType = '';
    this.searchControlNumber = '';
    this.dateMode = '';
    this.filterDate = '';
    this.rangeFrom = null;
    this.rangeTo = null;
  }

  badgeClass(status: PaymentStatus): string {
    switch (status) {
      case 'Paid':    return 'inline-flex rounded-full bg-green-50 border border-green-200 py-0.5 px-3 text-xs font-bold text-green-700';
      case 'Pending': return 'inline-flex rounded-full bg-yellow-50 border border-yellow-200 py-0.5 px-3 text-xs font-bold text-yellow-700';
      case 'Overdue': return 'inline-flex rounded-full bg-red-50 border border-red-200 py-0.5 px-3 text-xs font-bold text-red-600';
      case 'Waived':  return 'inline-flex rounded-full bg-gray-100 border border-gray-200 py-0.5 px-3 text-xs font-bold text-gray-500';
      default:        return '';
    }
  }
}
