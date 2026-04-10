import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable } from 'rxjs';
import { API_CONFIG } from '../config/api.config';
import { AuthService } from './auth.service';

export interface EquipmentRegistrationPayload {
  serviceTypeKey: string;
  serviceTypeLabel: string;
  category: string;
  items: any[];
}

@Injectable({
  providedIn: 'root'
})
export class EquipmentService {
  private apiUrl = `${API_CONFIG.baseUrl}/business/equipments`;

  constructor(private http: HttpClient, private authService: AuthService) { }

  private getHeaders(): HttpHeaders {
    return this.authService.getHeaders();
  }

  registerEquipment(payload: EquipmentRegistrationPayload, files: any[]): Observable<any> {
    const formData = new FormData();
    formData.append('serviceTypeKey', payload.serviceTypeKey);
    formData.append('serviceTypeLabel', payload.serviceTypeLabel);
    formData.append('category', payload.category);
    
    // We stringify the items so the backend can decode them
    formData.append('items', JSON.stringify(payload.items));

    // Append files
    // files format: [{ itemIndex: 0, fieldKey: 'inspectionChart', file: FileBlob }, ...]
    files.forEach(f => {
      formData.append(`files[${f.itemIndex}][${f.fieldKey}]`, f.file);
    });

    return this.http.post(this.apiUrl, formData, { headers: this.getHeaders() });
  }

  getEquipments(): Observable<any> {
    return this.http.get(this.apiUrl, { headers: this.getHeaders() });
  }

  getEquipmentById(id: number): Observable<any> {
    return this.http.get(`${this.apiUrl}/${id}`, { headers: this.getHeaders() });
  }

  updateEquipment(id: number, payload: EquipmentRegistrationPayload, files: any[]): Observable<any> {
    const formData = new FormData();
    formData.append('_method', 'PUT');
    formData.append('serviceTypeKey', payload.serviceTypeKey);
    formData.append('serviceTypeLabel', payload.serviceTypeLabel);
    formData.append('category', payload.category);
    formData.append('items', JSON.stringify(payload.items));

    files.forEach(f => {
      formData.append(`files[${f.itemIndex}][${f.fieldKey}]`, f.file);
    });

    return this.http.post(`${this.apiUrl}/${id}`, formData, { headers: this.getHeaders() });
  }

  deleteEquipment(id: number): Observable<any> {
    return this.http.delete(`${this.apiUrl}/${id}`, { headers: this.getHeaders() });
  }
}
