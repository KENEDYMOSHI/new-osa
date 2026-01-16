import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Router } from '@angular/router';
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

  constructor(private http: HttpClient, private router: Router) { }

  register(data: any): Observable<any> {
    return this.http.post(`${this.apiUrl}/register`, data).pipe(
      tap((response: any) => {
        if (response.token) {
          localStorage.setItem('token', response.token);
        }
      })
    );
  }

  checkPhone(phone: string): Observable<any> {
    return this.http.post(`${this.apiUrl}/check-phone`, { phone });
  }

  login(credentials: any): Observable<any> {
    return this.http.post(`${this.apiUrl}/login`, credentials).pipe(
      tap((response: any) => {
        if (response.token && response.user) {
          const userType = response.user.user_type || 'practitioner';
          // Store token with user type prefix
          const storageKey = `token_${userType}`;
          localStorage.setItem(storageKey, response.token);
          localStorage.setItem('current_user_type', userType);
          this.currentUserSubject.next(response.user);
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

  forgotPassword(phone: string): Observable<any> {
    return this.http.post(`${this.apiUrl}/forgot-password`, { phone });
  }

  verifyOtp(phone: string, otp: string): Observable<any> {
    return this.http.post(`${this.apiUrl}/verify-otp`, { phone, otp });
  }

  resetPassword(data: any): Observable<any> {
    return this.http.post(`${this.apiUrl}/reset-password`, data);
  }

  getToken(): string | null {
    const currentUserType = this.getCurrentUserType();
    if (!currentUserType) return null;
    return localStorage.getItem(`token_${currentUserType}`);
  }

  getCurrentUserType(): string | null {
    return localStorage.getItem('current_user_type');
  }

  logout(): void {
    const currentUserType = this.getCurrentUserType();
    if (currentUserType) {
      // Only clear current user type's session
      localStorage.removeItem(`token_${currentUserType}`);
      localStorage.removeItem('current_user_type');
    }
    this.currentUserSubject.next(null);
    this.router.navigate(['/signin']);
  }

  public getHeaders(): HttpHeaders {
    const token = this.getToken();
    return new HttpHeaders({
      'Authorization': `Bearer ${token}`
    });
  }
}
