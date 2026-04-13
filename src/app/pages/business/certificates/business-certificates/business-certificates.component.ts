import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { TranslateModule } from '@ngx-translate/core';

type CertificateTab = 'certificates' | 'stickers';

interface CertificateItem {
  id: string;
  instrument: string;
  issueDate: string;
  expiryDate: string;
  status: string;
  type: string;
}

interface StickerItem {
  instrument: string;
  verificationDate: string;
  nextVerification: string;
  stickerNumber: string;
}

@Component({
  selector: 'app-business-certificates',
  standalone: true,
  imports: [CommonModule, RouterModule, TranslateModule],
  templateUrl: './business-certificates.component.html',
})
export class BusinessCertificatesComponent {
  activeTab: CertificateTab = 'certificates';

  certificates: CertificateItem[] = [
    {
      id: 'CERT-2026-001',
      instrument: 'Weighbridge (60,000kg)',
      issueDate: '2026-01-15',
      expiryDate: '2027-01-14',
      status: 'Active',
      type: 'Verification Certificate'
    },
    {
      id: 'CERT-2026-002',
      instrument: 'Platform Scale (500kg)',
      issueDate: '2026-02-10',
      expiryDate: '2027-02-09',
      status: 'Active',
      type: 'Verification Certificate'
    },
    {
      id: 'CERT-2025-089',
      instrument: 'Fuel Dispenser (Double Nozzle)',
      issueDate: '2025-06-20',
      expiryDate: '2026-06-19',
      status: 'Expiring Soon',
      type: 'Verification Certificate'
    }
  ];

  stickers: StickerItem[] = [
    {
      instrument: 'PS : 250kg',
      verificationDate: '31-03-2026',
      nextVerification: '31-03-2027',
      stickerNumber: 'PS:ILA-1096-2026'
    },
    {
      instrument: 'Weighbridge : 60t',
      verificationDate: '15-01-2026',
      nextVerification: '15-01-2027',
      stickerNumber: 'WB:ILA-2045-2026'
    },
    {
      instrument: 'Fuel Dispenser : Petrol',
      verificationDate: '20-06-2025',
      nextVerification: '20-06-2026',
      stickerNumber: 'FD:ILA-8832-2025'
    },
    {
      instrument: 'Counter Scale : 15kg',
      verificationDate: '05-04-2026',
      nextVerification: '05-04-2027',
      stickerNumber: 'CS:ILA-1120-2026'
    },
    {
      instrument: 'Platform Scale : 1000kg',
      verificationDate: '10-02-2026',
      nextVerification: '10-02-2027',
      stickerNumber: 'PS:ILA-0542-2026'
    }
  ];

  setTab(tab: CertificateTab) {
    this.activeTab = tab;
  }

  previewSticker(sticker: StickerItem) {
    this.openStickerWindow(sticker, false);
  }

  printSticker(sticker: StickerItem) {
    this.openStickerWindow(sticker, true);
  }

  private openStickerWindow(sticker: StickerItem, shouldPrint: boolean) {
    const printWindow = window.open('', '_blank', 'width=900,height=700');

    if (!printWindow) {
      return;
    }

    printWindow.document.open();
    printWindow.document.write(this.buildStickerDocument(sticker, shouldPrint));
    printWindow.document.close();
  }

  private buildStickerDocument(sticker: StickerItem, shouldPrint: boolean): string {
    const escape = (value: string) => this.escapeHtml(value);

    return `
      <!doctype html>
      <html lang="en">
        <head>
          <meta charset="utf-8">
          <meta name="viewport" content="width=device-width, initial-scale=1">
          <title>${escape(sticker.stickerNumber)} Sticker</title>
          <style>
            :root {
              color-scheme: light;
            }

            * {
              box-sizing: border-box;
            }

            body {
              margin: 0;
              padding: 32px;
              font-family: Arial, sans-serif;
              background: #f3f4f6;
              color: #111827;
            }

            .actions {
              display: flex;
              justify-content: flex-end;
              gap: 12px;
              margin-bottom: 20px;
            }

            .button {
              border: 0;
              border-radius: 10px;
              padding: 10px 18px;
              font-size: 14px;
              font-weight: 700;
              cursor: pointer;
            }

            .button-secondary {
              background: #ffffff;
              color: #111827;
              box-shadow: 0 6px 18px rgba(15, 23, 42, 0.08);
            }

            .button-primary {
              background: #f7941d;
              color: #ffffff;
            }

            .page {
              display: flex;
              justify-content: center;
            }

            .sticker {
              width: min(100%, 760px);
              border: 1px solid #d1d5db;
              border-radius: 24px;
              padding: 28px;
              background: #efe8d8;
              box-shadow: 0 14px 40px rgba(15, 23, 42, 0.12);
            }

            .header {
              display: flex;
              align-items: center;
              gap: 16px;
              border-bottom: 2px solid #1f2937;
              padding-bottom: 18px;
            }

            .emblem {
              width: 64px;
              height: 64px;
              border-radius: 999px;
              border: 2px solid #1f2937;
              display: flex;
              align-items: center;
              justify-content: center;
              font-size: 11px;
              font-weight: 700;
              text-align: center;
              padding: 8px;
              background: rgba(255, 255, 255, 0.7);
            }

            .titles {
              flex: 1;
              text-align: center;
            }

            .titles h1,
            .titles h2 {
              margin: 0;
              font-size: 18px;
              line-height: 1.35;
              text-transform: uppercase;
            }

            .content {
              display: flex;
              justify-content: space-between;
              gap: 24px;
              margin-top: 24px;
            }

            .details {
              flex: 1;
            }

            .detail {
              margin-bottom: 14px;
              font-size: 16px;
              line-height: 1.5;
            }

            .label {
              display: inline-block;
              min-width: 170px;
              font-weight: 700;
            }

            .watermark {
              width: 130px;
              min-height: 130px;
              border: 2px dashed #6b7280;
              border-radius: 999px;
              display: flex;
              align-items: center;
              justify-content: center;
              font-weight: 700;
              text-align: center;
              color: #374151;
              background: rgba(255, 255, 255, 0.55);
            }

            .footer {
              display: flex;
              justify-content: space-between;
              align-items: end;
              gap: 24px;
              margin-top: 28px;
              padding-top: 18px;
              border-top: 2px solid #1f2937;
            }

            .qr {
              width: 78px;
              height: 78px;
              border: 1px solid #9ca3af;
              background:
                linear-gradient(90deg, #111827 12px, transparent 12px) 0 0 / 24px 24px,
                linear-gradient(#111827 12px, transparent 12px) 0 0 / 24px 24px,
                #ffffff;
            }

            .meta {
              font-size: 14px;
              text-align: right;
            }

            @media print {
              body {
                padding: 0;
                background: #ffffff;
              }

              .actions {
                display: none;
              }

              .sticker {
                width: 100%;
                border: 0;
                box-shadow: none;
                border-radius: 0;
              }
            }
          </style>
        </head>
        <body>
          <div class="actions">
            <button class="button button-secondary" onclick="window.close()">Close</button>
            <button class="button button-primary" onclick="window.print()">Print</button>
          </div>

          <div class="page">
            <article class="sticker">
              <header class="header">
                <div class="emblem">WMA</div>
                <div class="titles">
                  <h1>The United Republic of Tanzania</h1>
                  <h2>Weights and Measures Agency</h2>
                </div>
              </header>

              <section class="content">
                <div class="details">
                  <div class="detail"><span class="label">Instrument:</span> ${escape(sticker.instrument)}</div>
                  <div class="detail"><span class="label">Verification Date:</span> ${escape(sticker.verificationDate)}</div>
                  <div class="detail"><span class="label">Next Verification:</span> ${escape(sticker.nextVerification)}</div>
                  <div class="detail"><span class="label">Sticker Number:</span> <strong>${escape(sticker.stickerNumber)}</strong></div>
                </div>

                <div class="watermark">Official Sticker</div>
              </section>

              <footer class="footer">
                <div class="qr" aria-label="QR placeholder"></div>
                <div class="meta">
                  <div>Issued by WMA</div>
                  <div>${escape(sticker.stickerNumber)}</div>
                </div>
              </footer>
            </article>
          </div>

          ${shouldPrint ? '<script>window.addEventListener("load", function () { window.print(); });</script>' : ''}
        </body>
      </html>
    `;
  }

  private escapeHtml(value: string): string {
    return value
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#39;');
  }
}
