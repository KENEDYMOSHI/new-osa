import { CommonModule } from '@angular/common';
import { Component } from '@angular/core';
import { Router, RouterModule } from '@angular/router';
import { SERVICE_TYPES, ServiceType } from '../../../../shared/constants';
import { EQUIPMENT_FORM_CONFIGS } from '../forms/configs';
import { EquipmentFormConfig, FormField, ServiceCategory } from '../forms/models/form-field.model';
import { EquipmentService } from '../../../../core/services/equipment.service';

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
  icon: string;
}

const CATEGORY_META: Record<ServiceCategory, { label: string; color: string }> = {
  petroleum: { label: 'Petroleum & Fuel', color: '#EF4444' },
  weighing:  { label: 'Weighing & Mass',  color: '#3B82F6' },
  length:    { label: 'Length & Volume',  color: '#8B5CF6' },
  metering:  { label: 'Metering & Gauges', color: '#10B981' },
  other:     { label: 'Other',             color: '#6B7280' },
};

@Component({
  selector: 'app-add-equipment-page',
  standalone: true,
  imports: [CommonModule, RouterModule],
  templateUrl: './add-equipment-page.component.html',
})
export class AddEquipmentPageComponent {
  loading = false;
  
  constructor(private router: Router, private equipmentService: EquipmentService) {}

  currentStep: WizardStep = 1;
  serviceSearchTerm = '';
  selectedServiceKey = '';
  formItems: Record<string, any>[] = [{}];
  expandedItemIndex = 0;
  activeCategory: ServiceCategory | 'all' = 'all';
  submitted = false;

  readonly businessName = 'NOVAS Agency Limited';

  readonly steps: StepMeta[] = [
    {
      number: 1,
      label: 'Select Service',
      description: 'Choose the type of equipment you are registering',
      icon: 'M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2M9 5a2 2 0 0 0 2-2h2a2 2 0 0 0 2 2',
    },
    {
      number: 2,
      label: 'Equipment Details',
      description: 'Fill in the specific details for your equipment',
      icon: 'M11 5H6a2 2 0 0 0-2 2v11a2 2 0 0 0 2 2h11a2 2 0 0 0 2-2v-5m-1.414-9.414a2 2 0 1 1 2.828 2.828L11.828 15H9v-2.828l8.586-8.586z',
    },
    {
      number: 3,
      label: 'Review & Submit',
      description: 'Review your information and confirm registration',
      icon: 'M9 12l2 2 4-4m6 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0',
    },
  ];

  readonly serviceTypes: readonly ServiceType[] = SERVICE_TYPES;

  readonly categories = Object.entries(CATEGORY_META).map(([key, meta]) => ({
    key: key as ServiceCategory,
    ...meta,
  }));

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

  get overallProgress(): number {
    return this.totalFieldCount > 0
      ? Math.round((this.filledFieldCount / this.totalFieldCount) * 100)
      : 0;
  }

  // ── Step navigation ───────────────────────────────────────────────

  goToStep(step: WizardStep): void {
    if (step === 2 && !this.canProceedToStep2) return;
    if (step === 3 && !this.canProceedToStep3) return;
    this.currentStep = step;
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }

  nextStep(): void {
    if (this.currentStep < 3) {
      this.goToStep((this.currentStep + 1) as WizardStep);
    }
  }

  prevStep(): void {
    if (this.currentStep > 1) {
      this.currentStep = (this.currentStep - 1) as WizardStep;
      window.scrollTo({ top: 0, behavior: 'smooth' });
    }
  }

  // ── Step 1 ────────────────────────────────────────────────────────

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

  // ── Step 2 ────────────────────────────────────────────────────────

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
    this.formItems[itemIndex][fieldKey] = value;
  }

  updateFile(itemIndex: number, fieldKey: string, file: File): void {
    if (file) {
      this.formItems[itemIndex][fieldKey] = file;
    }
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

  // ── Step 3 ────────────────────────────────────────────────────────

  getDisplayValue(itemIndex: number, field: FormField): string {
    const raw = this.getFieldValue(itemIndex, field.key);
    if (!raw || String(raw).trim().length === 0) return '—';
    if (field.type === 'select' && field.options) {
      const match = field.options.find((o) => o.value === raw);
      return match?.label ?? raw;
    }
    if (field.type === 'file') return raw?.name || (typeof raw === 'string' ? raw : 'File selected');
    return String(raw);
  }

  // ── Submit ────────────────────────────────────────────────────────

  handleSubmit(): void {
    if (!this.activeConfig || !this.canProceedToStep3) return;
    this.loading = true;
    
    // Separate files from regular text items
    const payloadItems: any[] = [];
    const files: any[] = [];
    
    this.formItems.forEach((item, itemIndex) => {
      const cleanItem: any = {};
      Object.keys(item).forEach(key => {
        if (item[key] instanceof File) {
          files.push({ itemIndex, fieldKey: key, file: item[key] });
        } else {
          cleanItem[key] = item[key];
        }
      });
      payloadItems.push(cleanItem);
    });

    const payload = {
      serviceTypeKey: this.activeConfig.serviceTypeKey,
      serviceTypeLabel: this.activeConfig.serviceTypeLabel,
      category: this.activeConfig.category,
      items: payloadItems
    };

    this.equipmentService.registerEquipment(payload, files).subscribe({
      next: (res) => {
        this.loading = false;
        this.submitted = true;
        setTimeout(() => {
          this.router.navigate(['/business/equipments']);
        }, 2000);
      },
      error: (err) => {
        this.loading = false;
        console.error('Submission failed', err);
        // Error handling could be improved with Toast
      }
    });
  }

  goBack(): void {
    this.router.navigate(['/business/equipments']);
  }
}
