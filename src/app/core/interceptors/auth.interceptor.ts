import { HttpInterceptorFn } from '@angular/common/http';
import { inject } from '@angular/core';
import { Router } from '@angular/router';
import { catchError, throwError } from 'rxjs';

export const authInterceptor: HttpInterceptorFn = (req, next) => {
  const router = inject(Router);

  return next(req).pipe(
    catchError((error) => {
      // Check if error is 401 Unauthorized (token expired or invalid)
      if (error.status === 401) {
        // Clear token from localStorage
        localStorage.removeItem('token');
        
        // Log the user out
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
