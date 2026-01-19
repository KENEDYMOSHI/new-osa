import { inject } from '@angular/core';
import { Router, CanActivateFn } from '@angular/router';
import { AuthService } from '../services/auth.service';

export function userTypeGuard(requiredUserType: string | string[]): CanActivateFn {
  return () => {
    const authService = inject(AuthService);
    const router = inject(Router);
    
    const currentUserType = authService.getCurrentUserType();
    
    if (!currentUserType) {
      // No user logged in
      router.navigate(['/signin']);
      return false;
    }
    
    const allowedTypes = Array.isArray(requiredUserType) 
      ? requiredUserType.map(t => t.toLowerCase()) 
      : [requiredUserType.toLowerCase()];
    
    // Normalize current user type to lowercase for comparison
    const normalizedCurrentUserType = currentUserType.toLowerCase().trim();
    console.log('UserTypeGuard Check:', { required: allowedTypes, current: normalizedCurrentUserType });
    
    if (!allowedTypes.includes(normalizedCurrentUserType)) {
      console.warn('UserTypeGuard: Access Denied', { required: allowedTypes, current: normalizedCurrentUserType });
      // User type mismatch - redirect to appropriate dashboard
      if (normalizedCurrentUserType === 'pattern_approval') {
        router.navigate(['/pattern-approval/dashboard']);
      } else {
        router.navigate(['/']);
      }
      return false;
    }
    
    return true;
  };
}
