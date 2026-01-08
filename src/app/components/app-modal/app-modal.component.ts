import { Component, Input, Output, EventEmitter, OnInit, OnDestroy, ElementRef, ViewChild } from '@angular/core';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-modal',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './app-modal.component.html',
  styleUrls: ['./app-modal.component.css']
})
export class AppModalComponent implements OnInit, OnDestroy {
  @Input() size: 'sm' | 'md' | 'lg' | 'xl' = 'md';
  @Input() title: string = '';
  @Input() staticBackdrop: boolean = false;
  @Input() showCloseButton: boolean = true;
  
  @Output() close = new EventEmitter<void>();

  @ViewChild('modalBackdrop') backdropRef!: ElementRef;
  @ViewChild('modalContent') contentRef!: ElementRef;

  isActive: boolean = false;
  isShaking: boolean = false;

  constructor() {}

  ngOnInit(): void {
    // Trigger entry animation after a slight delay to allow rendering
    setTimeout(() => {
      this.isActive = true;
    }, 10);
    
    // ESC key listener
    document.addEventListener('keydown', this.handleEscKey);
  }

  ngOnDestroy(): void {
    document.removeEventListener('keydown', this.handleEscKey);
  }

  handleEscKey = (event: KeyboardEvent) => {
    if (event.key === 'Escape') {
      this.closeModal();
    }
  }

  closeModal() {
    this.isActive = false;
    // Wait for animation to finish before emitting close
    setTimeout(() => {
      this.close.emit();
    }, 300); // Matches transition duration
  }

  onBackdropClick(event: MouseEvent) {
    if (event.target === this.backdropRef.nativeElement) {
      if (this.staticBackdrop) {
        this.triggerShake();
      } else {
        this.closeModal();
      }
    }
  }

  triggerShake() {
    this.isShaking = false;
    // Force reflow
    setTimeout(() => {
      this.isShaking = true;
    }, 10);
  }
}
