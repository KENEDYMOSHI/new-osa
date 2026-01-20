import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { DomSanitizer, SafeResourceUrl } from '@angular/platform-browser';
import { PatternApprovalService } from '../../../services/pattern-approval.service';
import { FuelPumpFormComponent } from '../fuel-pump-form/fuel-pump-form.component';

interface InstrumentCategory {
  id: number;
  name: string;
  code: string;
  expanded: boolean;
}

interface InstrumentType {
  id: number;
  category_id: number;
  name: string;
  code: string;
}

interface SelectedInstrument {
  instrument_type_id: number;
  instrument_type_name: string;
  instrument_type_code: string;
  brand_name: string;
  make: string;
  serial_number: string;
  maximum_capacity: string;
  accuracy_class?: string;
  manual_calibration_doc: File | null;
  specification_doc: File | null;
  [key: string]: any;
}

@Component({
  selector: 'app-pattern-selection',
  standalone: true,
  imports: [CommonModule, FormsModule, FuelPumpFormComponent],
  templateUrl: './pattern-selection.component.html',
  styleUrl: './pattern-selection.component.css'
})
export class PatternSelectionComponent implements OnInit {
  // Document Configuration
  instrumentDocuments = [
    { key: 'manual_calibration_doc', name: 'Manual Calibration Document', accept: '.pdf,.doc,.docx' },
    { key: 'specification_doc', name: 'Specification of Instrument', accept: '.pdf,.doc,.docx' }
  ];

  // Modal State
  showPreviewModal = false;
  previewFile: File | null = null;
  previewFileUrl: any = null;
  previewTitle: string = '';
  // Pattern Types
  patternTypes: any[] = [];
  selectedPatternTypeId: number | null = null;

  // Instrument Categories
  instrumentCategories: InstrumentCategory[] = [];
  
  // Instrument Types by Category
  instrumentTypesByCategory: { [key: number]: InstrumentType[] } = {};
  
  // Selected Instruments with Details
  selectedInstruments: SelectedInstrument[] = [];
  
  // Current instrument being added
  currentInstrument: SelectedInstrument | null = null;
  selectedInstrumentType: InstrumentType | null = null;
  
  // UI State
  isLoading = false;
  isSaving = false;
  errorMessage = '';
  successMessage = '';

  get selectedPatternTypeName(): string {
    const type = this.patternTypes.find(t => t.id === this.selectedPatternTypeId);
    return type ? type.name : '';
  }

  // Application ID (if editing existing)
  applicationId: number | null = null;
  
  // Fuel Pump Form State
  showFuelPumpForm = false;

  constructor(
    private patternApprovalService: PatternApprovalService,
    private sanitizer: DomSanitizer
  ) {}

  ngOnInit() {
    this.loadPatternTypes();
    // Don't load categories immediately, wait for pattern type selection
    // this.loadInstrumentCategories(); 
  }
  
  // Check if selected pattern is Fuel Pump
  isFuelPumpPattern(): boolean {
    return this.selectedPatternTypeName.toLowerCase().includes('fuel');
  }

  loadPatternTypes() {
    this.isLoading = true;
    this.patternApprovalService.getPatternTypes().subscribe({
      next: (response: any) => {
        if (response.success) {
          this.patternTypes = response.data;
        }
        this.isLoading = false;
      },
      error: (error) => {
        console.error('Error loading pattern types:', error);
        this.errorMessage = 'Failed to load pattern types';
        this.isLoading = false;
      }
    });
  }

  loadInstrumentCategories() {
    if (!this.selectedPatternTypeId) {
      this.instrumentCategories = [];
      return;
    }

    this.patternApprovalService.getInstrumentCategories(this.selectedPatternTypeId).subscribe({
      next: (response: any) => {
        if (response.success) {
          this.instrumentCategories = response.data.map((cat: any) => ({
            ...cat,
            expanded: true // Always expanded
          }));

          // Automatically load instruments for each category
          this.instrumentCategories.forEach(cat => {
            this.loadInstrumentTypes(cat.id);
          });
        }
      },
      error: (error) => {
        console.error('Error loading instrument categories:', error);
        this.errorMessage = 'Failed to load instrument categories';
      }
    });
  }

  // toggleCategory removed as it is no longer needed
  /* toggleCategory(category: InstrumentCategory) { ... } */

  toggleCategory(category: InstrumentCategory) {
    category.expanded = !category.expanded;
    
    // Load instrument types if not already loaded
    if (category.expanded && !this.instrumentTypesByCategory[category.id]) {
      this.loadInstrumentTypes(category.id);
    }
  }

  // Modal State
  showChangePatternModal = false;



  changePattern() {
    this.showChangePatternModal = true;
  }

  confirmChangePattern() {
    this.selectedPatternTypeId = null;
    this.instrumentCategories = [];
    this.selectedInstrumentType = null;
    this.selectedInstruments = [];
    this.onPatternTypeChange();
    this.showChangePatternModal = false;
  }

  cancelChangePattern() {
    this.showChangePatternModal = false;
  }

  loadInstrumentTypes(categoryId: number) {
    this.patternApprovalService.getInstrumentTypesByCategory(categoryId).subscribe({
      next: (response: any) => {
        if (response.success) {
          this.instrumentTypesByCategory[categoryId] = response.data;
        }
      },
      error: (error) => {
        console.error('Error loading instrument types:', error);
        this.errorMessage = 'Failed to load instrument types';
      }
    });
  }

  selectInstrumentType(instrumentType: InstrumentType) {
    this.selectedInstrumentType = instrumentType;
    
    // Check if this is a Fuel Pump instrument
    if (this.isFuelPumpPattern() && instrumentType.name.toLowerCase().includes('standard')) {
      this.showFuelPumpForm = true;
      return;
    }
    
    // Check if already selected
    const alreadySelected = this.selectedInstruments.find(
      i => i.instrument_type_id === instrumentType.id
    );

    if (alreadySelected) {
      this.errorMessage = 'This instrument type has already been selected';
      setTimeout(() => this.errorMessage = '', 3000);
      return;
    }

    // Initialize current instrument for details entry
    this.currentInstrument = {
      instrument_type_id: instrumentType.id,
      instrument_type_name: instrumentType.name,
      instrument_type_code: instrumentType.code,
      brand_name: '',
      make: '',
      serial_number: '',
      maximum_capacity: '',
      manual_calibration_doc: null,
      specification_doc: null
    };
  }

  onPatternTypeChange() {
    // Strict Session Reset
    this.selectedInstruments = [];
    this.currentInstrument = null;
    this.selectedInstrumentType = null;
    this.instrumentCategories = [];
    this.instrumentTypesByCategory = {};
    this.showFuelPumpForm = false;
    this.errorMessage = '';
    this.successMessage = '';
    
    // Clear Fuel Pump Draft from LocalStorage
    localStorage.removeItem('fuelPumpFormDraft');

    // Load instrument categories for the new pattern type
    if (this.selectedPatternTypeId) {
      this.loadInstrumentCategories();
    }
  }

  onFileSelected(event: any, docKey: string) {
    const file = event.target.files[0];
    if (file && this.currentInstrument) {
      this.currentInstrument[docKey] = file;
    }
  }

  removeDocument(docKey: string) {
    if (this.currentInstrument) {
      this.currentInstrument[docKey] = null;
    }
  }

  openPreview(file: File | null, title: string) {
    if (!file) return;
    this.previewFile = file;
    this.previewTitle = title;
    
    if (file.type === 'application/pdf') {
      const url = URL.createObjectURL(file);
      this.previewFileUrl = this.sanitizer.bypassSecurityTrustResourceUrl(url);
    } else {
      this.previewFileUrl = null;
    }
    this.showPreviewModal = true;
  }

  closePreview() {
    this.showPreviewModal = false;
    if (this.previewFileUrl) {
        // Optional: revoke URL if needed, though Angular sanitizer might handle it
    }
    this.previewFile = null;
    this.previewFileUrl = null;
  }

  saveInstrumentDetails() {
    if (!this.currentInstrument) return;

    // Validate required fields
    if (!this.currentInstrument.brand_name || !this.currentInstrument.make || 
        !this.currentInstrument.serial_number || !this.currentInstrument.maximum_capacity) {
      this.errorMessage = 'Please fill in all instrument details';
      setTimeout(() => this.errorMessage = '', 3000);
      return;
    }

    // Add to selected instruments
    this.selectedInstruments.push({ ...this.currentInstrument });
    
    // Clear current instrument
    this.currentInstrument = null;
    this.selectedInstrumentType = null; // Also clear selection to go back to list
    
    this.successMessage = 'Instrument details saved successfully';
    setTimeout(() => this.successMessage = '', 3000);
  }

  cancelInstrumentDetails() {
    this.currentInstrument = null;
  }

  removeInstrument(index: number) {
    this.selectedInstruments.splice(index, 1);
    this.successMessage = 'Instrument removed';
    setTimeout(() => this.successMessage = '', 3000);
  }

  submitApplication() {
    if (!this.selectedPatternTypeId) {
      this.errorMessage = 'Please select a pattern type';
      setTimeout(() => this.errorMessage = '', 3000);
      return;
    }

    if (this.selectedInstruments.length === 0) {
      this.errorMessage = 'Please add at least one instrument';
      setTimeout(() => this.errorMessage = '', 3000);
      return;
    }

    this.isSaving = true;

    // Create application
    this.patternApprovalService.createApplication({
      pattern_type_id: this.selectedPatternTypeId
    }).subscribe({
      next: (response: any) => {
        if (response.success) {
          this.applicationId = response.data.id;
          this.saveInstruments();
        }
      },
      error: (error) => {
        console.error('Error creating application:', error);
        this.errorMessage = 'Failed to create application';
        this.isSaving = false;
      }
    });
  }

  saveInstruments() {
    if (!this.applicationId) return;

    // Save each instrument
    let savedCount = 0;
    this.selectedInstruments.forEach((instrument) => {
      const instrumentData = {
        instrument_type_id: instrument.instrument_type_id,
        brand_name: instrument.brand_name,
        make: instrument.make,
        serial_number: instrument.serial_number,
        maximum_capacity: instrument.maximum_capacity,
        // Note: File uploads would need to be handled separately via FormData
        // For now, we'll just save the metadata
      };

      this.patternApprovalService.addInstrument(this.applicationId!, instrument.instrument_type_id).subscribe({
        next: (response: any) => {
          savedCount++;
          if (savedCount === this.selectedInstruments.length) {
            this.isSaving = false;
            this.successMessage = 'Application submitted successfully!';
            // Reset form
            setTimeout(() => {
              this.resetForm();
            }, 2000);
          }
        },
        error: (error) => {
          console.error('Error saving instrument:', error);
          this.errorMessage = 'Failed to save some instruments';
          this.isSaving = false;
        }
      });
    });
  }

  resetForm() {
    this.selectedPatternTypeId = null;
    this.instrumentCategories = [];
    this.selectedInstruments = [];
    this.currentInstrument = null;
    this.applicationId = null;
    this.showFuelPumpForm = false;
    this.successMessage = '';
    this.errorMessage = '';
  }
  
  // Fuel Pump Form Handlers
  onFuelPumpFormSubmitted(formData: FormData) {
    this.isSaving = true;
    
    // Create application first
    this.patternApprovalService.createApplication({
      pattern_type_id: this.selectedPatternTypeId
    }).subscribe({
      next: (response: any) => {
        if (response.success) {
          this.applicationId = response.data.id;
          
          // For now, just show success message
          // You can add the fuel pump submission endpoint later
          this.isSaving = false;
          this.successMessage = 'Fuel pump application submitted successfully!';
          setTimeout(() => {
            this.resetForm();
          }, 2000);
        }
      },
      error: (error) => {
        console.error('Error creating application:', error);
        this.errorMessage = 'Failed to create application';
        this.isSaving = false;
      }
    });
  }
  
  onFuelPumpFormCancelled() {
    this.showFuelPumpForm = false;
    this.selectedInstrumentType = null;
  }
}
