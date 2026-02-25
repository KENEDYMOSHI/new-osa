import { Component, Input } from '@angular/core';
import { CommonModule } from '@angular/common';
import html2canvas from 'html2canvas';
import Swal from 'sweetalert2';

interface LicenseCardData {
  licenseNumber: string;
  applicantName: string;
  licenseType: string;
  issueDate: string;
  expiryDate: string;
  profilePicture?: string;
  userInitial?: string;
  companyName?: string;
  address?: string;
  phoneNumber?: string;
  region?: string;
  instruments?: any[];
  position?: string; // e.g., "CALIBRATION INSPECTOR"
  commissionerName?: string;
  commissionerSignature?: string;
}

@Component({
  selector: 'app-license-id-card',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './license-id-card.component.html',
  styleUrls: ['./license-id-card.component.css']
})
export class LicenseIdCardComponent {
  @Input() licenseData?: LicenseCardData;

  get qrCodeData(): string {
    return `https://wma.go.tz/verify/${this.licenseData?.licenseNumber}`;
  }

  get profilePictureUrl(): string | undefined {
    if (!this.licenseData?.profilePicture) return undefined;
    
    // Check if it's a backend upload path (e.g., 'uploads/pictures/filename.jpg')
    if (this.licenseData.profilePicture.includes('uploads/pictures/') || this.licenseData.profilePicture.includes('api/license/view-profile-picture')) {
       // Extract filename if it's a raw path
       if (this.licenseData.profilePicture.includes('uploads/pictures/')) {
          const filename = this.licenseData.profilePicture.split('/').pop();
          return `http://localhost:8080/api/license/view-profile-picture/${filename}`;
       }
       return this.licenseData.profilePicture;
    }
    
    return this.licenseData.profilePicture;
  }

  async downloadID() {
    try {
      const frontElement = document.getElementById('idCardFront');
      const backElement = document.getElementById('idCardBack');
      
      if (!frontElement || !backElement) {
        console.error('ID card elements not found');
        Swal.fire({
          icon: 'error',
          title: 'Imeshindikana',
          text: 'Kuna tatizo wakati wa kupakua kitambulisho. Tafadhali jaribu tena.',
          confirmButtonColor: '#4f46e5'
        });
        return;
      }

      console.log('Capturing ID cards...');
      
      // Wait for all images to load with CORS
      const images = document.querySelectorAll('#idCardFront img, #idCardBack img');
      const imageLoadPromises = Array.from(images).map((img: any) => {
        if (img.complete) {
          return Promise.resolve<void>(undefined);
        }
        return new Promise<void>((resolve) => {
          img.onload = () => resolve();
          img.onerror = () => {
            console.warn('Image failed to load:', img.src);
            resolve(); // Continue anyway
          };
        });
      });
      
      await Promise.all(imageLoadPromises);
      console.log('All images loaded');
      
      // ID Card standard size: 85.6mm x 54mm
      // At 300 DPI: 1012px x 638px
      // We'll use scale 6 for even higher quality (2024px x 1276px at 600 DPI)
      const scale = 6;
      
      // Capture front card
      console.log('Capturing front...');
      const frontCanvas = await html2canvas(frontElement, { 
        scale: scale,
        useCORS: true,
        allowTaint: false,
        backgroundColor: '#ffffff',
        logging: false,
        windowWidth: frontElement.scrollWidth,
        windowHeight: frontElement.scrollHeight,
        onclone: (clonedDoc) => {
          const clonedElement = clonedDoc.getElementById('idCardFront');
          if (clonedElement) {
            clonedElement.style.backgroundColor = '#ffffff';
            // Ensure all colors are rendered properly
            clonedElement.style.colorScheme = 'light';
          }
        }
      });
      
      // Capture back card
      console.log('Capturing back...');
      const backCanvas = await html2canvas(backElement, { 
        scale: scale,
        useCORS: true,
        allowTaint: false,
        backgroundColor: '#ffffff',
        logging: false,
        windowWidth: backElement.scrollWidth,
        windowHeight: backElement.scrollHeight,
        onclone: (clonedDoc) => {
          const clonedElement = clonedDoc.getElementById('idCardBack');
          if (clonedElement) {
            clonedElement.style.backgroundColor = '#ffffff';
            clonedElement.style.colorScheme = 'light';
          }
        }
      });

      // Download front card
      console.log('Downloading front...');
      const frontLink = document.createElement('a');
      frontLink.download = `WMA-ID-${this.licenseData?.licenseNumber || 'License'}-Front.png`;
      frontLink.href = frontCanvas.toDataURL('image/png', 1.0); // Maximum quality
      frontLink.click();
      
      // Small delay before downloading back
      await new Promise(resolve => setTimeout(resolve, 800));

      // Download back card
      console.log('Downloading back...');
      const backLink = document.createElement('a');
      backLink.download = `WMA-ID-${this.licenseData?.licenseNumber || 'License'}-Back.png`;
      backLink.href = backCanvas.toDataURL('image/png', 1.0); // Maximum quality
      backLink.click();
      
      console.log('✅ ID cards downloaded successfully!');
      
      // Show success message
      setTimeout(() => {
        Swal.fire({
          icon: 'success',
          title: 'Imefanikiwa!',
          text: 'Kitambulisho kimepakuliwa kikamilifu. Angalia downloads folder yako.',
          confirmButtonColor: '#4f46e5',
          confirmButtonText: 'Sawa'
        });
      }, 1000);

    } catch (error) {
      console.error('❌ Error downloading ID card:', error);
      Swal.fire({
        icon: 'error',
        title: 'Kosa Limetokea',
        text: 'Kuna tatizo wakati wa kupakua kitambulisho. Tafadhali jaribu tena.',
        confirmButtonColor: '#4f46e5',
        confirmButtonText: 'Sawa'
      });
    }
  }

  printCard() {
    window.print();
  }
}
