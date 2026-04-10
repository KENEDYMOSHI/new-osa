import { CommonModule } from '@angular/common';
import { Component } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { Router, RouterModule } from '@angular/router';

type WizardStep = 1 | 2 | 3;

interface StepMeta {
  number: WizardStep;
  label: string;
  description: string;
}

@Component({
  selector: 'app-request-inspection',
  standalone: true,
  imports: [CommonModule, RouterModule, FormsModule],
  template: `
  <div class="min-h-screen bg-gray-50 dark:bg-gray-950">

    <!-- SUCCESS STATE -->
    <div *ngIf="submitted" class="flex min-h-screen items-center justify-center px-4">
      <div class="w-full max-w-md text-center">
        <div class="mx-auto mb-6 flex h-20 w-20 items-center justify-center rounded-full bg-emerald-100 dark:bg-emerald-500/10">
          <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-emerald-600 dark:text-emerald-400">
            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m9 11 3 3L22 4"/>
          </svg>
        </div>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Request Submitted!</h2>
        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
          Your request for an inspection officer has been received. Redirecting you back...
        </p>
        <div class="mt-6 flex justify-center">
          <div class="h-1 w-40 overflow-hidden rounded-full bg-gray-100 dark:bg-gray-800">
            <div class="h-full animate-[width_2s_linear] rounded-full bg-emerald-500"></div>
          </div>
        </div>
      </div>
    </div>

    <ng-container *ngIf="!submitted">

      <!-- STICKY TOP BAR -->
      <div class="sticky top-0 z-30 border-b border-gray-200 bg-white/95 backdrop-blur-sm dark:border-gray-800 dark:bg-gray-900/95">
        <div class="mx-auto max-w-5xl px-3 sm:px-6">

          <!-- Nav row -->
          <div class="flex h-12 items-center gap-2">
            <!-- Back button -->
            <button type="button" (click)="goBack()"
              class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-gray-500 transition-colors hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white">
              <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <path d="m15 18-6-6 6-6"/>
              </svg>
            </button>

            <!-- Title & breadcrumb -->
            <div class="flex min-w-0 flex-1 items-center gap-1.5">
              <span class="truncate text-sm font-semibold text-gray-900 dark:text-white sm:hidden">
                Request Inspection
              </span>
              <nav class="hidden items-center gap-1.5 text-sm sm:flex">
                <a routerLink="/business/applications-and-requests"
                  class="shrink-0 text-gray-500 transition-colors hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">
                  Applications & Requests
                </a>
                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="shrink-0 text-gray-300 dark:text-gray-700">
                  <path d="m9 18 6-6-6-6"/>
                </svg>
                <span class="font-semibold text-gray-900 dark:text-white">Request Inspection</span>
              </nav>
            </div>

            <span class="shrink-0 rounded-full bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-600 dark:bg-gray-800 dark:text-gray-300 sm:hidden">
              Step {{ currentStep }}/{{ steps.length }}
            </span>
          </div>

          <!-- Step progress bar – mobile -->
          <div class="flex h-1 overflow-hidden rounded-full sm:hidden">
            <div class="h-full rounded-full bg-[#F7941D] transition-all duration-500"
              [style.width.%]="((currentStep - 1) / (steps.length - 1)) * 100 + (currentStep === steps.length ? 100 : 33)">
            </div>
            <div class="flex-1 bg-gray-100 dark:bg-gray-800"></div>
          </div>

          <!-- Step tabs – sm+ -->
          <div class="hidden items-center sm:flex">
            <ng-container *ngFor="let step of steps; let last = last">
              <button type="button" (click)="goToStep(step.number)"
                class="flex shrink-0 items-center gap-2 border-b-2 px-3 py-3 text-sm font-medium transition-colors"
                [ngClass]="{
                  'border-[#F7941D] text-[#F7941D]': currentStep === step.number,
                  'border-emerald-500 text-emerald-600 dark:text-emerald-400': currentStep > step.number,
                  'border-transparent text-gray-400 dark:text-gray-500': currentStep < step.number
                }"
                [disabled]="currentStep < step.number">
                <span class="inline-flex h-5 w-5 shrink-0 items-center justify-center rounded-full text-[11px] font-bold"
                  [ngClass]="{
                    'bg-[#F7941D] text-white': currentStep === step.number,
                    'bg-emerald-500 text-white': currentStep > step.number,
                    'bg-gray-100 text-gray-400 dark:bg-gray-800 dark:text-gray-500': currentStep < step.number
                  }">
                  <svg *ngIf="currentStep > step.number" xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 6 9 17l-5-5"/>
                  </svg>
                  <span *ngIf="currentStep <= step.number">{{ step.number }}</span>
                </span>
                {{ step.label }}
              </button>
              <div *ngIf="!last" class="mx-1 h-px w-6 shrink-0"
                [ngClass]="currentStep > step.number ? 'bg-emerald-300 dark:bg-emerald-600' : 'bg-gray-200 dark:bg-gray-700'">
              </div>
            </ng-container>
          </div>

        </div>
      </div>

      <!-- PAGE CONTENT -->
      <div class="mx-auto max-w-5xl px-4 py-8 sm:px-6">

        <!-- STEP 1 -->
        <section *ngIf="currentStep === 1">
          <div class="mb-6">
            <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">What needs inspection?</h1>
            <p class="mt-1.5 text-sm text-gray-500 dark:text-gray-400">Select the category of the equipment to be inspected.</p>
          </div>
          <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <button type="button" (click)="selectedType = 'new_equipment'"
              class="group relative flex items-start gap-4 rounded-2xl border p-5 text-left transition-all duration-200"
              [ngClass]="selectedType === 'new_equipment' ? 'border-[#F7941D] bg-[#FFF7ED] shadow-md ring-1 ring-[#F7941D]/20 dark:bg-[#F7941D]/10 dark:border-[#F7941D]/60' : 'border-gray-200 bg-white hover:border-gray-300 hover:shadow-sm dark:border-gray-800 dark:bg-gray-900 dark:hover:border-gray-700'">
              <div class="min-w-0 flex-1">
                <p class="text-sm font-semibold text-gray-900 dark:text-white">New Equipment</p>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Inspection for newly registered items.</p>
              </div>
            </button>
            <button type="button" (click)="selectedType = 'periodic'"
              class="group relative flex items-start gap-4 rounded-2xl border p-5 text-left transition-all duration-200"
              [ngClass]="selectedType === 'periodic' ? 'border-[#F7941D] bg-[#FFF7ED] shadow-md ring-1 ring-[#F7941D]/20 dark:bg-[#F7941D]/10 dark:border-[#F7941D]/60' : 'border-gray-200 bg-white hover:border-gray-300 hover:shadow-sm dark:border-gray-800 dark:bg-gray-900 dark:hover:border-gray-700'">
              <div class="min-w-0 flex-1">
                <p class="text-sm font-semibold text-gray-900 dark:text-white">Periodic Inspection</p>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Routine or annual mandatory checks.</p>
              </div>
            </button>
            <button type="button" (click)="selectedType = 'after_repair'"
              class="group relative flex items-start gap-4 rounded-2xl border p-5 text-left transition-all duration-200"
              [ngClass]="selectedType === 'after_repair' ? 'border-[#F7941D] bg-[#FFF7ED] shadow-md ring-1 ring-[#F7941D]/20 dark:bg-[#F7941D]/10 dark:border-[#F7941D]/60' : 'border-gray-200 bg-white hover:border-gray-300 hover:shadow-sm dark:border-gray-800 dark:bg-gray-900 dark:hover:border-gray-700'">
              <div class="min-w-0 flex-1">
                <p class="text-sm font-semibold text-gray-900 dark:text-white">After Repair</p>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Verification after significant repairs.</p>
              </div>
            </button>
          </div>
        </section>

        <!-- STEP 2 -->
        <section *ngIf="currentStep === 2">
          <div class="mb-6">
            <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Inspection Details</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Provide details for the inspection officer.</p>
          </div>
          <div class="space-y-4 rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
              <div class="sm:col-span-2">
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Equipments to Inspect</label>
                <input type="text" placeholder="e.g. Weighbridge 1" class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm text-gray-900 outline-none transition-all focus:border-[#F7941D] focus:bg-white dark:border-gray-700 dark:bg-gray-800 dark:text-white" />
              </div>
              <div>
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Proposed Date</label>
                <input type="date" class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm text-gray-900 outline-none transition-all focus:border-[#F7941D] focus:bg-white dark:border-gray-700 dark:bg-gray-800 dark:text-white" />
              </div>
              <div>
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Contact Person</label>
                <input type="text" placeholder="Name or Phone" class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm text-gray-900 outline-none transition-all focus:border-[#F7941D] focus:bg-white dark:border-gray-700 dark:bg-gray-800 dark:text-white" />
              </div>
            </div>
          </div>
        </section>

        <!-- STEP 3 -->
        <section *ngIf="currentStep === 3">
          <div class="mb-6">
            <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Review & Confirm</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Review your request details before submitting.</p>
          </div>
          <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
            <p class="text-sm font-medium text-gray-900 dark:text-white">Ready to send the inspection request.</p>
          </div>
        </section>

        <!-- BOTTOM ACTION BAR -->
        <div class="mt-8 flex items-center justify-between gap-4 rounded-2xl border border-gray-200 bg-white px-6 py-4 dark:border-gray-800 dark:bg-gray-900">
          <div class="min-w-0">
            <p *ngIf="currentStep === 1" class="text-sm text-gray-400">Step 1 of 3</p>
            <p *ngIf="currentStep === 2" class="text-sm text-gray-500">Step 2 of 3</p>
            <p *ngIf="currentStep === 3" class="text-sm text-emerald-600 font-medium">Ready to submit</p>
          </div>
          <div class="flex items-center gap-3">
            <button *ngIf="currentStep === 1" type="button" (click)="goBack()"
              class="inline-flex items-center gap-2 rounded-xl border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-800">
              Cancel
            </button>
            <button *ngIf="currentStep > 1" type="button" (click)="prevStep()"
              class="inline-flex items-center gap-2 rounded-xl border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-800">
              Back
            </button>
            <button *ngIf="currentStep < 3" type="button" (click)="nextStep()" [disabled]="currentStep === 1 && !selectedType"
              class="inline-flex items-center gap-2 rounded-xl bg-[#F7941D] px-6 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-[#E6820A] disabled:opacity-50">
              Continue
            </button>
            <button *ngIf="currentStep === 3" type="button" (click)="submit()"
              class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-6 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-emerald-700">
              Submit Request
            </button>
          </div>
        </div>

      </div>
    </ng-container>
  </div>
  `
})
export class RequestInspectionComponent {
  currentStep: WizardStep = 1;
  submitted = false;
  selectedType = '';

  steps: StepMeta[] = [
    { number: 1, label: 'Inspection Type', description: 'Select the inspection category' },
    { number: 2, label: 'Details', description: 'Provide request specifics' },
    { number: 3, label: 'Review', description: 'Review and submit' }
  ];

  constructor(private router: Router) {}

  goBack() {
    this.router.navigate(['/business/applications-and-requests']);
  }

  goToStep(step: WizardStep) {
    if (step > this.currentStep && (this.currentStep === 1 && !this.selectedType)) return;
    this.currentStep = step;
  }

  nextStep() {
    if (this.currentStep < 3) {
      this.currentStep++;
    }
  }

  prevStep() {
    if (this.currentStep > 1) {
      this.currentStep--;
    }
  }

  submit() {
    this.submitted = true;
    setTimeout(() => this.goBack(), 2000);
  }
}
