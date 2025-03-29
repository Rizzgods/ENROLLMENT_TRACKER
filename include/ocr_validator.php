<?php
require_once __DIR__ . '/../vendor/autoload.php';
use thiagoalessio\TesseractOCR\TesseractOCR;

/**
 * OCR Validator - Provides OCR text extraction and document validation
 * Based on functionality from test_ocr.php
 */
class OCRValidator {
    private $tesseractPath;
    private $tempDir;
    
    /**
     * Constructor - Initialize OCR validator
     */
    public function __construct() {
        // Define path to local Tesseract executable
        $this->tesseractPath = 'C:/wamp64/www/onlineenrolmentsystem/bin/tesseract/tesseract.exe';
        $this->tempDir = __DIR__ . '/../uploads/temp/';
        
        // Create temp directory if it doesn't exist
        if (!file_exists($this->tempDir)) {
            mkdir($this->tempDir, 0755, true);
        }
    }
    
    /**
     * Check if OCR is available
     */
    public function isOCRAvailable() {
        return file_exists($this->tesseractPath);
    }
    
    /**
     * Extract text from an image file
     */
    public function extractText($filePath) {
        try {
            error_log("OCRValidator: Starting text extraction for file: " . basename($filePath));
            
            // Check if Tesseract is available
            if (!$this->isOCRAvailable()) {
                throw new Exception("Tesseract executable not found at: {$this->tesseractPath}");
            }
            
            // Run OCR on the image
            $tesseract = new TesseractOCR($filePath);
            $tesseract->executable($this->tesseractPath);
            
            // Add configuration for better results
            $tesseract->lang('eng')
                     ->dpi(300)
                     ->psm(6)  // Page segmentation mode: 6 = Assume a single uniform block of text
                     ->oem(1); // OCR Engine Mode: 1 = Neural nets LSTM engine only
            
            // Get the text
            $extractedText = $tesseract->run();
            
            if (empty($extractedText)) {
                error_log("OCR returned empty text for file: " . basename($filePath));
                return null;
            }
            
            // Log the entire extracted text
            error_log("==== OCR EXTRACTED TEXT START ====");
            error_log($extractedText);
            error_log("==== OCR EXTRACTED TEXT END ====");
            
            return $extractedText;
        } catch (Exception $e) {
            error_log("OCR Error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Process an uploaded file with OCR
     */
    public function processUploadedFile($file) {
        if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'error' => 'File upload error'];
        }
        
        try {
            // Generate unique filename
            $filename = uniqid() . '_' . basename($file['name']);
            $filePath = $this->tempDir . $filename;
            
            // Move uploaded file to temp directory
            if (!move_uploaded_file($file['tmp_name'], $filePath)) {
                return ['success' => false, 'error' => 'Failed to save uploaded file'];
            }
            
            // Extract text using OCR
            $extractedText = $this->extractText($filePath);
            
            if ($extractedText === null) {
                return [
                    'success' => false, 
                    'error' => 'Could not extract text from the document',
                    'filePath' => $filePath
                ];
            }
            
            return [
                'success' => true,
                'text' => $extractedText,
                'filePath' => $filePath
            ];
        } catch (Exception $e) {
            error_log("File processing error: " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    /**
     * Validate a document against expected content
     */
    public function validateDocument($file, $documentType) {
        // Process the file with OCR
        $result = $this->processUploadedFile($file);
        
        if (!$result['success']) {
            return [
                'valid' => false,
                'message' => $result['error']
            ];
        }
        
        // Log the extracted text for validation
        error_log("Validating document type: " . $documentType);
        error_log("==== DOCUMENT VALIDATION TEXT START ====");
        error_log($result['text']);
        error_log("==== DOCUMENT VALIDATION TEXT END ====");
        
        // Define expected content patterns for different document types
        $expectedPatterns = [
            'form_138' => [
                'keywords' => ['form 138', 'report card', 'grades', 'academic'],
                'required' => 2
            ],
            'good_moral' => [
                'keywords' => ['good moral', 'character', 'certificate', 'certify'],
                'required' => 2
            ],
            'psa_birthCert' => [
                'keywords' => ['birth certificate', 'birth', 'certificate', 'psa', 'nso'],
                'required' => 2
            ],
            'Brgy_clearance' => [
                'keywords' => ['barangay', 'clearance', 'certificate', 'resident'],
                'required' => 2
            ],
            'tor' => [
                'keywords' => ['transcript', 'records', 'academic', 'grades'],
                'required' => 2
            ],
            'honor_dismissal' => [
                'keywords' => ['honorable', 'dismissal', 'transfer', 'certificate'],
                'required' => 2
            ]
        ];
        
        // For ID pictures, we don't need to validate content
        if ($documentType === 'id_pic') {
            return [
                'valid' => true,
                'message' => 'ID photo - no content validation required',
                'extractedText' => ''
            ];
        }
        
        // Check if we have expected patterns for this document type
        if (!isset($expectedPatterns[$documentType])) {
            return [
                'valid' => true,
                'message' => 'No validation patterns defined for this document type',
                'extractedText' => $result['text']
            ];
        }
        
        // Check the extracted text against expected keywords
        $extractedText = strtolower($result['text']);
        $pattern = $expectedPatterns[$documentType];
        $matchCount = 0;
        $matchedKeywords = [];
        
        foreach ($pattern['keywords'] as $keyword) {
            if (strpos($extractedText, $keyword) !== false) {
                $matchCount++;
                $matchedKeywords[] = $keyword;
            }
        }
        
        // Determine if the document is valid based on keyword matches
        $isValid = $matchCount >= $pattern['required'];
        
        return [
            'valid' => $isValid,
            'message' => $isValid 
                ? "Document validated as {$documentType} (matched keywords: " . implode(', ', $matchedKeywords) . ")"
                : "Document does not appear to be a valid {$documentType}. Expected keywords not found.",
            'extractedText' => $result['text'],
            'matchCount' => $matchCount,
            'requiredMatches' => $pattern['required'],
            'filePath' => $result['filePath']
        ];
    }
}
?>
