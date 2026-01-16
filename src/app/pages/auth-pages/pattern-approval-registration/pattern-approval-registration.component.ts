import { Component } from '@angular/core';
import { PatternApprovalRegistrationLayoutComponent } from '../../../shared/layout/pattern-approval-registration-layout/pattern-approval-registration-layout.component';
import { PatternApprovalRegistrationFormComponent } from '../../../shared/components/auth/pattern-approval-registration-form/pattern-approval-registration-form.component';

@Component({
  selector: 'app-pattern-approval-registration',
  standalone: true,
  imports: [PatternApprovalRegistrationLayoutComponent, PatternApprovalRegistrationFormComponent],
  template: '<app-pattern-approval-registration-layout><app-pattern-approval-registration-form></app-pattern-approval-registration-form></app-pattern-approval-registration-layout>'
})
export class PatternApprovalRegistrationComponent {}
