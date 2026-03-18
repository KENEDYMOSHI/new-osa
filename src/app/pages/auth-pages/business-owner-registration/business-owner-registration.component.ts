import { Component } from '@angular/core';
import { BusinessOwnerRegistrationLayoutComponent } from '../../../shared/layout/business-owner-registration-layout/business-owner-registration-layout.component';
import { BusinessOwnerRegistrationFormComponent } from '../../../shared/components/auth/business-owner-registration-form/business-owner-registration-form.component';

@Component({
  selector: 'app-business-owner-registration',
  standalone: true,
  imports: [
    BusinessOwnerRegistrationLayoutComponent,
    BusinessOwnerRegistrationFormComponent,
  ],
  template: `
    <app-business-owner-registration-layout>
      <app-business-owner-registration-form></app-business-owner-registration-form>
    </app-business-owner-registration-layout>
  `,
})
export class BusinessOwnerRegistrationComponent {}
