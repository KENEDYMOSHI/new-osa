import { CommonModule } from '@angular/common';
import { Component, OnInit } from '@angular/core';
import { RouterModule, Router } from '@angular/router';
import { DropdownComponent } from '../../ui/dropdown/dropdown.component';
import { DropdownItemComponent } from '../../ui/dropdown/dropdown-item/dropdown-item.component';
import { NotificationService, Notification } from '../../../../services/notification.service';

@Component({
  selector: 'app-notification-dropdown',
  templateUrl: './notification-dropdown.component.html',
  standalone: true,
  imports: [CommonModule, RouterModule, DropdownComponent, DropdownItemComponent]
})
export class NotificationDropdownComponent implements OnInit {
  isOpen = false;
  notifications: Notification[] = [];
  unreadCount = 0;

  constructor(
    private notificationService: NotificationService,
    private router: Router
  ) {}

  ngOnInit() {
    this.notificationService.notifications$.subscribe(notes => {
      this.notifications = notes;
      this.unreadCount = this.notificationService.getUnreadCount();
    });

    // Initial fetch
    this.notificationService.fetchNotifications();
    
    // Poll every 30 seconds
    setInterval(() => {
      this.notificationService.fetchNotifications();
    }, 30000);
  }

  toggleDropdown() {
    this.isOpen = !this.isOpen;
    if (this.isOpen) {
      this.notificationService.fetchNotifications();
    }
  }

  closeDropdown() {
    this.isOpen = false;
  }

  onNotificationClick(notification: Notification) {
    if (!notification.is_read) {
      this.notificationService.markAsRead(notification.id).subscribe();
    }

    this.closeDropdown();

    // Navigate based on type
    if (notification.type === 'document_returned') {
      // Assuming route structure for editing documents
      // We might need to adjust this depending on the route for document upload
      // For now, let's assume it's /license/documents or similar, or redirect to profile
      // Ideally we want to go deep link to the document upload for that application
      // Let's try redirecting to the license wizard step if possible, or 'my-applications'
      this.router.navigate(['/my-applications']); 
    } else if (notification.type === 'application_returned') {
      this.router.navigate(['/my-applications']);
    }
  }

  formatDate(dateStr: string): string {
    const date = new Date(dateStr);
    const now = new Date();
    const diffMs = now.getTime() - date.getTime();
    const diffMins = Math.round(diffMs / 60000);
    const diffHrs = Math.round(diffMs / 3600000);
    
    if (diffMins < 60) return `${diffMins} min ago`;
    if (diffHrs < 24) return `${diffHrs} hr ago`;
    return date.toLocaleDateString();
  }
}