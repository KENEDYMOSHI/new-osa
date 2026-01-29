import { Component, Input, Output, EventEmitter, forwardRef, HostListener, ViewChild, ElementRef, OnChanges, SimpleChanges } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ControlValueAccessor, NG_VALUE_ACCESSOR, FormsModule } from '@angular/forms';

@Component({
  selector: 'app-searchable-select',
  standalone: true,
  imports: [CommonModule, FormsModule],
  template: `
    <div class="relative">
      <!-- Trigger Button -->
      <div 
        class="w-full bg-gray-50 border border-gray-300 text-gray-800 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block p-2.5 flex justify-between items-center cursor-pointer transition-colors"
        [ngClass]="{'opacity-50 cursor-not-allowed': disabled, 'bg-white': isOpen}"
        (click)="toggleDropdown()">
        
        <span [class.text-gray-400]="!selectedValue" [class.text-gray-800]="selectedValue">
            {{ getDisplayValue(selectedValue) || placeholder }}
        </span>

        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500 transition-transform duration-200" [class.rotate-180]="isOpen" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
      </div>

      <!-- Dropdown Menu -->
      <div *ngIf="isOpen && !disabled" class="absolute z-50 w-full bg-white rounded-lg shadow-xl border border-gray-100 mt-1 overflow-hidden animate-fade-in-down">
        
        <!-- Search Input -->
        <div class="p-2 border-b border-gray-100 bg-gray-50">
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </div>
            <input 
              #searchInput
              type="text" 
              [(ngModel)]="searchTerm" 
              (input)="filterOptions()"
              class="w-full pl-9 pr-3 py-1.5 text-sm border border-gray-200 rounded-md focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500 bg-white placeholder-gray-400"
              placeholder="Search..."
              (click)="$event.stopPropagation()">
          </div>
        </div>

        <!-- Options List -->
        <div class="max-h-60 overflow-y-auto custom-scrollbar">
            <!-- Grouped Layout -->
            <ng-container *ngIf="groupChildren; else flatLayout">
                <div *ngFor="let group of filteredOptions">
                    <!-- Group Header -->
                    <div class="px-4 py-1.5 text-xs font-bold text-gray-500 bg-gray-50 uppercase tracking-wider">
                        {{ group[groupLabel] }}
                    </div>
                    <!-- Group Children -->
                    <div *ngFor="let option of group[groupChildren]" 
                         (click)="selectOption(option)"
                         class="px-4 py-2 pl-6 text-sm text-gray-700 hover:bg-orange-50 hover:text-orange-700 cursor-pointer transition-colors flex items-center justify-between"
                         [class.bg-orange-50]="isSelected(option)"
                         [class.text-orange-700]="isSelected(option)">
                        <span>{{ getDisplayValue(option) }}</span>
                        <svg *ngIf="isSelected(option)" class="h-4 w-4 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                </div>
            </ng-container>

            <!-- Flat Layout -->
            <ng-template #flatLayout>
                <div *ngFor="let option of filteredOptions" 
                     (click)="selectOption(option)"
                     class="px-4 py-2 text-sm text-gray-700 hover:bg-orange-50 hover:text-orange-700 cursor-pointer transition-colors flex items-center justify-between"
                     [class.bg-orange-50]="isSelected(option)"
                     [class.text-orange-700]="isSelected(option)">
                    <span>{{ getDisplayValue(option) }}</span>
                    <svg *ngIf="isSelected(option)" class="h-4 w-4 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
            </ng-template>

            <div *ngIf="filteredOptions.length === 0" class="px-4 py-3 text-sm text-gray-400 text-center italic">
                No results found
            </div>
        </div>
      </div>
    </div>
  `,
  providers: [
    {
      provide: NG_VALUE_ACCESSOR,
      useExisting: forwardRef(() => SearchableSelectComponent),
      multi: true
    }
  ],
  styles: [`
    .animate-fade-in-down {
        animation: fadeInDown 0.2s ease-out;
    }
    @keyframes fadeInDown {
        from { opacity: 0; transform: translateY(-5px); }
        to { opacity: 1; transform: translateY(0); }
    }
    /* Custom Scrollbar */
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f1f1f1;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #d1d5db;
        border-radius: 3px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #9ca3af;
    }
  `]
})
export class SearchableSelectComponent implements ControlValueAccessor, OnChanges {
  @Input() options: any[] = [];
  @Input() placeholder: string = 'Select Option';
  @Input() disabled: boolean = false;
  @Input() displayKey: string = ''; // Key to display if options are objects
  @Input() valueKey: string = '';   // Key to use as value if options are objects. If empty, uses whole object.
  @Input() groupChildren: string = ''; // Key for children array if grouped (e.g. 'units')
  @Input() groupLabel: string = '';    // Key for group label (e.g. 'name')

  @Output() selectionChange = new EventEmitter<any>();

  @ViewChild('searchInput') searchInput!: ElementRef;

  isOpen = false;
  searchTerm: string = '';
  filteredOptions: any[] = [];
  selectedValue: any = null;

  // ControlValueAccessor callbacks
  onChange: any = () => {};
  onTouched: any = () => {};

  ngOnChanges(changes: SimpleChanges) {
    if (changes['options']) {
      this.filterOptions();
    }
  }

  toggleDropdown() {
    if (this.disabled) return;
    this.isOpen = !this.isOpen;
    if (this.isOpen) {
        this.searchTerm = '';
        this.filterOptions();
        setTimeout(() => {
            if (this.searchInput) this.searchInput.nativeElement.focus();
        }, 100);
    } else {
        this.onTouched();
    }
  }

  closeDropdown() {
    if (this.isOpen) {
        this.isOpen = false;
        this.onTouched();
    }
  }

  selectOption(option: any) {
    const value = this.getValue(option);
    this.selectedValue = value;
    this.onChange(value);
    this.selectionChange.emit(value);
    this.closeDropdown();
  }

  filterOptions() {
    if (!this.searchTerm) {
      // If grouped, deep copy to ensure we don't mutate original structure when filtering later (if we did in-place, but here we just assign)
      this.filteredOptions = this.groupChildren ? JSON.parse(JSON.stringify(this.options)) : [...this.options];
      return;
    }

    const lowerTerm = this.searchTerm.toLowerCase();

    if (this.groupChildren) {
        // Grouped Filtering
        const result: any[] = [];
        for (const group of this.options) {
            const children = group[this.groupChildren] || [];
            const filteredChildren = children.filter((child: any) => {
                const display = this.getDisplayValue(child).toLowerCase();
                return display.includes(lowerTerm);
            });

            if (filteredChildren.length > 0) {
                // Create a new group object with filtered children
                result.push({
                    ...group,
                    [this.groupChildren]: filteredChildren
                });
            }
        }
        this.filteredOptions = result;
    } else {
        // Flat Filtering
        this.filteredOptions = this.options.filter(opt => {
            const display = this.getDisplayValue(opt).toLowerCase();
            return display.includes(lowerTerm);
        });
    }
  }

  getDisplayValue(option: any): string {
    if (option === null || option === undefined) return '';
    
    // If option is currently the selected VALUE (string/number) but we want to display the LABEL
    // We need to look it up.
    if (this.valueKey && typeof option !== 'object' && this.options.length > 0) {
         // Lookup in flat list
         if (!this.groupChildren) {
             const found = this.options.find(o => o[this.valueKey] === option);
             if (found) return found[this.displayKey];
         } else {
             // Lookup in grouped list
             for (const group of this.options) {
                 const children = group[this.groupChildren] || [];
                 const found = children.find((o: any) => {
                     // If children are objects
                     if (typeof o === 'object') return o[this.valueKey] === option;
                     // If children are primitives (strings), option should match child
                     return o === option;
                 });
                 // If found is a string/primitive and we are looking for display value, it is just itself.
                 // If found is object, return displayKey
                 if (found) {
                     if (typeof found === 'object' && this.displayKey) return found[this.displayKey];
                     return String(found);
                 }
             }
         }
         return option; // Fallback
    }

    if (this.displayKey && typeof option === 'object') {
      return option[this.displayKey];
    }
    return String(option);
  }

  getValue(option: any): any {
    if (this.valueKey && typeof option === 'object') {
      return option[this.valueKey];
    }
    return option;
  }

  isSelected(option: any): boolean {
    const optValue = this.getValue(option);
    return this.selectedValue === optValue;
  }

  // ControlValueAccessor implementation
  writeValue(value: any): void {
    this.selectedValue = value;
  }
  registerOnChange(fn: any): void {
    this.onChange = fn;
  }
  registerOnTouched(fn: any): void {
    this.onTouched = fn;
  }
  setDisabledState?(isDisabled: boolean): void {
    this.disabled = isDisabled;
  }

  // Handle click outside
  @HostListener('document:click', ['$event'])
  onClickOutside(event: Event) {
    const target = event.target as HTMLElement;
    const isInside = target.closest('app-searchable-select');
    if (!isInside) {
        this.closeDropdown();
    }
  }
}
