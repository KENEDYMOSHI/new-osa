import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormBuilder, FormGroup, ReactiveFormsModule, Validators, FormsModule } from '@angular/forms';
import { Router } from '@angular/router';
import Swal from 'sweetalert2';
import { AuthService } from '../../../../core/services/auth.service';
import { LocationService, District, Ward, PostalCode } from '../../../../services/location.service';

@Component({
  selector: 'app-practitioner-registration-form',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, FormsModule],
  templateUrl: './practitioner-registration-form.component.html',
})
export class PractitionerRegistrationFormComponent implements OnInit {
  currentStep = 1;
  totalSteps = 4;
  registrationForm!: FormGroup;
  isSubmitting = false;

  countries = [
    "Tanzania", 
    "Afghanistan", "Albania", "Algeria", "Andorra", "Angola", "Antigua and Barbuda", "Argentina", "Armenia", "Australia", "Austria", "Azerbaijan",
    "Bahamas", "Bahrain", "Bangladesh", "Barbados", "Belarus", "Belgium", "Belize", "Benin", "Bhutan", "Bolivia", "Bosnia and Herzegovina", "Botswana", "Brazil", "Brunei", "Bulgaria", "Burkina Faso", "Burundi",
    "Cabo Verde", "Cambodia", "Cameroon", "Canada", "Central African Republic", "Chad", "Chile", "China", "Colombia", "Comoros", "Congo (Congo-Brazzaville)", "Costa Rica", "Croatia", "Cuba", "Cyprus", "Czechia (Czech Republic)",
    "Democratic Republic of the Congo", "Denmark", "Djibouti", "Dominica", "Dominican Republic",
    "Ecuador", "Egypt", "El Salvador", "Equatorial Guinea", "Eritrea", "Estonia", "Eswatini (fmr. 'Swaziland')", "Ethiopia",
    "Fiji", "Finland", "France",
    "Gabon", "Gambia", "Georgia", "Germany", "Ghana", "Greece", "Grenada", "Guatemala", "Guinea", "Guinea-Bissau", "Guyana",
    "Haiti", "Holy See", "Honduras", "Hungary",
    "Iceland", "India", "Indonesia", "Iran", "Iraq", "Ireland", "Israel", "Italy",
    "Jamaica", "Japan", "Jordan",
    "Kazakhstan", "Kenya", "Kiribati", "Kuwait", "Kyrgyzstan",
    "Laos", "Latvia", "Lebanon", "Lesotho", "Liberia", "Libya", "Liechtenstein", "Lithuania", "Luxembourg",
    "Madagascar", "Malawi", "Malaysia", "Maldives", "Mali", "Malta", "Marshall Islands", "Mauritania", "Mauritius", "Mexico", "Micronesia", "Moldova", "Monaco", "Mongolia", "Montenegro", "Morocco", "Mozambique", "Myanmar (formerly Burma)",
    "Namibia", "Nauru", "Nepal", "Netherlands", "New Zealand", "Nicaragua", "Niger", "Nigeria", "North Korea", "North Macedonia", "Norway",
    "Oman",
    "Pakistan", "Palau", "Palestine State", "Panama", "Papua New Guinea", "Paraguay", "Peru", "Philippines", "Poland", "Portugal",
    "Qatar",
    "Romania", "Russia", "Rwanda",
    "Saint Kitts and Nevis", "Saint Lucia", "Saint Vincent and the Grenadines", "Samoa", "San Marino", "Sao Tome and Principe", "Saudi Arabia", "Senegal", "Serbia", "Seychelles", "Sierra Leone", "Singapore", "Slovakia", "Slovenia", "Solomon Islands", "Somalia", "South Africa", "South Korea", "South Sudan", "Spain", "Sri Lanka", "Sudan", "Suriname", "Sweden", "Switzerland", "Syria",
    "Tajikistan", "Thailand", "Timor-Leste", "Togo", "Tonga", "Trinidad and Tobago", "Tunisia", "Turkey", "Turkmenistan", "Tuvalu",
    "Uganda", "Ukraine", "United Arab Emirates", "United Kingdom", "United States of America", "Uruguay", "Uzbekistan",
    "Vanuatu", "Venezuela", "Vietnam",
    "Yemen",
    "Zambia", "Zimbabwe"
  ];
  isTanzanian = true;

  // Location data
  regions: string[] = [];
  personalDistricts: District[] = [];
  personalWards: Ward[] = [];
  businessDistricts: District[] = [];
  businessWards: Ward[] = [];
  businessPostalCodes: PostalCode[] = [];

  // Custom Dropdown State
  showNationalityDropdown = false;
  nationalitySearchTerm = '';

  constructor(
    private fb: FormBuilder, 
    private router: Router, 
    private authService: AuthService,
    private locationService: LocationService
  ) {}

  ngOnInit(): void {
    this.initForm();
    this.setupNationalityListener();
    this.loadRegions();
    this.setupLocationListeners();
  }

  // Filtered countries getter
  get filteredCountries(): string[] {
    if (!this.nationalitySearchTerm) {
      return this.countries;
    }
    const term = this.nationalitySearchTerm.toLowerCase();
    return this.countries.filter(c => c.toLowerCase().includes(term));
  }

  toggleNationalityDropdown(): void {
    this.showNationalityDropdown = !this.showNationalityDropdown;
    if (this.showNationalityDropdown) {
      // Focus search input slightly delayed to wait for render
      setTimeout(() => {
        const searchInput = document.getElementById('nationalitySearch');
        if (searchInput) searchInput.focus();
      }, 50);
    }
  }

  selectNationality(country: string): void {
    this.personalInfo.get('nationality')?.setValue(country);
    this.showNationalityDropdown = false;
    this.nationalitySearchTerm = '';
  }

  closeNationalityDropdown(): void {
    // Delay slightly to allow click event on option to fire first
    setTimeout(() => {
        this.showNationalityDropdown = false;
    }, 200);
  }

  // ... (keep existing methods)

  initForm(): void {
    this.registrationForm = this.fb.group({
      // Step 1: Personal Information
      personalInfo: this.fb.group({
        nationality: ['Tanzania', Validators.required],
        identityNumber: ['', [Validators.required, Validators.minLength(20), Validators.maxLength(20), Validators.pattern(/^[0-9]*$/)]],
        firstName: ['', Validators.required],
        secondName: ['', Validators.required],
        lastName: ['', Validators.required],
        gender: ['', Validators.required],
        dateOfBirth: ['', Validators.required],
        region: ['', Validators.required],
        district: ['', Validators.required],
        ward: ['', Validators.required],
        street: ['', Validators.required],
        phoneNumber: ['', [Validators.required, Validators.minLength(10), Validators.maxLength(10), Validators.pattern(/^[0-9]*$/)]],
      }),
      // Step 2: Business Information
      businessInfo: this.fb.group({
        tin: ['', Validators.required],
        companyName: ['', Validators.required],
        companyEmail: ['', [Validators.required, Validators.email]],
        companyPhone: ['', Validators.required],
        brelaNumber: ['', Validators.required],
        region: ['', Validators.required],
        district: ['', Validators.required],
        ward: ['', Validators.required],
        postalCode: ['', Validators.required],
        street: ['', Validators.required],
      }),
      // Step 3: Contact & Security
      contactSecurity: this.fb.group({
        email: ['', [Validators.required, Validators.email]],
        phone: ['', Validators.required],
        password: ['', [Validators.required, Validators.minLength(8)]],
        confirmPassword: ['', Validators.required],
        termsAccepted: [false, Validators.requiredTrue],
      }),
    });
  }

  setupNationalityListener(): void {
    this.personalInfo.get('nationality')?.valueChanges.subscribe((value) => {
      this.isTanzanian = value === 'Tanzania';
      const identityControl = this.personalInfo.get('identityNumber');
      const dobControl = this.personalInfo.get('dateOfBirth');
      
      if (this.isTanzanian) {
        identityControl?.setValidators([Validators.required, Validators.minLength(20), Validators.maxLength(20), Validators.pattern(/^[0-9]*$/)]);
        // If switching to Tanzania, trigger validation to potentially auto-fill if value exists
        identityControl?.updateValueAndValidity();
      } else {
        identityControl?.setValidators([Validators.required]);
        dobControl?.enable(); // Ensure it's enabled for non-citizens
      }
      identityControl?.updateValueAndValidity();
    });

    this.personalInfo.get('identityNumber')?.valueChanges.subscribe((value) => {
      if (this.isTanzanian && value && value.length === 20) {
        // Extract DOB from NIDA (YYYYMMDD)
        const year = value.substring(0, 4);
        const month = value.substring(4, 6);
        const day = value.substring(6, 8);
        
        // Basic validation to check if it's a valid date structure
        const dateString = `${year}-${month}-${day}`;
        const date = new Date(dateString);
        
        if (!isNaN(date.getTime())) {
             this.personalInfo.get('dateOfBirth')?.setValue(dateString);
             // Optional: Disable DOB field for Tanzanians to enforce NIDA match
             // this.personalInfo.get('dateOfBirth')?.disable(); 
        }
      }
    });
  }

  get personalInfo() {
    return this.registrationForm.get('personalInfo') as FormGroup;
  }

  get businessInfo() {
    return this.registrationForm.get('businessInfo') as FormGroup;
  }

  get contactSecurity() {
    return this.registrationForm.get('contactSecurity') as FormGroup;
  }

  nextStep(): void {
    let currentGroup: FormGroup | null = null;

    if (this.currentStep === 1) {
      currentGroup = this.personalInfo;
      
      // Real-time phone check
      if (this.personalInfo.valid) {
          const phone = this.personalInfo.get('phoneNumber')?.value;
          this.isSubmitting = true; // Show loading if needed (or add specific loading state)
          this.authService.checkPhone(phone).subscribe({
              next: (res) => {
                  this.isSubmitting = false;
                  if (res.exists) {
                      const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 4000,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                          toast.addEventListener('mouseenter', Swal.stopTimer)
                          toast.addEventListener('mouseleave', Swal.resumeTimer)
                        }
                      });

                      Toast.fire({
                        icon: 'error',
                        title: 'Phone Number Taken',
                        text: 'This phone number is already registered.'
                      });
                  } else {
                      // Proceed to step 2
                      this.currentStep++;
                      window.scrollTo(0, 0);
                  }
              },
              error: (err) => {
                  this.isSubmitting = false;
                  console.error('Phone check failed', err);
                   const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 4000,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                          toast.addEventListener('mouseenter', Swal.stopTimer)
                          toast.addEventListener('mouseleave', Swal.resumeTimer)
                        }
                      });
                   Toast.fire({
                          icon: 'error',
                          title: 'Error',
                          text: 'Could not verify phone number. Please try again.'
                      });
              }
          });
          return; // Stop here, wait for async check
      }
    } else if (this.currentStep === 2) {
      currentGroup = this.businessInfo;
    } else if (this.currentStep === 3) {
      currentGroup = this.contactSecurity;
    }

    if (currentGroup && currentGroup.invalid) {
      currentGroup.markAllAsTouched();
      const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 4000,
        timerProgressBar: true,
        didOpen: (toast) => {
          toast.addEventListener('mouseenter', Swal.stopTimer)
          toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
      });
      Toast.fire({
        icon: 'error',
        title: 'Validation Error',
        text: 'Please fill in all required fields correctly.'
      });
      return;
    }

    if (this.currentStep > 1 && this.currentStep < this.totalSteps) {
      this.currentStep++;
      window.scrollTo(0, 0);
    }
  }

  // Removed old nextStep logic block for incrementing step outside specific conditions
  // ...

  prevStep(): void {
    if (this.currentStep > 1) {
      this.currentStep--;
      window.scrollTo(0, 0);
    }
  }

  loadRegions(): void {
    this.locationService.getRegions().subscribe({
      next: (regions) => {
        this.regions = regions;
      },
      error: (err) => {
        console.error('Failed to load regions:', err);
      }
    });
  }

  setupLocationListeners(): void {
    // Personal Info Region Change
    this.personalInfo.get('region')?.valueChanges.subscribe((region) => {
      if (region) {
        this.locationService.getDistricts(region).subscribe({
          next: (districts) => {
            this.personalDistricts = districts;
            // Reset district and ward when region changes
            this.personalInfo.get('district')?.setValue('');
            this.personalWards = [];
          },
          error: (err) => {
            console.error('Failed to load districts:', err);
            this.personalDistricts = [];
          }
        });
      } else {
        this.personalDistricts = [];
        this.personalWards = [];
      }
    });

    // Personal Info District Change
    this.personalInfo.get('district')?.valueChanges.subscribe((district) => {
      if (district) {
        this.locationService.getWards(district).subscribe({
          next: (wards) => {
            this.personalWards = wards;
          },
          error: (err) => {
            console.error('Failed to load wards:', err);
            this.personalWards = [];
          }
        });
      } else {
        this.personalWards = [];
      }
    });

    // Business Info Region Change
    this.businessInfo.get('region')?.valueChanges.subscribe((region) => {
      if (region) {
        this.locationService.getDistricts(region).subscribe({
          next: (districts) => {
            this.businessDistricts = districts;
            // Reset district and ward when region changes
            this.businessInfo.get('district')?.setValue('');
            this.businessWards = [];
          },
          error: (err) => {
            console.error('Failed to load districts:', err);
            this.businessDistricts = [];
          }
        });
      } else {
        this.businessDistricts = [];
        this.businessWards = [];
      }
    });

    // Business Info District Change
    this.businessInfo.get('district')?.valueChanges.subscribe((district) => {
      if (district) {
        this.locationService.getWards(district).subscribe({
          next: (wards) => {
            this.businessWards = wards;
            // Reset ward and postal code when district changes
            this.businessInfo.get('ward')?.setValue('');
            this.businessPostalCodes = [];
          },
          error: (err) => {
            console.error('Failed to load wards:', err);
            this.businessWards = [];
          }
        });
      } else {
        this.businessWards = [];
        this.businessPostalCodes = [];
      }
    });

    // Business Info Ward Change - Load Postal Codes
    this.businessInfo.get('ward')?.valueChanges.subscribe((ward) => {
      if (ward) {
        this.locationService.getPostalCodes(ward).subscribe({
          next: (postalCodes) => {
            this.businessPostalCodes = postalCodes;
            // Auto-fill postal code if only one exists
            if (postalCodes.length === 1) {
              this.businessInfo.get('postalCode')?.setValue(postalCodes[0].postcode);
            } else {
              // Reset postal code if multiple options
              this.businessInfo.get('postalCode')?.setValue('');
            }
          },
          error: (err) => {
            console.error('Failed to load postal codes:', err);
            this.businessPostalCodes = [];
          }
        });
      } else {
        this.businessPostalCodes = [];
        this.businessInfo.get('postalCode')?.setValue('');
      }
    });
  }

  async onSubmit(): Promise<void> {
    if (this.registrationForm.invalid) {
      this.registrationForm.markAllAsTouched();
      const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 4000,
        timerProgressBar: true,
        didOpen: (toast) => {
          toast.addEventListener('mouseenter', Swal.stopTimer)
          toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
      });
      Toast.fire({
        icon: 'error',
        title: 'Validation Error',
        text: 'Please review the form and correct any errors.'
      });
      return;
    }

    this.isSubmitting = true;

    const formData = this.registrationForm.value;

    this.authService.register(formData).subscribe({
      next: (response) => {
        Swal.fire({
          icon: 'success',
          title: 'Registration Successful!',
          text: 'Your practitioner account has been created.',
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
      }
    });
  }
}
