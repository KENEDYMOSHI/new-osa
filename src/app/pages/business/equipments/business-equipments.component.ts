import { CommonModule } from '@angular/common';
import { Component } from '@angular/core';
import { RouterModule } from '@angular/router';

type EquipmentStatus = 'all' | 'pending' | 'verified';
type RegisteredEquipmentStatus = Exclude<EquipmentStatus, 'all'>;

type RegisteredEquipment = {
  equipmentName: string;
  serviceType: string;
  dateAdded: string;
  status: RegisteredEquipmentStatus;
  businessName: string;
};

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
export class BusinessEquipmentsComponent {
  readonly statusFilters: StatusFilterOption[] = [
    { label: 'All', value: 'all' },
    { label: 'Pending', value: 'pending' },
    { label: 'Verified', value: 'verified' },
  ];

  equipments: RegisteredEquipment[] = [
    {
      equipmentName: 'Forecourt Pump FP-01',
      serviceType: 'Fuel pump',
      dateAdded: '2026-03-18',
      status: 'verified',
      businessName: 'NOVAS Agency Limited',
    },
    {
      equipmentName: 'Flow Meter FM-02',
      serviceType: 'Flow Meter',
      dateAdded: '2026-03-16',
      status: 'pending',
      businessName: 'NOVAS Agency Limited',
    },
    {
      equipmentName: 'Storage Tank North Yard',
      serviceType: 'Fixed Storage Tank',
      dateAdded: '2026-03-14',
      status: 'verified',
      businessName: 'NOVAS Agency Limited',
    },
    {
      equipmentName: 'Weighbridge WB-01',
      serviceType: 'Weighbridge',
      dateAdded: '2026-03-12',
      status: 'verified',
      businessName: 'NOVAS Agency Limited',
    },
    {
      equipmentName: 'Tank Wagon WT-05',
      serviceType: 'Wagon Tank',
      dateAdded: '2026-03-11',
      status: 'pending',
      businessName: 'NOVAS Agency Limited',
    },
    {
      equipmentName: 'Pressure Gauge PG-03',
      serviceType: 'Pressure gauges',
      dateAdded: '2026-03-09',
      status: 'verified',
      businessName: 'NOVAS Agency Limited',
    },
    {
      equipmentName: 'Counter Scale CS-08',
      serviceType: 'Counter scale',
      dateAdded: '2026-03-08',
      status: 'pending',
      businessName: 'NOVAS Agency Limited',
    },
    {
      equipmentName: 'Domestic Meter DM-04',
      serviceType: 'Domestic gas meter',
      dateAdded: '2026-03-05',
      status: 'verified',
      businessName: 'NOVAS Agency Limited',
    },
    {
      equipmentName: 'Platform Scale PS-12',
      serviceType: 'Platform scale',
      dateAdded: '2026-03-03',
      status: 'pending',
      businessName: 'NOVAS Agency Limited',
    },
    {
      equipmentName: 'Vehicle Tank Unit VTV-09',
      serviceType: 'Vehicle Tank Verification (VTV)',
      dateAdded: '2026-02-28',
      status: 'verified',
      businessName: 'NOVAS Agency Limited',
    },
    {
      equipmentName: 'Proving Tank PT-06',
      serviceType: 'Proving Tank',
      dateAdded: '2026-02-24',
      status: 'verified',
      businessName: 'NOVAS Agency Limited',
    },
    {
      equipmentName: 'Length Kit LM-02',
      serviceType: 'Tape Measure',
      dateAdded: '2026-02-22',
      status: 'pending',
      businessName: 'NOVAS Agency Limited',
    },
  ];

  selectedStatus: EquipmentStatus = 'all';
  searchTerm = '';
  readonly businessName = 'NOVAS Agency Limited';

  get filteredEquipments(): RegisteredEquipment[] {
    return this.equipments.filter((equipment) => {
      const matchesStatus =
        this.selectedStatus === 'all' || equipment.status === this.selectedStatus;
      const matchesSearch =
        this.searchTerm.trim().length === 0 ||
        equipment.equipmentName.toLowerCase().includes(this.searchTerm.trim().toLowerCase());
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

  getStatusCount(status: RegisteredEquipmentStatus): number {
    return this.equipments.filter((e) => e.status === status).length;
  }

  getFilterCount(status: EquipmentStatus): number {
    return status === 'all' ? this.totalEquipments : this.getStatusCount(status);
  }

  isActiveFilter(status: EquipmentStatus): boolean {
    return this.selectedStatus === status;
  }
}
