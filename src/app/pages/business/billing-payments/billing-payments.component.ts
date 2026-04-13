import { CommonModule, DecimalPipe } from '@angular/common';
import { Component } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { RouterModule } from '@angular/router';
import { TranslateModule } from '@ngx-translate/core';

interface BillItem {
  id: string;
  controlNumber: string;
  payerName: string;
  phoneNumber: string;
  billAmount: number;
  paidAmount: number;
  outstanding: number;
  generatedBy: string;
  dateGenerated: string;
  expiryDate: string;
  status: 'pending' | 'paid' | 'expired';
  descriptionKey: string;
}

@Component({
  selector: 'app-business-billing-payments',
  standalone: true,
  imports: [CommonModule, RouterModule, FormsModule, TranslateModule],
  providers: [DecimalPipe],
  template: `
  <div class="min-h-screen bg-gray-50 pb-16 dark:bg-gray-950">
    <div class="mx-auto max-w-7xl px-4 pt-6 sm:px-6 lg:px-8">
      
      <!-- HEADER -->
      <div class="mb-8 flex flex-col sm:flex-row sm:items-end justify-between gap-4">
        <div>
          <nav aria-label="Breadcrumb">
            <ol class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
              <li>
                <a routerLink="/business/dashboard" class="transition-colors hover:text-gray-900 dark:hover:text-white">
                  {{ 'BUSINESS.SIDEBAR.DASHBOARD' | translate }}
                </a>
              </li>
              <li aria-hidden="true" class="text-gray-300 dark:text-gray-600">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="m9 18l6 -6l-6 -6" />
                </svg>
              </li>
              <li class="font-medium text-gray-900 dark:text-white">{{ 'BUSINESS.BILLING_PAYMENTS.BREADCRUMB' | translate }}</li>
            </ol>
          </nav>
          <div class="mt-3">
            <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
              {{ 'BUSINESS.BILLING_PAYMENTS.PAGE_TITLE' | translate }}
            </h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
              {{ 'BUSINESS.BILLING_PAYMENTS.PAGE_DESC' | translate }}
            </p>
          </div>
        </div>
      </div>

      <!-- FILTERS & SEARCH -->
      <div class="mb-6 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex overflow-x-auto pb-2 sm:pb-0 scrollbar-hide gap-2 w-full sm:w-auto">
          <button (click)="filterBy('all')" [ngClass]="currentFilter === 'all' ? 'bg-gray-900 text-white dark:bg-white dark:text-gray-900' : 'bg-white text-gray-700 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700'" class="whitespace-nowrap rounded-lg px-4 py-2 text-sm font-semibold transition-colors border border-gray-200 dark:border-gray-700">
            {{ 'BUSINESS.BILLING_PAYMENTS.FILTER_ALL' | translate }}
          </button>
          <button (click)="filterBy('pending')" [ngClass]="currentFilter === 'pending' ? 'bg-[#F7941D] text-white border-transparent' : 'bg-white text-gray-700 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700'" class="whitespace-nowrap rounded-lg border border-gray-200 px-4 py-2 text-sm font-semibold transition-colors dark:border-gray-700">
            {{ 'BUSINESS.BILLING_PAYMENTS.FILTER_PENDING' | translate }}
          </button>
          <button (click)="filterBy('paid')" [ngClass]="currentFilter === 'paid' ? 'bg-emerald-600 text-white border-transparent' : 'bg-white text-gray-700 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700'" class="whitespace-nowrap rounded-lg border border-gray-200 px-4 py-2 text-sm font-semibold transition-colors dark:border-gray-700">
            {{ 'BUSINESS.BILLING_PAYMENTS.FILTER_PAID' | translate }}
          </button>
        </div>
        <div class="w-full sm:w-72 relative">
          <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-400"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
          </div>
          <input type="text" [(ngModel)]="searchQuery" [placeholder]="'BUSINESS.BILLING_PAYMENTS.SEARCH_PLACEHOLDER' | translate" class="block w-full rounded-lg border border-gray-300 bg-white py-2 pl-10 pr-3 text-sm placeholder-gray-500 focus:border-[#F7941D] focus:outline-none focus:ring-1 focus:ring-[#F7941D] dark:border-gray-700 dark:bg-gray-900 dark:text-white dark:placeholder-gray-400">
        </div>
      </div>

      <!-- STANDARD RESPONSIVE TABLE -->
      <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
        <div class="overflow-x-auto">
          <table class="w-full text-left text-sm text-gray-500 dark:text-gray-400 whitespace-nowrap border-collapse">
            <thead class="bg-[#1C2434] text-xs font-semibold text-white">
              <tr>
                <th scope="col" class="border border-dotted border-gray-600 px-5 py-4">{{ 'BUSINESS.BILLING_PAYMENTS.COL_DATE' | translate }}</th>
                <th scope="col" class="border border-dotted border-gray-600 px-5 py-4">{{ 'BUSINESS.BILLING_PAYMENTS.COL_CONTROL_NO' | translate }}</th>
                <th scope="col" class="border border-dotted border-gray-600 px-5 py-4">{{ 'BUSINESS.BILLING_PAYMENTS.LBL_EXPIRY' | translate }}</th>
                <th scope="col" class="border border-dotted border-gray-600 px-5 py-4 text-right">{{ 'BUSINESS.BILLING_PAYMENTS.COL_AMOUNT' | translate }} (TZS)</th>
                <th scope="col" class="border border-dotted border-gray-600 px-5 py-4 text-right">{{ 'BUSINESS.BILLING_PAYMENTS.LBL_PAID_AMOUNT' | translate }} (TZS)</th>
                <th scope="col" class="border border-dotted border-gray-600 px-5 py-4 text-right">{{ 'BUSINESS.BILLING_PAYMENTS.COL_OUTSTANDING' | translate }} (TZS)</th>
                <th scope="col" class="border border-dotted border-gray-600 px-5 py-4 text-center">{{ 'BUSINESS.BILLING_PAYMENTS.COL_STATUS' | translate }}</th>
                <th scope="col" class="border border-dotted border-gray-600 px-5 py-4 text-center">Action</th>
              </tr>
            </thead>
            <tbody>
              <ng-container *ngFor="let bill of filteredBills">
                <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/20 transition-colors">
                  <td class="border border-dotted border-gray-300 px-5 py-4 dark:border-gray-700">
                    <p class="font-medium text-gray-900 dark:text-white">{{ bill.dateGenerated }}</p>
                  </td>
                  
                  <td class="border border-dotted border-gray-300 px-5 py-4 dark:border-gray-700">
                    <div class="flex items-center gap-2">
                      <span class="font-mono font-bold text-gray-800 dark:text-gray-200">
                        {{ bill.controlNumber }}
                      </span>
                      <button (click)="copyToClipboard(bill.controlNumber, bill.id)" class="text-gray-400 transition-colors hover:text-[#F7941D]" [title]="copiedId === bill.id ? 'Copied!' : 'Copy to clipboard'">
                        <svg *ngIf="copiedId !== bill.id" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="8" height="4" x="8" y="2" rx="1" ry="1"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/></svg>
                        <svg *ngIf="copiedId === bill.id" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="text-emerald-500"><path d="M20 6 9 17l-5-5"/></svg>
                      </button>
                    </div>
                  </td>

                  <td class="border border-dotted border-gray-300 px-5 py-4 text-gray-700 dark:border-gray-700 dark:text-gray-300">
                    {{ bill.expiryDate }}
                  </td>

                  <td class="border border-dotted border-gray-300 px-5 py-4 text-right font-medium text-gray-900 dark:border-gray-700 dark:text-white">
                    {{ bill.billAmount | number }}
                  </td>

                  <td class="border border-dotted border-gray-300 px-5 py-4 text-right font-medium text-gray-900 dark:border-gray-700 dark:text-white">
                    {{ bill.paidAmount | number }}
                  </td>

                  <td class="border border-dotted border-gray-300 px-5 py-4 text-right font-bold text-gray-900 dark:border-gray-700 dark:text-white" [ngClass]="{'text-red-500 font-black': bill.outstanding > 0}">
                    {{ bill.outstanding | number }}
                  </td>

                  <td class="border border-dotted border-gray-300 px-5 py-4 text-center dark:border-gray-700">
                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold capitalize" [ngClass]="getStatusBadge(bill.status)">
                      {{ bill.status }}
                    </span>
                  </td>
                  
                  <td class="border border-dotted border-gray-300 px-5 py-4 text-center dark:border-gray-700">
                    <button *ngIf="bill.status === 'pending'" class="inline-flex items-center justify-center gap-1.5 rounded-md bg-[#F7941D] px-3 py-1.5 text-xs font-semibold text-white shadow-sm hover:bg-[#E6820A] transition-colors">
                      <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/></svg>
                      Pay
                    </button>
                    <button *ngIf="bill.status === 'paid'" class="inline-flex items-center justify-center gap-1.5 rounded-md border border-gray-200 bg-white px-3 py-1.5 text-xs font-semibold text-gray-700 hover:bg-gray-50 transition-colors dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">
                      <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/></svg>
                      Receipt
                    </button>
                  </td>
                </tr>
              </ng-container>

              <tr *ngIf="filteredBills.length === 0">
                 <td colspan="9" class="px-6 py-12 text-center text-sm text-gray-500">
                    <div class="flex flex-col items-center justify-center">
                      <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="text-gray-300 mb-3"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/></svg>
                      No bills found matching your criteria.
                    </div>
                 </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </div>
  `
})
export class BillingPaymentsComponent {
  currentFilter: 'all' | 'pending' | 'paid' = 'all';
  searchQuery: string = '';
  copiedId: string | null = null;

  bills: BillItem[] = [
    {
      id: '1',
      controlNumber: '994192014435',
      payerName: 'SIBED TRANSPORT COMPANY TANZANIA LIMITED',
      phoneNumber: '0755236488',
      billAmount: 750000,
      paidAmount: 0,
      outstanding: 750000,
      generatedBy: 'Gaudence Gaspary',
      dateGenerated: '13 Apr 2026',
      expiryDate: '01 May 2026',
      status: 'pending',
      descriptionKey: 'BUSINESS.BILLING_PAYMENTS.DESC_VERIFICATION'
    },
    {
      id: '2',
      controlNumber: '994192021100',
      payerName: 'AZAM BAKERIES LIMITED',
      phoneNumber: '0688223344',
      billAmount: 500000,
      paidAmount: 500000,
      outstanding: 0,
      generatedBy: 'System Auto',
      dateGenerated: '10 Apr 2026',
      expiryDate: '24 Apr 2026',
      status: 'paid',
      descriptionKey: 'BUSINESS.BILLING_PAYMENTS.DESC_LICENSE'
    },
    {
      id: '3',
      controlNumber: '994192039988',
      payerName: 'KILIMO KWANZA AGRO DEALERS',
      phoneNumber: '0711889900',
      billAmount: 120000,
      paidAmount: 0,
      outstanding: 120000,
      generatedBy: 'Amina Juma',
      dateGenerated: '05 Apr 2026',
      expiryDate: '19 Apr 2026',
      status: 'pending',
      descriptionKey: 'BUSINESS.BILLING_PAYMENTS.DESC_INSPECTION'
    },
    {
      id: '4',
      controlNumber: '994192044455',
      payerName: 'CITY MALL SUPERMARKET',
      phoneNumber: '0755112233',
      billAmount: 1500000,
      paidAmount: 1500000,
      outstanding: 0,
      generatedBy: 'Gaudence Gaspary',
      dateGenerated: '25 Mar 2026',
      expiryDate: '08 Apr 2026',
      status: 'paid',
      descriptionKey: 'BUSINESS.BILLING_PAYMENTS.DESC_VERIFICATION'
    },
    {
      id: '5',
      controlNumber: '994192055667',
      payerName: 'SIBED TRANSPORT COMPANY TANZANIA LIMITED',
      phoneNumber: '0755236488',
      billAmount: 50000,
      paidAmount: 50000,
      outstanding: 0,
      generatedBy: 'System Auto',
      dateGenerated: '01 Mar 2026',
      expiryDate: '15 Mar 2026',
      status: 'paid',
      descriptionKey: 'BUSINESS.BILLING_PAYMENTS.DESC_PENALTY'
    }
  ];

  get filteredBills() {
    let filtered = this.bills;
    
    // Apply status filter
    if (this.currentFilter !== 'all') {
      filtered = filtered.filter(b => b.status === this.currentFilter);
    }
    
    // Apply search filter
    if (this.searchQuery && this.searchQuery.trim() !== '') {
      const q = this.searchQuery.toLowerCase().trim();
      filtered = filtered.filter(b => 
        b.controlNumber.includes(q) || 
        b.payerName.toLowerCase().includes(q)
      );
    }
    
    return filtered;
  }

  filterBy(filter: 'all' | 'pending' | 'paid') {
    this.currentFilter = filter;
  }

  async copyToClipboard(text: string, id: string) {
    try {
      await navigator.clipboard.writeText(text);
      this.copiedId = id;
      setTimeout(() => this.copiedId = null, 2000);
    } catch (err) {
      console.error('Failed to copy!', err);
    }
  }

  getStatusBadge(status: string) {
    if (status === 'paid') return 'bg-emerald-100 text-emerald-800 dark:bg-emerald-500/10 dark:text-emerald-400';
    if (status === 'expired') return 'bg-red-100 text-red-800 dark:bg-red-500/10 dark:text-red-400';
    return 'bg-orange-100 text-orange-800 dark:bg-[#F7941D]/10 dark:text-[#F7941D]';
  }

  getStatusDot(status: string) {
    if (status === 'paid') return 'bg-emerald-500';
    if (status === 'expired') return 'bg-red-500';
    return 'bg-[#F7941D]';
  }

  getStatusIcon(status: string) {
    if (status === 'paid') return 'ti ti-check text-emerald-600 dark:text-emerald-400';
    if (status === 'expired') return 'ti ti-x text-red-500';
    return 'ti ti-minus text-[#F7941D]';
  }

  getStatusBgShape(status: string) {
    if (status === 'paid') return 'bg-emerald-50 dark:bg-emerald-500/10';
    if (status === 'expired') return 'bg-red-50 dark:bg-red-500/10';
    return 'bg-orange-50 dark:bg-[#F7941D]/10';
  }

  getStatusText(status: string) {
    if (status === 'paid') return 'BUSINESS.BILLING_PAYMENTS.STATUS_PAID';
    if (status === 'expired') return 'BUSINESS.BILLING_PAYMENTS.STATUS_EXPIRED';
    return 'BUSINESS.BILLING_PAYMENTS.STATUS_PENDING';
  }
}
