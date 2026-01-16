import { Routes } from '@angular/router';
import { authGuard } from './core/guards/auth.guard';
import { userTypeGuard } from './core/guards/user-type.guard';
import { EcommerceComponent } from './pages/dashboard/ecommerce/ecommerce.component';
import { DashboardComponent } from './pages/dashboard/dashboard.component';
import { ProfileComponent } from './pages/profile/profile.component';
import { FormElementsComponent } from './pages/forms/form-elements/form-elements.component';
import { BasicTablesComponent } from './pages/tables/basic-tables/basic-tables.component';
import { BlankComponent } from './pages/blank/blank.component';
import { NotFoundComponent } from './pages/other-page/not-found/not-found.component';
import { AppLayoutComponent } from './shared/layout/app-layout/app-layout.component';
import { InvoicesComponent } from './pages/invoices/invoices.component';
import { LineChartComponent } from './pages/charts/line-chart/line-chart.component';
import { BarChartComponent } from './pages/charts/bar-chart/bar-chart.component';
import { AlertsComponent } from './pages/ui-elements/alerts/alerts.component';
import { AvatarElementComponent } from './pages/ui-elements/avatar-element/avatar-element.component';
import { BadgesComponent } from './pages/ui-elements/badges/badges.component';
import { ButtonsComponent } from './pages/ui-elements/buttons/buttons.component';
import { ImagesComponent } from './pages/ui-elements/images/images.component';
import { VideosComponent } from './pages/ui-elements/videos/videos.component';
import { SignInComponent } from './pages/auth-pages/sign-in/sign-in.component';
import { SignUpComponent } from './pages/auth-pages/sign-up/sign-up.component';
import { CalenderComponent } from './pages/calender/calender.component';

export const routes: Routes = [
  {
    path:'',
    component:AppLayoutComponent,
    canActivate: [authGuard, userTypeGuard('practitioner')],
    children:[
      {
        path: '',
        component: DashboardComponent,
        pathMatch: 'full',
        title:
          'WMA Portal | Dashboard',
      },
      {
        path:'calendar',
        component:CalenderComponent,
        title:'Angular Calender | TailAdmin - Angular Admin Dashboard Template'
      },
      {
        path:'profile',
        component:ProfileComponent,
        title:'Angular Profile Dashboard | TailAdmin - Angular Admin Dashboard Template'
      },
      {
        path: 'my-applications',
        loadComponent: () => import('./pages/my-applications/my-applications.component').then(m => m.MyApplicationsComponent),
        title: 'My Applications | WMA Portal'
      },
      {
        path: 'Initial-Application',
        loadComponent: () => import('./pages/license-application/license-application.component').then(m => m.LicenseApplicationComponent),
        title: 'License Application | WMA Portal'
      },
      {
        path:'form-elements',
        component:FormElementsComponent,
        title:'Angular Form Elements Dashboard | TailAdmin - Angular Admin Dashboard Template'
      },
      {
        path:'basic-tables',
        component:BasicTablesComponent,
        title:'Angular Basic Tables Dashboard | TailAdmin - Angular Admin Dashboard Template'
      },
      {
        path:'blank',
        component:BlankComponent,
        title:'Angular Blank Dashboard | TailAdmin - Angular Admin Dashboard Template'
      },
      // support tickets
      {
        path:'invoice',
        component:InvoicesComponent,
        title:'Angular Invoice Details Dashboard | TailAdmin - Angular Admin Dashboard Template'
      },
      {
        path:'line-chart',
        component:LineChartComponent,
        title:'Angular Line Chart Dashboard | TailAdmin - Angular Admin Dashboard Template'
      },
      {
        path:'bar-chart',
        component:BarChartComponent,
        title:'Angular Bar Chart Dashboard | TailAdmin - Angular Admin Dashboard Template'
      },
      {
        path:'alerts',
        component:AlertsComponent,
        title:'Angular Alerts Dashboard | TailAdmin - Angular Admin Dashboard Template'
      },
      {
        path:'avatars',
        component:AvatarElementComponent,
        title:'Angular Avatars Dashboard | TailAdmin - Angular Admin Dashboard Template'
      },
      {
        path:'badge',
        component:BadgesComponent,
        title:'Angular Badges Dashboard | TailAdmin - Angular Admin Dashboard Template'
      },
      {
        path:'buttons',
        component:ButtonsComponent,
        title:'Angular Buttons Dashboard | TailAdmin - Angular Admin Dashboard Template'
      },
      {
        path:'images',
        component:ImagesComponent,
        title:'Angular Images Dashboard | TailAdmin - Angular Admin Dashboard Template'
      },
      {
        path:'videos',
        component:VideosComponent,
        title:'Angular Videos Dashboard | TailAdmin - Angular Admin Dashboard Template'
      },
      {
        path:'modals',
        loadComponent: () => import('./pages/ui-elements/modals/modals.component').then(m => m.ModalsComponent),
        title:'Angular Modals Dashboard | TailAdmin - Angular Admin Dashboard Template'
      },
      {
        path: 'billing-payments',
        loadComponent: () => import('./pages/billing-payments/billing-payments.component').then(m => m.BillingPaymentsComponent),
        title: 'Billing & Payments | WMA Online Services Portal'
      },
      {
        path: 'license-application',
        loadComponent: () => import('./pages/license/license.component').then(m => m.LicenseComponent),
        title: 'License Application | WMA Online Services Portal'
      },
      {
        path: 'license-application',
        loadComponent: () => import('./pages/license/license.component').then(m => m.LicenseComponent),
        title: 'License Application | WMA Online Services Portal'
      },
      {
        path: 'notifications',
        loadComponent: () => import('./pages/notifications/notifications.component').then(m => m.NotificationsComponent),
        title: 'Notifications | WMA Online Services Portal'
      },
      {
        path: 'support-help',
        loadComponent: () => import('./pages/support-help/support-help.component').then(m => m.SupportHelpComponent),
        title: 'Help & Support | WMA Online Services Portal'
      },
      {
        path: 'osa/settings',
        loadComponent: () => import('./pages/osa/license-settings/license-settings.component').then(m => m.LicenseSettingsComponent),
        title: 'License Settings | WMA Online Services Portal'
      },
      {
        path: 'licenseSetting',
        redirectTo: 'osa/settings',
        pathMatch: 'full'
      },
      {
        path: 'document-view/:id',
        loadComponent: () => import('./pages/license-application/document-viewer/document-viewer.component').then(m => m.DocumentViewerComponent),
        title: 'View Document | WMA Portal'
      },
    ]
  },
  {
    path: 'landing',
    loadComponent: () => import('./pages/landing-page/landing-page.component').then(m => m.LandingPageComponent),
    title: 'WMA Online Services Portal'
  },
  {
    path: 'register-selection',
    loadComponent: () => import('./pages/auth-pages/registration-selection/registration-selection.component').then(m => m.RegistrationSelectionComponent),
    title: 'Registration Selection | WMA Online Services Portal'
  },
  {
    path: 'register-practitioner',
    loadComponent: () => import('./pages/auth-pages/practitioner-registration/practitioner-registration.component').then(m => m.PractitionerRegistrationComponent),
    title: 'Practitioner Registration | WMA Online Services Portal'
  },
  {
    path: 'register-pattern-approval',
    loadComponent: () => import('./pages/auth-pages/pattern-approval-registration/pattern-approval-registration.component').then(m => m.PatternApprovalRegistrationComponent),
    title: 'Pattern Approval Registration | WMA Online Services Portal'
  },

  // Pattern Approval Module Routes
  {
    path: 'pattern-approval',
    loadComponent: () => import('./shared/layout/pattern-approval-layout/pattern-approval-layout.component').then(m => m.PatternApprovalLayoutComponent),
    canActivate: [authGuard, userTypeGuard('pattern_approval')],
    children: [
      {
        path: '',
        redirectTo: 'dashboard',
        pathMatch: 'full'
      },
      {
        path: 'dashboard',
        loadComponent: () => import('./pages/pattern-approval/dashboard/pattern-approval-dashboard.component').then(m => m.PatternApprovalDashboardComponent),
        title: 'Dashboard | Pattern Approval - WMA'
      },
      {
        path: 'application',
        loadComponent: () => import('./pages/pattern-approval/application/pattern-approval-application.component').then(m => m.PatternApprovalApplicationComponent),
        title: 'Application | Pattern Approval - WMA'
      },
      // Future Pattern Approval routes will be added here
    ]
  },


  {
    path: 'admin-test',
    loadComponent: () => import('./pages/admin-dashboard/admin-dashboard.component').then(m => m.AdminDashboardComponent),
    title: 'Admin Test Page'
  },
  {
    path: 'admin/login',
    loadComponent: () => import('./pages/admin-login/admin-login.component').then(m => m.AdminLoginComponent),
    title: 'Admin Login'
  },
  {
    path: 'viewApplication/:id',
    loadComponent: () => import('./pages/application-detail/application-detail.component').then(m => m.ApplicationDetailComponent),
    title: 'Application Details'
  },

  // auth pages
  {
    path:'signin',
    component:SignInComponent,
    title:'Angular Sign In Dashboard | TailAdmin - Angular Admin Dashboard Template'
  },
  {
    path: 'auth/forgot-password',
    loadComponent: () => import('./pages/auth-pages/forgot-password/forgot-password.component').then(m => m.ForgotPasswordComponent),
    title: 'Forgot Password | WMA Online Services Portal'
  },
  {
    path:'signup',
    component:SignUpComponent,
    title:'Angular Sign Up Dashboard | TailAdmin - Angular Admin Dashboard Template'
  },
  // error pages
  {
    path:'**',
    component:NotFoundComponent,
    title:'Angular NotFound Dashboard | TailAdmin - Angular Admin Dashboard Template'
  },
];
