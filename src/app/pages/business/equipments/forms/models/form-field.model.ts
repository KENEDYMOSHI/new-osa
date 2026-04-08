export type FormFieldType = 'text' | 'number' | 'date' | 'select' | 'file' | 'textarea';

export type ServiceCategory = 'petroleum' | 'weighing' | 'length' | 'metering' | 'other';

export interface FormFieldOption {
  value: string;
  label: string;
}

export interface FormField {
  key: string;
  label: string;
  type: FormFieldType;
  placeholder?: string;
  required?: boolean;
  options?: FormFieldOption[];
  accept?: string;
  gridSpan?: 1 | 2;
  hint?: string;
}

export interface EquipmentFormConfig {
  serviceTypeKey: string;
  serviceTypeLabel: string;
  allowMultiple: boolean;
  itemLabel: string;
  description: string;
  category: ServiceCategory;
  fields: FormField[];
}
