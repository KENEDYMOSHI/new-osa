import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable, BehaviorSubject, tap } from 'rxjs';
import { AuthService } from '../core/services/auth.service';

export interface Notification {
  id: string;
  user_id: string;
  title: string;
  message: string;
  type: string;
  related_entity_id: string;
  is_read: boolean; // boolean in frontend, might come as 0/1 from backend
  created_at: string;
}

@Injectable({
  providedIn: 'root'
})
export class NotificationService {
  private apiUrl = 'http://localhost:8080/api/notifications';
  private notificationsSubject = new BehaviorSubject<Notification[]>([]);
  public notifications$ = this.notificationsSubject.asObservable();

  constructor(private http: HttpClient, private authService: AuthService) { }

  private getHeaders(): HttpHeaders {
    const token = this.authService.getToken();
    return new HttpHeaders({
      'Authorization': `Bearer ${token}`
    });
  }

  fetchNotifications(): void {
    if (!this.authService.getToken()) return;

    this.http.get<Notification[]>(this.apiUrl, { headers: this.getHeaders() })
      .subscribe({
        next: (notes) => {
          // Ensure is_read is boolean
          const processedNotes = notes.map(n => ({
            ...n,
            is_read: Boolean(Number(n.is_read))
          }));
          this.notificationsSubject.next(processedNotes);
        },
        error: (err) => console.error('Failed to fetch notifications', err)
      });
  }

  markAsRead(id: string): Observable<any> {
    return this.http.post(`${this.apiUrl}/${id}/read`, {}, { headers: this.getHeaders() })
      .pipe(
        tap(() => {
          // Update local state
          const current = this.notificationsSubject.value;
          const updated = current.map(n => 
            n.id === id ? { ...n, is_read: true } : n
          );
          this.notificationsSubject.next(updated);
        })
      );
  }

  getUnreadCount(): number {
    return this.notificationsSubject.value.filter(n => !n.is_read).length;
  }
}
