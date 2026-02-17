<?php

namespace App\Libraries;

use Exception;

/**
 * ID Card Generator Library
 * Generates WMA license ID cards with proper dimensions (85.6mm x 54mm)
 */
class IDCardGenerator
{
    // ID Card dimensions at 300 DPI
    private const WIDTH = 1012;  // 85.6mm at 300 DPI
    private const HEIGHT = 638;  // 54mm at 300 DPI
    
    // Colors
    private const COLOR_WHITE = [255, 255, 255];
    private const COLOR_BLACK = [0, 0, 0];
    private const COLOR_ORANGE = [246, 141, 43]; // #f68d2b
    private const COLOR_GREEN = [0, 128, 0];
    private const COLOR_GRAY = [128, 128, 128];
    
    private $licenseData;
    private $frontImage;
    private $backImage;
    
    public function __construct(array $licenseData)
    {
        $this->licenseData = $licenseData;
        
        // Check if GD is available
        if (!extension_loaded('gd')) {
            throw new Exception('GD extension is not loaded');
        }
    }
    
    /**
     * Generate the front side of the ID card
     */
    public function generateFront(): void
    {
        // Create image
        $this->frontImage = imagecreatetruecolor(self::WIDTH, self::HEIGHT);
        
        // Fill with white background
        $white = $this->allocateColor($this->frontImage, self::COLOR_WHITE);
        imagefill($this->frontImage, 0, 0, $white);
        
        // Add watermark pattern
        $this->addWatermark($this->frontImage);
        
        // Add header (coat of arms, titles, WMA logo)
        $this->addHeader($this->frontImage);
        
        // Add orange strip
        $this->addOrangeStrip($this->frontImage, 150);
        
        // Add profile picture
        $this->addProfilePicture($this->frontImage);
        
        // Add license details
        $this->addLicenseDetails($this->frontImage);
        
        // Add footer with dates and QR code
        $this->addFooter($this->frontImage);
    }
    
    /**
     * Generate the back side of the ID card
     */
    public function generateBack(): void
    {
        // Create image
        $this->backImage = imagecreatetruecolor(self::WIDTH, self::HEIGHT);
        
        // Fill with white background
        $white = $this->allocateColor($this->backImage, self::COLOR_WHITE);
        imagefill($this->backImage, 0, 0, $white);
        
        // Add border
        $orange = $this->allocateColor($this->backImage, self::COLOR_ORANGE);
        imagerectangle($this->backImage, 20, 20, self::WIDTH - 20, self::HEIGHT - 20, $orange);
        imagesetthickness($this->backImage, 3);
        imagerectangle($this->backImage, 22, 22, self::WIDTH - 22, self::HEIGHT - 22, $orange);
        
        // Add agency information
        $this->addBackContent($this->backImage);
    }
    
    /**
     * Output front image as PNG
     */
    public function outputFront(): void
    {
        if (!$this->frontImage) {
            $this->generateFront();
        }
        
        header('Content-Type: image/png');
        header('Content-Disposition: attachment; filename="WMA-ID-' . ($this->licenseData['licenseNumber'] ?? 'License') . '-Front.png"');
        imagepng($this->frontImage);
        imagedestroy($this->frontImage);
    }
    
    /**
     * Output back image as PNG
     */
    public function outputBack(): void
    {
        if (!$this->backImage) {
            $this->generateBack();
        }
        
        header('Content-Type: image/png');
        header('Content-Disposition: attachment; filename="WMA-ID-' . ($this->licenseData['licenseNumber'] ?? 'License') . '-Back.png"');
        imagepng($this->backImage);
        imagedestroy($this->backImage);
    }
    
    /**
     * Output both sides combined horizontally
     */
    public function outputBothSides(): void
    {
        if (!$this->frontImage) {
            $this->generateFront();
        }
        if (!$this->backImage) {
            $this->generateBack();
        }
        
        // Create combined image with gap
        $gap = 50;
        $combined = imagecreatetruecolor(self::WIDTH * 2 + $gap, self::HEIGHT);
        $white = $this->allocateColor($combined, self::COLOR_WHITE);
        imagefill($combined, 0, 0, $white);
        
        // Copy front and back
        imagecopy($combined, $this->frontImage, 0, 0, 0, 0, self::WIDTH, self::HEIGHT);
        imagecopy($combined, $this->backImage, self::WIDTH + $gap, 0, 0, 0, self::WIDTH, self::HEIGHT);
        
        header('Content-Type: image/png');
        header('Content-Disposition: attachment; filename="WMA-ID-' . ($this->licenseData['licenseNumber'] ?? 'License') . '-Both-Sides.png"');
        imagepng($combined);
        
        imagedestroy($combined);
        imagedestroy($this->frontImage);
        imagedestroy($this->backImage);
    }
    
    // Helper methods
    
    private function allocateColor($image, array $rgb)
    {
        return imagecolorallocate($image, $rgb[0], $rgb[1], $rgb[2]);
    }
    
    private function addWatermark($image): void
    {
        // Add subtle watermark pattern
        $gray = imagecolorallocatealpha($image, 200, 200, 200, 100);
        
        for ($x = 0; $x < self::WIDTH; $x += 30) {
            for ($y = 0; $y < self::HEIGHT; $y += 30) {
                imagefilledellipse($image, $x, $y, 2, 2, $gray);
            }
        }
    }
    
    private function addHeader($image): void
    {
        $black = $this->allocateColor($image, self::COLOR_BLACK);
        
        // Add text (simplified - you can add logo images here)
        $centerX = self::WIDTH / 2;
        $y = 40;
        
        $this->addCenteredText($image, 'THE UNITED REPUBLIC OF TANZANIA', $centerX, $y, 16, $black);
        $this->addCenteredText($image, 'MINISTRY OF INDUSTRY AND TRADE', $centerX, $y + 25, 14, $black);
        $this->addCenteredText($image, 'WEIGHTS AND MEASURES AGENCY', $centerX, $y + 48, 16, $black);
    }
    
    private function addOrangeStrip($image, $y): void
    {
        $orange = $this->allocateColor($image, self::COLOR_ORANGE);
        imagefilledrectangle($image, 0, $y, self::WIDTH, $y + 15, $orange);
    }
    
    private function addProfilePicture($image): void
    {
        $photoPath = $this->licenseData['profilePicture'] ?? null;
        
        if ($photoPath && file_exists(FCPATH . $photoPath)) {
            $photo = $this->loadImage(FCPATH . $photoPath);
            if ($photo) {
                // Resize and place photo
                $photoWidth = 180;
                $photoHeight = 225;
                $photoX = 80;
                $photoY = 200;
                
                imagecopyresampled($image, $photo, $photoX, $photoY, 0, 0, $photoWidth, $photoHeight, imagesx($photo), imagesy($photo));
                imagedestroy($photo);
                
                // Add border
                $orange = $this->allocateColor($image, self::COLOR_ORANGE);
                imagerectangle($image, $photoX - 2, $photoY - 2, $photoX + $photoWidth + 2, $photoY + $photoHeight + 2, $orange);
                imagesetthickness($image, 3);
                imagerectangle($image, $photoX - 1, $photoY - 1, $photoX + $photoWidth + 1, $photoY + $photoHeight + 1, $orange);
            }
        }
    }
    
    private function addLicenseDetails($image): void
    {
        $black = $this->allocateColor($image, self::COLOR_BLACK);
        $x = 300;
        $y = 220;
        $lineHeight = 35;
        
        $this->addText($image, 'ID No: ' . ($this->licenseData['licenseNumber'] ?? 'N/A'), $x, $y, 14, $black);
        $this->addText($image, 'Name: ' . strtoupper($this->licenseData['applicantName'] ?? 'N/A'), $x, $y + $lineHeight, 14, $black);
        $this->addText($image, 'Company: ' . strtoupper($this->licenseData['companyName'] ?? 'N/A'), $x, $y + $lineHeight * 2, 12, $black);
        $this->addText($image, 'License: ' . strtoupper($this->licenseData['licenseType'] ?? 'N/A'), $x, $y + $lineHeight * 3, 14, $black);
        $this->addText($image, 'Position: ' . strtoupper($this->licenseData['position'] ?? 'N/A'), $x, $y + $lineHeight * 4, 14, $black);
    }
    
    private function addFooter($image): void
    {
        $y = self::HEIGHT - 80;
        
        // Orange strip for footer
        $this->addOrangeStrip($image, $y);
        
        $black = $this->allocateColor($image, self::COLOR_BLACK);
        
        // Dates
        $this->addText($image, 'Issued: ' . ($this->licenseData['issueDate'] ?? 'N/A'), 50, $y + 35, 12, $black);
        $this->addText($image, 'Expires: ' . ($this->licenseData['expiryDate'] ?? 'N/A'), 250, $y + 35, 12, $black);
        
        // Add QR code placeholder (you can integrate a QR code library here)
        $this->addQRCode($image, $this->licenseData['licenseNumber'] ?? '', self::WIDTH - 150, $y - 20);
    }
    
    private function addBackContent($image): void
    {
        $black = $this->allocateColor($image, self::COLOR_BLACK);
        $orange = $this->allocateColor($image, self::COLOR_ORANGE);
        $gray = $this->allocateColor($image, self::COLOR_GRAY);
        
        $centerX = self::WIDTH / 2;
        
        $this->addCenteredText($image, 'This Identity Card is the property of', $centerX, 120, 14, $gray);
        $this->addCenteredText($image, 'WEIGHTS & MEASURES AGENCY', $centerX, 160, 18, $orange);
        
        $this->addCenteredText($image, 'Vipimo House, Chief Chemist Street', $centerX, 220, 12, $black);
        $this->addCenteredText($image, 'P.O. Box 2014, Dodoma - Tanzania', $centerX, 245, 12, $black);
        
        $this->addCenteredText($image, 'Tel: +255 22 220 3199 | Fax: +255 22 220 3200', $centerX, 290, 11, $black);
        $this->addCenteredText($image, 'Email: info@wma.go.tz | Website: www.wma.go.tz', $centerX, 315, 11, $black);
        
        // Commissioner signature section
        $this->addCenteredText($image, $this->licenseData['commissionerName'] ?? 'Alban M. Kihulla', $centerX, 420, 14, $black);
        $this->addCenteredText($image, 'COMMISSIONER FOR WEIGHTS AND MEASURES', $centerX, 450, 11, $black);
        
        $this->addCenteredText($image, 'If found please return to the above address', $centerX, 520, 10, $gray);
    }
    
    private function addQRCode($image, $data, $x, $y): void
    {
        // Placeholder for QR code - you can integrate a QR library or use external API
        $qrSize = 130;
        $white = $this->allocateColor($image, self::COLOR_WHITE);
        $black = $this->allocateColor($image, self::COLOR_BLACK);
        
        imagefilledrectangle($image, $x, $y, $x + $qrSize, $y + $qrSize, $white);
        imagerectangle($image, $x, $y, $x + $qrSize, $y + $qrSize, $black);
        
        // Add text placeholder
        $this->addCenteredText($image, 'QR', $x + $qrSize / 2, $y + $qrSize / 2, 20, $black);
    }
    
    private function addText($image, $text, $x, $y, $size, $color): void
    {
        // Use built-in font (you can use TTF fonts for better quality)
        imagestring($image, 5, $x, $y, $text, $color);
    }
    
    private function addCenteredText($image, $text, $centerX, $y, $size, $color): void
    {
        // Simple centering (for TTF fonts, use imagettfbbox)
        $textWidth = strlen($text) * 9; // Approximate
        $x = $centerX - ($textWidth / 2);
        $this->addText($image, $text, $x, $y, $size, $color);
    }
    
    private function loadImage($path)
    {
        $info = getimagesize($path);
        
        if (!$info) {
            return null;
        }
        
        switch ($info[2]) {
            case IMAGETYPE_JPEG:
                return imagecreatefromjpeg($path);
            case IMAGETYPE_PNG:
                return imagecreatefrompng($path);
            case IMAGETYPE_GIF:
                return imagecreatefromgif($path);
            default:
                return null;
        }
    }
}
