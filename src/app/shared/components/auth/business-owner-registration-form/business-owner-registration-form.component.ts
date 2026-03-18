import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormBuilder, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { Router } from '@angular/router';
import Swal from 'sweetalert2';
import { AuthService } from '../../../../core/services/auth.service';
import { District, LocationService, PostalCode, Ward } from '../../../../services/location.service';

type BusinessRegistrationStepKey = 'station' | 'ownership' | 'contact' | 'security' | 'review';

@Component({
  selector: 'app-business-owner-registration-form',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule],
  templateUrl: './business-owner-registration-form.component.html',
})
export class BusinessOwnerRegistrationFormComponent implements OnInit {
  currentStep = 1;
  registrationForm!: FormGroup;
  isSubmitting = false;

  regions: string[] = [];
  stationDistricts: District[] = [];
  stationWards: Ward[] = [];
  stationPostalCodes: PostalCode[] = [];

  constructor(
    private fb: FormBuilder,
    private router: Router,
    private authService: AuthService,
    private locationService: LocationService
  ) {}

  ngOnInit(): void {
    this.initForm();
    this.loadRegions();
    this.setupLocationListeners();
    this.setupStationTypeListener();
  }

  get visibleSteps(): Array<{ key: BusinessRegistrationStepKey; label: string }> {
    const steps: Array<{ key: BusinessRegistrationStepKey; label: string }> = [
      { key: 'station', label: 'Station' },
    ];

    if (this.isPrivateCompany) {
      steps.push({ key: 'ownership', label: 'Ownership' });
    }

    steps.push(
      { key: 'contact', label: 'Contact' },
      { key: 'security', label: 'Security' },
      { key: 'review', label: 'Review' }
    );

    return steps;
  }

  get totalSteps(): number {
    return this.visibleSteps.length;
  }

  get currentStepKey(): BusinessRegistrationStepKey {
    return this.visibleSteps[this.currentStep - 1]?.key ?? 'station';
  }

  get stationInfo(): FormGroup {
    return this.registrationForm.get('stationInfo') as FormGroup;
  }

  get ownershipDetails(): FormGroup {
    return this.registrationForm.get('ownershipDetails') as FormGroup;
  }

  get contactPerson(): FormGroup {
    return this.registrationForm.get('contactPerson') as FormGroup;
  }

  get security(): FormGroup {
    return this.registrationForm.get('security') as FormGroup;
  }

  get isPrivateCompany(): boolean {
    return this.stationInfo.get('stationType')?.value === 'private';
  }

  initForm(): void {
    this.registrationForm = this.fb.group({
      stationInfo: this.fb.group({
        stationType: ['private', Validators.required],
        companyName: ['', Validators.required],
        tin: ['', [Validators.required, Validators.minLength(9), Validators.maxLength(9), Validators.pattern(/^[0-9]*$/)]],
        businessLicenseNumber: ['', Validators.required],
        postalAddress: ['', Validators.required],
        region: ['', Validators.required],
        district: ['', Validators.required],
        ward: ['', Validators.required],
        postalCode: ['', Validators.required],
        officePhoneNumber: ['', [Validators.required, Validators.minLength(10), Validators.maxLength(10), Validators.pattern(/^[0-9]*$/)]],
      }),
      ownershipDetails: this.fb.group({
        firstName: ['', Validators.required],
        secondName: ['', Validators.required],
        lastName: ['', Validators.required],
        phoneNumber: ['', [Validators.required, Validators.minLength(10), Validators.maxLength(10), Validators.pattern(/^[0-9]*$/)]],
        emailAddress: ['', [Validators.required, Validators.email]],
        postalAddress: ['', Validators.required],
      }),
      contactPerson: this.fb.group({
        firstName: ['', Validators.required],
        secondName: ['', Validators.required],
        lastName: ['', Validators.required],
        designation: ['', Validators.required],
        phoneNumber: ['', [Validators.required, Validators.minLength(10), Validators.maxLength(10), Validators.pattern(/^[0-9]*$/)]],
        alternativePhoneNumber: ['', [Validators.minLength(10), Validators.maxLength(10), Validators.pattern(/^[0-9]*$/)]],
        emailAddress: ['', [Validators.required, Validators.email]],
      }),
      security: this.fb.group({
        password: ['', [Validators.required, Validators.minLength(8)]],
        confirmPassword: ['', Validators.required],
        termsAccepted: [false, Validators.requiredTrue],
      }),
    });
  }

  setupStationTypeListener(): void {
    this.stationInfo.get('stationType')?.valueChanges.subscribe((stationType) => {
      const ownershipControls = [
        'firstName',
        'secondName',
        'lastName',
        'phoneNumber',
        'emailAddress',
        'postalAddress',
      ];

      if (stationType === 'private') {
        ownershipControls.forEach((field) => {
          const control = this.ownershipDetails.get(field);
          control?.setValidators(field === 'emailAddress'
            ? [Validators.required, Validators.email]
            : field === 'phoneNumber'
              ? [Validators.required, Validators.minLength(10), Validators.maxLength(10), Validators.pattern(/^[0-9]*$/)]
              : [Validators.required]);
          control?.updateValueAndValidity({ emitEvent: false });
        });
      } else {
        this.ownershipDetails.reset();
        ownershipControls.forEach((field) => {
          const control = this.ownershipDetails.get(field);
          control?.clearValidators();
          control?.updateValueAndValidity({ emitEvent: false });
        });

        if (this.currentStepKey === 'ownership') {
          this.currentStep = 2;
        }
      }
    });
  }

  loadRegions(): void {
    this.locationService.getRegions().subscribe({
      next: (regions) => {
        this.regions = regions;
      },
      error: (err) => {
        console.error('Failed to load regions:', err);
      },
    });
  }

  setupLocationListeners(): void {
    this.stationInfo.get('region')?.valueChanges.subscribe((region) => {
      if (!region) {
        this.stationDistricts = [];
        this.stationWards = [];
        this.stationPostalCodes = [];
        return;
      }

      this.locationService.getDistricts(region).subscribe({
        next: (districts) => {
          this.stationDistricts = districts;
          this.stationInfo.patchValue({ district: '', ward: '', postalCode: '' }, { emitEvent: false });
          this.stationWards = [];
          this.stationPostalCodes = [];
        },
        error: (err) => {
          console.error('Failed to load districts:', err);
          this.stationDistricts = [];
        },
      });
    });

    this.stationInfo.get('district')?.valueChanges.subscribe((district) => {
      if (!district) {
        this.stationWards = [];
        this.stationPostalCodes = [];
        return;
      }

      this.locationService.getWards(district).subscribe({
        next: (wards) => {
          this.stationWards = wards;
          this.stationInfo.patchValue({ ward: '', postalCode: '' }, { emitEvent: false });
          this.stationPostalCodes = [];
        },
        error: (err) => {
          console.error('Failed to load wards:', err);
          this.stationWards = [];
        },
      });
    });

    this.stationInfo.get('ward')?.valueChanges.subscribe((ward) => {
      if (!ward) {
        this.stationPostalCodes = [];
        this.stationInfo.get('postalCode')?.setValue('', { emitEvent: false });
        return;
      }

      this.locationService.getPostalCodes(ward).subscribe({
        next: (postalCodes) => {
          this.stationPostalCodes = postalCodes;
          this.stationInfo.get('postalCode')?.setValue(
            postalCodes.length === 1 ? postalCodes[0].postcode : '',
            { emitEvent: false }
          );
        },
        error: (err) => {
          console.error('Failed to load postal codes:', err);
          this.stationPostalCodes = [];
        },
      });
    });
  }

  nextStep(): void {
    const stepKey = this.currentStepKey;

    if (stepKey === 'station') {
      this.advanceIfValid(this.stationInfo);
      return;
    }

    if (stepKey === 'ownership') {
      this.advanceIfValid(this.ownershipDetails);
      return;
    }

    if (stepKey === 'contact') {
      if (this.contactPerson.invalid) {
        this.showValidationError(this.contactPerson);
        return;
      }

      const phone = this.contactPerson.get('phoneNumber')?.value;
      this.isSubmitting = true;
      this.authService.checkPhone(phone).subscribe({
        next: (res) => {
          this.isSubmitting = false;
          if (res.exists) {
            this.showToast('error', 'Phone Number Taken', 'This contact phone number is already registered.');
            return;
          }

          this.currentStep++;
          window.scrollTo(0, 0);
        },
        error: (err) => {
          this.isSubmitting = false;
          console.error('Phone check failed', err);
          this.showToast('error', 'Error', 'Could not verify contact phone number. Please try again.');
        },
      });
      return;
    }

    if (stepKey === 'security') {
      this.advanceIfValid(this.security, true);
    }
  }

  prevStep(): void {
    if (this.currentStep > 1) {
      this.currentStep--;
      window.scrollTo(0, 0);
    }
  }

  advanceIfValid(group: FormGroup, validatePasswords = false): void {
    if (group.invalid) {
      this.showValidationError(group);
      return;
    }

    if (validatePasswords && this.security.get('password')?.value !== this.security.get('confirmPassword')?.value) {
      this.showToast('error', 'Validation Error', 'Password and confirm password must match.');
      this.security.get('confirmPassword')?.markAsTouched();
      return;
    }

    this.currentStep++;
    window.scrollTo(0, 0);
  }

  showValidationError(group: FormGroup): void {
    group.markAllAsTouched();
    this.showToast('error', 'Validation Error', 'Please fill in all required fields correctly.');
  }

  showToast(icon: 'error' | 'success', title: string, text: string): void {
    const Toast = Swal.mixin({
      toast: true,
      position: 'top-end',
      showConfirmButton: false,
      timer: 4000,
      timerProgressBar: true,
      didOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer);
        toast.addEventListener('mouseleave', Swal.resumeTimer);
      },
    });

    Toast.fire({ icon, title, text });
  }

  async onSubmit(): Promise<void> {
    if (this.registrationForm.invalid) {
      this.registrationForm.markAllAsTouched();
      this.showToast('error', 'Validation Error', 'Please review the form and correct any errors.');
      return;
    }

    if (this.security.get('password')?.value !== this.security.get('confirmPassword')?.value) {
      this.showToast('error', 'Validation Error', 'Password and confirm password must match.');
      return;
    }

    this.isSubmitting = true;

    const formValue = this.registrationForm.value;
    const payload = {
      ...formValue,
      registrationType: 'business_owner',
      userType: 'business_owner',
      email: formValue.contactPerson.emailAddress,
      phone: formValue.contactPerson.phoneNumber,
      password: formValue.security.password,
      confirmPassword: formValue.security.confirmPassword,
      companyName: formValue.stationInfo.companyName,
      stationType: formValue.stationInfo.stationType,
    };

    this.authService.register(payload).subscribe({
      next: () => {
        Swal.fire({
          icon: 'success',
          title: 'Registration Successful!',
          text: 'Your business owner account has been created.',
          confirmButtonColor: '#F7941D',
        }).then(() => {
          this.router.navigate(['/signin']);
        });
      },
      error: (error) => {
        console.error('Registration error:', error);
        let errorMessage = 'An error occurred while submitting your application. Please try again.';

        if (error.error?.message) {
          errorMessage = error.error.message;
        } else if (error.error?.messages) {
          const messages = error.error.messages;
          if (typeof messages === 'string') {
            errorMessage = messages;
          } else if (typeof messages === 'object') {
            errorMessage = Object.values(messages).join('\n');
          }
        }

        Swal.fire({
          icon: 'error',
          title: 'Submission Failed',
          text: errorMessage,
          confirmButtonColor: '#F7941D',
        });
        this.isSubmitting = false;
      },
      complete: () => {
        this.isSubmitting = false;
      },
    });
  }
}
