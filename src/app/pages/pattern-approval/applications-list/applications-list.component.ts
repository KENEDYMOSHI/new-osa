import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { FormsModule } from '@angular/forms';

interface Category {
  value: string;
  label: string;
}

@Component({
  selector: 'app-applications-list',
  standalone: true,
  imports: [CommonModule, RouterModule, FormsModule],
  templateUrl: './applications-list.component.html',
  styleUrl: './applications-list.component.css'
})
export class ApplicationsListComponent {
  // Toggle for viewing status inline
  viewStatus: boolean = false;

  // Selected filters
  selectedPatternType: string = '';
  selectedCategory: string = '';

  // Pattern types
  patternTypes = [
    { value: '1', label: 'Weighing Instrument' },
    { value: '2', label: 'Fuel Pump' },
    { value: '3', label: 'Meter' },
    { value: '4', label: 'Capacity Measures' },
    { value: '5', label: 'Other Pattern Instrument' },
  ];

  // Categories mapped per pattern type
  allCategories: Record<string, Category[]> = {
    '1': [ // Weighing Instrument
      { value: 'CIS', label: 'Counter Scale' },
      { value: 'PM', label: 'Platform Scale' },
      { value: 'SB', label: 'Balance Scale' },
      { value: 'BS', label: 'Spring Balance' },
      { value: 'WB', label: 'Weighbridge' },
    ],
    '2': [ // Fuel Pump
      { value: 'SFP', label: 'Standard Fuel Pump' },
    ],
    '3': [ // Meter
      { value: 'SWM', label: 'Standard Meter' },
    ],
    '4': [ // Capacity Measures
      { value: 'SCM', label: 'Standard Capacity Measure' },
    ],
    '5': [ // Other
      { value: 'OPI', label: 'Other Pattern Instrument' },
    ],
  };

  // Currently visible categories
  filteredCategories: Category[] = [];

  onPatternTypeChange(): void {
    this.selectedCategory = '';
    if (this.selectedPatternType && this.allCategories[this.selectedPatternType]) {
      this.filteredCategories = this.allCategories[this.selectedPatternType];
    } else {
      this.filteredCategories = [];
    }
  }
}
