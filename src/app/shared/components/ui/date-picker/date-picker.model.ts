/**
 * Date Picker Model Interfaces
 * Provides type safety for date picker values
 */

export interface DateValue {
  value: string;        // YYYY-MM-DD format
  displayValue: string; // dd/MM/yyyy format
}

export interface DateRangeValue {
  startDate: string;    // YYYY-MM-DD format
  endDate: string;      // YYYY-MM-DD format
  displayValue: string; // "dd/MM/yyyy - dd/MM/yyyy" format
}

export type DatePickerValue = DateValue | DateRangeValue | null;

/**
 * Utility class for date formatting and parsing
 */
export class DateUtils {
  /**
   * Format a Date object to YYYY-MM-DD string
   */
  static formatToYYYYMMDD(date: Date): string {
    const y = date.getFullYear();
    const m = (date.getMonth() + 1).toString().padStart(2, '0');
    const d = date.getDate().toString().padStart(2, '0');
    return `${y}-${m}-${d}`;
  }

  /**
   * Format a Date object to dd/MM/yyyy string for display
   */
  static formatToDisplay(date: Date): string {
    const day = String(date.getDate()).padStart(2, '0');
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const year = date.getFullYear();
    return `${day}/${month}/${year}`;
  }

  /**
   * Parse a YYYY-MM-DD string to Date object
   */
  static parseYYYYMMDD(dateStr: string): Date | null {
    if (!dateStr || !this.isValidDateString(dateStr)) {
      return null;
    }
    const date = new Date(dateStr);
    return isNaN(date.getTime()) ? null : date;
  }

  /**
   * Check if a string is a valid date string
   */
  static isValidDateString(dateStr: string): boolean {
    if (!dateStr) return false;
    const date = new Date(dateStr);
    return !isNaN(date.getTime());
  }

  /**
   * Compare two dates (-1 if date1 < date2, 0 if equal, 1 if date1 > date2)
   */
  static compareDates(date1: Date, date2: Date): number {
    const time1 = date1.getTime();
    const time2 = date2.getTime();
    if (time1 < time2) return -1;
    if (time1 > time2) return 1;
    return 0;
  }

  /**
   * Check if a date is the same day as another date
   */
  static isSameDay(date1: Date, date2: Date): boolean {
    return (
      date1.getDate() === date2.getDate() &&
      date1.getMonth() === date2.getMonth() &&
      date1.getFullYear() === date2.getFullYear()
    );
  }
}
