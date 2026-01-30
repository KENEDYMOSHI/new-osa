import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { FormsModule } from '@angular/forms';
import { LicenseService } from '../../services/license.service';
import { AuthService } from '../../core/services/auth.service';
import { AppModalComponent } from '../../components/app-modal/app-modal.component';

@Component({
  selector: 'app-requested-form-d-list',
  standalone: true,
  imports: [CommonModule, RouterModule, FormsModule, AppModalComponent],
  template: `
    <div class="p-6 bg-gray-50 min-h-screen font-sans">
      <!-- Header -->
      <div class="mb-8 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Requested Form D</h1>
            <p class="text-gray-500 text-sm mt-1">Manage submitted Form D requests.</p>
        </div>
      </div>

      <!-- Filters -->
      <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 mb-6 flex flex-wrap gap-4 items-end">
        <div class="flex-grow min-w-[200px]">
            <label class="block text-xs font-bold text-gray-500 mb-1">Search</label>
            <input type="text" [(ngModel)]="filters.search" (input)="applyFilters()" placeholder="Applicant, License, Form D No..." class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-orange-500 transition-colors">
        </div>
        <div class="w-[180px]">
            <label class="block text-xs font-bold text-gray-500 mb-1">Status</label>
            <select [(ngModel)]="filters.status" (change)="applyFilters()" class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-orange-500 transition-colors">
                <option value="">All Statuses</option>
                <option value="Pending Verification">Pending</option>
                <option value="Verified">Verified (Old)</option>
                <option value="Approved">Approved</option>
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
            <h2 class="text-lg font-bold text-gray-800">Requests List</h2>
            <span class="text-xs font-bold text-gray-400 bg-gray-100 px-2 py-1 rounded-full">{{ filteredRequests.length }} requests</span>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-gray-600 text-xs uppercase tracking-wider">
                        <th class="p-4 font-bold border-b border-gray-100">Form D No</th>
                        <th class="p-4 font-bold border-b border-gray-100">Applicant</th>
                        <th class="p-4 font-bold border-b border-gray-100">Date</th>
                        <th class="p-4 font-bold border-b border-gray-100">Status</th>
                        <th class="p-4 font-bold border-b border-gray-100 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    <tr *ngFor="let req of filteredRequests" class="hover:bg-gray-50 transition-colors border-b border-gray-50 last:border-0">
                        <td class="p-4 font-mono text-gray-500 text-xs">#{{ req.id }}</td>
                        <td class="p-4">
                            <div class="font-bold text-gray-700 text-xs">{{ req.company_name }}</div>
                            <div class="text-xs text-gray-500">{{ req.practitioner_name }}</div>
                        </td>
                        <td class="p-4 text-gray-600 text-xs whitespace-nowrap">
                            {{ req.created_at | date:'dd MMM yyyy' }}
                        </td>
                        <td class="p-4">
                            <span class="px-2 py-1 rounded text-[10px] font-bold uppercase"
                                  [ngClass]="{
                                    'bg-yellow-100 text-yellow-700': req.status === 'Pending Verification',
                                    'bg-green-100 text-green-700': req.status === 'Approved' || req.status === 'Verified',
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
                        <td colspan="5" class="p-8 text-center text-gray-400 italic">
                            No requests found matching your filters.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
      </div>
      
      <!-- View / Action Modal -->
      <app-modal *ngIf="selectedRequest"
                 [title]="'Request Details #' + selectedRequest.id"
                 size="lg"
                 [staticBackdrop]="true"
                 (close)="closeView()">
                 
          <div body class="w-full bg-white p-8 md:p-12 font-serif text-sm leading-relaxed text-black h-full overflow-y-auto">
                 
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
                 <div class="space-y-4 text-xs font-medium">
                     
                     <!-- Top Section -->
                     <div class="space-y-2">
                         <div class="flex items-end gap-2">
                             <span class="whitespace-nowrap w-48">Company employing mechanic:</span>
                             <div class="flex-grow border-b border-dotted border-black px-2 pb-0.5 font-bold text-sm">
                                 {{ selectedRequest.company_name }}
                             </div>
                         </div>
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
                                 {{ selectedRequest.company_name }} <!-- Assuming this is also the user, or context logic required if different -->
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
                         
                         <div class="flex gap-4">
                             <div class="flex items-end gap-2 flex-1">
                                 <span class="whitespace-nowrap">Product:</span>
                                 <div class="flex-grow border-b border-dotted border-black px-2 pb-0.5 font-bold">
                                     {{ selectedRequest.product }}
                                 </div>
                             </div>
                             <div class="flex items-end gap-2 flex-1" *ngIf="selectedRequest.quantity || selectedRequest.capacity">
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

                     <div class="grid grid-cols-1 gap-8 mt-6">
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

                         <div class="flex justify-between mt-4">
                             <div class="flex items-end gap-2 w-1/2">
                                 <span class="whitespace-nowrap">Date:</span>
                                 <div class="flex-grow border-b border-dotted border-black px-2 pb-0.5 font-bold">
                                     {{ selectedRequest.declarant_date | date:'dd/MM/yyyy' }}
                                 </div>
                             </div>
                         </div>
                     </div>

                     <!-- Admin Action Section - Appears below certificate -->
                     <div class="mt-8 pt-8 border-t-2 border-gray-100 print:hidden">
                        
                         <!-- Action Buttons -->
                         <div *ngIf="selectedRequest.status === 'Pending Verification'">
                            <h3 class="font-bold text-gray-800 mb-4 text-center">Officer Actions</h3>
                            <div class="flex gap-4 max-w-md mx-auto">
                                <button (click)="showApprove()" class="flex-1 py-3 bg-green-600 text-white rounded-lg font-bold hover:bg-green-700 transition shadow-sm">
                                    Approve & Assign Inspector
                                </button>
                                <button (click)="showReject()" class="flex-1 py-3 bg-red-600 text-white rounded-lg font-bold hover:bg-red-700 transition shadow-sm">
                                    Reject Request
                                </button>
                            </div>
                         </div>

                         <!-- Status Messages -->
                         <div *ngIf="selectedRequest.status === 'Approved'" class="p-4 bg-green-50 text-green-800 rounded-lg text-center border border-green-100">
                            <p class="font-bold text-sm uppercase mb-1 flex items-center justify-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                                Request Approved
                            </p>
                            <p class="text-xs">Assigned Inspector ID: <strong>{{ selectedRequest.inspector_id }}</strong></p>
                            <p class="text-xs mt-1 text-green-600">Approved on {{ selectedRequest.approved_at | date:'medium' }}</p>
                         </div>

                         <div *ngIf="selectedRequest.status === 'Rejected'" class="p-4 bg-red-50 text-red-800 rounded-lg text-center border border-red-100">
                            <p class="font-bold text-sm uppercase mb-1 flex items-center justify-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" /></svg>
                                Request Rejected
                            </p>
                            <p class="text-xs italic">"{{ selectedRequest.rejection_reason }}"</p>
                         </div>
                     </div>
                 </div>
          </div>
          
          <div footer class="flex gap-2 w-full justify-end print:hidden">
              <button (click)="closeView()" class="px-5 py-2.5 rounded-lg border border-gray-300 text-gray-700 font-medium hover:bg-gray-50 transition-colors">
                  Close
              </button>
          </div>
          
      </app-modal>

      <!-- Approve Modal -->
      <app-modal *ngIf="showApproveModal"
                 title="Approve Request"
                 size="sm"
                 (close)="cancelApprove()">
           <div body class="p-4">
               <label class="block text-sm font-bold text-gray-700 mb-2">Assign Inspector</label>
               <select [(ngModel)]="selectedInspectorId" class="w-full border border-gray-300 rounded-lg p-2.5 bg-white">
                   <option [ngValue]="null">Select Inspector...</option>
                   <option *ngFor="let insp of inspectors" [value]="insp.id">{{ insp.username || insp.email || ('Inspector #' + insp.id) }}</option>
               </select>
           </div>
           <div footer class="flex justify-end gap-2 w-full">
               <button (click)="cancelApprove()" class="px-4 py-2 border rounded-lg text-gray-600">Cancel</button>
               <button (click)="confirmApprove()" [disabled]="!selectedInspectorId" class="px-4 py-2 bg-green-600 text-white rounded-lg font-bold hover:bg-green-700 disabled:opacity-50">
                   Confirm Approval
               </button>
           </div>
      </app-modal>

      <!-- Reject Modal -->
      <app-modal *ngIf="showRejectModal"
                 title="Reject Request"
                 size="sm"
                 (close)="cancelReject()">
           <div body class="p-4">
               <label class="block text-sm font-bold text-gray-700 mb-2">Rejection Reason</label>
               <textarea [(ngModel)]="rejectionReason" rows="4" class="w-full border border-gray-300 rounded-lg p-3" placeholder="Enter reason for rejection..."></textarea>
           </div>
           <div footer class="flex justify-end gap-2 w-full">
               <button (click)="cancelReject()" class="px-4 py-2 border rounded-lg text-gray-600">Cancel</button>
               <button (click)="confirmReject()" [disabled]="!rejectionReason" class="px-4 py-2 bg-red-600 text-white rounded-lg font-bold hover:bg-red-700 disabled:opacity-50">
                   Confirm Rejection
               </button>
           </div>
      </app-modal>
  `
})
export class RequestedFormDListComponent implements OnInit {
  requests: any[] = [];
  filteredRequests: any[] = [];
  inspectors: any[] = [];
  loading: boolean = true;
  
  filters = {
    search: '',
    status: '',
    fromDate: '',
    toDate: ''
  };

  // Modal Logic
  selectedRequest: any = null;
  textConfig: any = {
    headerSubtitle: 'Form of Certificate to be used by a Pump Mechanic after Sealing or Re-sealing (Regulation 12(d))',
    certMechanicTitle: 'pump mechanic',
    certInstrumentName: 'pump',
    certUserDescription: 'liquid measuring pump',
    certActionContext: 'sealed/re-sealed'
  };

  // Action Modals
  showApproveModal = false;
  showRejectModal = false;
  selectedInspectorId: any = null;
  rejectionReason: string = '';

  constructor(
    private licenseService: LicenseService
  ) {}

  ngOnInit() {
    this.fetchRequests();
    this.fetchInspectors();
  }

  fetchRequests() {
    // Admin: Get all requests
    this.licenseService.getAllFormDRequests().subscribe({
        next: (data) => {
            this.requests = data;
            this.applyFilters();
            this.loading = false;
        },
        error: (err) => {
            console.error('Failed to load requests', err);
            this.loading = false;
        }
    });
  }

  fetchInspectors() {
      this.licenseService.getInspectors().subscribe({
          next: (data) => {
              this.inspectors = data;
              // Mock fallback if empty
              if (!this.inspectors || this.inspectors.length === 0) {
                  this.inspectors = [
                      { id: 101, username: 'Inspector John' },
                      { id: 102, username: 'Inspector Jane' }
                  ];
              }
          },
          error: (err) => console.error(err)
      });
  }

  applyFilters() {
    this.filteredRequests = this.requests.filter(req => {
        // 1. Status Filter
        if (this.filters.status && req.status !== this.filters.status) {
            return false;
        }

        // 2. Search Text
        if (this.filters.search) {
            const searchTerm = this.filters.search.toLowerCase();
            const matchesSearch = 
                (req.company_name?.toLowerCase().includes(searchTerm)) ||
                (req.practitioner_name?.toLowerCase().includes(searchTerm)) ||
                (req.license_number?.toLowerCase().includes(searchTerm)) ||
                (req.id?.toString().includes(searchTerm));
            
            if (!matchesSearch) return false;
        }

        // 3. Date Range
        if (this.filters.fromDate || this.filters.toDate) {
            const reqDate = new Date(req.created_at);
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
  
  openView(req: any) {
    this.selectedRequest = req;
    
    // Determine the text configuration based on instrument type
    const type = (req.instrument_name + ' ' + (req.license_type || '') + ' ' + (req.license_number || '')).toLowerCase();
    
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
    this.showApproveModal = false;
    this.showRejectModal = false;
  }

  // Approval Workflow
  showApprove() {
      this.showApproveModal = true;
      this.selectedInspectorId = null;
  }

  cancelApprove() {
      this.showApproveModal = false;
      this.selectedInspectorId = null;
  }

  confirmApprove() {
      if (!this.selectedRequest || !this.selectedInspectorId) return;
      
      this.licenseService.approveFormD(this.selectedRequest.id, this.selectedInspectorId).subscribe({
          next: (res) => {
              // Update local state
              this.selectedRequest.status = 'Approved';
              this.selectedRequest.inspector_id = this.selectedInspectorId;
              this.selectedRequest.approved_at = new Date();
              this.showApproveModal = false;
              alert('Request Approved!');
          },
          error: (err) => alert('Failed to approve.')
      });
  }

  // Rejection Workflow
  showReject() {
      this.showRejectModal = true;
      this.rejectionReason = '';
  }

  cancelReject() {
      this.showRejectModal = false;
      this.rejectionReason = '';
  }

  confirmReject() {
      if (!this.selectedRequest || !this.rejectionReason) return;
      
      this.licenseService.rejectFormD(this.selectedRequest.id, this.rejectionReason).subscribe({
          next: (res) => {
              // Update local state
              this.selectedRequest.status = 'Rejected';
              this.selectedRequest.rejection_reason = this.rejectionReason;
              this.showRejectModal = false;
              alert('Request Rejected.');
          },
          error: (err) => alert('Failed to reject.')
      });
  }
}
