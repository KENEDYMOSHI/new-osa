import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { RouterModule, Router } from '@angular/router';
import { AuthService } from '../../../core/services/auth.service';
import Swal from 'sweetalert2';
import { LoadingSpinnerComponent } from '../../../shared/components/ui/loading-spinner/loading-spinner.component';
import { firstValueFrom } from 'rxjs';

@Component({
  selector: 'app-forgot-password',
  standalone: true,
  imports: [CommonModule, FormsModule, RouterModule, LoadingSpinnerComponent],
  templateUrl: './forgot.component.html'
})
export class ForgotPasswordComponent {
  
  // Steps: 1=Phone, 2=OTP, 3=NewPassword, 4=Success
  currentStep = 1;
  isLoading = false;

  // Form Data
  phone = '';
  otp = '';
  newPassword = '';
  confirmPassword = '';

  // Validation
  phoneError = '';
  otpError = '';
  passwordError = '';
  confirmPasswordError = '';

  // UI State
  showPassword = false;
  showConfirmPassword = false;

  // Banner Slider (Reused from SignIn logic if needed, or static image)
  // For consistency, let's just use one static image or reused slider logic.
  // Using a static consistent image for simplicity.
  bannerImage = 'https://images.unsplash.com/photo-1565514020176-dbf2277cc168?q=80&w=2000&auto=format&fit=crop';


  constructor(private authService: AuthService, private router: Router) {}

  togglePasswordVisibility() {
    this.showPassword = !this.showPassword;
  }

  toggleConfirmPasswordVisibility() {
    this.showConfirmPassword = !this.showConfirmPassword;
  }

  // Step 1: Send OTP
  async onSendOtp() {
    this.phoneError = '';
    if (!this.phone) {
        this.phoneError = 'Phone number is required';
        return;
    }

    // Basic phone validation (optional, backend does main check)
    // Assuming varied formats, maybe just check length?
    if (this.phone.length < 10) {
        this.phoneError = 'Please enter a valid phone number';
        return;
    }

    this.isLoading = true;
    try {
        await firstValueFrom(this.authService.forgotPassword(this.phone));
        this.isLoading = false;
        // Proceed to next step
        this.currentStep = 2;
        Swal.fire({
            icon: 'info',
            title: 'OTP Sent',
            text: 'Please check your phone for the verification code.',
            timer: 2000,
            showConfirmButton: false
        });
    } catch (error: any) {
        this.isLoading = false;
        console.error('Send OTP failed', error);
        this.phoneError = error.error?.message || 'Failed to send OTP. Please check the number and try again.';
    }
  }

  // Step 2: Verify OTP
  async onVerifyOtp() {
      this.otpError = '';
      if (!this.otp) {
          this.otpError = 'OTP is required';
          return;
      }

      this.isLoading = true;
      try {
          await firstValueFrom(this.authService.verifyOtp(this.phone, this.otp));
          this.isLoading = false;
          // Proceed to next step
          this.currentStep = 3;
      } catch (error: any) {
          this.isLoading = false;
          console.error('Verify OTP failed', error);
          this.otpError = 'Invalid or expired OTP. Please try again.';
      }
  }

  // Step 3: Reset Password
  async onResetPassword() {
      this.passwordError = '';
      this.confirmPasswordError = '';

      if (!this.newPassword) {
          this.passwordError = 'New password is required';
          return;
      }
      if (this.newPassword.length < 8) {
          this.passwordError = 'Password must be at least 8 characters';
          return;
      }
      
      // Simple complexity check (optional but recommended)
      // "6+ Characters, 1 Capital letter" as per Login placeholder
      if (!/[A-Z]/.test(this.newPassword)) {
        this.passwordError = 'Password must contain at least one capital letter';
        return;
      }

      if (this.newPassword !== this.confirmPassword) {
          this.confirmPasswordError = 'Passwords do not match';
          return;
      }

      this.isLoading = true;
      try {
          await firstValueFrom(this.authService.resetPassword({
              phone: this.phone,
              otp: this.otp,
              newPassword: this.newPassword,
              confirmPassword: this.confirmPassword
          }));
          this.isLoading = false;
          this.currentStep = 4; // Success Step
      } catch (error: any) {
          this.isLoading = false;
          console.error('Reset Password failed', error);
          Swal.fire({
              icon: 'error',
              title: 'Reset Failed',
              text: error.error?.message || 'Failed to reset password. Please try again.'
          });
      }
  }

  goToLogin() {
      this.router.navigate(['/signin']);
  }

  goBack() {
      if (this.currentStep > 1 && this.currentStep < 4) {
          this.currentStep--;
      } else {
          this.router.navigate(['/signin']);
      }
  }
}
