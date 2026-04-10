import { Component } from '@angular/core';
import { SidebarService } from '../../services/sidebar.service';
import { CommonModule } from '@angular/common';
import { BusinessSidebarComponent } from '../business-sidebar/business-sidebar.component';
import { BackdropComponent } from '../backdrop/backdrop.component';
import { RouterModule } from '@angular/router';
import { BusinessHeaderComponent } from '../business-header/business-header.component';
import { TranslateModule } from '@ngx-translate/core';

@Component({
  selector: 'app-business-layout',
  standalone: true,
  imports: [
    CommonModule,
    RouterModule,
    BusinessHeaderComponent,
    BusinessSidebarComponent,
    BackdropComponent,
    TranslateModule
  ],
  templateUrl: './business-layout.component.html',
})

export class BusinessLayoutComponent {
  readonly isExpanded$;
  readonly isHovered$;
  readonly isMobileOpen$;

  constructor(public sidebarService: SidebarService) {
    this.isExpanded$ = this.sidebarService.isExpanded$;
    this.isHovered$ = this.sidebarService.isHovered$;
    this.isMobileOpen$ = this.sidebarService.isMobileOpen$;
  }

  get containerClasses() {
    return [
      'flex-1',
      'transition-all',
      'duration-300',
      'ease-in-out',
      (this.isExpanded$ || this.isHovered$) ? 'xl:ml-[290px]' : 'xl:ml-[90px]',
      this.isMobileOpen$ ? 'ml-0' : ''
    ];
  }

}
