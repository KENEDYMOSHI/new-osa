import { inject } from '@angular/core';
import { Router, CanActivateFn } from '@angular/router';
import { AuthService } from '../services/auth.service';

export function userTypeGuard(requiredUserType: string): CanActivateFn {
  return () => {
    const authService = inject(AuthService);
    const router = inject(Router);
    
    const currentUserType = authService.getCurrentUserType();
    
    if (!currentUserType) {
      // No user logged in
      router.navigate(['/signin']);
      return false;
    }
    
    if (currentUserType !== requiredUserType) {
      // User type mismatch - redirect to appropriate dashboard
      if (currentUserType === 'pattern_approval') {
        router.navigate(['/pattern-approval/dashboard']);
      } else {
        router.navigate(['/']);
      }
      return false;
    }
    
    return true;
  };
}
