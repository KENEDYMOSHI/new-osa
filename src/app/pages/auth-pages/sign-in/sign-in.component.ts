import { Component, OnInit, OnDestroy } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { RouterModule, Router, ActivatedRoute } from '@angular/router';
import { AuthService } from '../../../core/services/auth.service';
import Swal from 'sweetalert2';
import { LoadingSpinnerComponent } from '../../../shared/components/ui/loading-spinner/loading-spinner.component';
import { firstValueFrom } from 'rxjs';

@Component({
  selector: 'app-sign-in',
  standalone: true,
  imports: [CommonModule, FormsModule, RouterModule, LoadingSpinnerComponent],
  templateUrl: './sign-in.component.html'
})
export class SignInComponent implements OnInit, OnDestroy {
  email = '';
  password = '';
  showPassword = false;
  isLoading = false;
  
  // Validation errors
  emailError = '';
  passwordError = '';

  // Slider Logic
  slides = [
    {
      image: 'https://images.unsplash.com/photo-1565514020176-dbf2277cc168?q=80&w=2000&auto=format&fit=crop',
      title: 'Reliable Measurements',
      description: 'Ensuring accuracy in every trade transaction.'
    },
    {
      image: 'https://images.unsplash.com/photo-1532601224476-15c79f2f7a51?q=80&w=2000&auto=format&fit=crop',
      title: 'Standards Compliance',
      description: 'Upholding national and international metrology standards.'
    },
    {
      image: 'https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?q=80&w=2000&auto=format&fit=crop',
      title: 'Consumer Protection',
      description: 'Safeguarding fair trade practices for all.'
    }
  ];
  currentSlide = 0;
  slideInterval: any;

  constructor(
    private router: Router, 
    private authService: AuthService,
    private route: ActivatedRoute
  ) {}

  ngOnInit() {
    this.startSlideShow();
    
    // Check if redirected due to session expiration
    // this.route.queryParams.subscribe(params => {
    //   if (params['sessionExpired'] === 'true') {
    //     Swal.fire({
    //       title: 'Session Expired',
    //       text: 'Your session has expired. Please log in again.',
    //       icon: 'warning',
    //       confirmButtonText: 'OK',
    //       confirmButtonColor: '#F59E0B'
    //     });
    //   }
    // });
  }

  ngOnDestroy() {
    this.stopSlideShow();
  }

  startSlideShow() {
    this.slideInterval = setInterval(() => {
      this.nextSlide();
    }, 5000); // Change slide every 5 seconds
  }

  stopSlideShow() {
    if (this.slideInterval) {
      clearInterval(this.slideInterval);
    }
  }

  nextSlide() {
    this.currentSlide = (this.currentSlide + 1) % this.slides.length;
  }

  prevSlide() {
    this.currentSlide = (this.currentSlide - 1 + this.slides.length) % this.slides.length;
  }

  goToSlide(index: number) {
    this.currentSlide = index;
    // Reset timer when manually changing slides
    this.stopSlideShow();
    this.startSlideShow();
  }

  togglePasswordVisibility() {
    this.showPassword = !this.showPassword;
  }

  validateForm(): boolean {
    let isValid = true;
    this.emailError = '';
    this.passwordError = '';

    if (!this.email) {
      this.emailError = 'Email is required';
      isValid = false;
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.email)) {
      this.emailError = 'Please enter a valid email address';
      isValid = false;
    }

    if (!this.password) {
      this.passwordError = 'Password is required';
      isValid = false;
    }

    return isValid;
  }

  async onSignIn() {
    if (!this.validateForm()) {
      return;
    }

    this.isLoading = true;

    try {
      const response: any = await firstValueFrom(this.authService.login({ email: this.email, password: this.password }));
      
      this.isLoading = false;
      
      await Swal.fire({
        title: 'Success!',
        text: 'You have successfully logged in.',
        icon: 'success',
        timer: 1500,
        showConfirmButton: false
      });

      // Redirect based on user_type
      const rawUserType = response?.user?.user_type || '';
      const userType = rawUserType.toLowerCase().trim();
      console.log('SignIn Redirect:', { raw: rawUserType, processed: userType });
      
      if (userType === 'pattern_approval') {
        this.router.navigate(['/pattern-approval/dashboard']);
      } else if (userType === 'practitioner' || userType === 'applicant' || userType === 'user') {
        this.router.navigate(['/']); // Main dashboard for License Application / Business Owner
      } else {
        // Fallback for unknown types (or if type is missing/practitioner default)
        this.router.navigate(['/']); 
      }
    } catch (error) {
      this.isLoading = false;
      console.error('Login failed', error);
      
      Swal.fire({
        title: 'Error!',
        text: 'Invalid email or password. Please try again.',
        icon: 'error',
        confirmButtonText: 'OK',
        confirmButtonColor: '#F59E0B'
      });
    }
  }
}
