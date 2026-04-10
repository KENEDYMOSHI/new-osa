import { CommonModule } from '@angular/common';
import { Component, OnInit } from '@angular/core';
import { Router, RouterModule } from '@angular/router';
import { TranslateModule } from '@ngx-translate/core';
import { EquipmentService } from '../../../core/services/equipment.service';
import { NoDataComponent } from '../../../shared/components/no-data/no-data.component';

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
  imports: [CommonModule, RouterModule, NoDataComponent, TranslateModule],
  templateUrl: './business-equipments.component.html',
})
export class BusinessEquipmentsComponent implements OnInit {
  readonly statusFilters: StatusFilterOption[] = [
    { label: 'BUSINESS.EQUIPMENTS.FILTERS.ALL', value: 'all' },
    { label: 'BUSINESS.EQUIPMENTS.FILTERS.DRAFT', value: 'draft' },
    { label: 'BUSINESS.EQUIPMENTS.FILTERS.PENDING', value: 'pending' },
    { label: 'BUSINESS.EQUIPMENTS.FILTERS.VERIFIED', value: 'verified' },
    { label: 'BUSINESS.EQUIPMENTS.FILTERS.REJECTED', value: 'rejected' },
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

  constructor(
    private equipmentService: EquipmentService,
    private router: Router,
  ) {}

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

  canEditEquipment(eq: EquipmentRegistration | null): boolean {
    return !!eq && (eq.status === 'draft' || eq.status === 'pending');
  }

  editEquipment(eq: EquipmentRegistration | null, event?: Event): void {
    if (!eq) return;
    event?.stopPropagation();
    this.closeDetails();
    this.router.navigate(['/business/equipments', eq.id, 'edit']);
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

  getStatusClasses(status: string): string {
    switch (status) {
      case 'verified':
        return 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-500/10 dark:text-emerald-400 dark:ring-emerald-500/20';
      case 'pending':
        return 'bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-500/10 dark:text-amber-400 dark:ring-amber-500/20';
      case 'rejected':
        return 'bg-red-50 text-red-700 ring-red-600/20 dark:bg-red-500/10 dark:text-red-400 dark:ring-red-500/20';
      case 'draft':
      default:
        return 'bg-gray-100 text-gray-700 ring-gray-500/20 dark:bg-gray-800 dark:text-gray-300 dark:ring-gray-700';
    }
  }
}
