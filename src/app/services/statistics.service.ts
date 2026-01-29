import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

export interface LicenseStatistic {
  license_class: string;
  applicants: number;
  popularity: number;
}

export interface RegionStatistic {
  region: string;
  count: number;
  performance: number;
}

@Injectable({
  providedIn: 'root'
})
export class StatisticsService {
  private apiUrl = 'http://localhost:8080/api/statistics'; // Adjust base URL if needed

  constructor(private http: HttpClient) { }

  getLicenseStatistics(): Observable<LicenseStatistic[]> {
    return this.http.get<LicenseStatistic[]>(`${this.apiUrl}/licenses`);
  }

  getRegionStatistics(): Observable<RegionStatistic[]> {
    return this.http.get<RegionStatistic[]>(`${this.apiUrl}/regions`);
  }
}
