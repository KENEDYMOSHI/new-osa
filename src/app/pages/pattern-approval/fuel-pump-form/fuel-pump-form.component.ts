import { Component, OnInit, Output, EventEmitter } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { DomSanitizer, SafeResourceUrl } from '@angular/platform-browser';
import { AppModalComponent } from '../../../components/app-modal/app-modal.component';

interface FuelPumpFormData {
  // Step 1: Manufacturer Details
  manufacturerName: string;
  countryOfManufacture: string;
  
  // Step 2: Fuel Pump Identification
  make: string;
  model: string;
  quantityOfPumps: number;
  serialNumbers: string[];
  manufacturingYear: string;
  numberOfNozzles: number;
  dispenserType: string;
  
  // Step 3: Metrological Characteristics
  measuredQuantity: string; // Fixed: "Volume"
  fuelType: string; // Changed from array to single string
  otherFuelType: string;
  minFlowRate: number | null;
  maxFlowRate: number | null;
  minMeasuredVolume: number | null;
  operatingTempMin: number | null;
  operatingTempMax: number | null;
  
  // Step 4: Accuracy & Performance
  declaredAccuracyClass: string;
  maxPermissibleError: string;
  
  // Step 5: Indicating & Power System
  volumeIndicatorType: string;
  priceDisplay: string;
  displayLocation: string[];
  powerSupply: string[];
  
  // Step 6: Software Information (Conditional)
  softwareVersion: string;
  softwareLegallyRelevant: string;
  softwareProtectionMethod: string[];
  eventLogAvailable: string;
  
  // Step 7: Sealing & Security
  adjustmentPoints: string;
  sealType: string[];
  sealLocations: string;
  
  // Step 8: Installation Information
  intendedInstallation: string[];
  intendedCountryOfUse: string;
  installationManualAvailable: string;
  
  // Step 9: Supporting Documents
  documents: {
    calibrationManual: File | null;
    userManual: File | null;
    pumpExteriorPhoto: File | null;
    nameplatePhoto: File | null;
    displayPhoto: File | null;
    sealingPointsPhoto: File | null;
    typeExaminationCert: File | null;
    softwareDocumentation: File | null;
  };
}

@Component({
  selector: 'app-fuel-pump-form',
  standalone: true,
  imports: [CommonModule, FormsModule, AppModalComponent],
  templateUrl: './fuel-pump-form.component.html',
  styleUrl: './fuel-pump-form.component.css'
})
export class FuelPumpFormComponent implements OnInit {
  @Output() formSubmitted = new EventEmitter<any>();
  @Output() formCancelled = new EventEmitter<void>();

  currentStep = 1;
  totalSteps = 10;
  
  formData: FuelPumpFormData = {
    manufacturerName: '',
    countryOfManufacture: '',
    make: '',
    model: '',
    quantityOfPumps: 1,
    serialNumbers: [''],
    manufacturingYear: '',
    numberOfNozzles: 1,
    dispenserType: '',
    measuredQuantity: 'Volume',
    fuelType: '', // Initialized as empty string
    otherFuelType: '',
    minFlowRate: null,
    maxFlowRate: null,
    minMeasuredVolume: null,
    operatingTempMin: null,
    operatingTempMax: null,
    declaredAccuracyClass: '',
    maxPermissibleError: '',
    volumeIndicatorType: '',
    priceDisplay: '',
    displayLocation: [],
    powerSupply: [],
    softwareVersion: '',
    softwareLegallyRelevant: '',
    softwareProtectionMethod: [],
    eventLogAvailable: '',
    adjustmentPoints: '',
    sealType: [],
    sealLocations: '',
    intendedInstallation: [],
    intendedCountryOfUse: '',
    installationManualAvailable: '',
    documents: {
      calibrationManual: null,
      userManual: null,
      pumpExteriorPhoto: null,
      nameplatePhoto: null,
      displayPhoto: null,
      sealingPointsPhoto: null,
      typeExaminationCert: null,
      softwareDocumentation: null
    }
  };

  countries = [
    'Tanzania', 'Kenya', 'Uganda', 'Rwanda', 'Burundi', 'South Sudan',
    'China', 'Germany', 'United States', 'Japan', 'South Korea', 'India',
    'United Kingdom', 'France', 'Italy', 'Other'
  ];

  fuelTypeOptions = ['Petrol', 'Diesel', 'Kerosene', 'Other'];
  dispenserTypeOptions = ['Single hose', 'Multi-hose', 'Multi-product'];
  displayLocationOptions = ['Customer side', 'Operator side', 'Both'];
  powerSupplyOptions = ['Mains', 'Generator', 'Solar', 'Battery backup'];
  softwareProtectionOptions = ['Password', 'Hardware seal', 'Secure module'];
  sealTypeOptions = ['Wire seal', 'Lead seal', 'Electronic seal'];
  installationOptions = ['Fixed fuel station', 'Mobile tanker'];

  errorMessage = '';
  autoSaveInterval: any;
  
  // Searchable Dropdown
  showCountryDropdown = false;
  countrySearchTerm = '';
  filteredCountries: string[] = [];

  // Preview Modal State
  showPreviewModal = false;
  previewTitle = '';
  previewFileUrl: SafeResourceUrl | null = null;
  previewFileType = '';

  constructor(private sanitizer: DomSanitizer) {}

  ngOnInit() {
    this.loadDraft();
    this.startAutoSave();
    this.filteredCountries = [...this.countries];
  }

  ngOnDestroy() {
    if (this.autoSaveInterval) {
      clearInterval(this.autoSaveInterval);
    }
  }
  
  // Searchable Country Dropdown
  toggleCountryDropdown() {
    this.showCountryDropdown = !this.showCountryDropdown;
    if (this.showCountryDropdown) {
      this.countrySearchTerm = '';
      this.filteredCountries = [...this.countries];
    }
  }
  
  filterCountries() {
    const searchTerm = this.countrySearchTerm.toLowerCase();
    this.filteredCountries = this.countries.filter(country => 
      country.toLowerCase().includes(searchTerm)
    );
  }
  
  selectCountry(country: string) {
    this.formData.countryOfManufacture = country;
    this.showCountryDropdown = false;
    this.countrySearchTerm = '';
  }
  
  // Display Location Logic
  onDisplayLocationChange(value: string) {
    if (value === 'Both') {
      // If "Both" is selected, select Customer side and Operator side
      if (this.isChecked(this.formData.displayLocation, 'Both')) {
        // Both is now checked, so check the other two
        if (!this.isChecked(this.formData.displayLocation, 'Customer side')) {
          this.formData.displayLocation.push('Customer side');
        }
        if (!this.isChecked(this.formData.displayLocation, 'Operator side')) {
          this.formData.displayLocation.push('Operator side');
        }
      } else {
        // Both is now unchecked, so uncheck the other two
        this.formData.displayLocation = this.formData.displayLocation.filter(
          loc => loc !== 'Customer side' && loc !== 'Operator side'
        );
      }
    } else {
      // If Customer side or Operator side is unchecked, uncheck Both
      if (!this.isChecked(this.formData.displayLocation, value)) {
        this.formData.displayLocation = this.formData.displayLocation.filter(loc => loc !== 'Both');
      }
      // If both Customer side and Operator side are checked, check Both
      if (this.isChecked(this.formData.displayLocation, 'Customer side') && 
          this.isChecked(this.formData.displayLocation, 'Operator side')) {
        if (!this.isChecked(this.formData.displayLocation, 'Both')) {
          this.formData.displayLocation.push('Both');
        }
      }
    }
  }

  // Serial Number Management
  onQuantityChange() {
    const quantity = this.formData.quantityOfPumps || 1;
    const currentLength = this.formData.serialNumbers.length;
    
    if (quantity > currentLength) {
      // Add more serial number fields
      for (let i = currentLength; i < quantity; i++) {
        this.formData.serialNumbers.push('');
      }
    } else if (quantity < currentLength) {
      // Remove extra serial number fields
      this.formData.serialNumbers = this.formData.serialNumbers.slice(0, quantity);
    }
  }

  // Checkbox Toggle
  toggleCheckbox(array: string[], value: string) {
    const index = array.indexOf(value);
    if (index > -1) {
      array.splice(index, 1);
    } else {
      array.push(value);
    }
  }

  isChecked(array: string[], value: string): boolean {
    return array.includes(value);
  }

  // File Upload
  onFileSelected(event: any, documentKey: string) {
    const file = event.target.files[0];
    if (file) {
      // Validate file size (max 10MB)
      if (file.size > 10 * 1024 * 1024) {
        this.errorMessage = 'File size must be less than 10MB';
        return;
      }
      
      // Validate file type
      const allowedTypes = ['application/pdf', 'image/jpeg', 'image/png', 'image/jpg'];
      if (!allowedTypes.includes(file.type)) {
        this.errorMessage = 'Only PDF, JPG, and PNG files are allowed';
        return;
      }
      
      this.formData.documents[documentKey as keyof typeof this.formData.documents] = file;
      this.errorMessage = '';
    }
  }

  removeDocument(documentKey: string) {
    this.formData.documents[documentKey as keyof typeof this.formData.documents] = null;
  }

  openPreview(documentKey: string, title: string) {
    const file = this.formData.documents[documentKey as keyof typeof this.formData.documents];
    if (file) {
      this.previewTitle = title;
      this.previewFileType = file.type;
      
      const url = URL.createObjectURL(file);
      this.previewFileUrl = this.sanitizer.bypassSecurityTrustResourceUrl(url);
      this.showPreviewModal = true;
    }
  }

  closePreview() {
    this.showPreviewModal = false;
    this.previewFileUrl = null;
    this.previewTitle = '';
  }

  // Navigation
  nextStep() {
    if (this.validateCurrentStep()) {
      if (this.currentStep < this.totalSteps) {
        this.currentStep++;
      }
    }
  }

  previousStep() {
    if (this.currentStep > 1) {
      this.currentStep--;
    }
  }

  goToStep(step: number) {
    if (step <= this.currentStep || this.isStepCompleted(step - 1)) {
      this.currentStep = step;
    }
  }

  isStepCompleted(step: number): boolean {
    // Check if a step has been completed
    return step < this.currentStep;
  }

  // Validation
  validateCurrentStep(): boolean {
    this.errorMessage = '';
    
    switch (this.currentStep) {
      case 1: // Manufacturer Details
        if (!this.formData.manufacturerName.trim()) {
          this.errorMessage = 'Manufacturer Name is required';
          return false;
        }
        if (!this.formData.countryOfManufacture) {
          this.errorMessage = 'Country of Manufacture is required';
          return false;
        }
        break;
        
      case 2: // Fuel Pump Identification
        if (!this.formData.make.trim()) {
          this.errorMessage = 'Make/Brand is required';
          return false;
        }
        if (!this.formData.model.trim()) {
          this.errorMessage = 'Model/Type Designation is required';
          return false;
        }
        if (!this.formData.quantityOfPumps || this.formData.quantityOfPumps < 1) {
          this.errorMessage = 'Quantity of Pumps must be at least 1';
          return false;
        }
        // Validate serial numbers
        const serialNumbers = this.formData.serialNumbers.filter(sn => sn.trim());
        if (serialNumbers.length !== this.formData.quantityOfPumps) {
          this.errorMessage = 'All serial numbers must be filled';
          return false;
        }
        // Check for duplicate serial numbers
        const uniqueSerials = new Set(serialNumbers);
        if (uniqueSerials.size !== serialNumbers.length) {
          this.errorMessage = 'Serial numbers must be unique';
          return false;
        }
        if (!this.formData.numberOfNozzles || this.formData.numberOfNozzles < 1) {
          this.errorMessage = 'Number of Nozzles is required';
          return false;
        }
        if (!this.formData.dispenserType) {
          this.errorMessage = 'Dispenser Type is required';
          return false;
        }
        break;
        
      case 3: // Metrological Characteristics
        if (!this.formData.fuelType) {
          this.errorMessage = 'Please select a Fuel Type';
          return false;
        }
        if (this.formData.fuelType === 'Other' && !this.formData.otherFuelType.trim()) {
          this.errorMessage = 'Please specify Other Fuel Type';
          return false;
        }
        if (!this.formData.minFlowRate || this.formData.minFlowRate <= 0) {
          this.errorMessage = 'Minimum Flow Rate is required and must be greater than 0';
          return false;
        }
        if (!this.formData.maxFlowRate || this.formData.maxFlowRate <= 0) {
          this.errorMessage = 'Maximum Flow Rate is required and must be greater than 0';
          return false;
        }
        if (this.formData.maxFlowRate <= this.formData.minFlowRate) {
          this.errorMessage = 'Maximum Flow Rate must be greater than Minimum Flow Rate';
          return false;
        }
        if (!this.formData.minMeasuredVolume || this.formData.minMeasuredVolume <= 0) {
          this.errorMessage = 'Minimum Measured Volume is required';
          return false;
        }
        if (this.formData.operatingTempMin === null || this.formData.operatingTempMax === null) {
          this.errorMessage = 'Operating Temperature Range is required';
          return false;
        }
        break;
        
      case 4: // Accuracy & Performance
        if (!this.formData.declaredAccuracyClass.trim()) {
          this.errorMessage = 'Declared Accuracy Class is required';
          return false;
        }
        if (!this.formData.maxPermissibleError.trim()) {
          this.errorMessage = 'Maximum Permissible Error is required';
          return false;
        }
        break;
        
      case 5: // Indicating & Power System
        if (!this.formData.volumeIndicatorType) {
          this.errorMessage = 'Volume Indicator Type is required';
          return false;
        }
        if (!this.formData.priceDisplay) {
          this.errorMessage = 'Price Display selection is required';
          return false;
        }
        if (this.formData.displayLocation.length === 0) {
          this.errorMessage = 'At least one Display Location must be selected';
          return false;
        }
        if (this.formData.powerSupply.length === 0) {
          this.errorMessage = 'At least one Power Supply option must be selected';
          return false;
        }
        break;
        
      case 6: // Software Information (only if Electronic)
        if (this.formData.volumeIndicatorType === 'Electronic') {
          if (!this.formData.softwareVersion.trim()) {
            this.errorMessage = 'Software Version is required for electronic pumps';
            return false;
          }
          if (!this.formData.softwareLegallyRelevant) {
            this.errorMessage = 'Software Legally Relevant selection is required';
            return false;
          }
          if (this.formData.softwareProtectionMethod.length === 0) {
            this.errorMessage = 'At least one Software Protection Method must be selected';
            return false;
          }
          if (!this.formData.eventLogAvailable) {
            this.errorMessage = 'Event Log Available selection is required';
            return false;
          }
        }
        break;
        
      case 7: // Sealing & Security
        if (!this.formData.adjustmentPoints.trim()) {
          this.errorMessage = 'Adjustment Points Requiring Sealing is required';
          return false;
        }
        if (this.formData.sealType.length === 0) {
          this.errorMessage = 'At least one Seal Type must be selected';
          return false;
        }
        if (!this.formData.sealLocations.trim()) {
          this.errorMessage = 'Seal Locations is required';
          return false;
        }
        break;
        
      case 8: // Installation Information
        if (this.formData.intendedInstallation.length === 0) {
          this.errorMessage = 'At least one Intended Installation must be selected';
          return false;
        }
        if (!this.formData.intendedCountryOfUse.trim()) {
          this.errorMessage = 'Intended Country of Use is required';
          return false;
        }
        if (!this.formData.installationManualAvailable) {
          this.errorMessage = 'Installation Manual Available selection is required';
          return false;
        }
        break;
        
      case 9: // Supporting Documents
        // At least calibration manual and user manual are required
        if (!this.formData.documents.calibrationManual) {
          this.errorMessage = 'Calibration Manual is required';
          return false;
        }
        if (!this.formData.documents.userManual) {
          this.errorMessage = 'User/Installation Manual is required';
          return false;
        }
        break;
    }
    
    return true;
  }

  // Draft Management
  saveDraft() {
    localStorage.setItem('fuelPumpFormDraft', JSON.stringify(this.formData));
  }

  loadDraft() {
    const draft = localStorage.getItem('fuelPumpFormDraft');
    if (draft) {
      try {
        const parsedDraft = JSON.parse(draft);
        // Don't load documents from draft (files can't be serialized)
        const { documents, ...restOfData } = parsedDraft;
        this.formData = { ...this.formData, ...restOfData };
      } catch (e) {
        console.error('Error loading draft:', e);
      }
    }
  }

  startAutoSave() {
    this.autoSaveInterval = setInterval(() => {
      this.saveDraft();
    }, 30000); // Auto-save every 30 seconds
  }

  clearDraft() {
    localStorage.removeItem('fuelPumpFormDraft');
  }

  // Form Submission
  submitForm() {
    if (!this.validateCurrentStep()) {
      return;
    }
    
    // Create FormData for file upload
    const formDataToSubmit = new FormData();
    
    // Append all form fields
    Object.keys(this.formData).forEach(key => {
      if (key === 'documents') {
        // Handle documents separately
        Object.keys(this.formData.documents).forEach(docKey => {
          const file = this.formData.documents[docKey as keyof typeof this.formData.documents];
          if (file) {
            formDataToSubmit.append(docKey, file);
          }
        });
      } else if (Array.isArray(this.formData[key as keyof FuelPumpFormData])) {
        // Handle arrays
        formDataToSubmit.append(key, JSON.stringify(this.formData[key as keyof FuelPumpFormData]));
      } else {
        formDataToSubmit.append(key, String(this.formData[key as keyof FuelPumpFormData]));
      }
    });
    
    this.formSubmitted.emit(formDataToSubmit);
    this.clearDraft();
  }

  cancel() {
    if (confirm('Are you sure you want to cancel? All unsaved changes will be lost.')) {
      this.clearDraft();
      this.formCancelled.emit();
    }
  }

  // Conditional Display
  shouldShowSoftwareSection(): boolean {
    return this.formData.volumeIndicatorType === 'Electronic';
  }
}
