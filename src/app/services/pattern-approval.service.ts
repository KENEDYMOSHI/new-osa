import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class PatternApprovalService {
  private apiUrl = 'http://localhost:8080/api/pattern-approval';

  constructor(private http: HttpClient) {}

  private getHeaders(): HttpHeaders {
    const token = localStorage.getItem('token');
    return new HttpHeaders({
      'Content-Type': 'application/json',
      'Authorization': `Bearer ${token}`
    });
  }

  // Pattern Types
  getPatternTypes(): Observable<any> {
    return this.http.get(`${this.apiUrl}/pattern-types`, { 
      headers: this.getHeaders() 
    });
  }

  // Instrument Categories
  getInstrumentCategories(patternTypeId?: number): Observable<any> {
    let url = `${this.apiUrl}/instrument-categories`;
    if (patternTypeId) {
      url += `?pattern_type_id=${patternTypeId}`;
    }
    return this.http.get(url, { 
      headers: this.getHeaders() 
    });
  }

  // Instrument Types by Category
  getInstrumentTypesByCategory(categoryId: number): Observable<any> {
    return this.http.get(`${this.apiUrl}/instrument-types/${categoryId}`, { 
      headers: this.getHeaders() 
    });
  }

  // Applications
  createApplication(data: any): Observable<any> {
    return this.http.post(`${this.apiUrl}/applications`, data, { 
      headers: this.getHeaders() 
    });
  }

  getApplication(id: number): Observable<any> {
    return this.http.get(`${this.apiUrl}/applications/${id}`, { 
      headers: this.getHeaders() 
    });
  }

  getMyApplications(): Observable<any> {
    return this.http.get(`${this.apiUrl}/my-applications`, { 
      headers: this.getHeaders() 
    });
  }

  updateApplication(id: number, data: any): Observable<any> {
    return this.http.put(`${this.apiUrl}/applications/${id}`, data, { 
      headers: this.getHeaders() 
    });
  }

  // Instruments
  addInstrument(applicationId: number, instrumentTypeId: number): Observable<any> {
    return this.http.post(
      `${this.apiUrl}/applications/${applicationId}/instruments`, 
      { instrument_type_id: instrumentTypeId }, 
      { headers: this.getHeaders() }
    );
  }

  removeInstrument(applicationId: number, instrumentTypeId: number): Observable<any> {
    return this.http.delete(
      `${this.apiUrl}/applications/${applicationId}/instruments/${instrumentTypeId}`, 
      { headers: this.getHeaders() }
    );
  }
}
