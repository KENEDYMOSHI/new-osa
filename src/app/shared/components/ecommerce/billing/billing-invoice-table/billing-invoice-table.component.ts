import { CommonModule } from '@angular/common';
import { Component, Input } from '@angular/core';
import { ButtonComponent } from '../../../ui/button/button.component';

export interface Invoice {
  id: string;
  billId: string;
  controlNumber: string;
  amount: string;
  paymentStatus: string;
  billDescription: string;
  date: string;
  licenseType: string;
  billType: number; // 1 = Application Fee, 2 = License Fee
}

@Component({
  selector: 'app-billing-invoice-table',
  imports: [
    CommonModule,
  ],
  templateUrl: './billing-invoice-table.component.html',
})
export class BillingInvoiceTableComponent {

  @Input() invoices: Invoice[] = [];

  currentPage: number = 1;
  itemsPerPage: number = 20;

  get totalInvoices(): number {
    return this.invoices.length;
  }

  get totalPages(): number {
    return Math.ceil(this.totalInvoices / this.itemsPerPage);
  }

  get startItem(): number {
    return (this.currentPage - 1) * this.itemsPerPage + 1;
  }

  get endItem(): number {
    return Math.min(this.currentPage * this.itemsPerPage, this.totalInvoices);
  }

  get paginatedInvoices(): Invoice[] {
    return this.invoices.slice(
      (this.currentPage - 1) * this.itemsPerPage,
      this.currentPage * this.itemsPerPage
    );
  }

  visiblePages(): number[] {
    const maxVisible: number = 5;
    let start: number = Math.max(1, this.currentPage - Math.floor(maxVisible / 2));
    let end: number = Math.min(this.totalPages, start + maxVisible - 1);
    if (end - start + 1 < maxVisible) {
      start = Math.max(1, end - maxVisible + 1);
    }
    return Array.from({ length: end - start + 1 }, (_, i) => start + i);
  }

  goToPage(page: number): void {
    if (page >= 1 && page <= this.totalPages) {
      this.currentPage = page;
    }
  }

  nextPage(): void {
    if (this.currentPage < this.totalPages) {
      this.currentPage++;
    }
  }

  previousPage(): void {
    if (this.currentPage > 1) {
      this.currentPage--;
    }
  }

  onDownloadAll(): void {
    console.log('Download All clicked');
  }

  onDownloadInvoice(id: string): void {
    console.log(`Download invoice ${id}`);
  }

  onViewInvoice(id: string): void {
    console.log(`View invoice ${id}`);
  }
}
