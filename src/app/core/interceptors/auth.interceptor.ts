import { HttpInterceptorFn } from '@angular/common/http';
import { inject } from '@angular/core';
import { Router } from '@angular/router';
import { catchError, throwError } from 'rxjs';
import { AuthService } from '../services/auth.service';

export const authInterceptor: HttpInterceptorFn = (req, next) => {
  const router = inject(Router);
  const authService = inject(AuthService);

  return next(req).pipe(
    catchError((error) => {
      // Check if error is 401 Unauthorized (token expired or invalid)
      if (error.status === 401) {
        // Log the user out using service to clear correct tokens
        authService.logout();
        console.warn('Session expired. Redirecting to login...');
        
        // Redirect to login page
        router.navigate(['/signin'], {
          queryParams: { sessionExpired: 'true' }
        });
      }
      
      return throwError(() => error);
    })
  );
};
