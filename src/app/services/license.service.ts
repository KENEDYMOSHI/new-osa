import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';


@Injectable({
  providedIn: 'root'
})
export class LicenseService {
  private apiUrl = 'http://localhost:8080/api/license'; // Hardcoded for now based on context

  constructor(private http: HttpClient) { }

  uploadDocument(file: File, documentType: string, applicationId?: string, category?: string): Observable<any> {
    const formData = new FormData();
    formData.append('file', file);
    formData.append('documentType', documentType);
    if (applicationId) {
      formData.append('applicationId', applicationId);
    }
    if (category) {
      formData.append('category', category);
    }
    return this.http.post(`${this.apiUrl}/upload`, formData, { headers: this.getHeaders() });
  }

  getUserDocuments(): Observable<any> {
    return this.http.get(`${this.apiUrl}/documents`, { headers: this.getHeaders() });
  }

  deleteDocument(id: string): Observable<any> {
    return this.http.delete(`${this.apiUrl}/document/${id}`, { headers: this.getHeaders() });
  }

  submitDocument(id: string): Observable<any> {
    return this.http.post(`${this.apiUrl}/document/${id}/submit`, {}, { headers: this.getHeaders() });
  }

  viewDocument(id: string): Observable<Blob> {
    return this.http.get(`${this.apiUrl}/document/${id}/view`, { 
      headers: this.getHeaders(),
      responseType: 'blob' 
    });
  }

  submitApplication(data: any): Observable<any> {
    return this.http.post(`${this.apiUrl}/submit`, data, { headers: this.getHeaders() });
  }

  getUserBills(filters: any = {}): Observable<any> {
    let params = new HttpParams();
    
    if (filters.controlNumber) params = params.set('controlNumber', filters.controlNumber);
    if (filters.licenseType) params = params.set('licenseType', filters.licenseType);
    if (filters.feeType) params = params.set('feeType', filters.feeType);
    if (filters.paymentStatus) params = params.set('paymentStatus', filters.paymentStatus);
    if (filters.fromDate) params = params.set('fromDate', filters.fromDate);
    if (filters.toDate) params = params.set('toDate', filters.toDate);

    return this.http.get(`${this.apiUrl}/user-bills`, { headers: this.getHeaders(), params });
  }

  getUserApplications(): Observable<any> {
    return this.http.get(`${this.apiUrl}/user-applications`, { headers: this.getHeaders() });
  }

  getUserNotifications(): Observable<any> {
    // Note: backend route is api/notifications, not api/license/notifications
    // Adjusting base URL or using absolute path if needed.
    // Assuming backend routes group 'api' contains 'notifications' directly, not under 'license'.
    // However, LicenseService uses this.apiUrl = '.../api/license'.
    // Let's assume we need to go up one level or just hardcode for now to be safe, 
    // OR create a new Service. But adding here is easiest.
    const baseUrl = this.apiUrl.replace('/license', ''); 
    return this.http.get(`${baseUrl}/notifications`, { headers: this.getHeaders() });
  }

  markNotificationAsRead(id: string): Observable<any> {
    const baseUrl = this.apiUrl.replace('/license', '');
    return this.http.post(`${baseUrl}/notifications/${id}/read`, {}, { headers: this.getHeaders() });
  }

  // License Type Management
  getLicenseTypes(): Observable<any> {
    return this.http.get(`${this.apiUrl}/types`, { headers: this.getHeaders() });
  }

  createLicenseType(data: any): Observable<any> {
    const baseUrl = this.apiUrl.replace('/license', '');
    return this.http.post(`${baseUrl}/admin/license-types`, data, { headers: this.getHeaders() });
  }

  updateLicenseType(id: string, data: any): Observable<any> {
    const baseUrl = this.apiUrl.replace('/license', '');
    return this.http.put(`${baseUrl}/admin/license-types/${id}`, data, { headers: this.getHeaders() });
  }

  deleteLicenseType(id: string): Observable<any> {
    const baseUrl = this.apiUrl.replace('/license', '');
    return this.http.delete(`${baseUrl}/admin/license-types/${id}`, { headers: this.getHeaders() });
  }

  getApplicationFees(): Observable<any> {
    return this.http.get(`${this.apiUrl}/fees`, { headers: this.getHeaders() });
  }

  checkEligibility(): Observable<any> {
    return this.http.get(`${this.apiUrl}/eligibility`, { headers: this.getHeaders() });
  }

  getEligibleApplications(): Observable<any> {
    return this.http.get(`${this.apiUrl}/eligible-applications`, { headers: this.getHeaders() });
  }

  // License Fee and Payment Management
  generateLicenseFee(applicationId: string): Observable<any> {
    return this.http.post(`${this.apiUrl}/generate-fee/${applicationId}`, {}, { headers: this.getHeaders() });
  }

  checkPaymentStatus(applicationId: string): Observable<any> {
    return this.http.get(`${this.apiUrl}/payment-status/${applicationId}`, { headers: this.getHeaders() });
  }

  viewLicense(applicationId: string): Observable<any> {
    return this.http.get(`${this.apiUrl}/view/${applicationId}`, { headers: this.getHeaders() });
  }

  getApplicationDetails(applicationId: string): Observable<any> {
    return this.http.get(`${this.apiUrl}/details/${applicationId}`, { headers: this.getHeaders() });
  }

  getApprovedLicenses(): Observable<any> {
    return this.http.get(`${this.apiUrl}/approved-licenses`, { headers: this.getHeaders() });
  }

  private getHeaders(): HttpHeaders {
    const token = localStorage.getItem('token');
    return new HttpHeaders({
      'Authorization': `Bearer ${token}`
    });
  }
}
