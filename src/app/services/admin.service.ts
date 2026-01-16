import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable } from 'rxjs';
import { AuthService } from '../core/services/auth.service';

@Injectable({
  providedIn: 'root'
})
export class AdminService {
  private apiUrl = 'http://localhost:8080/api/admin';

  constructor(private http: HttpClient, private authService: AuthService) { }

  private getHeaders(): HttpHeaders {
    const token = this.authService.getToken();
    return new HttpHeaders({
      'Authorization': `Bearer ${token}`
    });
  }

  getApplications(): Observable<any[]> {
    return this.http.get<any[]>(`${this.apiUrl}/applications`, { headers: this.getHeaders() });
  }

  getApplicants(): Observable<any[]> {
    return this.http.get<any[]>(`${this.apiUrl}/applicants`, { headers: this.getHeaders() });
  }

  getApplicationDetails(id: string): Observable<any> {
    return this.http.get<any>(`${this.apiUrl}/application/${id}`, { headers: this.getHeaders() });
  }

  getDocumentUrl(id: string): string {
    return `${this.apiUrl}/document/${id}/view`;
  }

  approveApplication(id: string): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/application/${id}/approve`, {}, { headers: this.getHeaders() });
  }

  acceptDocument(documentId: string): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/document/accept`, 
      { doc_id: documentId }, 
      { headers: this.getHeaders() }
    );
  }

  returnDocument(documentId: string, rejectionReason: string): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/document/return`, 
      { document_id: documentId, rejection_reason: rejectionReason }, 
      { headers: this.getHeaders() }
    );
  }
}
