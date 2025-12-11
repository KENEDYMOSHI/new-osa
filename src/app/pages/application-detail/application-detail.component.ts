import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ActivatedRoute, Router } from '@angular/router';
import { AdminService } from '../../services/admin.service';
import { DomSanitizer, SafeResourceUrl } from '@angular/platform-browser';

@Component({
  selector: 'app-application-detail',
  imports: [CommonModule],
  templateUrl: './application-detail.component.html',
  styleUrl: './application-detail.component.css'
})
export class ApplicationDetailComponent implements OnInit {
  applicationId: string = '';
  applicationDetails: any = null;
  loading: boolean = true;
  previewUrl: SafeResourceUrl | null = null;
  previewLoading: boolean = false;

  constructor(
    private route: ActivatedRoute,
    private router: Router,
    private adminService: AdminService,
    private sanitizer: DomSanitizer
  ) {}

  ngOnInit(): void {
    this.applicationId = this.route.snapshot.paramMap.get('id') || '';
    if (this.applicationId) {
      this.fetchApplicationDetails();
    }
  }

  fetchApplicationDetails() {
    this.loading = true;
    this.adminService.getApplicationDetails(this.applicationId).subscribe({
      next: (data) => {
        this.applicationDetails = data;
        this.loading = false;
      },
      error: (err) => {
        console.error('Failed to fetch application details', err);
        this.loading = false;
      }
    });
  }

  goBack() {
    this.router.navigate(['/admin-test']);
  }

  parseJson(jsonString: any): any {
    if (typeof jsonString === 'string') {
      try {
        return JSON.parse(jsonString);
      } catch (e) {
        return [];
      }
    }
    return jsonString;
  }

  openDocument(id: string) {
    this.previewLoading = true;
    const token = localStorage.getItem('token');
    fetch(this.adminService.getDocumentUrl(id), {
      headers: {
        'Authorization': `Bearer ${token}`
      }
    })
    .then(response => response.blob())
    .then(blob => {
      const objectUrl = window.URL.createObjectURL(blob);
      this.previewUrl = this.sanitizer.bypassSecurityTrustResourceUrl(objectUrl);
      this.previewLoading = false;
    })
    .catch(err => {
      console.error('Error downloading document', err);
      this.previewLoading = false;
    });
  }
}
