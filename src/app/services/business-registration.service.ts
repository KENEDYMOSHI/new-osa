import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable } from 'rxjs';
import { AuthService } from '../core/services/auth.service';
import { API_CONFIG } from '../core/config/api.config';

@Injectable({
  providedIn: 'root'
})
export class BusinessRegistrationService {
  private apiUrl = `${API_CONFIG.baseUrl}/business`;

  constructor(private http: HttpClient, private authService: AuthService) {}

  getProfile(): Observable<any> {
    return this.http.get(`${this.apiUrl}/profile`, { headers: this.getHeaders() });
  }

  updateBusinessInfo(data: any): Observable<any> {
    return this.http.post(`${this.apiUrl}/update-info`, data, { headers: this.getHeaders() });
  }

  uploadLogo(formData: FormData): Observable<any> {
    return this.http.post(`${this.apiUrl}/upload-logo`, formData, { headers: this.getHeaders() });
  }

  removeLogo(): Observable<any> {
    return this.http.post(`${this.apiUrl}/remove-logo`, {}, { headers: this.getHeaders() });
  }

  private getHeaders(): HttpHeaders {
    const token = this.authService.getToken();
    return new HttpHeaders({ 'Authorization': `Bearer ${token}` });
  }
}
