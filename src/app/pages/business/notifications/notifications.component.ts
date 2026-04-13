import { CommonModule } from '@angular/common';
import { Component } from '@angular/core';
import { RouterModule } from '@angular/router';
import { TranslateModule, TranslateService } from '@ngx-translate/core';

interface NotificationItem {
  id: string;
  type: 'success' | 'info' | 'warning' | 'error';
  titleKey: string;
  bodyKey: string;
  timeKey: string;
  isRead: boolean;
  isImportant: boolean;
  iconType: string;
  link?: string;
}

@Component({
  selector: 'app-business-notifications',
  standalone: true,
  imports: [CommonModule, RouterModule, TranslateModule],
  template: `
  <div class="min-h-screen bg-gray-50 pb-16 dark:bg-gray-950">
    <div class="mx-auto max-w-5xl px-4 pt-6 sm:px-6">
      
      <!-- HEADER -->
      <div class="mb-8 flex flex-col sm:flex-row sm:items-end justify-between gap-4">
        <div>
          <nav aria-label="Breadcrumb">
            <ol class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
              <li>
                <a routerLink="/business/dashboard" class="transition-colors hover:text-gray-900 dark:hover:text-white">
                  {{ 'BUSINESS.SIDEBAR.DASHBOARD' | translate }}
                </a>
              </li>
              <li aria-hidden="true" class="text-gray-300 dark:text-gray-600">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="m9 18l6 -6l-6 -6" />
                </svg>
              </li>
              <li class="font-medium text-gray-900 dark:text-white">{{ 'BUSINESS.NOTIFICATIONS.BREADCRUMB' | translate }}</li>
            </ol>
          </nav>
          <div class="mt-3 flex items-center gap-3">
            <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
              {{ 'BUSINESS.NOTIFICATIONS.PAGE_TITLE' | translate }}
            </h1>
            <span *ngIf="unreadCount > 0" class="inline-flex items-center rounded-full bg-[#F7941D] px-2.5 py-0.5 text-xs font-semibold text-white shadow-sm">
              {{ unreadCount }} {{ 'BUSINESS.NOTIFICATIONS.NEW' | translate }}
            </span>
          </div>
        </div>
        <button (click)="markAllAsRead()" class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm transition-all hover:bg-gray-50 hover:text-gray-900 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:bg-gray-800 dark:hover:text-white">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-emerald-500"><path d="M18 6 7 17l-5-5"/><path d="m22 10-7.5 7.5L13 16"/></svg>
          {{ 'BUSINESS.NOTIFICATIONS.MARK_ALL_READ' | translate }}
        </button>
      </div>

      <!-- MAIN CONTENT AREA -->
      <div class="flex flex-col lg:flex-row gap-8">
        
        <!-- SIDEBAR FILTERS (Desktop) -->
        <div class="hidden lg:block w-56 shrink-0">
          <div class="sticky top-24 rounded-2xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <nav class="space-y-1.5">
              <button (click)="filterBy('all')" [ngClass]="currentFilter === 'all' ? 'bg-[#FFF7ED] text-[#F7941D] dark:bg-[#F7941D]/10' : 'text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-gray-800/50'" class="flex w-full items-center justify-between rounded-xl px-3 py-2 text-sm font-medium transition-colors">
                <span>{{ 'BUSINESS.NOTIFICATIONS.FILTER_ALL' | translate }}</span>
                <span class="rounded-full bg-gray-100 px-2 py-0.5 text-xs text-gray-500 dark:bg-gray-800 dark:text-gray-400">{{ notifications.length }}</span>
              </button>
              <button (click)="filterBy('unread')" [ngClass]="currentFilter === 'unread' ? 'bg-[#FFF7ED] text-[#F7941D] dark:bg-[#F7941D]/10' : 'text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-gray-800/50'" class="flex w-full items-center justify-between rounded-xl px-3 py-2 text-sm font-medium transition-colors">
                <span>{{ 'BUSINESS.NOTIFICATIONS.FILTER_UNREAD' | translate }}</span>
                <span *ngIf="unreadCount > 0" class="rounded-full bg-[#F7941D] px-2 py-0.5 text-xs text-white">{{ unreadCount }}</span>
              </button>
              <button (click)="filterBy('important')" [ngClass]="currentFilter === 'important' ? 'bg-[#FFF7ED] text-[#F7941D] dark:bg-[#F7941D]/10' : 'text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-gray-800/50'" class="flex w-full items-center justify-between rounded-xl px-3 py-2 text-sm font-medium transition-colors">
                <span>{{ 'BUSINESS.NOTIFICATIONS.FILTER_IMPORTANT' | translate }}</span>
                <span class="rounded-full bg-gray-100 px-2 py-0.5 text-xs text-gray-500 dark:bg-gray-800 dark:text-gray-400">{{ importantCount }}</span>
              </button>
            </nav>
          </div>
        </div>

        <!-- MOBILE FILTERS -->
        <div class="lg:hidden flex overflow-x-auto pb-2 scrollbar-hide gap-2">
          <button (click)="filterBy('all')" [ngClass]="currentFilter === 'all' ? 'bg-[#F7941D] text-white border-transparent' : 'bg-white text-gray-700 border-gray-200 dark:bg-gray-900 dark:text-gray-300 dark:border-gray-800'" class="whitespace-nowrap rounded-xl border px-4 py-2 text-sm font-medium transition-colors shadow-sm">
            {{ 'BUSINESS.NOTIFICATIONS.FILTER_ALL' | translate }} ({{ notifications.length }})
          </button>
          <button (click)="filterBy('unread')" [ngClass]="currentFilter === 'unread' ? 'bg-[#F7941D] text-white border-transparent' : 'bg-white text-gray-700 border-gray-200 dark:bg-gray-900 dark:text-gray-300 dark:border-gray-800'" class="whitespace-nowrap rounded-xl border px-4 py-2 text-sm font-medium transition-colors shadow-sm inline-flex items-center gap-1">
            {{ 'BUSINESS.NOTIFICATIONS.FILTER_UNREAD' | translate }} 
            <span *ngIf="unreadCount > 0" class="ml-1 rounded-full bg-red-500 px-1.5 py-0.5 text-[10px] text-white">{{ unreadCount }}</span>
          </button>
          <button (click)="filterBy('important')" [ngClass]="currentFilter === 'important' ? 'bg-[#F7941D] text-white border-transparent' : 'bg-white text-gray-700 border-gray-200 dark:bg-gray-900 dark:text-gray-300 dark:border-gray-800'" class="whitespace-nowrap rounded-xl border px-4 py-2 text-sm font-medium transition-colors shadow-sm">
            {{ 'BUSINESS.NOTIFICATIONS.FILTER_IMPORTANT' | translate }}
          </button>
        </div>

        <!-- NOTIFICATION LIST -->
        <div class="flex-1 space-y-3">
          
          <ng-container *ngIf="filteredNotifications.length > 0; else noData">
            <div *ngFor="let item of filteredNotifications" class="group relative flex flex-col sm:flex-row gap-4 items-start sm:items-center rounded-2xl border bg-white p-5 text-left transition-all duration-300"
              [ngClass]="item.isRead ? 'border-gray-200 bg-white hover:border-[#F7941D]/40 hover:shadow-sm dark:border-gray-800 dark:bg-gray-900 dark:hover:border-[#F7941D]/40' : 'border-[#F7941D]/30 bg-[#FFF7ED]/60 shadow-sm hover:shadow-md dark:bg-[#F7941D]/10 dark:border-[#F7941D]/40'">
              
              <!-- Icon -->
              <div class="mt-0.5 flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gray-50 text-gray-400 transition-colors group-hover:bg-[#FFF7ED] group-hover:text-[#F7941D] dark:bg-gray-800 dark:group-hover:bg-[#F7941D]/20">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg>
              </div>

              <!-- Content -->
              <div class="min-w-0 flex-1">
                <div class="flex items-center justify-between gap-2 mb-1">
                  <div class="flex items-center gap-2">
                    <p class="text-[15px] font-semibold text-gray-900 dark:text-white transition-colors" [ngClass]="!item.isRead ? 'text-[#F7941D] dark:text-[#F7941D]' : ''">
                      {{ item.titleKey | translate }}
                    </p>
                    <svg *ngIf="item.isImportant" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="currentColor" class="text-red-500"><path d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z"/></svg>
                  </div>
                  <span class="shrink-0 text-xs text-gray-500 font-medium whitespace-nowrap">{{ item.timeKey | translate }}</span>
                </div>
                <p class="text-sm text-gray-600 leading-relaxed dark:text-gray-400" [ngClass]="{'opacity-80': item.isRead}">
                  {{ item.bodyKey | translate }}
                </p>
                
                <!-- Action Link (Optional) -->
                <div *ngIf="item.link" class="mt-3">
                  <a [routerLink]="item.link" class="inline-flex items-center gap-1.5 text-sm font-semibold text-[#F7941D] hover:text-[#E6820A] transition-colors">
                    {{ 'BUSINESS.NOTIFICATIONS.VIEW_DETAILS' | translate }}
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                  </a>
                </div>
              </div>
              
              <!-- Mark as read tick (Hover only) -->
              <div *ngIf="!item.isRead" class="absolute right-4 top-4 sm:top-1/2 sm:-translate-y-1/2 opacity-0 transition-opacity duration-300 group-hover:opacity-100 hidden sm:block">
                <button (click)="markAsRead(item.id)" class="flex h-8 w-8 items-center justify-center rounded-full bg-gray-100 text-gray-500 hover:bg-emerald-100 hover:text-emerald-600 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-emerald-500/20 dark:hover:text-emerald-400 transition-colors" title="Mark as read">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                </button>
              </div>

            </div>
          </ng-container>

          <ng-template #noData>
            <div class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-gray-300 bg-white py-16 px-6 text-center dark:border-gray-800 dark:bg-gray-900/50">
              <div class="flex h-16 w-16 items-center justify-center rounded-full bg-gray-50 text-gray-400 dark:bg-gray-800 dark:text-gray-500 mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.26 3.27A6.6 6.6 0 0 1 12 3c7.2 0 9 1.8 9 9 0 2.27-.14 4.09-.59 5.48"/><path d="M3.27 10.26A76.7 76.7 0 0 0 3 12c0 7.2 1.8 9 9 9 2.27 0 4.09-.14 5.48-.59"/><path d="M21.13 21.13A76 76 0 0 1 12 21c-7.2 0-9-1.8-9-9 0-2.27.14-4.09.59-5.48"/><path d="m2 2 20 20"/></svg>
              </div>
              <h3 class="text-sm font-semibold text-gray-900 dark:text-white">{{ 'BUSINESS.NOTIFICATIONS.NO_NOTIFICATIONS' | translate }}</h3>
              <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ 'BUSINESS.NOTIFICATIONS.NO_NOTIFICATIONS_DESC' | translate }}</p>
            </div>
          </ng-template>

        </div>
      </div>

    </div>
  </div>
  `
})
export class NotificationsComponent {
  currentFilter: 'all' | 'unread' | 'important' = 'all';

  // Dummy notifications
  notifications: NotificationItem[] = [
    {
      id: '1',
      type: 'success',
      iconType: 'verified',
      titleKey: 'BUSINESS.NOTIFICATIONS.NOTIF_VERIFIED_TITLE',
      bodyKey: 'BUSINESS.NOTIFICATIONS.NOTIF_VERIFIED_BODY',
      timeKey: 'BUSINESS.NOTIFICATIONS.JUST_NOW',
      isRead: false,
      isImportant: true,
      link: '/business/equipments'
    },
    {
      id: '2',
      type: 'info',
      iconType: 'tech',
      titleKey: 'BUSINESS.NOTIFICATIONS.NOTIF_TECH_REQ_TITLE',
      bodyKey: 'BUSINESS.NOTIFICATIONS.NOTIF_TECH_REQ_BODY',
      timeKey: 'BUSINESS.NOTIFICATIONS.MINS_AGO', // Just use mins ago for dummy mapping to look cleanly translated
      isRead: false,
      isImportant: false
    },
    {
      id: '3',
      type: 'warning',
      iconType: 'bill',
      titleKey: 'BUSINESS.NOTIFICATIONS.NOTIF_BILL_TITLE',
      bodyKey: 'BUSINESS.NOTIFICATIONS.NOTIF_BILL_BODY',
      timeKey: 'BUSINESS.NOTIFICATIONS.HOURS_AGO',
      isRead: true,
      isImportant: true,
      link: '/business/billing-payments'
    },
    {
      id: '4',
      type: 'success',
      iconType: 'cert',
      titleKey: 'BUSINESS.NOTIFICATIONS.NOTIF_CERT_TITLE',
      bodyKey: 'BUSINESS.NOTIFICATIONS.NOTIF_CERT_BODY',
      timeKey: 'BUSINESS.NOTIFICATIONS.YESTERDAY',
      isRead: true,
      isImportant: false
    },
    {
      id: '5',
      type: 'error',
      iconType: 'reject',
      titleKey: 'BUSINESS.NOTIFICATIONS.NOTIF_REJECTED_TITLE',
      bodyKey: 'BUSINESS.NOTIFICATIONS.NOTIF_REJECTED_BODY',
      timeKey: '3 BUSINESS.NOTIFICATIONS.DAYS_AGO',
      isRead: true,
      isImportant: true
    }
  ];

  get unreadCount(): number {
    return this.notifications.filter(n => !n.isRead).length;
  }

  get importantCount(): number {
    return this.notifications.filter(n => n.isImportant).length;
  }

  get filteredNotifications(): NotificationItem[] {
    if (this.currentFilter === 'unread') {
      return this.notifications.filter(n => !n.isRead);
    }
    if (this.currentFilter === 'important') {
      return this.notifications.filter(n => n.isImportant);
    }
    return this.notifications;
  }

  filterBy(filter: 'all' | 'unread' | 'important') {
    this.currentFilter = filter;
  }

  markAsRead(id: string) {
    const notif = this.notifications.find(n => n.id === id);
    if (notif) {
      notif.isRead = true;
    }
  }

  markAllAsRead() {
    this.notifications.forEach(n => n.isRead = true);
  }
}
