import {
  Component,
  EventEmitter,
  HostListener,
  Input,
  OnInit,
  Output,
} from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

export interface DateRange {
  from: Date | null;
  to: Date | null;
}

@Component({
  selector: 'app-date-range-picker',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './date-range-picker.component.html',
  styleUrl: './date-range-picker.component.css',
})
export class DateRangePickerComponent implements OnInit {
  @Input() label = 'Date Range';
  @Output() rangeSelected = new EventEmitter<DateRange>();
  @Output() enabledChange = new EventEmitter<boolean>();

  @Input() set forceEnabled(val: boolean) {
    this.enabled = val;
    if (!val) this.isOpen = false;
  }

  isOpen = false;
  enabled = false;

  // Two visible months
  leftYear = 0;
  leftMonth = 0; // 0-based
  rightYear = 0;
  rightMonth = 0;

  // Selection state
  hoverDate: Date | null = null;
  tempFrom: Date | null = null;
  tempTo: Date | null = null;

  // Committed range
  from: Date | null = null;
  to: Date | null = null;

  readonly DAYS = ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'];
  readonly MONTHS = [
    'Jan','Feb','Mar','Apr','May','Jun',
    'Jul','Aug','Sep','Oct','Nov','Dec',
  ];

  ngOnInit() {
    const today = new Date();
    this.leftMonth = today.getMonth();
    this.leftYear = today.getFullYear();
    this.updateRight();
  }

  /** Update right calendar to be one month after left */
  updateRight() {
    if (this.leftMonth === 11) {
      this.rightMonth = 0;
      this.rightYear = this.leftYear + 1;
    } else {
      this.rightMonth = this.leftMonth + 1;
      this.rightYear = this.leftYear;
    }
  }

  get displayValue(): string {
    if (!this.from && !this.to) return '';
    const fmt = (d: Date) =>
      `${String(d.getMonth() + 1).padStart(2, '0')}/${String(d.getDate()).padStart(2, '0')}/${d.getFullYear()}`;
    if (this.from && this.to) return `${fmt(this.from)} - ${fmt(this.to)}`;
    if (this.from) return `${fmt(this.from)} - ...`;
    return '';
  }

  toggle() {
    if (!this.enabled) return;
    this.isOpen = !this.isOpen;
    if (this.isOpen) {
      // pre-fill temp from committed
      this.tempFrom = this.from;
      this.tempTo = this.to;
    }
  }

  prevMonth() {
    if (this.leftMonth === 0) { this.leftMonth = 11; this.leftYear--; }
    else { this.leftMonth--; }
    this.updateRight();
  }

  nextMonth() {
    if (this.leftMonth === 11) { this.leftMonth = 0; this.leftYear++; }
    else { this.leftMonth++; }
    this.updateRight();
  }

  /** Build the grid of day cells for a given month/year */
  buildGrid(year: number, month: number): (Date | null)[][] {
    const firstDay = new Date(year, month, 1).getDay();
    const daysInMonth = new Date(year, month + 1, 0).getDate();
    const cells: (Date | null)[] = [];

    for (let i = 0; i < firstDay; i++) cells.push(null);
    for (let d = 1; d <= daysInMonth; d++) cells.push(new Date(year, month, d));

    // Fill to complete week
    while (cells.length % 7 !== 0) cells.push(null);

    const rows: (Date | null)[][] = [];
    for (let i = 0; i < cells.length; i += 7) rows.push(cells.slice(i, i + 7));
    return rows;
  }

  get leftGrid() { return this.buildGrid(this.leftYear, this.leftMonth); }
  get rightGrid() { return this.buildGrid(this.rightYear, this.rightMonth); }

  onDayClick(date: Date) {
    if (!this.tempFrom || (this.tempFrom && this.tempTo)) {
      // Start new selection
      this.tempFrom = date;
      this.tempTo = null;
    } else {
      // Second click
      if (date < this.tempFrom) {
        this.tempTo = this.tempFrom;
        this.tempFrom = date;
      } else {
        this.tempTo = date;
      }
    }
  }

  onDayHover(date: Date) {
    if (this.tempFrom && !this.tempTo) this.hoverDate = date;
  }

  isSelected(date: Date): boolean {
    return (
      (this.tempFrom !== null && this.sameDay(date, this.tempFrom)) ||
      (this.tempTo !== null && this.sameDay(date, this.tempTo))
    );
  }

  isInRange(date: Date): boolean {
    const end = this.tempTo ?? this.hoverDate;
    if (!this.tempFrom || !end) return false;
    const [a, b] = this.tempFrom <= end
      ? [this.tempFrom, end]
      : [end, this.tempFrom];
    return date > a && date < b;
  }

  isStart(date: Date): boolean {
    return this.tempFrom !== null && this.sameDay(date, this.tempFrom);
  }

  isEnd(date: Date): boolean {
    const end = this.tempTo ?? (this.hoverDate && this.tempFrom ? this.hoverDate : null);
    return end !== null && this.sameDay(date, end);
  }

  sameDay(a: Date, b: Date): boolean {
    return (
      a.getFullYear() === b.getFullYear() &&
      a.getMonth() === b.getMonth() &&
      a.getDate() === b.getDate()
    );
  }

  isToday(date: Date): boolean {
    return this.sameDay(date, new Date());
  }

  apply() {
    this.from = this.tempFrom;
    this.to = this.tempTo;
    this.isOpen = false;
    this.rangeSelected.emit({ from: this.from, to: this.to });
  }

  cancel() {
    this.tempFrom = this.from;
    this.tempTo = this.to;
    this.hoverDate = null;
    this.isOpen = false;
  }

  clear() {
    this.from = null;
    this.to = null;
    this.tempFrom = null;
    this.tempTo = null;
    this.hoverDate = null;
    this.isOpen = false;
    this.rangeSelected.emit({ from: null, to: null });
  }

  @HostListener('document:click', ['$event'])
  onOutsideClick(event: MouseEvent) {
    const el = (event.target as HTMLElement).closest('app-date-range-picker');
    if (!el) this.isOpen = false;
  }
}
