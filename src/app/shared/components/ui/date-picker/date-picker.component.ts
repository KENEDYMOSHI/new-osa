import { Component, forwardRef, Input, Output, EventEmitter, OnInit, ElementRef, HostListener } from '@angular/core';
import { ControlValueAccessor, NG_VALUE_ACCESSOR } from '@angular/forms';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { DateUtils } from './date-picker.model';

@Component({
  selector: 'app-date-picker',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './date-picker.component.html',
  styleUrls: ['./date-picker.component.css'],
  providers: [
    {
      provide: NG_VALUE_ACCESSOR,
      useExisting: forwardRef(() => DatePickerComponent),
      multi: true
    }
  ]
})
export class DatePickerComponent implements ControlValueAccessor, OnInit {
  @Input() placeholder: string = 'dd/mm/yyyy';
  @Input() label: string = '';
  @Input() required: boolean = false;
  @Input() selectionMode: 'single' | 'range' = 'single';
  @Output() cleared = new EventEmitter<void>();

  isOpen: boolean = false;
  dropdownPosition: 'position-down' | 'position-up' = 'position-down';
  
  selectedDate: Date | null = null;
  startDate: Date | null = null; // For range mode
  endDate: Date | null = null;   // For range mode
  hoverDate: Date | null = null; // For range preview
  currentMonth: Date = new Date();
  days: (Date | null)[] = [];
  years: number[] = [];
  
  monthNames = [
    'January', 'February', 'March', 'April', 'May', 'June',
    'July', 'August', 'September', 'October', 'November', 'December'
  ];

  weekDays = ['S', 'M', 'T', 'W', 'T', 'F', 'S'];

  private onChange: (value: any) => void = () => {};
  private onTouched: () => void = () => {};

  constructor(private el: ElementRef) {}

  ngOnInit(): void {
    this.generateYears();
    this.generateCalendar();
  }

  @HostListener('window:scroll', ['$event'])
  @HostListener('window:resize', ['$event'])
  onScrollOrResize() {
    if (this.isOpen) {
      this.calculatePosition();
    }
  }

  calculatePosition(): void {
    if (!this.el) return;
    // Delay to allow any opening animation/rendering to happen, but immediate calculation is fine
    setTimeout(() => {
      const rect = this.el.nativeElement.getBoundingClientRect();
      const dropdownHeight = 350; // Approximated height of the calendar dropdown
      const spaceBelow = window.innerHeight - rect.bottom;
      
      // If there's not enough space below AND there is enough space above
      if (spaceBelow < dropdownHeight && rect.top > dropdownHeight) {
        this.dropdownPosition = 'position-up';
      } else {
        this.dropdownPosition = 'position-down';
      }
    });
  }

  writeValue(value: any): void {
    if (value) {
      if (this.selectionMode === 'single') {
        this.selectedDate = new Date(value);
        this.currentMonth = new Date(value);
      } else {
        // Assume value is "YYYY-MM-DD to YYYY-MM-DD" string for range
        const parts = value.split(' to ');
        if (parts.length >= 1) this.startDate = new Date(parts[0]);
        if (parts.length >= 2) this.endDate = new Date(parts[1]);
        if (this.startDate) this.currentMonth = new Date(this.startDate);
      }
      this.generateCalendar();
    }
  }

  registerOnChange(fn: any): void {
    this.onChange = fn;
  }

  registerOnTouched(fn: any): void {
    this.onTouched = fn;
  }

  toggleCalendar(): void {
    this.isOpen = !this.isOpen;
    if (this.isOpen) {
      this.calculatePosition();
    } else {
      this.onTouched();
    }
  }

  generateCalendar(): void {
    const year = this.currentMonth.getFullYear();
    const month = this.currentMonth.getMonth();
    const firstDay = new Date(year, month, 1);
    const lastDay = new Date(year, month + 1, 0);
    const daysInMonth = lastDay.getDate();
    const startingDayOfWeek = firstDay.getDay();

    this.days = [];

    for (let i = 0; i < startingDayOfWeek; i++) {
      this.days.push(null);
    }

    for (let day = 1; day <= daysInMonth; day++) {
      this.days.push(new Date(year, month, day));
    }
  }

  generateYears(): void {
    const currentYear = new Date().getFullYear();
    for (let i = currentYear + 10; i >= currentYear - 100; i--) {
      this.years.push(i);
    }
  }

  onMonthChange(event: any): void {
    const monthIndex = parseInt(event.target.value, 10);
    this.currentMonth = new Date(this.currentMonth.getFullYear(), monthIndex, 1);
    this.generateCalendar();
  }

  onYearChange(event: any): void {
    const year = parseInt(event.target.value, 10);
    this.currentMonth = new Date(year, this.currentMonth.getMonth(), 1);
    this.generateCalendar();
  }

  previousMonth(): void {
    this.currentMonth = new Date(
      this.currentMonth.getFullYear(),
      this.currentMonth.getMonth() - 1
    );
    this.generateCalendar();
  }

  nextMonth(): void {
    this.currentMonth = new Date(
      this.currentMonth.getFullYear(),
      this.currentMonth.getMonth() + 1
    );
    this.generateCalendar();
  }

  selectDate(date: Date | null): void {
    if (!date) return;

    if (this.selectionMode === 'single') {
      this.selectedDate = date;
      const y = date.getFullYear();
      const m = (date.getMonth() + 1).toString().padStart(2, '0');
      const d = date.getDate().toString().padStart(2, '0');
      this.onChange(`${y}-${m}-${d}`);
      this.isOpen = false;
    } else {
      // Range Mode Logic
      if (!this.startDate || (this.startDate && this.endDate)) {
        // Start new range
        this.startDate = date;
        this.endDate = null;
      } else if (this.startDate && !this.endDate) {
        // Complete range
        if (date < this.startDate) {
          this.endDate = this.startDate;
          this.startDate = date;
        } else {
          this.endDate = date;
        }
        
        // Emit value
        const sY = this.startDate!.getFullYear();
        const sM = (this.startDate!.getMonth() + 1).toString().padStart(2, '0');
        const sD = this.startDate!.getDate().toString().padStart(2, '0');
        
        const eY = this.endDate!.getFullYear();
        const eM = (this.endDate!.getMonth() + 1).toString().padStart(2, '0');
        const eD = this.endDate!.getDate().toString().padStart(2, '0');
        
        this.onChange(`${sY}-${sM}-${sD} to ${eY}-${eM}-${eD}`);
        this.isOpen = false;
      }
    }
    this.onTouched();
  }

  formatDate(date: Date | null): string {
    if (!date) return '';
    const day = String(date.getDate()).padStart(2, '0');
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const year = date.getFullYear();
    return `${day}/${month}/${year}`;
  }

  onDateHover(date: Date | null): void {
    if (this.selectionMode === 'range' && this.startDate && !this.endDate) {
      this.hoverDate = date;
    } else {
      this.hoverDate = null;
    }
  }

  isSameDay(date1: Date | null, date2: Date | null): boolean {
    if (!date1 || !date2) return false;
    return (
      date1.getDate() === date2.getDate() &&
      date1.getMonth() === date2.getMonth() &&
      date1.getFullYear() === date2.getFullYear()
    );
  }

  isToday(date: Date | null): boolean {
    if (!date) return false;
    const today = new Date();
    return this.isSameDay(date, today);
  }

  isInRange(date: Date | null): boolean {
    if (!date || this.selectionMode !== 'range' || !this.startDate) return false;
    
    // Finalized range
    if (this.endDate) {
      return date > this.startDate && date < this.endDate;
    }
    
    // Preview range (during selection)
    if (this.hoverDate) {
      const start = this.startDate < this.hoverDate ? this.startDate : this.hoverDate;
      const end = this.startDate < this.hoverDate ? this.hoverDate : this.startDate;
      return date > start && date < end;
    }
    
    return false;
  }

  isRangeStart(date: Date | null): boolean {
    if (!date || this.selectionMode !== 'range' || !this.startDate) return false;
    return this.isSameDay(date, this.startDate);
  }

  isRangeEnd(date: Date | null): boolean {
    if (!date || this.selectionMode !== 'range' || !this.endDate) return false;
    return this.isSameDay(date, this.endDate);
  }

  get displayValue(): string {
    if (this.selectionMode === 'single') {
      return this.formatDate(this.selectedDate);
    } else {
      if (this.startDate && this.endDate) {
        return `${this.formatDate(this.startDate)} - ${this.formatDate(this.endDate)}`;
      } else if (this.startDate) {
        return `${this.formatDate(this.startDate)} - ...`;
      }
      return '';
    }
  }

  get currentMonthYear(): string {
    return `${this.monthNames[this.currentMonth.getMonth()]} ${this.currentMonth.getFullYear()}`;
  }

  /**
   * Clear the current selection and notify parent
   */
  clearSelection(): void {
    if (this.selectionMode === 'single') {
      this.selectedDate = null;
    } else {
      this.startDate = null;
      this.endDate = null;
      this.hoverDate = null;
    }
    this.onChange(null);
    this.cleared.emit();
  }

  /**
   * Check if there is any selection
   */
  get hasSelection(): boolean {
    if (this.selectionMode === 'single') {
      return this.selectedDate !== null;
    } else {
      return this.startDate !== null || this.endDate !== null;
    }
  }
}
