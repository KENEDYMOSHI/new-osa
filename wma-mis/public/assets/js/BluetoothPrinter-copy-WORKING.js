
  class BluetoothPrinter {
    constructor(printData) {
      this.printData = printData;
    }

    async print() {
      try {
        const device = await navigator.bluetooth.requestDevice({
          filters: [{ name: '4B-2044PA-B167' }],
          optionalServices: ['000018f0-0000-1000-8000-00805f9b34fb'],
        });

        const server = await device.gatt.connect();
        console.log('Connected to printer:', device.name);

        const service = await server.getPrimaryService('000018f0-0000-1000-8000-00805f9b34fb');
        const writeCharacteristic = await service.getCharacteristic('00002af1-0000-1000-8000-00805f9b34fb');

        await writeCharacteristic.writeValue(new Uint8Array([0x1B, 0x40])); // Reset

        const {stickerId, instrument, verificationDate, nextVerification, stickerNumber, certificateNumber,baseUrl } = this.printData;

        const textString = `\z  Instrument: ${instrument}\z  Verification Date: ${verificationDate}\z  Next Verification: ${nextVerification}\z  Sticker Number: ${stickerNumber}`;

        const lowByte = 58;
        const highByte = 0;
        await writeCharacteristic.writeValue(new Uint8Array([0x1D, 0x4C, lowByte, highByte]));
        await writeCharacteristic.writeValue(new Uint8Array([0x1B, 0x33, 32]));
        await writeCharacteristic.writeValue(new Uint8Array([0x1B, 0x4A, 16]));
        await writeCharacteristic.writeValue(new Uint8Array([0x1B, 0x45, 1]));

        const encoder = new TextEncoder();
        const lines = textString.split('\z');
        for (const line of lines) {
          const data = encoder.encode(line + '\n');
          await writeCharacteristic.writeValue(data);
          await writeCharacteristic.writeValue(new Uint8Array([0x1B, 0x64, 1]));
        }

        await writeCharacteristic.writeValue(new Uint8Array([0x1B, 0x33, 16]));

        const qrText = `${baseUrl}verification/verifySticker/${stickerId}`;
        await this.printQRCode(writeCharacteristic, qrText, certificateNumber);

        console.log('Print successful!');
      } catch (error) {
        console.error('Error: ', error);
      }
    }

    async printQRCode(characteristic, qrData, certificateNumber) {
      const qrBytes = new TextEncoder().encode(qrData);
      const qrDataLength = qrBytes.length + 3;

      const cert = '';

      const storeQRCode = new Uint8Array([
        0x1D, 0x28, 0x6B, qrDataLength & 0xFF, (qrDataLength >> 8) & 0xFF, 0x31, 0x50, 0x30, ...qrBytes
      ]);

      const setQRSize = new Uint8Array([0x1D, 0x28, 0x6B, 3, 0, 0x31, 0x43, 4]);
      const printQRCode = new Uint8Array([0x1D, 0x28, 0x6B, 3, 0, 0x31, 0x51, 0x30]);

      await characteristic.writeValue(storeQRCode);
      await characteristic.writeValue(setQRSize);
      await characteristic.writeValue(printQRCode);
      await characteristic.writeValue(new TextEncoder().encode("\t\t\t  " + cert + "\n\n\n\n"));
      await characteristic.writeValue(new Uint8Array([0x1B, 0x33, 4]));
      await characteristic.writeValue(new Uint8Array([0x1B, 0x64, 1]));

      console.log("QR Code sent to printer");
    }
  }

  // Example usage:
  // const printData = {
  //   instrument: 'VTV',
  //   verificationDate: '12/12/2024',
  //   nextVerification: '12/12/2025',
  //   stickerNumber: 'SN-9009-90660',
  //   certificateNumber: 'CT-3426-U667'
  // };

  // document.getElementById('connect-btn').addEventListener('click', () => {
  //   const printer = new BluetoothPrinter(printData);
  //   printer.print();
  // });
