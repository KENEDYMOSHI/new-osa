import { CommonModule } from '@angular/common';
import { Component, Input, Output, EventEmitter, ElementRef, ViewChild, forwardRef } from '@angular/core';
import { ControlValueAccessor, NG_VALUE_ACCESSOR } from '@angular/forms';

@Component({
  selector: 'app-time-picker',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './time-picker.component.html',
  styles: `
    .clock-face {
        position: relative;
        width: 250px;
        height: 250px;
        border-radius: 50%;
        background: #f3f0f7; /* Light purplebg */
    }
    .clock-number {
        position: absolute;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        text-align: center;
        line-height: 32px;
        cursor: pointer;
        font-weight: 500;
        transform-origin: center;
    }
    .clock-center {
        position: absolute;
        top: 50%;
        left: 50%;
        width: 8px;
        height: 8px;
        background: #9d8afe;
        border-radius: 50%;
        transform: translate(-50%, -50%);
    }
    .clock-hand {
        position: absolute;
        bottom: 50%;
        left: 50%;
        width: 2px;
        background: #9d8afe;
        transform-origin: bottom center;
        pointer-events: none;
    }
  `,
  providers: [
    {
      provide: NG_VALUE_ACCESSOR,
      useExisting: forwardRef(() => TimePickerComponent),
      multi: true
    }
  ]
})
export class TimePickerComponent implements ControlValueAccessor {

  @Input() id!: string;
  @Input() label: string = 'Time Select Input';
  @Input() placeholder: string = 'Select time';
  @Input() defaultTime?: string;
  
  @Output() timeChange = new EventEmitter<string>();

  value: string = '';
  showModal = false;

  // Clock State
  hours: number[] = [12, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11];
  minutes: number[] = [0, 5, 10, 15, 20, 25, 30, 35, 40, 45, 50, 55];
  
  selectedHour: number = 12;
  selectedMinute: number = 0;
  period: 'AM' | 'PM' = 'AM';
  
  // View State: 'hour' or 'minute'
  view: 'hour' | 'minute' = 'hour';

  onChange: any = () => {};
  onTouched: any = () => {};

  writeValue(value: string): void {
      this.value = value;
      if (value) {
          this.parseTime(value);
      }
  }

  registerOnChange(fn: any): void {
    this.onChange = fn;
  }

  registerOnTouched(fn: any): void {
    this.onTouched = fn;
  }

  openModal() {
      this.showModal = true;
      if (this.value) {
          this.parseTime(this.value);
      } else {
          // Default to current time or 12:00
          const now = new Date();
          let h = now.getHours();
          this.selectedMinute = Math.floor(now.getMinutes() / 5) * 5; // Snap to 5
          this.period = h >= 12 ? 'PM' : 'AM';
          this.selectedHour = h % 12 || 12;
      }
      this.view = 'hour';
  }

  closeModal() {
      this.showModal = false;
  }

  parseTime(timeStr: string) {
      if (!timeStr) return;
      const [hStr, mStr] = timeStr.split(':');
      let h = parseInt(hStr, 10);
      const m = parseInt(mStr, 10);

      this.period = h >= 12 ? 'PM' : 'AM';
      this.selectedHour = h % 12 || 12;
      this.selectedMinute = m;
  }

  confirmTime() {
      let h = this.selectedHour;
      if (this.period === 'PM' && h !== 12) h += 12;
      if (this.period === 'AM' && h === 12) h = 0;

      const hStr = h < 10 ? `0${h}` : `${h}`;
      const mStr = this.selectedMinute < 10 ? `0${this.selectedMinute}` : `${this.selectedMinute}`;
      
      this.value = `${hStr}:${mStr}`;
      this.onChange(this.value);
      this.timeChange.emit(this.value);
      this.closeModal();
  }

  selectHour(h: number) {
      this.selectedHour = h;
      this.view = 'minute'; // Auto switch
  }

  selectMinute(m: number) {
      this.selectedMinute = m;
  }

  getHandRotation(): number {
      if (this.view === 'hour') {
          // 360 / 12 = 30 deg per hour
          // 12 is at 0 deg (top) if we rotate correctly?
          // Default CSS absolute top is -90deg?
          // Let's assume 12 is at top (0deg).
          // 1 = 30deg, 2 = 60deg... 
          return this.selectedHour * 30; 
      } else {
          // 360 / 60 = 6 deg per minute
          return this.selectedMinute * 6;
      }
  }

  togglePeriod() {
      this.period = this.period === 'AM' ? 'PM' : 'AM';
  }
}
