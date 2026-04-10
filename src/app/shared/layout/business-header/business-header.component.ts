import { Component } from '@angular/core';
import { SidebarService } from '../../services/sidebar.service';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { ThemeToggleButtonComponent } from '../../components/common/theme-toggle/theme-toggle-button.component';
import { NotificationDropdownComponent } from '../../components/header/notification-dropdown/notification-dropdown.component';
import { TranslateModule, TranslateService } from '@ngx-translate/core';

@Component({
  selector: 'app-business-header',
  standalone: true,
  imports: [
    CommonModule,
    RouterModule,
    ThemeToggleButtonComponent,
    NotificationDropdownComponent,
    TranslateModule
  ],
  templateUrl: './business-header.component.html',
})
export class BusinessHeaderComponent {
  readonly isMobileOpen$;
  currentLang: string;

  constructor(
    public sidebarService: SidebarService,
    private translate: TranslateService
  ) {
    this.isMobileOpen$ = this.sidebarService.isMobileOpen$;
    this.currentLang =
      (typeof localStorage !== 'undefined' && localStorage.getItem('app_language')) ||
      this.translate.currentLang ||
      'en';
  }

  handleToggle() {
    if (window.innerWidth >= 1280) {
      this.sidebarService.toggleExpanded();
    } else {
      this.sidebarService.toggleMobileOpen();
    }
  }

  switchLanguage(lang: string) {
    if (typeof localStorage !== 'undefined') {
      localStorage.setItem('app_language', lang);
    }
    this.translate.use(lang);
    this.currentLang = lang;
  }
}
