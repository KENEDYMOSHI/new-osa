import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ComponentCardComponent } from '../../../shared/components/common/component-card/component-card.component';
import { PageBreadcrumbComponent } from '../../../shared/components/common/page-breadcrumb/page-breadcrumb.component';
import { ButtonComponent } from '../../../shared/components/ui/button/button.component';

@Component({
  selector: 'app-modals',
  standalone: true,
  imports: [
    CommonModule,
    ComponentCardComponent,
    PageBreadcrumbComponent,
    ButtonComponent,
  ],
  templateUrl: './modals.component.html',
})
export class ModalsComponent {
  isSmallModalOpen = false;
  isLargeModalOpen = false;

  openSmallModal() {
    this.isSmallModalOpen = true;
  }

  closeSmallModal() {
    this.isSmallModalOpen = false;
  }

  openLargeModal() {
    this.isLargeModalOpen = true;
  }

  closeLargeModal() {
    this.isLargeModalOpen = false;
  }
}
