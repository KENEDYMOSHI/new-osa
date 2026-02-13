import { Component, OnInit, AfterViewInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormBuilder, FormGroup, ReactiveFormsModule, FormsModule, Validators } from '@angular/forms';
import { TechniciansCustomerRegistryService } from '../../services/technicians-customer-registry.service';
import { LocationService, District, Ward } from '../../services/location.service';
import { CurrencyPipe, DatePipe } from '@angular/common';
import flatpickr from 'flatpickr';
import Swal from 'sweetalert2';
import { DatePickerComponent } from '../../shared/components/ui/date-picker/date-picker.component';
// @ts-ignore
import html2pdf from 'html2pdf.js';

@Component({
  selector: 'app-technicians-customer-registry',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, FormsModule, DatePipe, DatePickerComponent],
  templateUrl: './technicians-customer-registry.component.html',
  styleUrls: ['./technicians-customer-registry.component.css']
})
export class TechniciansCustomerRegistryComponent implements OnInit {
  registryList: any[] = [];
  filteredList: any[] = [];
  profile: any = {};
  isLoading = false;
  showModal = false;
  registryForm: FormGroup;
  submitted = false;
  errorMessage = '';
  editMode = false;
  currentEditId: number | null = null;

  // Location data for dropdowns
  regions: string[] = [];
  districts: District[] = [];
  wards: Ward[] = [];

  filters = {
    keyword: '',
    customerName: '',
    instrument: '',
    stickerNumber: '',
    singleDate: '',
    startDate: '',
    endDate: '',
    year: ''
  };
  
  years: number[] = [];
  
  // Custom Dropdown Props
  showYearDropdown: boolean = false;
  yearSearchTerm: string = '';
  filteredYears: number[] = [];

  // Date Picker values for components
  singleDateValue: string = '';
  rangeValue: string = '';

  private datePickerInstance: any;

  constructor(
    private registryService: TechniciansCustomerRegistryService,
    private fb: FormBuilder,
    private locationService: LocationService
  ) {
    this.registryForm = this.fb.group({
      customer_name: ['', [Validators.required, Validators.minLength(3)]],
      service_date: ['', Validators.required],
      instrument_type: ['', Validators.required],
      sticker_number: [''],
      instrument_issue: ['', Validators.required],
      work_performed: ['', Validators.required],
      region: [''],
      district: [''],
      ward: ['']
    });
  }

  ngOnInit(): void {
    this.generateYears();
    this.fetchProfile();
    this.fetchRegistry();
    this.loadRegions();
    this.setupLocationListeners();
  }
  
  generateYears() {
    const currentYear = new Date().getFullYear();
    for (let i = currentYear; i >= currentYear - 10; i--) {
      this.years.push(i);
    }
    this.filteredYears = [...this.years];
  }

  ngAfterViewInit() {
    // Date pickers handled via components
  }

  fetchProfile() {
    this.registryService.getProfile().subscribe({
      next: (data) => {
        this.profile = data;
      },
      error: (error) => {
        console.error('Error fetching profile:', error);
      }
    });
  }

  fetchRegistry() {
    this.isLoading = true;
    this.registryService.getRegistry().subscribe({
      next: (response: any) => {
        // Handle both direct array and wrapped response
        this.registryList = Array.isArray(response) ? response : (response.data || []);
        this.applyFilters();
        this.isLoading = false;
      },
      error: (error) => {
        console.error('Error fetching registry:', error);
        this.isLoading = false;
        // Silently handle error - user can still add new records
      }
    });
  }

  // DataTable Properties
  itemsPerPage: number = 10;

  applyFilters() {
    this.filteredList = this.registryList.filter(item => {
      // 1. Text Filters
      const matchesCustomer = !this.filters.customerName || 
        (item.customer_name && item.customer_name.toLowerCase().includes(this.filters.customerName.toLowerCase()));
      
      const matchesInstrument = !this.filters.instrument || 
        (item.instrument_type && item.instrument_type.toLowerCase().includes(this.filters.instrument.toLowerCase()));
        
      const matchesSticker = !this.filters.stickerNumber || 
        (item.sticker_number && item.sticker_number.toLowerCase().includes(this.filters.stickerNumber.toLowerCase()));

      // 2. Date Filters
      const itemDate = new Date(item.service_date);
      let matchesDate = true;

      // Note: singleDate is YYYY-MM-DD from flatpickr value (dateFormat)
      if (this.filters.singleDate) {
        const filterDate = new Date(this.filters.singleDate);
        matchesDate = matchesDate && (itemDate.toDateString() === filterDate.toDateString());
      }
      
      if (this.filters.startDate && this.filters.endDate) {
        const start = new Date(this.filters.startDate);
        const end = new Date(this.filters.endDate);
        // Include end date fully
        end.setHours(23, 59, 59, 999);
        // Start date at 00:00:00
        start.setHours(0, 0, 0, 0);
        matchesDate = matchesDate && (itemDate >= start && itemDate <= end);
      }

      // 3. Year Filter
      if (this.filters.year) {
        const year = parseInt(this.filters.year);
        if (!isNaN(year)) {
             matchesDate = matchesDate && (itemDate.getFullYear() === year);
        }
      }

      return matchesCustomer && matchesInstrument && matchesSticker && matchesDate;
    });
  }

  // Custom Dropdown Methods
  toggleYearDropdown() {
    this.showYearDropdown = !this.showYearDropdown;
    if (this.showYearDropdown) {
        this.yearSearchTerm = '';
        this.filteredYears = [...this.years];
        setTimeout(() => {
            const input = document.getElementById('yearSearch');
            if(input) input.focus();
        }, 50);
    }
  }

  closeYearDropdown() {
    this.showYearDropdown = false;
  }

  selectYear(year: number) {
    this.filters.year = year.toString();
    this.applyFilters();
    this.closeYearDropdown();
  }

  filterYears() {
    if (!this.yearSearchTerm) {
        this.filteredYears = [...this.years];
    } else {
        const term = this.yearSearchTerm.toString();
        this.filteredYears = this.years.filter(y => y.toString().includes(term));
    }
  }

  onSingleDateChange(dateStr: string) {
    this.filters.singleDate = dateStr;
    if (dateStr) {
      this.filters.startDate = '';
      this.filters.endDate = '';
      this.rangeValue = ''; // Clear range picker value
    }
    this.applyFilters();
  }

  onDateRangeChange(rangeStr: string) {
    const parts = rangeStr.split(' to ');
    if (parts.length === 2) {
      this.filters.startDate = parts[0];
      this.filters.endDate = parts[1];
      
      this.filters.singleDate = ''; // Clear single date picker value
      this.singleDateValue = '';
      this.applyFilters();
    } else {
      // If range is cleared or invalid, clear filters
      this.filters.startDate = '';
      this.filters.endDate = '';
      this.applyFilters();
    }
  }
  
  private formatDate(date: Date): string {
    const y = date.getFullYear();
    const m = (date.getMonth() + 1).toString().padStart(2, '0');
    const d = date.getDate().toString().padStart(2, '0');
    return `${y}-${m}-${d}`;
  }

  // Helper to clear filters
  clearFilters() {
    this.filters = {
        keyword: '',
        customerName: '',
        instrument: '',
        stickerNumber: '',
        singleDate: '',
        startDate: '',
        endDate: '',
        year: ''
    };
    
    // Clear inputs
    const inputs = ['singleDatePicker', 'dateRangePicker'] as const;
    inputs.forEach(id => {
        const el = document.getElementById(id) as HTMLInputElement;
        if(el && (el as any)._flatpickr) {
            (el as any)._flatpickr.clear();
        }
        if(el) el.value = '';
    });
    
    this.applyFilters();
  }

  // DataTable Methods
  onPageSizeChange() {
    // Logic to handle page size if using client-side pagination
  }

  get paginatedList() {
    return this.filteredList.slice(0, this.itemsPerPage);  
  }

  // Export Methods
  
  now: Date = new Date();

  exportToPdf() {
    this.now = new Date(); // Update print time
    const element = document.getElementById('report-content');
    if (!element) return;
    
    // Temporarily unhide to generate PDF
    element.classList.remove('hidden');
    
    const opt = {
      margin:       10,
      filename:     `Service_Records_${this.formatDateForFile(new Date())}.pdf`,
      image:        { type: 'jpeg' as const, quality: 0.98 },
      html2canvas:  { scale: 2 },
      jsPDF:        { unit: 'mm' as const, format: 'a4' as const, orientation: 'portrait' as const }
    };
    
    // Helper to hide again after
    const hide = () => element.classList.add('hidden');

    // Use html2pdf global (assumed loaded or imported)
    // If working with import 'html2pdf.js'
    // Use html2pdf global (assumed loaded or imported)
    // If working with import 'html2pdf.js'
    html2pdf().from(element).set(opt).save().then(hide).catch(hide);
  }

  async exportToExcel() {
    const fileName = `Service_Records_${this.formatDateForFile(new Date())}.xls`;
    
    // Helper to fetch and convert image to Base64
    const getBase64FromUrl = async (url: string): Promise<string> => {
      try {
        const response = await fetch(url);
        const blob = await response.blob();
        return new Promise((resolve, reject) => {
          const reader = new FileReader();
          reader.onloadend = () => resolve(reader.result as string);
          reader.onerror = reject;
          reader.readAsDataURL(blob);
        });
      } catch (error) {
        console.error('Error loading image:', url, error);
        return ''; // Return empty string if failed
      }
    };

    // Fetch images
    const coatOfArmsBase64 = await getBase64FromUrl('assets/logo/coat-of-arms.png');
    const wmaLogoBase64 = await getBase64FromUrl('assets/logo/wma-logo.png');

    // Construct Excel Content with Header
    let excelContent = `
      <html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
      <head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>Service Records</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head>
      <body>
        <table style="width: 100%; border: none;">
          <tr>
            <td style="text-align: left; vertical-align: top;">
               <img src="${coatOfArmsBase64}" width="100" height="100" alt="Coat of Arms">
            </td>
            <td style="text-align: center; vertical-align: top;" colspan="7">
                <h2 style="margin: 0;">THE UNITED REPUBLIC OF TANZANIA</h2>
                <h3 style="margin: 0;">MINISTRY OF INDUSTRY AND TRADE</h3>
                <h3 style="margin: 0;">WEIGHTS AND MEASURES AGENCY</h3>


                <br/>
                <p><strong>Technician Name:</strong> ${this.profile.personal?.first_name} ${this.profile.personal?.last_name}</p>
                <p><strong>Company Name:</strong> ${this.profile.business?.company_name || 'N/A'}</p>
                <p><strong>Phone Number:</strong> ${this.profile.personal?.phone || 'N/A'}</p>
                <p><strong>License Number:</strong> ${this.profile.license?.license_number || 'N/A'}</p>
            </td>
            <td style="text-align: right; vertical-align: top;">
               <img src="${wmaLogoBase64}" width="100" height="100" alt="WMA Logo">
            </td>
          </tr>
        </table>
        <br/>
        <table border="1">
          <thead>
            <tr style="background-color: #f2f2f2;">
              <th>Service Date</th>
              <th>Customer Name</th>
              <th>Region</th>
              <th>District</th>
              <th>Ward</th>
              <th>Instrument</th>
              <th>Sticker Number</th>
              <th>Issue/Problem</th>
              <th>Work Performed</th>
            </tr>
          </thead>
          <tbody>
    `;

    this.filteredList.forEach(item => {
      const date = new Date(item.service_date).toLocaleDateString();
      excelContent += `
        <tr>
          <td>${date}</td>
          <td>${item.customer_name}</td>
          <td>${item.region || '-'}</td>
          <td>${item.district || '-'}</td>
          <td>${item.ward || '-'}</td>
          <td>${item.instrument_type}</td>
          <td>${item.sticker_number || '-'}</td>
          <td>${item.instrument_issue}</td>
          <td>${item.work_performed}</td>
        </tr>
      `;
    });

    excelContent += `
          </tbody>
        </table>
      </body>
      </html>
    `;

    this.downloadFile(excelContent, fileName, 'application/vnd.ms-excel');
  }

  exportToCsv() {
    const fileName = `Service_Records_${this.formatDateForFile(new Date())}.csv`;
    
    // Add Headers as comment rows or just layout at top
    const data = [
      ['THE UNITED REPUBLIC OF TANZANIA'],
      ['MINISTRY OF INDUSTRY AND TRADE'],
      ['WEIGHTS AND MEASURES AGENCY'],
      [''],
      ['Technician Name', `${this.profile.personal?.first_name} ${this.profile.personal?.last_name}`],
      ['Company Name', this.profile.business?.company_name || 'N/A'],
      ['Phone Number', this.profile.personal?.phone || 'N/A'],
      ['License Number', this.profile.license?.license_number || 'N/A'],
      [''],
      ['Service Date', 'Customer Name', 'Region', 'District', 'Ward', 'Instrument', 'Sticker #', 'Issue', 'Work Performed']
    ];
    
    this.filteredList.forEach(item => {
      data.push([
        item.service_date, // Maybe format?
        item.customer_name,
        item.region || '-',
        item.district || '-',
        item.ward || '-',
        item.instrument_type,
        item.sticker_number || '-',
        item.instrument_issue,
        item.work_performed
      ]);
    });

    const csvContent = data.map(row => row.map(cell => {
         const cellStr = String(cell || '');
         // Escape quotes
         if (cellStr.includes(',') || cellStr.includes('"') || cellStr.includes('\n')) {
             return `"${cellStr.replace(/"/g, '""')}"`;
         }
         return cellStr;
    }).join(',')).join('\n');
    
    this.downloadFile(csvContent, fileName, 'text/csv;charset=utf-8;');
  }

  private formatDateForFile(date: Date): string {
    return date.toISOString().split('T')[0];
  }

  private downloadFile(content: string, fileName: string, mimeType: string) {
    const blob = new Blob(['\ufeff' + content], { type: mimeType }); // Add BOM for Excel/CSV utf-8
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = fileName;
    a.click();
    window.URL.revokeObjectURL(url);
  }

  openModal() {
    this.showModal = true;
    this.editMode = false;
    this.currentEditId = null;
    this.submitted = false;
    this.registryForm.reset();
  }

  closeModal() {
    this.showModal = false;
  }

  onSubmit() {
    this.submitted = true;
    if (this.registryForm.invalid) {
      return;
    }

    this.isLoading = true;
    
    if (this.editMode && this.currentEditId !== null) {
      // Update existing record
      this.registryService.updateRegistry(this.currentEditId, this.registryForm.value).subscribe({
        next: (response: any) => {
          this.isLoading = false;
          this.closeModal();
          
          // Optimistic update: Update local list directly
          // We merge the existing item with the form values (in case server response is minimal)
          // and then any server response data on top of that.
          const index = this.registryList.findIndex(item => item.id === this.currentEditId);
          if (index !== -1) {
             const updatedData = response.data || response;
             // If the server returns just a success message or incomplete object, use form values
             const formValues = this.registryForm.value;
             
             // Create the updated item by merging:
             // 1. Original Item (preserve ID, etc.)
             // 2. Form Values (user changes)
             // 3. Server Response (in case server computes fields, assuming it returns an object)
             
             this.registryList[index] = { 
                 ...this.registryList[index], 
                 ...formValues,
                 ...(typeof updatedData === 'object' ? updatedData : {})
             };
             
             this.registryList = [...this.registryList]; // Trigger change detection
          }
          this.applyFilters();
          
          const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
          });
          Toast.fire({
            icon: 'success',
            title: 'Record updated successfully!'
          });
        },
        error: (error) => {
          console.error('Error updating record:', error);
          this.isLoading = false;
          Swal.fire({
            icon: 'error',
            title: 'Update Failed',
            text: 'Failed to update record. Please try again.',
            confirmButtonColor: '#F59E0B'
          });
        }
      });
    } else {
      // Add new record
      this.registryService.addRegistry(this.registryForm.value).subscribe({
        next: (response: any) => {
          this.isLoading = false;
          this.closeModal();
          
          // Optimistic update: Add to local list directly
          // Handle various response types (wrapped in data property or direct object)
          const newItem = response.data || response || this.registryForm.value;
          this.registryList = [newItem, ...this.registryList];
          this.applyFilters();
          
          const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
          });
          Toast.fire({
            icon: 'success',
            title: 'Record saved successfully!'
          });
        },
        error: (error) => {
          console.error('Error adding record:', error);
          this.isLoading = false;
          Swal.fire({
            icon: 'error',
            title: 'Save Failed',
            text: 'Failed to save record. Please try again.',
            confirmButtonColor: '#F59E0B'
          });
        }
      });
    }
  }

  editRecord(item: any) {
    this.editMode = true;
    this.currentEditId = item.id;
    
    // Pre-load districts and wards if region/district exist
    if (item.region) {
      this.locationService.getDistricts(item.region).subscribe({
        next: (districts) => {
          this.districts = districts;
          
          // Pre-load wards if district exists
          if (item.district) {
            this.locationService.getWards(item.district).subscribe({
              next: (wards) => {
                this.wards = wards;
              },
              error: (err) => console.error('Failed to load wards:', err)
            });
          }
        },
        error: (err) => console.error('Failed to load districts:', err)
      });
    }
    
    this.registryForm.patchValue({
      customer_name: item.customer_name,
      service_date: item.service_date,
      instrument_type: item.instrument_type,
      sticker_number: item.sticker_number,
      instrument_issue: item.instrument_issue,
      work_performed: item.work_performed,
      region: item.region,
      district: item.district,
      ward: item.ward
    });
    this.showModal = true;
    this.submitted = false;
  }

  deleteRecord(id: number) {
    Swal.fire({
      title: 'Delete Record?',
      text: "This action cannot be undone!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#F59E0B',
      cancelButtonColor: '#6B7280',
      confirmButtonText: 'Yes, delete it!',
      cancelButtonText: 'Cancel'
    }).then((result) => {
      if (result.isConfirmed) {
        this.registryService.deleteRegistry(id).subscribe({
          next: () => {
            this.fetchRegistry();
            
            const Toast = Swal.mixin({
              toast: true,
              position: 'top-end',
              showConfirmButton: false,
              timer: 3000,
              timerProgressBar: true
            });
            Toast.fire({
              icon: 'success',
              title: 'Record deleted successfully!'
            });
          },
          error: (error) => {
            console.error('Error deleting record:', error);
            Swal.fire({
              icon: 'error',
              title: 'Delete Failed',
              text: 'Failed to delete record. Please try again.',
              confirmButtonColor: '#F59E0B'
            });
          }
        });
      }
    });
  }

  loadRegions(): void {
    this.locationService.getRegions().subscribe({
      next: (regions) => {
        this.regions = regions;
      },
      error: (err) => {
        console.error('Failed to load regions:', err);
      }
    });
  }

  setupLocationListeners(): void {
    // Region Change - Load Districts
    this.registryForm.get('region')?.valueChanges.subscribe((region) => {
      if (region) {
        this.locationService.getDistricts(region).subscribe({
          next: (districts) => {
            this.districts = districts;
            // Reset district and ward when region changes
            this.registryForm.get('district')?.setValue('');
            this.wards = [];
          },
          error: (err) => {
            console.error('Failed to load districts:', err);
            this.districts = [];
          }
        });
      } else {
        this.districts = [];
        this.wards = [];
      }
    });

    // District Change - Load Wards
    this.registryForm.get('district')?.valueChanges.subscribe((district) => {
      if (district) {
        this.locationService.getWards(district).subscribe({
          next: (wards) => {
            this.wards = wards;
          },
          error: (err) => {
            console.error('Failed to load wards:', err);
            this.wards = [];
          }
        });
      } else {
        this.wards = [];
      }
    });
  }
}
