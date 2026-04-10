import { CommonModule } from '@angular/common';
import { Component, OnInit } from '@angular/core';
import { RouterModule } from '@angular/router';
import { EquipmentService } from '../../../core/services/equipment.service';

type EquipmentStatus = 'all' | 'draft' | 'pending' | 'verified' | 'rejected';

export interface EquipmentRegistration {
  id: number;
  user_uuid: string;
  registration_no: string;
  service_type_key: string;
  service_type_label: string;
  category: string;
  equipment_data: any;
  status: string;
  created_at: string;
  
  // Computed for frontend convenience
  _displayName?: string;
}

type StatusFilterOption = {
  label: string;
  value: EquipmentStatus;
};

@Component({
  selector: 'app-business-equipments',
  standalone: true,
  imports: [CommonModule, RouterModule],
  templateUrl: './business-equipments.component.html',
})
export class BusinessEquipmentsComponent implements OnInit {
  readonly statusFilters: StatusFilterOption[] = [
    { label: 'All', value: 'all' },
    { label: 'Draft', value: 'draft' },
    { label: 'Pending', value: 'pending' },
    { label: 'Verified', value: 'verified' },
    { label: 'Rejected', value: 'rejected' },
  ];

  equipments: EquipmentRegistration[] = [];
  isLoading = true;

  selectedStatus: EquipmentStatus = 'all';
  searchTerm = '';
  
  // We can fetch business details from AuthService, for now hardcoding or leaving empty
  readonly businessName = 'My Business Profile';

  // Modal State
  selectedEquipment: EquipmentRegistration | null = null;
  isModalOpen = false;

  constructor(private equipmentService: EquipmentService) {}

  ngOnInit(): void {
    this.fetchEquipments();
  }

  fetchEquipments(): void {
    this.isLoading = true;
    this.equipmentService.getEquipments().subscribe({
      next: (data) => {
        this.equipments = data.map((eq: EquipmentRegistration) => {
           eq._displayName = this.extractEquipmentName(eq);
           return eq;
        });
        this.isLoading = false;
      },
      error: (err) => {
        console.error('Failed to load equipments', err);
        this.isLoading = false;
      }
    });
  }

  /**
   * Intelligently extracts a display name from the JSON data
   */
  extractEquipmentName(eq: EquipmentRegistration): string {
    if (!eq.equipment_data) return eq.registration_no || 'Unknown Equipment';
    
    // Look for common name paths in our dynamic JSON
    const data = eq.equipment_data;
    const nameKeys = ['pumpName', 'tankNumber', 'tankName', 'weighbridgeName', 'meterName', 'scaleName', 'balanceName', 'instrumentName', 'ruleName', 'tapeName', 'measureName', 'weigherName', 'wareName', 'vehicleReg'];
    
    for (const key of nameKeys) {
      if (data[key]) return data[key];
    }
    
    return eq.registration_no || `${eq.service_type_label} Item`;
  }

  get filteredEquipments(): EquipmentRegistration[] {
    return this.equipments.filter((equipment) => {
      const matchesStatus =
        this.selectedStatus === 'all' || equipment.status === this.selectedStatus;
      const matchesSearch =
        this.searchTerm.trim().length === 0 ||
        (equipment._displayName || '').toLowerCase().includes(this.searchTerm.trim().toLowerCase()) ||
        equipment.registration_no?.toLowerCase().includes(this.searchTerm.trim().toLowerCase());
      return matchesStatus && matchesSearch;
    });
  }

  get totalEquipments(): number {
    return this.equipments.length;
  }

  setStatusFilter(status: EquipmentStatus): void {
    this.selectedStatus = status;
  }

  setSearchTerm(value: string): void {
    this.searchTerm = value;
  }

  getStatusCount(status: string): number {
    return this.equipments.filter((e) => e.status === status).length;
  }

  getFilterCount(status: EquipmentStatus): number {
    return status === 'all' ? this.totalEquipments : this.getStatusCount(status);
  }

  isActiveFilter(status: EquipmentStatus): boolean {
    return this.selectedStatus === status;
  }

  // --- Modal Logic ---
  
  openDetails(eq: EquipmentRegistration): void {
    this.selectedEquipment = eq;
    this.isModalOpen = true;
  }

  closeDetails(): void {
    this.isModalOpen = false;
    this.selectedEquipment = null;
  }

  // Helper to cleanly iterate JSON keys in HTML
  getObjectKeys(obj: any): string[] {
    if (!obj) return [];
    return Object.keys(obj);
  }
}
