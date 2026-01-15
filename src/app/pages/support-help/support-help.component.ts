import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { HttpClient } from '@angular/common/http';
import { RouterModule, RouterLink } from '@angular/router';

interface SupportDetails {
  address: string;
  phone_label_1?: string;
  phone_number_1?: string;
  phone_label_2?: string;
  phone_number_2?: string;
  phone_label_3?: string;
  phone_number_3?: string;
  email_general?: string;
  email_tech?: string;
  website?: string;
}

@Component({
  selector: 'app-support-help',
  standalone: true,
  imports: [CommonModule, RouterLink],
  templateUrl: './support-help.component.html',
})
export class SupportHelpComponent implements OnInit {
  supportDetails: SupportDetails | null = null;
  loading = true;
  error = '';

  constructor(private http: HttpClient) {}

  ngOnInit(): void {
    this.loadSupportDetails();
  }

  loadSupportDetails(): void {
    this.loading = true;
    this.http.get<SupportDetails>('http://localhost:8080/api/support-details')
      .subscribe({
        next: (details) => {
          this.supportDetails = details;
          this.loading = false;
        },
        error: (err) => {
          console.error('Error loading support details:', err);
          this.error = 'Failed to load support information. Please try again later.';
          this.loading = false;
        }
      });
  }
}
