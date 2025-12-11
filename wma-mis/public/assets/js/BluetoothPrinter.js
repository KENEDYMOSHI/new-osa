class BluetoothPrinter {
  constructor() {
    this.device = null;
    this.server = null;
    this.writeCharacteristic = null;
  }

  async connect() {
    try {
      if (!this.device) {
        this.device = await navigator.bluetooth.requestDevice({
          filters: [{ name: '4B-2044PA-B167' }],
          optionalServices: ['000018f0-0000-1000-8000-00805f9b34fb'],
        });
      }

      this.server = await this.device.gatt.connect();
      console.log('Connected to printer:', this.device.name);

      const service = await this.server.getPrimaryService('000018f0-0000-1000-8000-00805f9b34fb');
      this.writeCharacteristic = await service.getCharacteristic('00002af1-0000-1000-8000-00805f9b34fb');
    } catch (error) {
      console.error('Connection error:', error);
      throw error;
    }
  }

  async disconnect() {
    if (this.server) {
      await this.server.disconnect();
      this.server = null;
      this.writeCharacteristic = null;
      console.log('Disconnected from printer');
    }
  }

  async print(printData) {
    try {
      if (!this.writeCharacteristic) {
        await this.connect();
      }

      await this.writeCharacteristic.writeValue(new Uint8Array([0x1B, 0x40])); // Reset

      const { stickerId, instrument, verificationDate, nextVerification, stickerNumber, certificateNumber, baseUrl } = printData;

      const textString = `\z  Instrument: ${instrument}\z  Verification Date: ${verificationDate}\z  Next Verification: ${nextVerification}\z  Sticker Number: ${stickerNumber}`;

      const lowByte = 58;
      const highByte = 0;
      await this.writeCharacteristic.writeValue(new Uint8Array([0x1D, 0x4C, lowByte, highByte]));
      await this.writeCharacteristic.writeValue(new Uint8Array([0x1B, 0x33, 32]));
      await this.writeCharacteristic.writeValue(new Uint8Array([0x1B, 0x4A, 16]));
      await this.writeCharacteristic.writeValue(new Uint8Array([0x1B, 0x45, 1]));

      const encoder = new TextEncoder();
      const lines = textString.split('\z');
      for (const line of lines) {
        const data = encoder.encode(line + '\n');
        await this.writeCharacteristic.writeValue(data);
        await this.writeCharacteristic.writeValue(new Uint8Array([0x1B, 0x64, 1]));
      }

      await this.writeCharacteristic.writeValue(new Uint8Array([0x1B, 0x33, 16]));

      const qrText = `${baseUrl}verification/verifySticker/${stickerId}`;
      await this.printQRCode(this.writeCharacteristic, qrText, certificateNumber);

      // Add a delay to ensure the printer processes the job
      await new Promise(resolve => setTimeout(resolve, 1000));

      console.log('Print successful for sticker:', stickerNumber);
    } catch (error) {
      console.error('Print error:', error);
      throw error;
    }
  }

  async printQRCode(characteristic, qrData, certificateNumber) {
    const qrBytes = new TextEncoder().encode(qrData);
    const qrDataLength = qrBytes.length + 3;

    const storeQRCode = new Uint8Array([
      0x1D, 0x28, 0x6B, qrDataLength & 0xFF, (qrDataLength >> 8) & 0xFF, 0x31, 0x50, 0x30, ...qrBytes
    ]);

    const setQRSize = new Uint8Array([0x1D, 0x28, 0x6B, 3, 0, 0x31, 0x43, 4]);
    const printQRCode = new Uint8Array([0x1D, 0x28, 0x6B, 3, 0, 0x31, 0x51, 0x30]);

    await characteristic.writeValue(storeQRCode);
    await characteristic.writeValue(setQRSize);
    await characteristic.writeValue(printQRCode);
    await characteristic.writeValue(new TextEncoder().encode("\t\t\t  " + certificateNumber + "\n\n\n\n"));
    await characteristic.writeValue(new Uint8Array([0x1B, 0x33, 4]));
    await characteristic.writeValue(new Uint8Array([0x1B, 0x64, 1]));

    console.log("QR Code sent to printer");
  }
}