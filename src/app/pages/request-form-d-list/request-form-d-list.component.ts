import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { FormsModule } from '@angular/forms'; // Import FormsModule
import { LicenseService } from '../../services/license.service';
import { AuthService } from '../../core/services/auth.service';
import { AppModalComponent } from '../../components/app-modal/app-modal.component';

@Component({
  selector: 'app-request-form-d-list',
  standalone: true,
  imports: [CommonModule, RouterModule, FormsModule, AppModalComponent], // Add AppModalComponent
  template: `
    <div class="p-6 bg-gray-50 min-h-screen font-sans">
      <!-- Header -->
      <div class="mb-8 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Form D Requests</h1>
            <p class="text-gray-500 text-sm mt-1">Manage and view your submitted Form D requests.</p>
        </div>
        <a routerLink="/request-form-d" class="px-4 py-2 bg-orange-500 text-white rounded-lg shadow-sm font-bold text-sm hover:bg-orange-600 transition-colors">
            + New Request
        </a>
      </div>

      <!-- Filters -->
      <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 mb-6 flex flex-wrap gap-4 items-end">
        <div class="flex-grow min-w-[200px]">
            <label class="block text-xs font-bold text-gray-500 mb-1">Search</label>
            <input type="text" [(ngModel)]="filters.search" (input)="applyFilters()" placeholder="License, Instrument, Serial..." class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-orange-500 transition-colors">
        </div>
        <div class="w-[180px]">
            <label class="block text-xs font-bold text-gray-500 mb-1">Status</label>
            <select [(ngModel)]="filters.status" (change)="applyFilters()" class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-orange-500 transition-colors">
                <option value="">All Statuses</option>
                <option value="Pending Verification">Pending Verification</option>
                <option value="Verified">Verified</option>
                <option value="Rejected">Rejected</option>
            </select>
        </div>
        <div class="w-[160px]">
            <label class="block text-xs font-bold text-gray-500 mb-1">From Date</label>
            <input type="date" [(ngModel)]="filters.fromDate" (change)="applyFilters()" class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-orange-500 transition-colors">
        </div>
        <div class="w-[160px]">
             <label class="block text-xs font-bold text-gray-500 mb-1">To Date</label>
             <input type="date" [(ngModel)]="filters.toDate" (change)="applyFilters()" class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-orange-500 transition-colors">
        </div>
        <div class="pb-0.5">
            <button (click)="clearFilters()" class="px-3 py-2 text-gray-500 hover:text-gray-700 text-sm font-medium transition-colors">
                Clear
            </button>
        </div>
      </div>

      <!-- Table Area -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center">
            <h2 class="text-lg font-bold text-gray-800">Submitted Requests</h2>
            <span class="text-xs font-bold text-gray-400 bg-gray-100 px-2 py-1 rounded-full">{{ filteredRequests.length }} Found</span>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-gray-600 text-xs uppercase tracking-wider">
                        <th class="p-4 font-bold border-b border-gray-100">ID</th>
                        <th class="p-4 font-bold border-b border-gray-100">Date</th>
                        <th class="p-4 font-bold border-b border-gray-100">License / Sticker</th>
                        <th class="p-4 font-bold border-b border-gray-100">Customer / Location</th>
                        <th class="p-4 font-bold border-b border-gray-100">Instrument</th>
                        <th class="p-4 font-bold border-b border-gray-100">Job Type</th>
                        <th class="p-4 font-bold border-b border-gray-100">Status</th>
                        <th class="p-4 font-bold border-b border-gray-100 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    <tr *ngFor="let req of filteredRequests" class="hover:bg-gray-50 transition-colors border-b border-gray-50 last:border-0">
                        <td class="p-4 font-mono text-gray-500 text-xs">#{{ req.id }}</td>
                        <td class="p-4 text-gray-600 text-xs whitespace-nowrap">
                            {{ req.created_at | date:'dd MMM yyyy' }}
                        </td>
                        <td class="p-4">
                            <div class="font-bold text-gray-800 text-xs">{{ req.license_number }}</div>
                            <div class="text-xs text-gray-500 font-mono">{{ req.sticker_number || '-' }}</div>
                        </td>
                        <td class="p-4">
                            <div class="font-bold text-gray-700 text-xs">{{ req.company_name }}</div>
                            <div class="text-xs text-gray-500">{{ req.region }} - {{ req.district }}</div>
                        </td>
                        <td class="p-4">
                            <div class="text-gray-600 text-xs font-bold">{{ req.instrument_name }}</div>
                            <div class="text-xs text-gray-500 font-mono">{{ req.serial_number }}</div>
                        </td>
                        <td class="p-4">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide border"
                                  [ngClass]="{
                                    'bg-blue-50 text-blue-600 border-blue-100': req.certification_action === 'Erected',
                                    'bg-purple-50 text-purple-600 border-purple-100': req.certification_action === 'Adjusted',
                                    'bg-orange-50 text-orange-600 border-orange-100': req.certification_action === 'Repaired'
                                  }">
                                {{ req.certification_action }}
                            </span>
                        </td>
                        <td class="p-4">
                            <span class="px-2 py-1 rounded text-[10px] font-bold uppercase"
                                  [ngClass]="{
                                    'bg-yellow-100 text-yellow-700': req.status === 'Pending Verification',
                                    'bg-green-100 text-green-700': req.status === 'Verified',
                                    'bg-red-100 text-red-700': req.status === 'Rejected'
                                  }">
                                {{ req.status }}
                            </span>
                        </td>
                        <td class="p-4 text-right">
                            <button (click)="openView(req)" class="text-blue-600 hover:text-blue-800 bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded text-xs font-bold transition-colors">
                                View
                            </button>
                        </td>
                    </tr>
                    <tr *ngIf="filteredRequests.length === 0">
                        <td colspan="8" class="p-8 text-center text-gray-400 italic">
                            No requests found matching your filters.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
      </div>
      
      <!-- View Modal (Form D) -->
      <app-modal *ngIf="selectedRequest"
                 [title]="'Form D Request Details'"
                 size="lg"
                 [staticBackdrop]="true"
                 (close)="closeView()">
                 
          <div body class="w-full bg-white p-8 md:p-12 print:p-8 font-serif text-sm leading-relaxed text-black h-full overflow-y-auto" id="printableArea">
                 
                 <!-- Document Header -->
                 <div class="text-center mb-6">
                     <h1 class="font-bold text-base uppercase mb-0.5">WEIGHTS AND MEASURES AGENCY</h1>
                     <p class="font-bold uppercase text-[10px] mb-2">P.O BOX 313 DAR ES SALAAM</p>
                     
                     <div class="flex justify-center mb-2">
                         <img src="/images/logo/wma-logo.jpg" alt="WMA Logo" class="h-16 w-auto object-contain">
                     </div>

                     <h2 class="font-bold text-lg underline uppercase mb-0.5">FORM D</h2>
                     <h3 class="font-bold uppercase text-xs mb-0.5">FORM OF CERTIFICATE TO BE USED BY A {{ textConfig.certMechanicTitle | uppercase }}</h3>
                     <h3 class="font-bold uppercase text-xs mb-1">AFTER {{ textConfig.certActionContext | uppercase }}</h3>
                     <p class="font-bold text-xs italic">(Made under Regulation 12(d))</p>
                 </div>

                 <!-- Content Body -->
                 <div class="space-y-4 text-xs font-medium"> <!-- Reduced gap and font size -->
                     
                     <!-- Top Section -->
                     <div class="space-y-2">
                         <div class="flex items-end gap-2">
                             <span class="whitespace-nowrap w-48">Company employing mechanic:</span>
                             <div class="flex-grow border-b border-dotted border-black px-2 pb-0.5 font-bold text-sm">
                                 {{ companyName || selectedRequest.practitioner_name }}
                             </div>
                         </div>
                         <!-- Added License No and Phone for completeness -->
                         <div class="flex gap-4">
                             <div class="flex items-end gap-2 flex-1">
                                 <span class="whitespace-nowrap">License No:</span>
                                 <div class="flex-grow border-b border-dotted border-black px-2 pb-0.5 font-bold">
                                     {{ selectedRequest.license_number }}
                                 </div>
                             </div>
                             <div class="flex items-end gap-2 flex-1">
                                 <span class="whitespace-nowrap">Phone:</span>
                                 <div class="flex-grow border-b border-dotted border-black px-2 pb-0.5 font-bold">
                                     {{ selectedRequest.practitioner_phone || '---' }}
                                 </div>
                             </div>
                         </div>
                     </div>

                     <p class="mt-4">I hereby certify that the under- mentioned {{ textConfig.certUserDescription }} has been</p>
                     
                     <!-- Actions -->
                     <div class="flex justify-center gap-16 my-2">
                         <div class="flex items-center gap-2">
                             <span *ngIf="selectedRequest.certification_action === 'Erected'" class="font-bold text-lg">✓</span>
                             <span [class.font-bold]="selectedRequest.certification_action === 'Erected'">*Erected</span>
                         </div>
                         <div class="flex items-center gap-2">
                             <span *ngIf="selectedRequest.certification_action === 'Adjusted'" class="font-bold text-lg">✓</span>
                             <span [class.font-bold]="selectedRequest.certification_action === 'Adjusted'">Adjusted</span>
                         </div>
                         <div class="flex items-center gap-2">
                             <span *ngIf="selectedRequest.certification_action === 'Repaired'" class="font-bold text-lg">✓</span>
                             <span [class.font-bold]="selectedRequest.certification_action === 'Repaired'">Repaired</span>
                         </div>
                     </div>
                     <p class="text-center text-[10px] italic mb-4">(*Delete where not applicable)</p>

                     <!-- Details List -->
                     <div class="space-y-2">
                         <div class="flex items-end gap-2">
                             <span class="whitespace-nowrap w-40">By me and sealed with my seal No.</span>
                             <div class="flex-grow border-b border-dotted border-black px-2 pb-0.5 font-bold">
                                 {{ selectedRequest.seal_number || '---' }}
                             </div>
                         </div>
                         
                         <div class="flex items-end gap-2">
                             <span class="whitespace-nowrap w-40">Name of user of pump:</span>
                             <div class="flex-grow border-b border-dotted border-black px-2 pb-0.5 font-bold">
                                 {{ selectedRequest.company_name }}
                             </div>
                         </div>

                         <div class="flex items-end gap-2">
                             <span class="whitespace-nowrap w-40">Location:</span>
                             <div class="flex-grow border-b border-dotted border-black px-2 pb-0.5 font-bold break-words">
                                 {{ selectedRequest.region }}, {{ selectedRequest.district }} ({{ selectedRequest.ward }}) - {{ selectedRequest.street }}
                                 <span *ngIf="selectedRequest.address">, {{ selectedRequest.address }}</span>
                             </div>
                         </div>

                         <div class="flex items-end gap-2">
                             <span class="whitespace-nowrap w-40">Make and type of pump:</span>
                             <div class="flex-grow border-b border-dotted border-black px-2 pb-0.5 font-bold">
                                 {{ selectedRequest.instrument_name }} ({{ selectedRequest.type_of_instrument }})
                             </div>
                         </div>
                         
                         <!-- Added Product & Capacity/Quantity -->
                         <div class="flex gap-4">
                             <div class="flex items-end gap-2 flex-1">
                                 <span class="whitespace-nowrap">Product:</span>
                                 <div class="flex-grow border-b border-dotted border-black px-2 pb-0.5 font-bold">
                                     {{ selectedRequest.product }}
                                 </div>
                             </div>
                             <div class="flex items-end gap-2 flex-1" *ngIf="selectedRequest.quality || selectedRequest.capacity">
                                 <span class="whitespace-nowrap">Capacity/Nozzles:</span>
                                 <div class="flex-grow border-b border-dotted border-black px-2 pb-0.5 font-bold">
                                     {{ selectedRequest.capacity ? selectedRequest.capacity + ' Litres' : (selectedRequest.quantity + ' Nozzles') }}
                                 </div>
                             </div>
                         </div>

                         <div class="flex gap-4">
                             <div class="flex items-end gap-2 flex-1">
                                 <span class="whitespace-nowrap">Serial No:</span>
                                 <div class="flex-grow border-b border-dotted border-black px-2 pb-0.5 font-bold">
                                     {{ selectedRequest.serial_number }}
                                 </div>
                             </div>
                             <div class="flex items-end gap-2 flex-1" *ngIf="selectedRequest.sticker_number">
                                 <span class="whitespace-nowrap">Sticker No:</span>
                                 <div class="flex-grow border-b border-dotted border-black px-2 pb-0.5 font-bold">
                                     {{ selectedRequest.sticker_number }}
                                 </div>
                             </div>
                         </div>

                         <div class="flex items-end gap-2">
                             <span class="whitespace-nowrap w-40">Date of sealing:</span>
                             <div class="flex-grow border-b border-dotted border-black px-2 pb-0.5 font-bold">
                                 {{ selectedRequest.verification_date | date:'longDate' }}
                             </div>
                         </div>
                         
                         <div class="flex items-end gap-2">
                             <span class="whitespace-nowrap w-40">Next Verification Date:</span>
                             <div class="flex-grow border-b border-dotted border-black px-2 pb-0.5 font-bold">
                                 {{ selectedRequest.next_verification_date | date:'longDate' }}
                             </div>
                         </div>
                     </div>

                     <p class="mt-4 text-justify leading-snug">
                         I further certify that the above {{ textConfig.certInstrumentName }} was fully tested against approved stamped measures and found correct within the permitted limits of error before {{ textConfig.certActionContext.split('/')[0] }}.
                     </p>

                     <!-- Signatures Row - REMOVED -->
                     <div class="grid grid-cols-1 gap-8 mt-6">
                         <!-- Only Cert Auth No remains or purely removed? 
                              User said "remove sehemu za signature".
                              Reviewing original:
                              Left: Signature (Mechanic)
                              Right: Cert Auth No
                              Removing Signature leave Cert Auth No.
                          -->
                         <div class="flex items-end gap-2 justify-end">
                             <span class="whitespace-nowrap">Certificate of Authorization No:</span>
                             <div class="border-b border-dotted border-black px-2 pb-0.5 font-bold min-w-[100px]">
                                 {{ selectedRequest.cert_auth_number }}
                             </div>
                         </div>
                     </div>

                     <!-- Declarant Section -->
                     <div class="mt-6 pt-2">
                         <div class="flex items-end gap-2 mb-2">
                             <span class="whitespace-nowrap">I / We</span>
                             <div class="flex-grow border-b border-dotted border-black px-2 pb-0.5 font-bold">
                                 {{ selectedRequest.declarant_name }}
                             </div>
                         </div>
                         
                         <div class="flex gap-4 mb-2">
                             <div class="flex items-end gap-2 flex-1">
                                 <span class="whitespace-nowrap">Designation:</span>
                                 <div class="flex-grow border-b border-dotted border-black px-2 pb-0.5 font-bold">
                                     {{ selectedRequest.declarant_designation }}
                                 </div>
                             </div>
                             <div class="flex items-end gap-2 flex-1">
                                 <span class="whitespace-nowrap">Phone Number:</span>
                                 <div class="flex-grow border-b border-dotted border-black px-2 pb-0.5 font-bold">
                                     {{ selectedRequest.declarant_phone || '---' }}
                                 </div>
                             </div>
                         </div>

                         <p class="text-justify leading-snug">
                             Being the user(s) for trade purposes of the {{ textConfig.certUserDescription }} described above, which has been {{ textConfig.certActionContext }} by the {{ textConfig.certMechanicTitle }}, request the Inspector of Weights and Measures that arrangements may be made for its verification.
                         </p>

                         <div class="flex justify-between mt-4">
                             <div class="flex items-end gap-2 w-1/2">
                                 <span class="whitespace-nowrap">Date:</span>
                                 <div class="flex-grow border-b border-dotted border-black px-2 pb-0.5 font-bold">
                                     {{ selectedRequest.declarant_date | date:'dd/MM/yyyy' }}
                                 </div>
                             </div>
                             <!-- Signature Removed -->
                         </div>
                     </div>

                     <!-- Initial Inspection Details (For Record Only - Below Certificate) -->
                     <div class="mt-8 pt-8 border-t-2 border-dashed border-gray-300">
                         <h4 class="font-bold text-xs uppercase text-gray-500 mb-2">Additional Inspection Details</h4>
                         
                         <!-- Inspection Report -->
                         <div class="mb-4">
                             <span class="font-bold block mb-1">Inspection Report:</span>
                             <div class="p-2 bg-gray-50 border border-gray-100 rounded text-xs min-h-[40px]">
                                 {{ selectedRequest.inspection_report || 'No specific report remarks.' }}
                             </div>
                         </div>
                         
                         <!-- Inspection Schedule -->
                         <div class="grid grid-cols-2 gap-4" *ngIf="selectedRequest.start_date || selectedRequest.end_date">
                             <div>
                                 <span class="font-bold block mb-1">Inspection Start:</span>
                                 <div class="text-xs">
                                     {{ selectedRequest.start_date | date:'shortDate' }} 
                                     <span *ngIf="selectedRequest.start_time">at {{ selectedRequest.start_time }}</span>
                                 </div>
                             </div>
                             <div>
                                 <span class="font-bold block mb-1">Inspection End:</span>
                                 <div class="text-xs">
                                     {{ selectedRequest.end_date | date:'shortDate' }}
                                     <span *ngIf="selectedRequest.end_time">at {{ selectedRequest.end_time }}</span>
                                 </div>
                             </div>
                         </div>
                     </div>
                 </div>
          </div>
          
          <!-- Footer Buttons -->
          <div footer class="flex gap-2 w-full justify-end">
              <button (click)="closeView()" class="px-5 py-2.5 rounded-lg border border-gray-300 text-gray-700 font-medium hover:bg-gray-50 transition-colors">
                  Close
              </button>
              <button (click)="printForm()" class="px-5 py-2.5 rounded-lg bg-[#F59E0B] text-white font-bold hover:bg-[#D97706] shadow-sm flex items-center gap-2 transition-transform active:scale-95">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                  </svg>
                  Print / Download
              </button>
          </div>
          
      </app-modal>
      
      <!-- Print Styles -->
      <style>
        @media print {
            @page {
                size: auto;
                margin: 0mm;
            }
            body {
                visibility: hidden;
            }
            /* Target AppModal Styles */
            .modal-backdrop {
                visibility: visible !important;
                background: white !important;
                position: fixed;
                inset: 0;
                z-index: 99999;
                padding: 0 !important;
                display: block !important;
                width: 100% !important;
                height: 100% !important;
            }
            .modal-content {
                visibility: visible !important;
                box-shadow: none !important;
                border: none !important;
                border-radius: 0 !important;
                width: 100% !important;
                height: 100% !important;
                max-width: none !important;
                max-height: none !important;
                margin: 0 !important;
                padding: 0 !important;
                transform: none !important;
                position: static !important;
                overflow: visible !important; /* Allow printing overflow */
            }
            .modal-header, .modal-footer, .btn-close-x {
                display: none !important;
            }
            .modal-body {
                padding: 20mm !important; /* Page padding for print */
                overflow: visible !important;
                display: block !important;
                height: auto !important;
            }
            /* Inner Content */
            #printableArea {
                padding: 0 !important;
                margin: 0 !important;
                width: 100% !important;
            }
            
            .print\\:hidden {
                display: none !important;
            }
            /* Explicitly hide standard header classes */
            header, nav, aside, .sidebar, .header, .tailadmin-header {
                display: none !important;
            }
        }
      </style>
  `
})
export class RequestFormDListComponent implements OnInit {
  requests: any[] = [];
  filteredRequests: any[] = []; // Store filtered list
  loading: boolean = true;
  
  filters = {
    search: '',
    status: '',
    fromDate: '',
    toDate: ''
  };

  companyName: string = '';

  constructor(
    private licenseService: LicenseService,
    private authService: AuthService
  ) {}

  ngOnInit() {
    this.fetchRequests();
  }

  fetchRequests() {
    this.authService.getProfile().subscribe({
        next: (profile) => {
            // Extract company name from profile
            if (profile.businessInfo && profile.businessInfo.company_name) {
                this.companyName = profile.businessInfo.company_name;
            }
            
            // Assuming profile has user.id or id
            const userId = profile.user?.id || profile.id || 1; // Fallback
            this.licenseService.getUserFormDRequests(userId).subscribe({
                next: (data) => {
                    this.requests = data;
                    this.filteredRequests = data; // Initialize filtered list
                    this.loading = false;
                },
                error: (err) => {
                    console.error('Failed to load requests', err);
                    this.loading = false;
                }
            });
        },
        error: (err) => {
             console.error('Failed to get user profile', err);
             this.loading = false;
        }
    });
  }

  applyFilters() {
    this.filteredRequests = this.requests.filter(req => {
        // 1. Status Filter
        if (this.filters.status && req.status !== this.filters.status) {
            return false;
        }

        // 2. Search Text (License, Instrument, Serial)
        if (this.filters.search) {
            const searchTerm = this.filters.search.toLowerCase();
            const matchesSearch = 
                (req.license_number?.toLowerCase().includes(searchTerm)) ||
                (req.instrument_name?.toLowerCase().includes(searchTerm)) ||
                (req.serial_number?.toLowerCase().includes(searchTerm)) ||
                (req.id?.toString().includes(searchTerm));
            
            if (!matchesSearch) return false;
        }

        // 3. Date Range
        if (this.filters.fromDate || this.filters.toDate) {
            const reqDate = new Date(req.created_at);
            // Reset time part for accurate comparison
            reqDate.setHours(0,0,0,0);

            if (this.filters.fromDate) {
                const from = new Date(this.filters.fromDate);
                from.setHours(0,0,0,0);
                if (reqDate < from) return false;
            }
            if (this.filters.toDate) {
                const to = new Date(this.filters.toDate);
                to.setHours(0,0,0,0);
                if (reqDate > to) return false;
            }
        }

        return true;
    });
  }

  clearFilters() {
    this.filters = {
        search: '',
        status: '',
        fromDate: '',
        toDate: ''
    };
    this.applyFilters();
  }
  
  // Modal Logic
  selectedRequest: any = null;
  
  // Dynamic Text Configuration (Copied from RequestFormDComponent)
  textConfig: any = {
    headerSubtitle: 'Form of Certificate to be used by a Pump Mechanic after Sealing or Re-sealing (Regulation 12(d))',
    certMechanicTitle: 'pump mechanic',
    certInstrumentName: 'pump',
    certUserDescription: 'liquid measuring pump',
    certActionContext: 'sealed/re-sealed'
  };

  openView(req: any) {
    this.selectedRequest = req;
    
    // Determine the license type or instrument type from the request.
    // Concatenate all potential fields to check for keywords.
    // Also explicitly check license_number for 'Class D'
    const type = (req.instrument_name + ' ' + (req.license_type || '') + ' ' + (req.license_number || '')).toLowerCase();
    this.updateConfig(type);
  }

  updateConfig(type: string) {
      if (type.includes('tank') || type.includes('construction') || type.includes('calibration')) {
          // Tank Constructor
          this.textConfig = {
              headerSubtitle: 'Form of Certificate to be used by a Tank Constructor after Calibration (Regulation 12(d))',
              certMechanicTitle: 'tank constructor',
              certInstrumentName: 'tank',
              certUserDescription: 'storage tank',
              certActionContext: 'calibrated'
          };
      } else if (type.includes('class d') || type.includes('pump') || type.includes('mechanic')) {
          // Explicit Pump Mechanic (Class D)
           this.textConfig = {
              headerSubtitle: 'Form of Certificate to be used by a Pump Mechanic after Sealing or Re-sealing (Regulation 12(d))',
              certMechanicTitle: 'pump mechanic',
              certInstrumentName: 'pump',
              certUserDescription: 'liquid measuring pump',
              certActionContext: 'sealed/re-sealed'
          };
      } else {
          // Default fallback (Pump Mechanic)
          this.textConfig = {
              headerSubtitle: 'Form of Certificate to be used by a Pump Mechanic after Sealing or Re-sealing (Regulation 12(d))',
              certMechanicTitle: 'pump mechanic',
              certInstrumentName: 'pump',
              certUserDescription: 'liquid measuring pump',
              certActionContext: 'sealed/re-sealed'
          };
      }
  }

  closeView() {
    this.selectedRequest = null;
  }

  printForm() {
    window.print();
  }
}
