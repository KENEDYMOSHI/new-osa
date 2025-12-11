
  class BluetoothPrinter {
    constructor(stickerData) {

      this.instrument = stickerData.instrument;
      this.verificationDate = stickerData.verificationDate;
      this.reverificationBefore = stickerData.reverificationBefore;
      this.certificateNumber = stickerData.certificateNumber;
      this.stickerNumber = stickerData.stickerNumber;

      // UUIDs
      this.serviceUUID = '000018f0-0000-1000-8000-00805f9b34fb';
      this.characteristicUUID = '00002af1-0000-1000-8000-00805f9b34fb';
      this.deviceName = '4B-2044PA-B167';
    }

    async print() {
      try {
        const device = await navigator.bluetooth.requestDevice({
          filters: [{ name: this.deviceName }],
          optionalServices: [this.serviceUUID],
        });

        const server = await device.gatt.connect();
        const service = await server.getPrimaryService(this.serviceUUID);
        const characteristic = await service.getCharacteristic(this.characteristicUUID);

        await characteristic.writeValue(new Uint8Array([0x1B, 0x40])); // Reset printer
        await characteristic.writeValue(new Uint8Array([0x1D, 0x4C, 54, 0])); // Left margin
        await characteristic.writeValue(new Uint8Array([0x1B, 0x33, 4])); // Line height

        const encoder = new TextEncoder();
        const textLines = [
          this.instrument,
          this.verificationDate,
          this.reverificationBefore,
          this.certificateNumber,
        ];

        for (const line of textLines) {
          await characteristic.writeValue(encoder.encode(line + '\n'));
          await characteristic.writeValue(new Uint8Array([0x1B, 0x64, 1])); // Feed line
        }

        await characteristic.writeValue(new Uint8Array([0x1B, 0x33, 16])); // Reset line height

        const qrLink = `https://training.wma.go.tz/verifySticker/${this.stickerNumber}`;
        await this.printQRCode(characteristic, qrLink);

        console.log("Printing completed");
      } catch (error) {
        console.error("Printing failed: ", error);
      }
    }

    async printQRCode(characteristic, data) {
      const qrBytes = new TextEncoder().encode(data);
      const qrDataLength = qrBytes.length + 3;

      const storeQRCode = new Uint8Array([
        0x1D, 0x28, 0x6B,
        qrDataLength & 0xFF,
        (qrDataLength >> 8) & 0xFF,
        0x31, 0x50, 0x30,
        ...qrBytes
      ]);

      const setQRSize = new Uint8Array([0x1D, 0x28, 0x6B, 3, 0, 0x31, 0x43, 4]);
      const printQR = new Uint8Array([0x1D, 0x28, 0x6B, 3, 0, 0x31, 0x51, 0x30]);

      await characteristic.writeValue(storeQRCode);
      await characteristic.writeValue(setQRSize);
      await characteristic.writeValue(printQR);
      await characteristic.writeValue(new Uint8Array([0x1B, 0x64, 1]));

      console.log("QR Code printed");
    }
  }

  // Attach event to button
//   document.getElementById('connect-btn').addEventListener('click', () => {
//     const printer = new BluetoothPrinter({
//       instrument: 'SBL',
//       verificationDate: '12/12/2024',
//       reverificationBefore: '12/12/2025',
//       certificateNumber: 'CT-3426-U667'
//     });

//     printer.print();
//   });
