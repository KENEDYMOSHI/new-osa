import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { Router } from '@angular/router';
import { AuthService } from '../../core/services/auth.service';

@Component({
  selector: 'app-admin-login',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './admin-login.component.html',
})
export class AdminLoginComponent {
  username = '';
  password = '';
  error = '';
  loading = false;

  constructor(private authService: AuthService, private router: Router) {}

  onLogin() {
    this.error = '';
    this.loading = true;

    // Map 'admin' to email for backend compatibility
    const email = this.username === 'admin' ? 'admin@example.com' : this.username;

    this.authService.login({ email, password: this.password }).subscribe({
      next: (res) => {
        this.loading = false;
        // Redirect to admin dashboard
        this.router.navigate(['/admin-test']);
      },
      error: (err) => {
        this.loading = false;
        this.error = 'Invalid credentials';
        console.error(err);
      }
    });
  }
}
