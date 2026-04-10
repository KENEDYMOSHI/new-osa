import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { NoDataComponent } from '../../../shared/components/no-data/no-data.component';
import { TranslateModule } from '@ngx-translate/core';

@Component({
  selector: 'app-business-applications',
  standalone: true,
  imports: [CommonModule, RouterModule, NoDataComponent, TranslateModule],
  templateUrl: './business-applications.component.html',
})
export class BusinessApplicationsComponent {
  activeView: 'services' | 'history' = 'services';
}

