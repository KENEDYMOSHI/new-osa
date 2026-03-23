import { CommonModule } from '@angular/common';
import { Component, EventEmitter, Input, OnChanges, Output, SimpleChanges } from '@angular/core';

export type NewEquipmentPayload = {
  equipmentName: string;
  serviceType: string;
};

@Component({
  selector: 'app-add-equipment-modal',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './add-equipment-modal.component.html',
})
export class AddEquipmentModalComponent implements OnChanges {
  @Input() visible = false;
  @Input() serviceTypes: string[] = [];
  @Input() businessName = '';

  @Output() close = new EventEmitter<void>();
  @Output() create = new EventEmitter<NewEquipmentPayload>();

  serviceTypeSearchTerm = '';
  equipmentNameDraft = '';
  selectedServiceType = '';

  ngOnChanges(changes: SimpleChanges): void {
    if (changes['visible']?.currentValue) {
      this.resetForm();
    }
  }

  get filteredServiceTypes(): string[] {
    const query = this.serviceTypeSearchTerm.trim().toLowerCase();

    if (!query) {
      return this.serviceTypes;
    }

    return this.serviceTypes.filter((serviceType) =>
      serviceType.toLowerCase().includes(query),
    );
  }

  get canCreateEquipment(): boolean {
    return (
      this.equipmentNameDraft.trim().length > 0 &&
      this.selectedServiceType.trim().length > 0
    );
  }

  setEquipmentName(value: string): void {
    this.equipmentNameDraft = value;
  }

  setServiceTypeSearchTerm(value: string): void {
    this.serviceTypeSearchTerm = value;
  }

  selectServiceType(serviceType: string): void {
    this.selectedServiceType = serviceType;
  }

  handleClose(): void {
    this.close.emit();
  }

  handleCreate(): void {
    if (!this.canCreateEquipment) {
      return;
    }

    this.create.emit({
      equipmentName: this.equipmentNameDraft.trim(),
      serviceType: this.selectedServiceType,
    });
  }

  private resetForm(): void {
    this.serviceTypeSearchTerm = '';
    this.equipmentNameDraft = '';
    this.selectedServiceType = '';
  }
}
