import { CommonModule } from '@angular/common';
import { Component } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { Router, RouterModule } from '@angular/router';
import { TranslateModule } from '@ngx-translate/core';

type WizardStep = 1 | 2 | 3;

interface StepMeta {
  number: WizardStep;
  label: string;
  description: string;
}

@Component({
  selector: 'app-business-support-help',
  standalone: true,
  imports: [CommonModule, RouterModule, FormsModule, TranslateModule],
  template: `
  <div class="min-h-screen bg-gray-50 dark:bg-gray-950 pb-16">

    <!-- SUCCESS STATE -->
    <div *ngIf="submitted" class="flex min-h-[80vh] items-center justify-center px-4">
      <div class="w-full max-w-md text-center">
        <div class="mx-auto mb-6 flex h-24 w-24 items-center justify-center rounded-full bg-emerald-100 dark:bg-emerald-500/10">
          <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-emerald-600 dark:text-emerald-400">
            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m9 11 3 3L22 4"/>
          </svg>
        </div>
        <h2 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white">{{ 'BUSINESS.SUPPORT_HELP.SUCCESS_TITLE' | translate }}</h2>
        <p class="mt-3 text-base text-gray-500 dark:text-gray-400 max-w-sm mx-auto leading-relaxed">
          {{ 'BUSINESS.SUPPORT_HELP.SUCCESS_DESC' | translate }}
        </p>
        <div class="mt-8 flex justify-center">
          <div class="h-1.5 w-48 overflow-hidden rounded-full bg-gray-100 dark:bg-gray-800">
            <div class="h-full animate-[width_2s_linear] rounded-full bg-emerald-500"></div>
          </div>
        </div>
      </div>
    </div>

    <ng-container *ngIf="!submitted">

      <!-- HEADER & BREADCRUMB -->
      <div class="mx-auto max-w-5xl px-4 pt-6 pb-2 sm:px-6">
        <nav aria-label="Breadcrumb">
          <ol class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
            <li>
              <a routerLink="/business/dashboard" class="transition-colors hover:text-gray-900 dark:hover:text-white">
                {{ 'BUSINESS.SIDEBAR.DASHBOARD' | translate }}
              </a>
            </li>
            <li aria-hidden="true" class="text-gray-300 dark:text-gray-600">
              <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="m9 18l6 -6l-6 -6" />
              </svg>
            </li>
            <li class="font-medium text-gray-900 dark:text-white">{{ 'BUSINESS.SUPPORT_HELP.BREADCRUMB_SUPPORT' | translate }}</li>
          </ol>
        </nav>
        <h1 class="mt-3 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
          {{ 'BUSINESS.SUPPORT_HELP.PAGE_TITLE' | translate }}
        </h1>
      </div>

      <!-- WIZARD CONTINUED -->
      <div class="mx-auto max-w-5xl px-4 py-4 sm:px-6">
        <!-- STEPS PROGRESS CARD -->
        <div class="mb-8 overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
          <!-- Step progress bar – mobile -->
          <div class="flex h-1.5 w-full bg-gray-100 dark:bg-gray-800 sm:hidden">
            <div class="h-full bg-[#F7941D] transition-all duration-500"
              [style.width.%]="((currentStep - 1) / (steps.length - 1)) * 100 + (currentStep === steps.length ? 100 : 33)">
            </div>
          </div>
          <!-- Step tabs – sm+ -->
          <div class="flex items-center justify-around sm:justify-start px-2 py-1 sm:px-6 sm:py-3">
            <ng-container *ngFor="let step of steps; let last = last">
              <div class="flex flex-1 sm:flex-none items-center justify-center sm:justify-start">
                <button type="button" (click)="goToStep(step.number)"
                  class="flex flex-col sm:flex-row items-center gap-2 sm:gap-3 px-2 py-3 transition-colors"
                  [ngClass]="{
                    'text-[#F7941D]': currentStep === step.number,
                    'text-emerald-600 dark:text-emerald-400': currentStep > step.number,
                    'text-gray-400 dark:text-gray-500': currentStep < step.number
                  }"
                  [disabled]="currentStep < step.number">
                  <span class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-sm font-bold shadow-sm transition-all"
                    [ngClass]="{
                      'bg-[#F7941D] text-white ring-4 ring-[#F7941D]/20': currentStep === step.number,
                      'bg-emerald-500 text-white ring-4 ring-emerald-500/20': currentStep > step.number,
                      'bg-gray-100 text-gray-400 dark:bg-gray-800 dark:text-gray-500': currentStep < step.number
                    }">
                    <svg *ngIf="currentStep > step.number" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                      <path d="M20 6 9 17l-5-5"/>
                    </svg>
                    <span *ngIf="currentStep <= step.number">{{ step.number }}</span>
                  </span>
                  <div class="hidden sm:block text-left">
                    <p class="text-sm font-bold">{{ step.label | translate }}</p>
                    <p class="text-[11px] font-medium opacity-70">{{ step.description | translate }}</p>
                  </div>
                </button>
                <div *ngIf="!last" class="hidden sm:block mx-4 h-px w-12 shrink-0"
                  [ngClass]="currentStep > step.number ? 'bg-emerald-300 dark:bg-emerald-600' : 'bg-gray-200 dark:bg-gray-800'">
                </div>
              </div>
            </ng-container>
          </div>
        </div>

        <!-- STEP 1 -->
        <section *ngIf="currentStep === 1" class="animate-in fade-in slide-in-from-bottom-4 duration-500">
          <div class="mb-6 text-center sm:text-left">
            <h2 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">{{ 'BUSINESS.SUPPORT_HELP.Q1_TITLE' | translate }}</h2>
            <p class="mt-1.5 text-sm md:text-base text-gray-500 dark:text-gray-400">{{ 'BUSINESS.SUPPORT_HELP.Q1_DESC' | translate }}</p>
          </div>
          <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <button *ngFor="let category of issueCategories" type="button" (click)="selectedType = category.value"
              class="group relative flex items-start gap-4 rounded-2xl border p-5 text-left transition-all duration-300"
              [ngClass]="selectedType === category.value ? 'border-[#F7941D] bg-[#FFF7ED] shadow-md ring-2 ring-[#F7941D]/20 dark:bg-[#F7941D]/10 dark:border-[#F7941D]/60' : 'border-gray-200 bg-white hover:border-[#F7941D]/40 hover:shadow-sm dark:border-gray-800 dark:bg-gray-900 dark:hover:border-[#F7941D]/40'">
              <div class="mt-0.5 flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gray-50 text-gray-400 transition-colors group-hover:bg-[#FFF7ED] group-hover:text-[#F7941D] dark:bg-gray-800 dark:group-hover:bg-[#F7941D]/20"
                [ngClass]="selectedType === category.value ? '!bg-[#F7941D] !text-white' : ''">
                
                <svg *ngIf="category.value === 'new_equipment'" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>

                <svg *ngIf="category.value === 'reverification'" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/><path d="M12 7v5l4 2"/></svg>
                
                <svg *ngIf="category.value === 'request_technician'" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m2 22 1-1h3l9-9"/><path d="M3 21v-3l9-9"/><path d="m15 6 3.4-3.4a2.1 2.1 0 1 1 3 3L18 9l.4.4a2.1 2.1 0 1 1-3 3l-3.8-3.8a2.1 2.1 0 1 1 3-3l.4.4Z"/></svg>
                
                <svg *ngIf="category.value === 'billing'" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/></svg>
                
                <svg *ngIf="category.value === 'other'" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" x2="12.01" y1="17" y2="17"/></svg>
              </div>
              <div class="min-w-0 flex-1 pl-1">
                <p class="text-[15px] font-semibold text-gray-900 dark:text-white transition-colors"
                   [ngClass]="{'text-[#F7941D]': selectedType === category.value}">
                  {{ category.label | translate }}
                </p>
                <p class="mt-1 text-xs text-gray-500 leading-relaxed dark:text-gray-400">{{ category.description | translate }}</p>
              </div>
              <div class="ml-2 mt-1 flex shrink-0 h-[18px] w-[18px] items-center justify-center rounded-full border-2 transition-all duration-300"
                [ngClass]="selectedType === category.value ? 'border-[#F7941D] bg-[#F7941D]' : 'border-gray-200 dark:border-gray-700'">
                <div class="h-1.5 w-1.5 rounded-full bg-white transition-all duration-300"
                   [ngClass]="selectedType === category.value ? 'scale-100' : 'scale-0'"></div>
              </div>
            </button>
          </div>
        </section>

        <!-- STEP 2 -->
        <section *ngIf="currentStep === 2" class="animate-in fade-in slide-in-from-bottom-4 duration-500">
          <div class="mb-6">
            <h2 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">{{ 'BUSINESS.SUPPORT_HELP.Q2_TITLE' | translate }}</h2>
            <p class="mt-1 text-sm md:text-base text-gray-500 dark:text-gray-400">{{ 'BUSINESS.SUPPORT_HELP.Q2_DESC' | translate }}</p>
          </div>
          <div class="space-y-6 rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900 sm:p-8">
            <div>
              <label class="mb-2 block text-sm font-semibold text-gray-900 dark:text-gray-200">{{ 'BUSINESS.SUPPORT_HELP.MESSAGE_LABEL' | translate }} <span class="text-red-500">*</span></label>
              <textarea [(ngModel)]="messageText" rows="6" [placeholder]="'BUSINESS.SUPPORT_HELP.MESSAGE_PLACEHOLDER' | translate" 
                class="w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm text-gray-900 outline-none transition-all focus:border-[#F7941D] focus:bg-white focus:ring-4 focus:ring-[#F7941D]/10 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:focus:border-[#F7941D]"></textarea>
              <p *ngIf="messageError" class="mt-2 text-xs text-red-500 flex items-center gap-1">
                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                {{ 'BUSINESS.SUPPORT_HELP.MESSAGE_REQUIRED' | translate }}
              </p>
            </div>
          </div>
        </section>

        <!-- STEP 3 -->
        <section *ngIf="currentStep === 3" class="animate-in fade-in slide-in-from-bottom-4 duration-500">
          <div class="mb-6">
            <h2 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">{{ 'BUSINESS.SUPPORT_HELP.REVIEW_TITLE' | translate }}</h2>
            <p class="mt-1 text-sm md:text-base text-gray-500 dark:text-gray-400">{{ 'BUSINESS.SUPPORT_HELP.REVIEW_DESC' | translate }}</p>
          </div>
          <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <div class="p-6 sm:p-8">
              <div class="mb-8 flex items-center gap-4 border-b border-gray-100 pb-6 dark:border-gray-800">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-[#FFF7ED] text-[#F7941D] dark:bg-[#F7941D]/10">
                  <svg *ngIf="selectedType === 'new_equipment'" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
                  <svg *ngIf="selectedType === 'reverification'" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/><path d="M12 7v5l4 2"/></svg>
                  <svg *ngIf="selectedType === 'request_technician'" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m2 22 1-1h3l9-9"/><path d="M3 21v-3l9-9"/><path d="m15 6 3.4-3.4a2.1 2.1 0 1 1 3 3L18 9l.4.4a2.1 2.1 0 1 1-3 3l-3.8-3.8a2.1 2.1 0 1 1 3-3l.4.4Z"/></svg>
                  <svg *ngIf="selectedType === 'billing'" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/></svg>
                  <svg *ngIf="selectedType === 'other'" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" x2="12.01" y1="17" y2="17"/></svg>
                </div>
                <div>
                  <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ 'BUSINESS.SUPPORT_HELP.SELECTED_ISSUE' | translate }}</p>
                  <p class="text-lg font-bold text-gray-900 dark:text-white">{{ getSelectedCategory()?.label | translate }}</p>
                </div>
              </div>
              <div>
                <p class="mb-2 text-sm font-medium text-gray-500 dark:text-gray-400">{{ 'BUSINESS.SUPPORT_HELP.YOUR_MESSAGE' | translate }}</p>
                <div class="rounded-xl border border-gray-100 bg-gray-50 p-4 dark:border-gray-800 dark:bg-gray-800/50">
                  <p class="text-sm leading-relaxed text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ messageText }}</p>
                </div>
              </div>
            </div>
            <div class="border-t border-gray-100 bg-gray-50/50 px-6 py-4 dark:border-gray-800 dark:bg-gray-800/30">
              <p class="text-sm font-medium text-gray-900 flex items-center gap-2 dark:text-white">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-emerald-500"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m9 11 3 3L22 4"/></svg>
                {{ 'BUSINESS.SUPPORT_HELP.READY_TO_SUBMIT' | translate }}
              </p>
            </div>
          </div>
        </section>

        <!-- BOTTOM ACTION BAR -->
        <div class="mt-8 flex flex-col-reverse sm:flex-row items-center justify-between gap-4 rounded-2xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-800 dark:bg-gray-900 sm:px-6">
          <div class="hidden sm:block min-w-0">
            <p *ngIf="currentStep === 1" class="text-sm font-medium text-gray-500">{{ 'BUSINESS.SUPPORT_HELP.STEP' | translate }} 1 {{ 'BUSINESS.SUPPORT_HELP.OF' | translate }} 3</p>
            <p *ngIf="currentStep === 2" class="text-sm font-medium text-gray-500">{{ 'BUSINESS.SUPPORT_HELP.STEP' | translate }} 2 {{ 'BUSINESS.SUPPORT_HELP.OF' | translate }} 3</p>
            <p *ngIf="currentStep === 3" class="text-sm font-semibold text-emerald-600">{{ 'BUSINESS.SUPPORT_HELP.READY_STATUS' | translate }}</p>
          </div>
          <div class="w-full flex sm:w-auto items-center justify-between sm:justify-end gap-3">
            <button *ngIf="currentStep === 1" type="button" (click)="goBack()"
              class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-xl border border-gray-300 px-5 py-2.5 text-sm font-semibold text-gray-700 transition-all hover:bg-gray-50 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-800">
              {{ 'BUSINESS.SUPPORT_HELP.BTN_CANCEL' | translate }}
            </button>
            <button *ngIf="currentStep > 1" type="button" (click)="prevStep()"
              class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-xl border border-gray-300 px-5 py-2.5 text-sm font-semibold text-gray-700 transition-all hover:bg-gray-50 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-800">
              {{ 'BUSINESS.SUPPORT_HELP.BTN_BACK' | translate }}
            </button>
            <button *ngIf="currentStep < 3" type="button" (click)="nextStep()" [disabled]="(currentStep === 1 && !selectedType)"
              class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-xl bg-[#F7941D] px-8 py-2.5 text-sm font-semibold text-white shadow-sm transition-all hover:bg-[#E6820A] disabled:opacity-60 disabled:cursor-not-allowed hover:shadow-md">
              {{ 'BUSINESS.SUPPORT_HELP.BTN_CONTINUE' | translate }}
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ml-1"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
            </button>
            <button *ngIf="currentStep === 3" type="button" (click)="submit()"
              class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 px-8 py-2.5 text-sm font-semibold text-white shadow-sm transition-all hover:bg-emerald-700 hover:shadow-md">
              {{ 'BUSINESS.SUPPORT_HELP.BTN_SUBMIT' | translate }}
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m22 2-7 20-4-9-9-4Z"/><path d="M22 2 11 13"/></svg>
            </button>
          </div>
        </div>

      </div>
    </ng-container>
  </div>
  `
})
export class SupportHelpComponent {
  currentStep: WizardStep = 1;
  submitted = false;
  selectedType = '';
  messageText = '';
  messageError = false;

  issueCategories = [
    { value: 'new_equipment', label: 'BUSINESS.SUPPORT_HELP.ISSUE_NEW_EQUIP', description: 'BUSINESS.SUPPORT_HELP.ISSUE_NEW_EQUIP_DESC', icon: 'ti ti-tool' },
    { value: 'reverification', label: 'BUSINESS.SUPPORT_HELP.ISSUE_REVERIFICATION', description: 'BUSINESS.SUPPORT_HELP.ISSUE_REVERIFICATION_DESC', icon: 'ti ti-refresh' },
    { value: 'request_technician', label: 'BUSINESS.SUPPORT_HELP.ISSUE_REQUEST_TECH', description: 'BUSINESS.SUPPORT_HELP.ISSUE_REQUEST_TECH_DESC', icon: 'ti ti-helmet' },
    { value: 'billing', label: 'BUSINESS.SUPPORT_HELP.ISSUE_BILLING', description: 'BUSINESS.SUPPORT_HELP.ISSUE_BILLING_DESC', icon: 'ti ti-receipt' },
    { value: 'other', label: 'BUSINESS.SUPPORT_HELP.ISSUE_OTHER', description: 'BUSINESS.SUPPORT_HELP.ISSUE_OTHER_DESC', icon: 'ti ti-message-circle' }
  ];

  steps: StepMeta[] = [
    { number: 1, label: 'BUSINESS.SUPPORT_HELP.STEP_1_LABEL', description: 'BUSINESS.SUPPORT_HELP.STEP_1_DESC' },
    { number: 2, label: 'BUSINESS.SUPPORT_HELP.STEP_2_LABEL', description: 'BUSINESS.SUPPORT_HELP.STEP_2_DESC' },
    { number: 3, label: 'BUSINESS.SUPPORT_HELP.STEP_3_LABEL', description: 'BUSINESS.SUPPORT_HELP.STEP_3_DESC' }
  ];

  constructor(private router: Router) {}

  getSelectedCategory() {
    return this.issueCategories.find(c => c.value === this.selectedType);
  }

  goBack() {
    this.router.navigate(['/business/dashboard']);
  }

  goToStep(step: WizardStep) {
    if (step === 2 && !this.selectedType) return;
    if (step === 3) {
      if (!this.selectedType) return;
      if (!this.messageText.trim()) {
         this.messageError = true;
         this.currentStep = 2;
         return;
      }
    }
    this.currentStep = step;
  }

  nextStep() {
    if (this.currentStep === 1) {
      if (this.selectedType) this.currentStep++;
    } else if (this.currentStep === 2) {
      if (this.messageText.trim()) {
        this.messageError = false;
        this.currentStep++;
      } else {
        this.messageError = true;
      }
    }
  }

  prevStep() {
    if (this.currentStep > 1) {
      this.currentStep--;
    }
  }

  submit() {
    // Implement standard submission logic to your backend here
    const payload = {
       issueType: this.selectedType,
       message: this.messageText
    };
    console.log('Support Ticket Payload:', payload);
    
    this.submitted = true;
    setTimeout(() => this.goBack(), 3000);
  }
}
