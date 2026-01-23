import { Component, Input } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule, ActivatedRoute } from '@angular/router';

@Component({
  selector: 'app-coming-soon',
  standalone: true,
  imports: [CommonModule, RouterModule],
  templateUrl: './coming-soon.component.html',
})
export class ComingSoonComponent {
  
  pageTitle: string = 'Coming Soon';
  
  constructor(private route: ActivatedRoute) {
    // Determine context based on route if needed, or stick to generic
    // We can use route data to customize the title
    this.route.data.subscribe(data => {
        if (data && data['title']) {
            // Strip the suffix " | WMA..." if present for the header
            this.pageTitle = data['title'].split('|')[0].trim();
        }
    });
  }
}
