import { inject } from '@angular/core';
import { Router } from '@angular/router';
import { AuthService } from '../services/auth.service';
import { map, catchError } from 'rxjs/operators';
import { of } from 'rxjs';
import Swal from 'sweetalert2';

export const passportPhotoGuard = () => {
  const authService = inject(AuthService);
  const router = inject(Router);

  return authService.getProfile().pipe(
    map((profile: any) => {
      // Check if passport photo exists
      if (!profile.personalInfo?.picture) {
        // Show warning message
        Swal.fire({
          title: 'Profile Picture Required',
          text: 'Please upload your profile picture before proceeding with license applications.',
          icon: 'warning',
          confirmButtonText: 'Go to Profile',
          confirmButtonColor: '#F59E0B',
          allowOutsideClick: false
        }).then((result) => {
          if (result.isConfirmed) {
            router.navigate(['/profile']);
          }
        });
        return false;
      }
      return true;
    }),
    catchError(() => {
      router.navigate(['/profile']);
      return of(false);
    })
  );
};
