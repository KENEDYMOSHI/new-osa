import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

export interface District {
  id: number;
  name: string;
}

export interface Ward {
  id: number;
  name: string;
}

export interface PostalCode {
  id: number;
  postcode: string;
}

@Injectable({
  providedIn: 'root'
})
export class LocationService {
  private apiUrl = 'http://localhost:8080/api';

  constructor(private http: HttpClient) {}

  /**
   * Get all regions
   */
  getRegions(): Observable<string[]> {
    return this.http.get<string[]>(`${this.apiUrl}/locations/regions`);
  }

  /**
   * Get districts for a specific region
   */
  getDistricts(region: string): Observable<District[]> {
    return this.http.get<District[]>(`${this.apiUrl}/locations/districts/${encodeURIComponent(region)}`);
  }

  /**
   * Get wards for a specific district
   */
  getWards(district: string): Observable<Ward[]> {
    return this.http.get<Ward[]>(`${this.apiUrl}/locations/wards/${encodeURIComponent(district)}`);
  }

  /**
   * Get postal codes for a specific ward
   */
  getPostalCodes(ward: string): Observable<PostalCode[]> {
    return this.http.get<PostalCode[]>(`${this.apiUrl}/locations/postalcodes/${encodeURIComponent(ward)}`);
  }
}
