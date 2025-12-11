import { Component } from '@angular/core';
import { PractitionerRegistrationLayoutComponent } from '../../../shared/layout/practitioner-registration-layout/practitioner-registration-layout.component';
import { PractitionerRegistrationFormComponent } from '../../../shared/components/auth/practitioner-registration-form/practitioner-registration-form.component';

@Component({
  selector: 'app-practitioner-registration',
  standalone: true,
  imports: [
    PractitionerRegistrationLayoutComponent,
    PractitionerRegistrationFormComponent
  ],
  template: `
    <app-practitioner-registration-layout>
      <app-practitioner-registration-form></app-practitioner-registration-form>
    </app-practitioner-registration-layout>
  `,
})
export class PractitionerRegistrationComponent {}
