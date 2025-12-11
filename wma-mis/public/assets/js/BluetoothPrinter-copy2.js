class BluetoothPrinter {
    constructor(printData) {
      this.printData = printData;
      this.deviceName = '4B-2044PA-B167';
      this.serviceUUID = '000018f0-0000-1000-8000-00805f9b34fb';
      this.writeCharacteristicUUID = '00002af1-0000-1000-8000-00805f9b34fb';
    }
  
    async print() {
      
      try {
        // Request Bluetooth device
        const device = await navigator.bluetooth.requestDevice({
          filters: [{ name: this.deviceName }],
          optionalServices: [this.serviceUUID]
        });
  
        // Connect to the device
        const server = await device.gatt.connect();
        console.log('Connected to printer:', device.name);
  
        // Access service and characteristic
        const service = await server.getPrimaryService(this.serviceUUID);
        const writeCharacteristic = await service.getCharacteristic(this.writeCharacteristicUUID);
        
        // Reset printer
        await writeCharacteristic.writeValue(new Uint8Array([0x1B, 0x40]));
  
        // Prepare print data
        const instrument = this.printData.instrument;
        const verificationDate = this.printData.verificationDate;
        const reverificationBefore = this.printData.reverificationBefore;
        const certificateNo = this.printData.certificateNo;
        const textString = `\z\t${'Instrument : '+ instrument}\z\t\t${'Verification Date :'+ verificationDate}\z\t\t\t${'Next verification :'+ reverificationBefore}\z\t\t${'Certificate No :'+ certificateNo}`;
  
        // Set print formatting
        const lowByte = 54;
        const highByte = 0;
        const setLeftMargin = new Uint8Array([0x1D, 0x4C, lowByte, highByte]);
        await writeCharacteristic.writeValue(setLeftMargin);
        await writeCharacteristic.writeValue(new Uint8Array([0x1B, 0x33, 4]));
        await writeCharacteristic.writeValue(new Uint8Array([0x1B, 0x64, 1]));
  
        // Print text lines
        const encoder = new TextEncoder();
        const lines = textString.split('\z');
        for (const line of lines) {
          const data = encoder.encode(line + '\n');
          await writeCharacteristic.writeValue(data);
          await writeCharacteristic.writeValue(new Uint8Array([0x1B, 0x64, 1]));
        }
        await writeCharacteristic.writeValue(new Uint8Array([0x1B, 0x33, 16]));
  
        // Print QR code
        const qrText = `https://training.wma.go.tz/verifySticker/${this.stickerNumber}`;
        await this.printQRCode(writeCharacteristic, qrText);
  
        console.log('Print successful!');
        console.log(' text'+ textString);
      } catch (error) {
        console.error('Error: ', error);
      }
    }
  
    async printQRCode(characteristic, qrData) {
      // Convert QR data to Uint8Array
      const qrBytes = new TextEncoder().encode(qrData);
      const qrDataLength = qrBytes.length + 3;
  
      const alignLeft = new Uint8Array([0x1B, 0x61, 0]);
      
      // ESC/POS commands for QR code
      const storeQRCode = new Uint8Array([
        0x1D, 0x28, 0x6B, qrDataLength & 0xFF, (qrDataLength >> 8) & 0xFF, 0x31, 0x50, 0x30, ...qrBytes
      ]);
      const setQRSize = new Uint8Array([0x1D, 0x28, 0x6B, 3, 0, 0x31, 0x43, 4]);
      const printQRCode = new Uint8Array([0x1D, 0x28, 0x6B, 3, 0, 0x31, 0x51, 0x30]);
  
      // Send QR code commands
      await characteristic.writeValue(storeQRCode);
      await characteristic.writeValue(setQRSize);
      await characteristic.writeValue(printQRCode);
      await characteristic.writeValue(new Uint8Array([0x1B, 0x33, 4]));
      await characteristic.writeValue(new Uint8Array([0x1B, 0x64, 1]));
  
      console.log("QR Code sent to printer");
    }
  }