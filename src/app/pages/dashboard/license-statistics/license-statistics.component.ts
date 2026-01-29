import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { StatisticsService, LicenseStatistic, RegionStatistic } from '../../../services/statistics.service';

@Component({
  selector: 'app-license-statistics',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './license-statistics.component.html',
  styleUrls: ['./license-statistics.component.css']
})
export class LicenseStatisticsComponent implements OnInit {
  licenseStats: LicenseStatistic[] = [];
  regionStats: RegionStatistic[] = [];
  isLoading = true;

  constructor(private statisticsService: StatisticsService) {}

  ngOnInit(): void {
    this.fetchData();
  }

  fetchData(): void {
    this.isLoading = true;
    // Fetch both stats in parallel
    // In a real app we might use forkJoin, but separate subscriptions are fine here
    this.statisticsService.getLicenseStatistics().subscribe({
      next: (data) => {
        this.licenseStats = data;
        this.checkLoading();
      },
      error: (err) => {
        console.error('Error fetching license stats', err);
        this.checkLoading();
      }
    });

    this.statisticsService.getRegionStatistics().subscribe({
      next: (data) => {
        this.regionStats = data; // Top regions usually sorted by backend
        this.checkLoading();
      },
      error: (err) => {
        console.error('Error fetching region stats', err);
        this.checkLoading();
      }
    });
  }

  checkLoading(): void {
    if (this.licenseStats && this.regionStats) {
      this.isLoading = false;
    }
  }
}
