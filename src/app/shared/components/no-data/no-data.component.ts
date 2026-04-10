import { Component, Input } from '@angular/core';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-no-data',
  standalone: true,
  imports: [CommonModule],
  template: `
    <div class="flex w-full flex-col items-center justify-center p-8 text-center sm:p-14">
      <div class="relative w-full max-w-[280px]">
        <!-- Premium SVG Illustration for Empty State -->
        <svg width="240" height="200" viewBox="0 0 240 200" fill="none" xmlns="http://www.w3.org/2000/svg" class="mx-auto block drop-shadow-sm">
          <!-- Shadows/Background -->
          <ellipse cx="120" cy="180" rx="80" ry="8" class="fill-gray-100 dark:fill-gray-900/80"/>
          
          <!-- Background Document -->
          <rect x="85" y="30" width="70" height="90" rx="4" class="fill-white dark:fill-gray-800 stroke-gray-200 dark:stroke-gray-700" stroke-width="2"/>
          <path d="M97 45H143M97 60H125M97 75H135" class="stroke-gray-200 dark:stroke-gray-700" stroke-width="2" stroke-linecap="round"/>
          
          <!-- The Box (Isometric) -->
          <path d="M40 95L120 60L200 95V145L120 180L40 145V95Z" class="fill-gray-50 dark:fill-gray-800/50 stroke-gray-200 dark:stroke-gray-700" stroke-width="2" stroke-linejoin="round"/>
          <path d="M40 95L120 130L200 95" class="stroke-gray-200 dark:stroke-gray-700" stroke-width="2" stroke-linejoin="round"/>
          <path d="M120 130V180" class="stroke-gray-200 dark:stroke-gray-700" stroke-width="2" stroke-linejoin="round"/>
          
          <!-- Floating Magnifying Glass Element -->
          <g class="transition-transform duration-700 hover:scale-105">
            <!-- Shadow for glass -->
            <circle cx="160" cy="120" r="22" class="fill-gray-900/5 dark:fill-black/20"/>
            <circle cx="160" cy="115" r="20" class="fill-white dark:fill-gray-900 stroke-[#F7941D]" stroke-width="2.5"/>
            <!-- Glass glare -->
            <path d="M148 107C150 102 155 100 160 100" class="stroke-[#F7941D]/30" stroke-width="2" stroke-linecap="round"/>
            <path d="M174 129L192 147" class="stroke-[#F7941D]" stroke-width="4" stroke-linecap="round"/>
          </g>
          
          <!-- Floating decorative particles -->
          <circle cx="35" cy="65" r="4" class="fill-gray-300 dark:fill-gray-600 animate-pulse"/>
          <path d="M210 50L215 55L210 60L205 55L210 50Z" class="fill-gray-300 dark:fill-gray-600 animate-pulse" style="animation-delay: 1s;"/>
          <circle cx="50" cy="160" r="2.5" class="fill-gray-300 dark:fill-gray-700"/>
          <circle cx="190" cy="165" r="3" class="fill-gray-200 dark:fill-gray-700"/>
        </svg>
      </div>
      
      <h3 class="mt-6 text-base font-semibold tracking-tight text-gray-900 dark:text-white">
        {{ title }}
      </h3>
      
      <p *ngIf="description" class="mt-2 max-w-sm text-sm text-gray-500 dark:text-gray-400">
        {{ description }}
      </p>
      
      <!-- Optional slot for actions (like a button) -->
      <div class="mt-6 flex items-center justify-center gap-3">
        <ng-content></ng-content>
      </div>
    </div>
  `
})
export class NoDataComponent {
  @Input() title: string = 'No data found';
  @Input() description?: string | null = null;
}
