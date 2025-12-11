import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable, BehaviorSubject } from 'rxjs';
import { tap } from 'rxjs/operators';
import { API_CONFIG } from '../config/api.config';

@Injectable({
  providedIn: 'root'
})
export class AuthService {
  private apiUrl = `${API_CONFIG.baseUrl}/auth`;
  private currentUserSubject = new BehaviorSubject<any>(null);
  public currentUser$ = this.currentUserSubject.asObservable();

  constructor(private http: HttpClient) { }

  register(data: any): Observable<any> {
    return this.http.post(`${this.apiUrl}/register`, data).pipe(
      tap((response: any) => {
        if (response.token) {
          localStorage.setItem('token', response.token);
        }
      })
    );
  }

  login(credentials: any): Observable<any> {
    return this.http.post(`${this.apiUrl}/login`, credentials).pipe(
      tap((response: any) => {
        if (response.token) {
          localStorage.setItem('token', response.token);
          if (response.user) {
              this.currentUserSubject.next(response.user);
          }
        }
      })
    );
  }

  getProfile(): Observable<any> {
    return this.http.get<any>(`${this.apiUrl}/me`, { headers: this.getHeaders() }).pipe(
        tap(data => {
            this.currentUserSubject.next(data);
        })
    );
  }

  updatePersonalProfile(data: any): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/update-personal`, data, { headers: this.getHeaders() }).pipe(
        tap(() => {
            // Refresh profile after update
            this.getProfile().subscribe();
        })
    );
  }

  updateBusinessProfile(data: any): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/update-business`, data, { headers: this.getHeaders() }).pipe(
        tap(() => {
            // Refresh profile after update
            this.getProfile().subscribe();
        })
    );
  }

  changePassword(data: any): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/change-password`, data, { headers: this.getHeaders() });
  }

  getToken(): string | null {
    return localStorage.getItem('token');
  }

  logout(): void {
    localStorage.removeItem('token');
    this.currentUserSubject.next(null);
  }

  public getHeaders(): HttpHeaders {
    const token = this.getToken();
    return new HttpHeaders({
      'Authorization': `Bearer ${token}`
    });
  }
}
