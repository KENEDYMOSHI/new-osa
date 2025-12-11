import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';


@Injectable({
  providedIn: 'root'
})
export class LicenseService {
  private apiUrl = 'http://localhost:8080/api/license'; // Hardcoded for now based on context

  constructor(private http: HttpClient) { }

  uploadDocument(file: File, documentType: string, applicationId?: string): Observable<any> {
    const formData = new FormData();
    formData.append('file', file);
    formData.append('documentType', documentType);
    if (applicationId) {
      formData.append('applicationId', applicationId);
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

  private getHeaders(): HttpHeaders {
    const token = localStorage.getItem('token');
    return new HttpHeaders({
      'Authorization': `Bearer ${token}`
    });
  }
}
