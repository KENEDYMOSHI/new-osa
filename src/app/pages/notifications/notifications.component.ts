import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { NotificationService, Notification } from '../../services/notification.service';

interface Message {
  id: string;
  sender: string;
  initials: string;
  subject: string;
  preview: string;
  content: string;
  date: string;
  time: string;
  source: string;
  email: string;
  phone: string;
  isRead: boolean;
  isStarred: boolean;
}

@Component({
  selector: 'app-notifications',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './notifications.component.html',
  styleUrls: ['./notifications.component.css']
})
export class NotificationsComponent implements OnInit {
  messages: Message[] = [];
  filteredMessages: Message[] = [];
  selectedMessage: Message | null = null;
  activeFilter: 'all' | 'unread' | 'read' | 'starred' = 'all';
  
  constructor(private notificationService: NotificationService) {}

  ngOnInit(): void {
    this.loadNotifications();
  }

  loadNotifications(): void {
    this.notificationService.notifications$.subscribe(notifications => {
      this.messages = notifications.map((notif, index) => ({
        id: notif.id,
        sender: this.extractSenderName(notif.title),
        initials: this.getInitials(this.extractSenderName(notif.title)),
        subject: notif.title,
        preview: notif.message.substring(0, 50) + '...',
        content: notif.message.replace('Reason:', '<br><br><strong class="font-bold text-gray-900">Reason:</strong>'),
        date: this.formatDate(notif.created_at),
        time: this.formatTime(notif.created_at),
        source: 'ONLINE SEARCH',
        email: 'user@mailinator.com',
        phone: '+1 (107) 477-7849',
        isRead: notif.is_read,
        isStarred: false
      }));
      
      this.applyFilter();
      
      // Select first message by default
      if (this.filteredMessages.length > 0 && !this.selectedMessage) {
        this.selectMessage(this.filteredMessages[0]);
      }
    });
    
    this.notificationService.fetchNotifications();
  }

  extractSenderName(title: string): string {
    // Extract name from title or use default
    const match = title.match(/^([A-Za-z\s]+)/);
    return match ? match[1].trim() : 'System Notification';
  }

  getInitials(name: string): string {
    const parts = name.split(' ');
    if (parts.length >= 2) {
      return (parts[0][0] + parts[1][0]).toUpperCase();
    }
    return name.substring(0, 2).toUpperCase();
  }

  formatDate(dateString: string): string {
    const date = new Date(dateString);
    const today = new Date();
    
    if (date.toDateString() === today.toDateString()) {
      return 'Today';
    }
    
    const options: Intl.DateTimeFormatOptions = { month: 'short', day: 'numeric' };
    return date.toLocaleDateString('en-US', options);
  }

  formatTime(dateString: string): string {
    const date = new Date(dateString);
    const hours = date.getHours();
    const minutes = date.getMinutes().toString().padStart(2, '0');
    return `${hours}:${minutes}`;
  }

  selectMessage(message: Message): void {
    this.selectedMessage = message;
    
    // Mark as read
    if (!message.isRead) {
      message.isRead = true;
      this.notificationService.markAsRead(message.id).subscribe();
    }
  }

  toggleStar(message: Message, event: Event): void {
    event.stopPropagation();
    message.isStarred = !message.isStarred;
  }

  setFilter(filter: 'all' | 'unread' | 'read' | 'starred'): void {
    this.activeFilter = filter;
    this.applyFilter();
  }

  applyFilter(): void {
    switch (this.activeFilter) {
      case 'unread':
        this.filteredMessages = this.messages.filter(m => !m.isRead);
        break;
      case 'read':
        this.filteredMessages = this.messages.filter(m => m.isRead);
        break;
      case 'starred':
        this.filteredMessages = this.messages.filter(m => m.isStarred);
        break;
      default:
        this.filteredMessages = [...this.messages];
    }
    
    // Update selected message if it's filtered out
    if (this.selectedMessage && !this.filteredMessages.find(m => m.id === this.selectedMessage!.id)) {
      this.selectedMessage = this.filteredMessages.length > 0 ? this.filteredMessages[0] : null;
    }
  }

  get unreadCount(): number {
    return this.messages.filter(m => !m.isRead).length;
  }

  get readCount(): number {
    return this.messages.filter(m => m.isRead).length;
  }

  get starredCount(): number {
    return this.messages.filter(m => m.isStarred).length;
  }
}
