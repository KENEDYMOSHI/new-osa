import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { DomSanitizer, SafeResourceUrl } from '@angular/platform-browser';
import { PatternApprovalService } from '../../../services/pattern-approval.service';
import { FuelPumpFormComponent } from '../fuel-pump-form/fuel-pump-form.component';

interface InstrumentCategory {
  id: number | string;
  name: string;
  code: string;
  expanded: boolean;
  [key: string]: any; // Allow optional properties
}

interface InstrumentType {
  id: number | string;
  category_id: number | string;
  name: string;
  code: string;
}

interface SelectedInstrument {
  instrument_type_id: number | string;
  instrument_type_name: string;
  instrument_type_code: string;
  brand_name: string;
  make: string;
  serial_number: string;
  maximum_capacity: string;
  accuracy_class?: string;
  quantity?: number;
  nominal_flow_rate?: string;
  meter_class?: string;
  ratio?: string;
  max_admissible_pressure?: string;
  max_temperature?: string;
  meter_size_dn?: string;
  diameter?: string;
  position_hv_type?: string; // 'horizontal' | 'vertical'
  sealing_mechanism_type?: string; // 'provided' | 'not_provided'
  flow_direction_type?: string; // 'indicated' | 'not_indicated'
  
  // Weighing Scale Fields
  scale_type?: string; // 'mechanical' | 'digital'
  instrument_use?: string;
  value_e?: string;
  value_d?: string;
  
  // Electrical Meter Fields
  meter_model?: string;
  meter_type?: string; // 'electromechanical' | 'static'
  nominal_voltage?: string;
  nominal_frequency?: string;
  maximum_current?: string;
  transitional_current?: string;
  minimum_current?: string;
  starting_current?: string;
  connection_type?: string; // 'direct', 'ct', 'ct_vt'
  connection_mode?: string;
  alternative_connection_mode?: string;
  energy_flow_direction?: string;
  meter_constant?: string;
  clock_frequency?: string;
  environment?: string;
  ip_rating?: string;
  terminal_arrangement?: string;
  insulation_protection_class?: string;
  temperature_lower?: string;
  temperature_upper?: string;
  humidity_class?: string;
  hardware_version?: string;
  software_version?: string;
  remarks?: string;
  test_voltage?: string;
  test_frequency?: string;
  test_connection_mode?: string;
  test_remarks?: string;
  // Capacity Measure Fields
  material_construction?: string;
  year_manufacture?: string;
  measurement_unit?: string; // 'Litre' | 'Cubic Metre'
  nominal_capacity?: string;
  max_permissible_error?: string;
  temperature_range?: string;
  intended_liquid?: string; // 'Petrol' | 'Kerosene' | 'Diesel' | 'Water'
  has_seal_arrangement?: string; // 'Yes' | 'No'
  has_adjustment_mechanism?: string; // 'Yes' | 'No'
  has_gauge_glass?: string; // 'Yes' | 'No'

  serial_numbers?: string[]; // Array for dynamic serials
  manual_calibration_doc: File | null;
  specification_doc: File | null;
  other_doc: File | null;
  type_approval_doc: File | null;
  calculated_fee?: number; // Store the calculated fee for this instrument
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
    { key: 'manual_calibration_doc', name: 'Manual Calibration Document *', accept: '.pdf,.jpg,.jpeg,.png' },
    { key: 'specification_doc', name: 'Specification of Instrument *', accept: '.pdf,.jpg,.jpeg,.png' },
    { key: 'type_approval_doc', name: 'Type examination certificate (Optional)', accept: '.pdf,.jpg,.jpeg,.png' },
    { key: 'other_doc', name: 'Other Document (Optional)', accept: '.pdf,.jpg,.jpeg,.png' }
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
  instrumentTypesByCategory: { [key: string]: InstrumentType[] } = {}; // Changed key to string to support both
  
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

  // Application Fee
  applicationFee: number = 0;
  feeBaseAmount: number = 0;

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

    // Check for specific pattern types that have hardcoded categories
    switch (this.selectedPatternTypeName) {
      case 'Meter': // Changed from 'Meters' to match backend name 'Meter'
        this.instrumentCategories = [
          {
            id: 'water-meter',
            name: 'Water Meters',
            icon: 'assets/icons/water-meter.svg',
            description: 'Domestic and industrial water meters',
            expanded: true,
            code: 'WM'
          },
          {
            id: 'flow-meter',
            name: 'Flow Meter',
            icon: 'assets/icons/flow-meter.svg',
            description: 'General purpose flow meters',
            expanded: true,
            code: 'FM'
          },
          {
            id: 'bulk-flow-meter',
            name: 'Bulk Flow Meter',
            icon: 'assets/icons/bulk-flow-meter.svg',
            description: 'High volume flow measurement',
            expanded: true,
            code: 'BFM'
          },
          {
            id: 'electrical-meter',
            name: 'Electrical Meter',
            icon: 'assets/icons/electrical-meter.svg',
            description: 'Electricity consumption meters',
            expanded: true,
            code: 'EM'
          }
        ];
        
        // Pre-populate instrument types (sub-categories) for these hardcoded categories
        this.instrumentTypesByCategory['water-meter'] = [
            { id: 'standard-water-meter', category_id: 'water-meter', name: 'Standard Water Meter', code: 'SWM' }
        ];
        this.instrumentTypesByCategory['flow-meter'] = [
            { id: 'standard-flow-meter', category_id: 'flow-meter', name: 'Standard Flow Meter', code: 'SFM' }
        ];
        this.instrumentTypesByCategory['bulk-flow-meter'] = [
            { id: 'standard-bulk-flow-meter', category_id: 'bulk-flow-meter', name: 'Standard Bulk Flow Meter', code: 'SBFM' }
        ];
        this.instrumentTypesByCategory['electrical-meter'] = [
            { id: 'standard-electrical-meter', category_id: 'electrical-meter', name: 'Standard Electrical Meter', code: 'SEM' }
        ];
        break;

      case 'Weighing Instrument':
        this.instrumentCategories = [
          {
            id: 'counter-scale',
            name: 'Counter Scale',
            icon: 'assets/icons/weighing-scale.svg', // Generic icon
            description: 'Counter scales for general weighing',
            expanded: true,
            code: 'CIS'
          },
          {
            id: 'platform-scale',
            name: 'Platform Scale',
            icon: 'assets/icons/platform-scale.svg',
            description: 'Heavy duty platform scales',
            expanded: true,
            code: 'P/M'
          },
          {
            id: 'balance-scale',
            name: 'Balance Scale',
            icon: 'assets/icons/balance-scale.svg',
            description: 'Precision balance scales',
            expanded: true,
            code: 'S/B'
          },
          {
            id: 'spring-balance',
            name: 'Spring Balance',
            icon: 'assets/icons/spring-balance.svg',
            description: 'Spring operated weighing devices',
            expanded: true,
            code: 'BS'
          },
          {
            id: 'weighbridge',
            name: 'Weighbridge',
            icon: 'assets/icons/weighbridge.svg',
            description: 'Large capacity vehicle weighing',
            expanded: true,
            code: 'W/B'
          }
        ];

        // Pre-populate Standard types similar to Meter design
        this.instrumentTypesByCategory['counter-scale'] = [
            { id: 'standard-counter-scale', category_id: 'counter-scale', name: 'Standard Counter Scale', code: 'CIS' }
        ];
        this.instrumentTypesByCategory['platform-scale'] = [
            { id: 'standard-platform-scale', category_id: 'platform-scale', name: 'Standard Platform Scale', code: 'P/M' }
        ];
        this.instrumentTypesByCategory['balance-scale'] = [
            { id: 'standard-balance-scale', category_id: 'balance-scale', name: 'Standard Balance Scale', code: 'S/B' }
        ];
        this.instrumentTypesByCategory['spring-balance'] = [
            { id: 'standard-spring-balance', category_id: 'spring-balance', name: 'Standard Spring Balance', code: 'BS' }
        ];
        this.instrumentTypesByCategory['weighbridge'] = [
            { id: 'standard-weighbridge', category_id: 'weighbridge', name: 'Standard Weighbridge', code: 'W/B' }
        ];
        break;

      case 'Capacity Measures':
      case 'Capacity Measure':
        this.instrumentCategories = [
          {
            id: 'proving-tank',
            name: 'Proving Tank',
            icon: 'assets/icons/proving-tank.svg',
            description: 'Volumetric proving tanks',
            expanded: true,
            code: 'PT'
          },
          {
            id: 'check-pump',
            name: 'Check Pump',
            icon: 'assets/icons/check-pump.svg',
            description: 'Standard check pumps',
            expanded: true,
            code: 'CP'
          },
          {
            id: 'measuring-cylinder',
            name: 'Measuring Cylinders',
            icon: 'assets/icons/measuring-cylinder.svg',
            description: 'Graduated measuring cylinders',
            expanded: true,
            code: 'MC'
          },
          {
            id: 'other-capacity-measure',
            name: 'Other Capacity Measures',
            icon: 'assets/icons/other-capacity.svg',
            description: 'Other volumetric measures',
            expanded: true,
            code: 'OCM'
          }
        ];

        // Pre-populate standard types
        this.instrumentTypesByCategory['proving-tank'] = [
            { id: 'standard-proving-tank', category_id: 'proving-tank', name: 'Proving Tank', code: 'PT' }
        ];
        this.instrumentTypesByCategory['check-pump'] = [
            { id: 'standard-check-pump', category_id: 'check-pump', name: 'Check Pump', code: 'CP' }
        ];
        this.instrumentTypesByCategory['measuring-cylinder'] = [
            { id: 'standard-measuring-cylinder', category_id: 'measuring-cylinder', name: 'Measuring Cylinder', code: 'MC' }
        ];
        this.instrumentTypesByCategory['other-capacity-measure'] = [
            { id: 'standard-other-capacity', category_id: 'other-capacity-measure', name: 'Other Capacity Measure', code: 'OCM' }
        ];
        break;
      default:
        // Default behavior: load categories from the API
        this.patternApprovalService.getInstrumentCategories(this.selectedPatternTypeId).subscribe({
          next: (response: any) => {
            if (response.success) {
              this.instrumentCategories = response.data.map((cat: any) => ({
                ...cat,
                expanded: true // Always expanded
              }));

              // Automatically load instruments for each category
              this.instrumentCategories.forEach(cat => {
                this.loadInstrumentTypes(cat.id as number); // Cast to number for API call
              });
            }
          },
          error: (error) => {
            console.error('Error loading instrument categories:', error);
            this.errorMessage = 'Failed to load instrument categories';
          }
        });
        break;
    }
  }

  // toggleCategory removed as it is no longer needed
  /* toggleCategory(category: InstrumentCategory) { ... } */

  toggleCategory(category: InstrumentCategory) {
    category.expanded = !category.expanded;
    
    // Load instrument types if not already loaded
    if (category.expanded && !this.instrumentTypesByCategory[category.id]) {
        if (typeof category.id === 'number') {
           this.loadInstrumentTypes(category.id);
        }
    }
  }

  // Modal State
  showChangePatternModal = false;
  showSaveConfirmModal = false;
  attemptedSave = false; // Track if user attempted to save



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

  confirmSaveInstrument() {
    this.showSaveConfirmModal = false;
    this.proceedWithSave();
  }

  cancelSaveInstrument() {
    this.showSaveConfirmModal = false;
  }

  loadInstrumentTypes(categoryId: number | string) {
    // Skip API call for hardcoded non-numeric categories (e.g. 'water-meter', 'flow-meter')
    if (typeof categoryId === 'string' && isNaN(Number(categoryId))) return;

    this.patternApprovalService.getInstrumentTypesByCategory(categoryId as any).subscribe({
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
    this.attemptedSave = false; // Reset validation state
    
    // Check if this is a Fuel Pump instrument
    if (this.isFuelPumpPattern() && instrumentType.name.toLowerCase().includes('standard')) {
      this.showFuelPumpForm = true;
      return;
    }
    
    // Calculate Fee based on category
    this.calculateFee();

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
      // Meter Fields Initialization
      quantity: undefined,
      nominal_flow_rate: '',
      meter_class: '',
      ratio: '',
      max_admissible_pressure: '',
      max_temperature: '',
      meter_size_dn: '',
      diameter: '',
      position_hv_type: '',
      sealing_mechanism_type: '',
      flow_direction_type: '',
      
      // Electrical Fields
      meter_model: '',
      meter_type: '',
      nominal_voltage: '',
      nominal_frequency: '',
      maximum_current: '',
      transitional_current: '',
      minimum_current: '',
      starting_current: '',
      connection_type: '',
      connection_mode: '',
      alternative_connection_mode: '',
      energy_flow_direction: '',
      meter_constant: '',
      clock_frequency: '',
      environment: '',
      ip_rating: '',
      terminal_arrangement: '',
      insulation_protection_class: '',
      temperature_lower: '',
      temperature_upper: '',
      humidity_class: '',
      hardware_version: '',
      software_version: '',
      remarks: '',
      test_voltage: '',
      test_frequency: '',
      test_connection_mode: '',
      test_remarks: '',

      // Capacity Measure Fields
      material_construction: '',
      year_manufacture: '',
      measurement_unit: '',
      nominal_capacity: '',
      max_permissible_error: '',
      temperature_range: '',
      intended_liquid: '', 
      has_seal_arrangement: '',
      has_adjustment_mechanism: '',
      has_gauge_glass: '',

      serial_numbers: [''], // Start with one serial number field
      manual_calibration_doc: null,
      other_doc: null,
      specification_doc: null,
      type_approval_doc: null
    };
  }

  // Helper Methods
  isFlowMeterCategory(): boolean {
    const flowCategories = ['water-meter', 'flow-meter', 'bulk-flow-meter'];
    return this.selectedInstrumentType && 
           flowCategories.includes(this.selectedInstrumentType.category_id.toString()) ? true : false;
  }

  isElectricalMeterCategory(): boolean {
    return this.selectedInstrumentType && 
           this.selectedInstrumentType.category_id.toString() === 'electrical-meter' ? true : false;
  }

  isCapacityMeasureCategory(): boolean {
    return this.selectedPatternTypeName === 'Capacity Measure' || 
           this.selectedPatternTypeName === 'Capacity Measures';
  }

  shouldShowDocument(docKey: string): boolean {
    if (docKey === 'type_approval_doc') {
      return this.isCapacityMeasureCategory();
    }
    return true;
  }

  calculateFee() {
    let categoryId = '';

    if (this.selectedInstrumentType) {
        categoryId = this.selectedInstrumentType.category_id.toString();
    } else if (this.selectedInstruments.length > 0) {
        // Find category from first selected instrument
        const firstInst = this.selectedInstruments[0];
        // Loop through loaded categories to find the instrument type
        for (const catId of Object.keys(this.instrumentTypesByCategory)) {
             const types = this.instrumentTypesByCategory[catId];
             if (types && types.some(t => t.id === firstInst.instrument_type_id)) {
                 categoryId = catId;
                 break;
             }
        }
    } else {
        this.applicationFee = 0;
        this.feeBaseAmount = 0;
        return;
    }

    // Base calculation for other categories
    const multiplier = 5;
    
    // Special handling for Weighing Instruments
    if (this.selectedPatternTypeName && this.selectedPatternTypeName.toLowerCase().includes('weigh')) {
          let total = this.selectedInstruments.reduce((sum, inst) => sum + (inst.calculated_fee || 0), 0);
          if (this.currentInstrument) {
               total += this.calculateWeighingInstrumentFee(this.currentInstrument);
          }
          this.applicationFee = total;
          return;
    }

    switch (categoryId) {
        case 'water-meter':
            this.feeBaseAmount = 10000;
            break;
        case 'flow-meter':
            this.feeBaseAmount = 500000;
            break;
        case 'bulk-flow-meter':
            this.feeBaseAmount = 2500000;
            break;
        case 'electrical-meter':
            this.feeBaseAmount = 10000;
            break;
        default:
            this.feeBaseAmount = 0;
    }

    this.applicationFee = this.feeBaseAmount * multiplier;
  }

  // --- Weighing Instrument Fee Logic ---
  calculateWeighingInstrumentFee(instrument: SelectedInstrument): number {
    if (!instrument.maximum_capacity) return 0;

    // 1. Parse Maximum Capacity to kg
    const capacityStr = instrument.maximum_capacity.toLowerCase();
    let capacityValue = parseFloat(capacityStr.replace(/[^0-9.]/g, ''));
    
    // Simple unit conversion handling (assuming input might have 'g', 'kg', 'ton')
    // Defaulting to kg if no unit found or if assumed input is in kg as per requirements
    // Improving robustness: check for 'ton' or 't' -> * 1000, check for 'g' (and not kg) -> / 1000
    if (capacityStr.includes('ton') || capacityStr.includes('tonne')) {
        capacityValue *= 1000;
    } else if (capacityStr.endsWith('g') && !capacityStr.endsWith('kg')) {
        capacityValue /= 1000;
    }
    
    if (isNaN(capacityValue)) return 0;

    // 2. Determine Base Amount based on Capacity (Weight-based Fee Table)
    let baseAmount = 0;
    if (capacityValue <= 5) baseAmount = 7000;
    else if (capacityValue <= 10) baseAmount = 7000;
    else if (capacityValue <= 20) baseAmount = 7000; // 10-20
    else if (capacityValue <= 50) baseAmount = 10000;
    else if (capacityValue <= 100) baseAmount = 20000;
    else if (capacityValue <= 200) baseAmount = 30000;
    else if (capacityValue <= 300) baseAmount = 40000;
    else if (capacityValue <= 500) baseAmount = 50000;
    else if (capacityValue <= 2000) baseAmount = 60000;
    else if (capacityValue <= 5000) baseAmount = 150000;
    else if (capacityValue <= 10000) baseAmount = 175000;
    else if (capacityValue <= 30000) baseAmount = 200000;
    else if (capacityValue <= 50000) baseAmount = 300000;
    else if (capacityValue <= 100000) baseAmount = 500000;
    else if (capacityValue <= 200000) baseAmount = 700000;
    else baseAmount = 800000; // Exceeding 200,000kg

    // 3. Apply Usage Multiplier
    let useMultiplier = 1;
    const usage = instrument.instrument_use || 'Normal Trade';
    
    if (usage === 'Pharmaceutical Laboratory') {
        useMultiplier = 3;
    } else if (usage === 'Precision Stones') {
        useMultiplier = 5;
    }
    // 'Normal Trade' stays 1

    let feeAfterUse = baseAmount * useMultiplier;

    // 4. Apply Accuracy Class Multiplier
    let classMultiplier = 1;
    const accuracyClass = (instrument.accuracy_class || '').toLowerCase();
    
    if (accuracyClass === 'i' || accuracyClass === 'ii') {
        classMultiplier = 7.5;
    } else if (accuracyClass === 'iii' || accuracyClass === 'iv') {
        classMultiplier = 5;
    }
    // Fallback if class not selected yet (though valid form requires it) -> 1 or 0? 
    // Assuming 5 as minimal safety or keeping as is if user hasn't selected? 
    // Logic says "If selected...". If not selected, effective fee calculation might be partial.
    // Let's enforce the rule: if data missing, default to minimal valid or handle as incomplete.
    // Here we assume valid input flow.

    const finalFee = feeAfterUse * classMultiplier;
    
    return finalFee;
  }

  onQuantityChange() {
    if (!this.currentInstrument || !this.currentInstrument.quantity) return;
    
    const count = this.currentInstrument.quantity;
    const currentSerials = this.currentInstrument.serial_numbers || [];
    
    // Adjust array size
    if (count > currentSerials.length) {
        // Add more fields
        const toAdd = count - currentSerials.length;
        for (let i = 0; i < toAdd; i++) {
            currentSerials.push('');
        }
    } else if (count < currentSerials.length) {
        // Remove excess fields
        currentSerials.splice(count);
    }
    
    this.currentInstrument.serial_numbers = currentSerials;
  }

  // Track by index for *ngFor
  trackByIndex(index: number, obj: any): any {
    return index;
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
    
    // Handle different file types
    if (file.type === 'application/pdf') {
      const url = URL.createObjectURL(file);
      this.previewFileUrl = this.sanitizer.bypassSecurityTrustResourceUrl(url);
    } else if (file.type.startsWith('image/')) {
      // Handle images (jpg, jpeg, png, etc.)
      const url = URL.createObjectURL(file);
      this.previewFileUrl = this.sanitizer.bypassSecurityTrustResourceUrl(url);
    } else {
      // For Word documents and other unsupported types, show "Preview not available"
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

  // Helper method to check if a field should show error styling
  isFieldInvalid(fieldValue: any): boolean {
    return false; // Using template validation primarily
  }

  // Method to check if current form is complete enough to show fee
  isCurrentFormComplete(): boolean {
    if (!this.currentInstrument) return false;
    
    // Common required fields
    if (!this.currentInstrument.brand_name || !this.currentInstrument.make) return false;

    // Category specific checks
    if (this.selectedPatternTypeName?.toLowerCase().includes('weigh')) {
        // Weighing Instrument Requirements
        const basicReqs = !!(this.currentInstrument.brand_name &&
                  this.currentInstrument.make &&
                  this.currentInstrument.accuracy_class && 
                  this.currentInstrument.quantity && 
                  this.currentInstrument.serial_numbers && 
                  this.currentInstrument.serial_numbers.length > 0 &&
                  this.currentInstrument.maximum_capacity &&
                  this.currentInstrument.scale_type);
        
        if (!basicReqs) return false;

        // Digital specific checks
        if (this.currentInstrument.scale_type === 'digital') {
            return !!(this.currentInstrument.value_e && this.currentInstrument.value_d);
        }
        
        return true;
    } 
    else if (this.isFlowMeterCategory()) {
        // Flow Meter Requirements
        return !!(this.currentInstrument.quantity && 
                  this.currentInstrument.serial_numbers && 
                  this.currentInstrument.serial_numbers.length > 0 &&
                  this.currentInstrument.nominal_flow_rate && 
                  this.currentInstrument.meter_class && 
                  this.currentInstrument.meter_size_dn && 
                  this.currentInstrument.diameter);
    }
    else if (this.isElectricalMeterCategory()) {
        // Electrical Meter Requirements
        return !!(this.currentInstrument.quantity && 
                  this.currentInstrument.serial_numbers && 
                  this.currentInstrument.serial_numbers.length > 0 && 
                  this.currentInstrument.meter_model && 
                  this.currentInstrument.meter_type && 
                  this.currentInstrument.accuracy_class && 
                  this.currentInstrument.nominal_voltage && 
                  this.currentInstrument.nominal_frequency && 
                  this.currentInstrument.maximum_current && 
                  this.currentInstrument.minimum_current);
    }
    else if (this.isCapacityMeasureCategory()) {
         // Capacity Measure Requirements
         return !!(this.currentInstrument.brand_name &&
                   this.currentInstrument.make &&
                   this.currentInstrument.quantity &&
                   this.currentInstrument.measurement_unit &&
                   this.currentInstrument.nominal_capacity &&
                   this.currentInstrument.material_construction && 
                   this.currentInstrument.serial_numbers && 
                   this.currentInstrument.serial_numbers.length > 0);
    }

    // Default basic check for other types
    return !!this.currentInstrument.serial_number; 
  }

  saveInstrumentDetails() {
    if (!this.currentInstrument) return;

    let isValid = false;

    // Validate based on instrument type
    if (this.isFlowMeterCategory()) {
        // Flow Meter Validation
        if (this.currentInstrument.brand_name && 
            this.currentInstrument.quantity != null && this.currentInstrument.quantity > 0 && 
            this.currentInstrument.nominal_flow_rate && 
            this.currentInstrument.meter_class && 
            this.currentInstrument.max_temperature && 
            this.currentInstrument.meter_size_dn && 
            this.currentInstrument.diameter &&
            this.currentInstrument.position_hv_type &&
            this.currentInstrument.sealing_mechanism_type &&
            this.currentInstrument.flow_direction_type) {
            
            isValid = true;
        }
    } else if (this.isElectricalMeterCategory()) {
        // Electrical Meter Validation
        if (this.currentInstrument.brand_name &&
            this.currentInstrument.make && 
            this.currentInstrument.meter_model &&
            this.currentInstrument.quantity != null && this.currentInstrument.quantity > 0 &&
            this.currentInstrument.meter_type &&
            this.currentInstrument.accuracy_class &&
            this.currentInstrument.nominal_voltage &&
            this.currentInstrument.nominal_frequency &&
            this.currentInstrument.maximum_current &&
            this.currentInstrument.minimum_current &&
            this.currentInstrument.connection_type &&
            this.currentInstrument.connection_mode &&
            this.currentInstrument.energy_flow_direction &&
            this.currentInstrument.meter_constant &&
            this.currentInstrument.environment &&
            this.currentInstrument.ip_rating &&
            this.currentInstrument.temperature_lower &&
            this.currentInstrument.temperature_upper &&
            this.currentInstrument.humidity_class) {

           isValid = true;
        }
    } else if (this.isCapacityMeasureCategory()) {
        // Capacity Measure Validation
        if (this.currentInstrument.brand_name &&
            this.currentInstrument.make &&
            this.currentInstrument.quantity != null && this.currentInstrument.quantity > 0 &&
            this.currentInstrument.measurement_unit &&
            this.currentInstrument.nominal_capacity &&
            this.currentInstrument.material_construction &&
            this.currentInstrument.year_manufacture &&
            this.currentInstrument.intended_liquid && 
            this.currentInstrument.max_permissible_error &&
            this.currentInstrument.temperature_range) {
            
            // Check serial numbers
            const serials = this.currentInstrument.serial_numbers || [];
            const allSerialsFilled = serials.every(s => s && s.trim().length > 0);
            
            if (allSerialsFilled && serials.length === this.currentInstrument.quantity) {
                isValid = true;
            }
        }
    } else if (this.selectedPatternTypeName.toLowerCase().includes('weigh')) {
        // Weighing Instrument Validation
        if (this.currentInstrument.brand_name && 
            this.currentInstrument.make && 
            this.currentInstrument.quantity != null && this.currentInstrument.quantity > 0 && 
            this.currentInstrument.accuracy_class &&
            this.currentInstrument.maximum_capacity &&
            this.currentInstrument.manual_calibration_doc &&
            this.currentInstrument.specification_doc) {
            
            // Check serial numbers
            const serials = this.currentInstrument.serial_numbers || [];
            const allSerialsFilled = serials.every(s => s && s.trim().length > 0);
            
            if (allSerialsFilled && serials.length === this.currentInstrument.quantity) {
                isValid = true;
            }
        }
    } else {
        // Standard Validation (other instruments)
        if (this.currentInstrument.brand_name && 
            this.currentInstrument.make && 
            this.currentInstrument.serial_number && 
            this.currentInstrument.maximum_capacity &&
            this.currentInstrument.manual_calibration_doc &&
            this.currentInstrument.specification_doc) {
            isValid = true;
        }
    }

    if (!isValid) {
      this.attemptedSave = true; // Mark that save was attempted
      this.errorMessage = 'Please fill in all required fields (marked with *) and upload all required documents';
      setTimeout(() => this.errorMessage = '', 3000);
      return;
    }

    // All validation passed - show confirmation modal
    this.showSaveConfirmModal = true;

  }

  proceedWithSave() {
    if (!this.currentInstrument) return;

    // Validation already done in saveInstrumentDetails() - just save the instrument
    const instrumentToSave = { ...this.currentInstrument };
    
    // Calculate fee for Weighing Instruments
    if (this.selectedPatternTypeName && this.selectedPatternTypeName.toLowerCase().includes('weigh')) {
        instrumentToSave.calculated_fee = this.calculateWeighingInstrumentFee(instrumentToSave);
    }
    
    this.selectedInstruments.push(instrumentToSave);
    
    // Clear current instrument
    this.currentInstrument = null;
    this.selectedInstrumentType = null;
    this.attemptedSave = false; // Reset attemptedSave for the next instrument
    
    
    this.successMessage = 'Instrument details saved successfully';
    setTimeout(() => this.successMessage = '', 3000);
    
    this.calculateFee();
  }

  cancelInstrumentDetails() {
    this.currentInstrument = null;
    this.selectedInstrumentType = null;
    this.attemptedSave = false; // Reset attemptedSave when cancelling
    this.calculateFee();
  }

  editInstrument(index: number) {
    const instrument = this.selectedInstruments[index];
    
    // Find category to set selectedInstrumentType
    let categoryId = '';
    let foundType: InstrumentType | null = null;

    // Search through all loaded categories
    for (const catId of Object.keys(this.instrumentTypesByCategory)) {
         const types = this.instrumentTypesByCategory[catId];
         foundType = types.find(t => t.id === instrument.instrument_type_id) || null;
         if (foundType) {
             categoryId = catId;
             break;
         }
    }

    if (foundType) {
        this.selectedInstrumentType = foundType;
        
        // Populate currentInstrument with a copy of the data
        this.currentInstrument = { ...instrument };
        
        // Remove from list so it can be re-added (updated)
        this.selectedInstruments.splice(index, 1);
        
        // Recalculate fee
        this.calculateFee();
        
        // Show fuel pump form if applicable
        if (this.isFuelPumpPattern() && foundType.name.toLowerCase().includes('standard')) {
            this.showFuelPumpForm = true;
        }

        // Add scroll to top or form logic here if needed
        window.scroll({ 
          top: 0, 
          left: 0, 
          behavior: 'smooth' 
        });
    }
  }

  removeInstrument(index: number) {
    this.selectedInstruments.splice(index, 1);
    this.successMessage = 'Instrument removed';
    setTimeout(() => this.successMessage = '', 3000);
    this.calculateFee();
  }

  // Submission State
  isSubmitted = false;

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

    // Determine if it's a weighing instrument application (fee per instrument)
    const isWeighing = this.selectedPatternTypeName && this.selectedPatternTypeName.toLowerCase().includes('weigh');

    // Save each instrument
    let savedCount = 0;
    this.selectedInstruments.forEach((instrument, index) => {
      
      // Calculate fee logic:
      // For Weighing: use the instrument's calculated fee
      // For Others (Meters): Fee is global (per application). Assign total fee to the first instrument, 0 for others.
      let feeToSave = 0;
      if (isWeighing) {
          feeToSave = instrument.calculated_fee || 0;
      } else {
          feeToSave = (index === 0) ? this.applicationFee : 0;
      }

      const instrumentData = {
        instrument_type_id: instrument.instrument_type_id,
        brand_name: instrument.brand_name,
        make: instrument.make,
        // Use dynamically entered serial numbers if available, otherwise the single one
        serial_number: instrument.serial_numbers && instrument.serial_numbers.length > 0 
            ? instrument.serial_numbers.join(', ') 
            : instrument.serial_number,
        maximum_capacity: instrument.maximum_capacity,
        
        // Fee
        application_fee: feeToSave,
        pattern_fee: feeToSave,

        // Meter Specific Fields
        quantity: instrument.quantity,
        nominal_flow_rate: instrument.nominal_flow_rate,
        meter_class: instrument.meter_class,
        ratio: instrument.ratio,
        max_admissible_pressure: instrument.max_admissible_pressure,
        max_temperature: instrument.max_temperature,
        meter_size_dn: instrument.meter_size_dn,
        diameter: instrument.diameter,
        position_hv_type: instrument.position_hv_type,
        sealing_mechanism_type: instrument.sealing_mechanism_type,
        flow_direction_type: instrument.flow_direction_type,

        // Electrical Meter Fields
        meter_model: instrument.meter_model,
        meter_type: instrument.meter_type,
        nominal_voltage: instrument.nominal_voltage,
        nominal_frequency: instrument.nominal_frequency,
        maximum_current: instrument.maximum_current,
        transitional_current: instrument.transitional_current,
        minimum_current: instrument.minimum_current,
        starting_current: instrument.starting_current,
        connection_type: instrument.connection_type,
        connection_mode: instrument.connection_mode,
        alternative_connection_mode: instrument.alternative_connection_mode,
        energy_flow_direction: instrument.energy_flow_direction,
        meter_constant: instrument.meter_constant,
        clock_frequency: instrument.clock_frequency,
        environment: instrument.environment,
        ip_rating: instrument.ip_rating,
        terminal_arrangement: instrument.terminal_arrangement,
        insulation_protection_class: instrument.insulation_protection_class,
        temperature_lower: instrument.temperature_lower,
        temperature_upper: instrument.temperature_upper,
        humidity_class: instrument.humidity_class,
        hardware_version: instrument.hardware_version,
        software_version: instrument.software_version,
        remarks: instrument.remarks,
        test_voltage: instrument.test_voltage,
        test_frequency: instrument.test_frequency,
        test_connection_mode: instrument.test_connection_mode,
        test_remarks: instrument.test_remarks,
        
        // Capacity Measure Fields
        material_construction: instrument.material_construction,
        year_manufacture: instrument.year_manufacture,
        measurement_unit: instrument.measurement_unit,
        nominal_capacity: instrument.nominal_capacity,
        max_permissible_error: instrument.max_permissible_error,
        temperature_range: instrument.temperature_range,
        intended_liquid: instrument.intended_liquid,
        has_seal_arrangement: instrument.has_seal_arrangement,
        has_adjustment_mechanism: instrument.has_adjustment_mechanism,
        has_gauge_glass: instrument.has_gauge_glass
      };

      // Convert to FormData for file upload support
      const formData = new FormData();
      
      // Append standard fields
      Object.keys(instrumentData).forEach(key => {
        // @ts-ignore
        const value = instrumentData[key];
        if (value !== null && value !== undefined) {
             formData.append(key, value);
        }
      });

      // Append Files
      if (instrument.manual_calibration_doc) {
        formData.append('manual_calibration_doc', instrument.manual_calibration_doc);
      }
      if (instrument.specification_doc) {
        formData.append('specification_doc', instrument.specification_doc);
      }
      if (instrument.other_doc) {
        formData.append('other_doc', instrument.other_doc);
      }
      if (instrument.type_approval_doc) {
        formData.append('type_approval_doc', instrument.type_approval_doc);
      }

      this.patternApprovalService.addInstrument(this.applicationId!, formData).subscribe({
        next: (response: any) => {
          savedCount++;
          if (savedCount === this.selectedInstruments.length) {
            this.isSaving = false;
            this.isSubmitted = true;
            this.successMessage = 'Application submitted successfully!';
            
            // REMOVED AUTO RESET
            // setTimeout(() => {
            //   this.resetForm();
            // }, 2000);
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

  startNewApplication() {
    this.resetForm();
  }

  resetForm() {
    this.selectedPatternTypeId = null;
    this.instrumentCategories = [];
    this.selectedInstruments = [];
    this.currentInstrument = null;
    this.applicationId = null;
    this.showFuelPumpForm = false;
    this.isSubmitted = false;
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

  get selectedInstrumentCategories(): string {
    const categories: string[] = [];
    
    // Add categories from already selected instruments
    if (this.selectedInstruments && this.selectedInstruments.length > 0) {
        this.selectedInstruments.forEach(i => categories.push(i.instrument_type_name));
    }
    
    // Add category from current instrument draft
    if (this.currentInstrument && this.currentInstrument.instrument_type_name) {
        categories.push(this.currentInstrument.instrument_type_name);
    }
    
    if (categories.length === 0) {
      return 'None';
    }

    // Get unique categories
    const uniqueCategories = categories.filter((value, index, self) => self.indexOf(value) === index);
    return uniqueCategories.join(', ');
  }
}
