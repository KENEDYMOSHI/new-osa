import { CommonModule } from '@angular/common';
import {
  Component,
  EventEmitter,
  Input,
  OnChanges,
  Output,
  SimpleChanges,
} from '@angular/core';
import { SERVICE_TYPES, ServiceType } from '../../../../shared/constants';
import { EQUIPMENT_FORM_CONFIGS } from './configs';
import { EquipmentFormConfig, FormField, ServiceCategory } from './models/form-field.model';

export interface EquipmentRegistrationPayload {
  serviceTypeKey: string;
  serviceTypeLabel: string;
  items: Record<string, any>[];
}

type WizardStep = 1 | 2 | 3;

interface StepMeta {
  number: WizardStep;
  label: string;
  description: string;
}

const CATEGORY_META: Record<ServiceCategory, { label: string; color: string }> = {
  petroleum: { label: 'Petroleum & Fuel', color: '#EF4444' },
  weighing:  { label: 'Weighing & Mass', color: '#3B82F6' },
  length:    { label: 'Length & Volume', color: '#8B5CF6' },
  metering:  { label: 'Metering & Gauges', color: '#10B981' },
  other:     { label: 'Other', color: '#6B7280' },
};

@Component({
  selector: 'app-equipment-registration-wizard',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './equipment-registration-wizard.component.html',
})
export class EquipmentRegistrationWizardComponent implements OnChanges {
  @Input() visible = false;
  @Input() businessName = '';
  @Output() closeWizard = new EventEmitter<void>();
  @Output() register = new EventEmitter<EquipmentRegistrationPayload>();

  currentStep: WizardStep = 1;
  serviceSearchTerm = '';
  selectedServiceKey = '';
  formItems: Record<string, any>[] = [];
  expandedItemIndex = 0;
  activeCategory: ServiceCategory | 'all' = 'all';

  readonly steps: StepMeta[] = [
    { number: 1, label: 'Select Service', description: 'Choose the type of equipment' },
    { number: 2, label: 'Equipment Details', description: 'Fill in equipment information' },
    { number: 3, label: 'Review & Submit', description: 'Confirm and register' },
  ];

  readonly serviceTypes: readonly ServiceType[] = SERVICE_TYPES;

  readonly categories = Object.entries(CATEGORY_META).map(([key, meta]) => ({
    key: key as ServiceCategory,
    ...meta,
  }));

  // ── Lifecycle ─────────────────────────────────────────────────────

  ngOnChanges(changes: SimpleChanges): void {
    if (changes['visible']?.currentValue) {
      this.reset();
    }
  }

  // ── Computed ──────────────────────────────────────────────────────

  get activeConfig(): EquipmentFormConfig | null {
    return EQUIPMENT_FORM_CONFIGS[this.selectedServiceKey] ?? null;
  }

  get filteredServiceTypes(): readonly ServiceType[] {
    const q = this.serviceSearchTerm.trim().toLowerCase();
    return this.serviceTypes.filter((st) => {
      const matchesSearch = !q || st.label.toLowerCase().includes(q);
      const config = EQUIPMENT_FORM_CONFIGS[st.key];
      const matchesCategory =
        this.activeCategory === 'all' || config?.category === this.activeCategory;
      return matchesSearch && matchesCategory;
    });
  }

  get canProceedToStep2(): boolean {
    return this.selectedServiceKey.length > 0;
  }

  get canProceedToStep3(): boolean {
    if (!this.activeConfig) return false;
    const requiredFields = this.activeConfig.fields.filter((f) => f.required);
    return this.formItems.every((item) =>
      requiredFields.every((f) => {
        const val = item[f.key];
        return val !== undefined && val !== null && String(val).trim().length > 0;
      }),
    );
  }

  get filledFieldCount(): number {
    if (!this.activeConfig) return 0;
    return this.formItems.reduce((total, item) => {
      return (
        total +
        this.activeConfig!.fields.filter((f) => {
          const v = item[f.key];
          return v !== undefined && v !== null && String(v).trim().length > 0;
        }).length
      );
    }, 0);
  }

  get totalFieldCount(): number {
    if (!this.activeConfig) return 0;
    return this.activeConfig.fields.length * this.formItems.length;
  }

  // ── Step navigation ───────────────────────────────────────────────

  goToStep(step: WizardStep): void {
    if (step === 2 && !this.canProceedToStep2) return;
    if (step === 3 && !this.canProceedToStep3) return;
    if (step < this.currentStep || step === this.currentStep + 1) {
      this.currentStep = step;
    }
  }

  nextStep(): void {
    if (this.currentStep < 3) {
      this.goToStep((this.currentStep + 1) as WizardStep);
    }
  }

  prevStep(): void {
    if (this.currentStep > 1) {
      this.currentStep = (this.currentStep - 1) as WizardStep;
    }
  }

  // ── Step 1: Service selection ─────────────────────────────────────

  setServiceSearch(value: string): void {
    this.serviceSearchTerm = value;
  }

  setCategory(cat: ServiceCategory | 'all'): void {
    this.activeCategory = cat;
  }

  selectService(key: string): void {
    this.selectedServiceKey = key;
  }

  isServiceSelected(key: string): boolean {
    return this.selectedServiceKey === key;
  }

  getServiceConfig(key: string): EquipmentFormConfig | undefined {
    return EQUIPMENT_FORM_CONFIGS[key];
  }

  getCategoryColor(key: string): string {
    const config = EQUIPMENT_FORM_CONFIGS[key];
    return config ? CATEGORY_META[config.category]?.color ?? '#6B7280' : '#6B7280';
  }

  getCategoryLabel(key: string): string {
    const config = EQUIPMENT_FORM_CONFIGS[key];
    return config ? CATEGORY_META[config.category]?.label ?? 'Other' : 'Other';
  }

  // ── Step 2: Form management ───────────────────────────────────────

  addItem(): void {
    this.formItems = [...this.formItems, {}];
    this.expandedItemIndex = this.formItems.length - 1;
  }

  removeItem(index: number): void {
    if (this.formItems.length <= 1) return;
    this.formItems = this.formItems.filter((_, i) => i !== index);
    if (this.expandedItemIndex >= this.formItems.length) {
      this.expandedItemIndex = this.formItems.length - 1;
    }
  }

  toggleItem(index: number): void {
    this.expandedItemIndex = this.expandedItemIndex === index ? -1 : index;
  }

  updateField(itemIndex: number, fieldKey: string, value: any): void {
    const updated = [...this.formItems];
    updated[itemIndex] = { ...updated[itemIndex], [fieldKey]: value };
    this.formItems = updated;
  }

  getFieldValue(itemIndex: number, fieldKey: string): any {
    return this.formItems[itemIndex]?.[fieldKey] ?? '';
  }

  isFieldFilled(itemIndex: number, field: FormField): boolean {
    const v = this.getFieldValue(itemIndex, field.key);
    return v !== undefined && v !== null && String(v).trim().length > 0;
  }

  getItemProgress(itemIndex: number): number {
    if (!this.activeConfig) return 0;
    const total = this.activeConfig.fields.length;
    const filled = this.activeConfig.fields.filter((f) =>
      this.isFieldFilled(itemIndex, f),
    ).length;
    return total > 0 ? Math.round((filled / total) * 100) : 0;
  }

  // ── Step 3: Review helpers ────────────────────────────────────────

  getDisplayValue(itemIndex: number, field: FormField): string {
    const raw = this.getFieldValue(itemIndex, field.key);
    if (!raw || String(raw).trim().length === 0) return '—';

    if (field.type === 'select' && field.options) {
      const match = field.options.find((o) => o.value === raw);
      return match?.label ?? raw;
    }

    if (field.type === 'file') {
      return typeof raw === 'string' ? raw : 'File selected';
    }

    return String(raw);
  }

  // ── Actions ───────────────────────────────────────────────────────

  handleClose(): void {
    this.closeWizard.emit();
  }

  handleSubmit(): void {
    if (!this.activeConfig || !this.canProceedToStep3) return;
    this.register.emit({
      serviceTypeKey: this.selectedServiceKey,
      serviceTypeLabel: this.activeConfig.serviceTypeLabel,
      items: this.formItems,
    });
  }

  // ── Internal ──────────────────────────────────────────────────────

  private reset(): void {
    this.currentStep = 1;
    this.serviceSearchTerm = '';
    this.selectedServiceKey = '';
    this.formItems = [{}];
    this.expandedItemIndex = 0;
    this.activeCategory = 'all';
  }
}
