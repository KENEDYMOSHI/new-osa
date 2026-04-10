import { CommonModule } from '@angular/common';
import { Component } from '@angular/core';
import { RouterModule } from '@angular/router';
import { NoDataComponent } from '../../../shared/components/no-data/no-data.component';

@Component({
  selector: 'app-business-applications',
  standalone: true,
  imports: [CommonModule, RouterModule, NoDataComponent],
  templateUrl: './business-applications.component.html',
})
export class BusinessApplicationsComponent {
  activeView: string = 'history';
}
