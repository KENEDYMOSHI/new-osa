import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { DomSanitizer, SafeResourceUrl } from '@angular/platform-browser';
import { PatternApprovalService } from '../../../services/pattern-approval.service';

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
  manual_calibration_doc: File | null;
  specification_doc: File | null;
}

@Component({
  selector: 'app-pattern-selection',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './pattern-selection.component.html',
  styleUrl: './pattern-selection.component.css'
})
export class PatternSelectionComponent implements OnInit {
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
  
  // UI State
  isLoading = false;
  isSaving = false;
  errorMessage = '';
  successMessage = '';

  // Document Preview
  previewDocument: File | null = null;
  previewDocumentUrl: SafeResourceUrl | null = null;

  // Application ID (if editing existing)
  applicationId: number | null = null;

  constructor(
    private patternApprovalService: PatternApprovalService,
    private sanitizer: DomSanitizer
  ) {}

  ngOnInit() {
    this.loadPatternTypes();
    this.loadInstrumentCategories();
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
    this.patternApprovalService.getInstrumentCategories().subscribe({
      next: (response: any) => {
        if (response.success) {
          this.instrumentCategories = response.data.map((cat: any) => ({
            ...cat,
            expanded: false
          }));
        }
      },
      error: (error) => {
        console.error('Error loading instrument categories:', error);
        this.errorMessage = 'Failed to load instrument categories';
      }
    });
  }

  toggleCategory(category: InstrumentCategory) {
    category.expanded = !category.expanded;
    
    // Load instrument types if not already loaded
    if (category.expanded && !this.instrumentTypesByCategory[category.id]) {
      this.loadInstrumentTypes(category.id);
    }
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
    // Load instrument categories when pattern type changes
    if (this.selectedPatternTypeId) {
      this.loadInstrumentCategories();
    }
  }

  onFileSelected(event: any, docType: 'manual_calibration' | 'specification') {
    const file = event.target.files[0];
    if (file && this.currentInstrument) {
      if (docType === 'manual_calibration') {
        this.currentInstrument.manual_calibration_doc = file;
      } else {
        this.currentInstrument.specification_doc = file;
      }
      
      // Set for preview
      this.setPreviewDocument(file);
    }
  }

  setPreviewDocument(file: File) {
    this.previewDocument = file;
    
    // Create URL for preview
    if (file.type === 'application/pdf') {
      const url = URL.createObjectURL(file);
      this.previewDocumentUrl = this.sanitizer.bypassSecurityTrustResourceUrl(url);
    } else {
      this.previewDocumentUrl = null;
    }
  }

  clearPreview() {
    if (this.previewDocumentUrl && this.previewDocument?.type === 'application/pdf') {
      // Revoke the object URL to free memory
      const url = (this.previewDocumentUrl as any).changingThisBreaksApplicationSecurity;
      if (url) {
        URL.revokeObjectURL(url);
      }
    }
    this.previewDocument = null;
    this.previewDocumentUrl = null;
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
    this.selectedInstruments = [];
    this.currentInstrument = null;
    this.applicationId = null;
    this.successMessage = '';
    this.errorMessage = '';
  }
}
