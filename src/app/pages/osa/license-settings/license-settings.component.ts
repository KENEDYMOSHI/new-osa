import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { LicenseService } from '../../../services/license.service';
import Swal from 'sweetalert2';

@Component({
  selector: 'app-license-settings',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './license-settings.component.html',
  styleUrls: ['./license-settings.component.css'] // Stylesheet for License Settings
})
export class LicenseSettingsComponent implements OnInit {
  licenseTypes: any[] = [];
  isModalOpen = false;
  isEditMode = false;
  
  currentType: any = {
    name: '',
    description: '',
    fee: 0,
    currency: 'TZS'
  };

  constructor(private licenseService: LicenseService) {}

  ngOnInit() {
    this.loadLicenseTypes();
  }

  loadLicenseTypes() {
    this.licenseService.getLicenseTypes().subscribe({
      next: (data) => {
        this.licenseTypes = data;
      },
      error: (err) => {
        console.error('Failed to load types', err);
        Swal.fire('Error', 'Failed to load license types', 'error');
      }
    });
  }

  openModal() {
    this.isEditMode = false;
    this.currentType = { name: '', description: '', fee: 0, currency: 'TZS' };
    this.isModalOpen = true;
  }

  editLicenseType(type: any) {
    this.isEditMode = true;
    this.currentType = { ...type }; // Copy
    this.isModalOpen = true;
  }

  closeModal() {
    this.isModalOpen = false;
  }

  saveLicenseType() {
    if (this.isEditMode) {
      this.licenseService.updateLicenseType(this.currentType.id, this.currentType).subscribe({
        next: () => {
          Swal.fire('Success', 'License Type updated successfully', 'success');
          this.closeModal();
          this.loadLicenseTypes();
        },
        error: (err) => {
          const msg = this.getErrorMessage(err);
          Swal.fire('Error', msg, 'error');
        }
      });
    } else {
      this.licenseService.createLicenseType(this.currentType).subscribe({
        next: () => {
          Swal.fire('Success', 'License Type created successfully', 'success');
          this.closeModal();
          this.loadLicenseTypes();
        },
        error: (err) => {
          const msg = this.getErrorMessage(err);
          Swal.fire('Error', msg, 'error');
        }
      });
    }
  }

  deleteLicenseType(id: string) {
    Swal.fire({
      title: 'Are you sure?',
      text: 'You won\'t be able to revert this!',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
      confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
      if (result.isConfirmed) {
        this.licenseService.deleteLicenseType(id).subscribe({
          next: () => {
            Swal.fire('Deleted!', 'License type has been deleted.', 'success');
            this.loadLicenseTypes();
          },
          error: (err) => {
            const msg = this.getErrorMessage(err);
            Swal.fire('Error', msg, 'error');
          }
        });
      }
    });
  }

  private getErrorMessage(err: any): string {
    if (err.error && err.error.messages) {
      if (typeof err.error.messages === 'object') {
        return Object.values(err.error.messages).join('<br>');
      }
      return err.error.messages;
    }
    if (err.error && err.error.message) {
      return err.error.message;
    }
    if (err.message) {
      return err.message;
    }
    return 'An unknown error occurred.';
  }
}
