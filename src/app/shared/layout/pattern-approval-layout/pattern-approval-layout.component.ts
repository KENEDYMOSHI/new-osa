import { Component } from '@angular/core';
import { SidebarService } from '../../services/sidebar.service';
import { CommonModule } from '@angular/common';
import { PatternApprovalSidebarComponent } from '../pattern-approval-sidebar/pattern-approval-sidebar.component';
import { BackdropComponent } from '../backdrop/backdrop.component';
import { RouterModule } from '@angular/router';
import { PatternApprovalHeaderComponent } from '../pattern-approval-header/pattern-approval-header.component';

@Component({
  selector: 'app-pattern-approval-layout',
  imports: [
    CommonModule,
    RouterModule,
    PatternApprovalHeaderComponent,
    PatternApprovalSidebarComponent,
    BackdropComponent
  ],
  templateUrl: './pattern-approval-layout.component.html',
})

export class PatternApprovalLayoutComponent {
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
