import { Component } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { CommonModule } from '@angular/common';
import { ButtonComponent } from '../../../ui/button/button.component';
import { ModalComponent } from '../../../ui/modal/modal.component';

@Component({
  selector: 'app-invoice-preview-modal',
  imports: [
    ButtonComponent,
    ModalComponent,
    CommonModule
  ],
  templateUrl: './invoice-preview-modal.component.html',
  styles: ``
})
export class InvoicePreviewModalComponent {
  isOpen = false;
  billData: any = null;
  isLoading = false;

  constructor(private http: HttpClient) {}

  openModal(billId: string) {
    this.isOpen = true;
    this.fetchBillDetails(billId);
  }

  closeModal() {
    this.isOpen = false;
    this.billData = null;
  }

  fetchBillDetails(billId: string) {
    this.isLoading = true;
    this.http.get<any>(`http://localhost:8080/api/license/bill/${billId}`).subscribe({
      next: (data) => {
        this.billData = data;
        this.isLoading = false;
      },
      error: (error) => {
        console.error('Error fetching bill details:', error);
        this.isLoading = false;
      }
    });
  }
}
