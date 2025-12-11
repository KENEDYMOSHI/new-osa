import { CommonModule } from '@angular/common';
import {
  Component,
  Input,
  Output,
  EventEmitter,
  ElementRef,
  OnInit,
  OnDestroy,
  HostListener
} from '@angular/core';
import { trigger, transition, style, animate, query, animateChild, group } from '@angular/animations';

@Component({
  selector: 'app-modal',
  standalone: true,
  imports: [
    CommonModule,
  ],
  templateUrl: './modal.component.html',
  styles: ``,
  animations: [
    trigger('modalContainer', [
      transition(':enter', [
        group([
          query('@fade', animateChild(), { optional: true }),
          query('@scale', animateChild(), { optional: true })
        ])
      ]),
      transition(':leave', [
        group([
          query('@fade', animateChild(), { optional: true }),
          query('@scale', animateChild(), { optional: true })
        ])
      ])
    ]),
    trigger('fade', [
      transition(':enter', [
        style({ opacity: 0 }),
        animate('200ms ease-out', style({ opacity: 1 }))
      ]),
      transition(':leave', [
        animate('150ms ease-in', style({ opacity: 0 }))
      ])
    ]),
    trigger('scale', [
      transition(':enter', [
        style({ transform: 'scale(0.98)', opacity: 0 }),
        animate('200ms cubic-bezier(0.16, 1, 0.3, 1)', style({ transform: 'scale(1)', opacity: 1 }))
      ]),
      transition(':leave', [
        animate('150ms ease-in', style({ transform: 'scale(0.98)', opacity: 0 }))
      ])
    ])
  ]
})
export class ModalComponent {

  @Input() isOpen = false;
  @Output() close = new EventEmitter<void>();
  @Input() className = '';
  @Input() showCloseButton = true;
  @Input() isFullscreen = false;

  constructor(private el: ElementRef) {}

  ngOnInit() {
    if (this.isOpen) {
      document.body.style.overflow = 'hidden';
    }
  }

  ngOnDestroy() {
    document.body.style.overflow = 'unset';
  }

  ngOnChanges() {
    document.body.style.overflow = this.isOpen ? 'hidden' : 'unset';
  }

  onBackdropClick(event: MouseEvent) {
    if (!this.isFullscreen) {
      this.close.emit();
    }
  }

  onContentClick(event: MouseEvent) {
    event.stopPropagation();
  }

  @HostListener('document:keydown.escape', ['$event'])
  onEscape(event: KeyboardEvent) {
    if (this.isOpen) {
      this.close.emit();
    }
  }
}
