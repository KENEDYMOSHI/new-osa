import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { LicenseService } from '../../services/license.service';

@Component({
  selector: 'app-notifications',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './notifications.component.html'
})
export class NotificationsComponent implements OnInit {
  notifications: any[] = [];
  loading = true;
  activeTab: 'all' | 'unread' | 'starred' = 'all';
  filteredNotifications: any[] = [];

  constructor(private licenseService: LicenseService) {}

  ngOnInit() {
    this.loadNotifications();
  }

  loadNotifications() {
    this.loading = true;
    this.licenseService.getUserNotifications().subscribe({
      next: (data) => {
        // Initialize starred and flagged properties if not present
        this.notifications = data.map((n: any) => ({
          ...n,
          starred: n.starred || false,
          flagged: n.flagged || false
        }));
        this.filterNotifications();
        this.loading = false;
      },
      error: (err) => {
        console.error('Failed to load notifications', err);
        this.loading = false;
      }
    });
  }

  switchTab(tab: 'all' | 'unread' | 'starred') {
    this.activeTab = tab;
    this.filterNotifications();
  }

  filterNotifications() {
    if (this.activeTab === 'all') {
      this.filteredNotifications = this.notifications;
    } else if (this.activeTab === 'unread') {
      this.filteredNotifications = this.notifications.filter(n => !n.is_read);
    } else if (this.activeTab === 'starred') {
      this.filteredNotifications = this.notifications.filter(n => n.starred);
    }
  }

  get unreadCount(): number {
    return this.notifications.filter(n => !n.is_read).length;
  }

  get starredCount(): number {
    return this.notifications.filter(n => n.starred).length;
  }

  markAsRead(notification: any) {
    if (!notification.is_read) {
      this.licenseService.markNotificationAsRead(notification.id).subscribe({
        next: () => {
          notification.is_read = 1; // Update local state
          this.filterNotifications(); // Refresh filtered list
        },
        error: (err) => console.error('Failed to mark as read', err)
      });
    }
  }

  toggleStar(notification: any) {
    notification.starred = !notification.starred;
    this.filterNotifications();
    // TODO: Persist to backend if needed
  }

  toggleFlag(notification: any) {
    notification.flagged = !notification.flagged;
    // TODO: Persist to backend if needed
  }

  deleteNotification(notification: any) {
    if (confirm('Are you sure you want to delete this notification?')) {
      const index = this.notifications.indexOf(notification);
      if (index > -1) {
        this.notifications.splice(index, 1);
        this.filterNotifications();
      }
      // TODO: Persist to backend if needed
    }
  }
}
