import { CommonModule } from '@angular/common';
import { Component, Input, Output, EventEmitter, ElementRef, ViewChild, forwardRef } from '@angular/core';
import { ControlValueAccessor, NG_VALUE_ACCESSOR } from '@angular/forms';
import { LabelComponent } from '../label/label.component';

@Component({
  selector: 'app-date-picker',
  standalone: true,
  imports: [CommonModule, LabelComponent],
  templateUrl: './date-picker.component.html',
  styles: `
    :host {
      display: block;
    }
  `,
  providers: [
    {
      provide: NG_VALUE_ACCESSOR,
      useExisting: forwardRef(() => DatePickerComponent),
      multi: true
    }
  ]
})
export class DatePickerComponent implements ControlValueAccessor {

  @Input() id!: string;
  @Input() label?: string;
  @Input() placeholder: string = 'Select date';
  
  @Output() dateChange = new EventEmitter<string>();

  value: string = '';
  showModal = false;

  // Calendar State
  currentDate = new Date();
  currentMonth = new Date().getMonth();
  currentYear = new Date().getFullYear();
  
  // Views: 'calendar' or 'year'
  view: 'calendar' | 'year' = 'calendar';
  
  daysInMonth: number[] = [];
  paddingDays: number[] = [];
  
  years: number[] = [];
  
  weekDays = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
  months = [
    'January', 'February', 'March', 'April', 'May', 'June',
    'July', 'August', 'September', 'October', 'November', 'December'
  ];

  onChange: any = () => {};
  onTouched: any = () => {};

  writeValue(value: string): void {
      this.value = value;
      if (value) {
          const date = new Date(value);
          if (!isNaN(date.getTime())) {
              this.currentMonth = date.getMonth();
              this.currentYear = date.getFullYear();
              this.generateCalendar();
          }
      }
      this.generateYears();
  }

  registerOnChange(fn: any): void {
    this.onChange = fn;
  }

  registerOnTouched(fn: any): void {
    this.onTouched = fn;
  }

  openModal() {
      this.showModal = true;
      this.view = 'calendar';
      this.generateCalendar();
      this.generateYears();
  }

  closeModal() {
      this.showModal = false;
      this.onTouched();
  }

  generateCalendar() {
      const firstDay = new Date(this.currentYear, this.currentMonth, 1);
      const lastDay = new Date(this.currentYear, this.currentMonth + 1, 0);
      
      const daysCount = lastDay.getDate();
      const paddingCount = firstDay.getDay();

      this.daysInMonth = Array.from({ length: daysCount }, (_, i) => i + 1);
      this.paddingDays = Array.from({ length: paddingCount }, (_, i) => i);
  }
  
  generateYears() {
      const current = new Date().getFullYear();
      const start = current - 100;
      const end = current + 20;
      this.years = [];
      for (let y = start; y <= end; y++) {
          this.years.push(y);
      }
      // Reverse to show latest years first if desired, or keep ascending.
      this.years.reverse(); 
  }

  toggleView() {
      this.view = this.view === 'calendar' ? 'year' : 'calendar';
  }

  selectYear(year: number) {
      this.currentYear = year;
      this.view = 'calendar';
      this.generateCalendar();
  }

  prevMonth() {
      if (this.currentMonth === 0) {
          this.currentMonth = 11;
          this.currentYear--;
      } else {
          this.currentMonth--;
      }
      this.generateCalendar();
  }

  nextMonth() {
      if (this.currentMonth === 11) {
          this.currentMonth = 0;
          this.currentYear++;
      } else {
          this.currentMonth++;
      }
      this.generateCalendar();
  }

  selectDate(day: number) {
      const date = new Date(this.currentYear, this.currentMonth, day);
      // Format YYYY-MM-DD (safe for HTML date inputs and usually expected by backends)
      // Manually adjusting for timezone offset can be tricky, let's use simple string concat
      const y = date.getFullYear();
      const m = (date.getMonth() + 1).toString().padStart(2, '0');
      const d = day.toString().padStart(2, '0');
      
      const dateStr = `${y}-${m}-${d}`;
      
      this.value = dateStr;
      this.onChange(this.value);
      this.dateChange.emit(this.value);
      this.closeModal();
  }

  isSelected(day: number): boolean {
      if (!this.value) return false;
      const [y, m, d] = this.value.split('-').map(Number);
      return y === this.currentYear && (m - 1) === this.currentMonth && d === day;
  }

  // Helper for Template
  newDate(year: number, month: number, day: number): Date {
      return new Date(year, month, day);
  }
}
