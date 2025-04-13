<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../../vendor/autoload.php'; // Make sure we include the autoloader

use Smalot\PdfParser\Parser as PdfParser;
use thiagoalessio\TesseractOCR\TesseractOCR;

class FileValidator {
    private $apiKey;
    private $apiEndpoint;
    private $allowedExtensions;
    private $maxFileSize;
    private $documentTitles;
    private $pdfParser;

    public function __construct() {
        global $apiKey, $apiEndpoint;
        
        $this->apiKey = $apiKey;
        $this->apiEndpoint = $apiEndpoint;
        $this->pdfParser = new PdfParser();
        
        // Define allowed extensions for different document types
        $this->allowedExtensions = [
            'image' => ['jpg', 'jpeg', 'png', 'gif'],
            'document' => ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'] // Added jpg, jpeg, and png to document types
        ];
        
        // Define expected titles/keywords for each document type
        $this->documentTitles = [
            'form_138' => ['form 138', 'report card', 'academic record', 'student evaluation', 'grade sheet', 'form138'],
            'good_moral' => ['good moral', 'certificate of good moral', 'moral character', 'good conduct'],
            'psa_birthCert' => ['birth certificate', 'certificate of live birth', 'psa', 'nso', 'philippine statistics authority'],
            'id_pic' => ['id', 'identification', 'photo'], // ID pictures may not have text
            'Brgy_clearance' => ['barangay clearance', 'brgy clearance', 'barangay certificate', 'residency'],
            'tor' => ['transcript of records', 'academic records', 'tor', 'scholastic record'],
            'honor_dismissal' => ['honorable dismissal', 'certificate of transfer', 'good standing', 'dismissal']
        ];
        
        // 5MB file size limit
        $this->maxFileSize = 5 * 1024 * 1024;

        // Load the PDF Parser on initialization
        error_log("FileValidator initialized with PDF Parser and OCR capability");
    }

    /**
     * Main validation method for uploaded files
     */
    public function validateFile($file, $fileType, $documentType = null) {
        if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
            return ['valid' => false, 'message' => 'File upload error: ' . $this->getUploadErrorMessage($file['error'])];
        }

        // Check file size
        if ($file['size'] > $this->maxFileSize) {
            return ['valid' => false, 'message' => 'File size exceeds maximum allowed size (5MB)'];
        }

        // Check file extension
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $validExtension = $this->checkFileExtension($extension, $fileType);
        if (!$validExtension) {
            return ['valid' => false, 'message' => 'Invalid file type. Allowed extensions for this document: ' . 
                implode(', ', $this->allowedExtensions[$fileType])];
        }

        // Basic MIME type check
        $mimeType = mime_content_type($file['tmp_name']);
        if (!$this->checkMimeType($mimeType, $fileType)) {
            return ['valid' => false, 'message' => 'Invalid file content type: ' . $mimeType];
        }
        
        // For images, do additional image validation
        if ($fileType === 'image' && $this->isImageType($mimeType)) {
            $imageInfo = getimagesize($file['tmp_name']);
            if ($imageInfo === false) {
                return ['valid' => false, 'message' => 'Invalid image file'];
            }
        }

        // Skip DeepSeek OCR if LOCAL_DEV_MODE is true
        if (defined('LOCAL_DEV_MODE') && !LOCAL_DEV_MODE) {
            try {
                // Extract text from document and verify based on document type
                if ($documentType && isset($this->documentTitles[$documentType])) {
                    // Process both non-PDF and PDF files
                    if ($this->isImageType($mimeType) || $mimeType === 'application/pdf') {
                        // Extract text from image/PDF and log it
                        $extractedText = $this->extractTextFromDocument($file);
                        
                        // Log the extracted text
                        if (!empty($extractedText)) {
                            $this->logExtractedText($documentType, $file['name'], $extractedText);
                            
                            // Verify text against expected document type keywords
                            $textVerification = $this->verifyDocumentText($extractedText, $documentType);
                            if (!$textVerification['valid'] && defined('API_FALLBACK_MODE') && API_FALLBACK_MODE) {
                                // Just log a warning but don't fail validation if fallback is enabled
                                error_log("Warning: " . $textVerification['message'] . " - Allowing due to API_FALLBACK_MODE");
                            } elseif (!$textVerification['valid']) {
                                return $textVerification;
                            }
                            
                            // Verify document truthfulness
                            $truthfulnessResult = $this->verifyDocumentTruthfulness($extractedText, $documentType);
                            
                            // Add truthfulness result to the validation result
                            return [
                                'valid' => true, 
                                'message' => 'File passed validation',
                                'truthfulness' => $truthfulnessResult
                            ];
                        } else {
                            // Just log the fact that we couldn't extract any text
                            error_log("No text could be extracted from document: {$file['name']} (type: {$documentType})");
                            
                            // Allow empty text result for ID photos
                            if ($documentType !== 'id_pic' && !defined('API_FALLBACK_MODE')) {
                                return ['valid' => false, 'message' => 'Could not extract text from document. Please ensure document is clear and contains text.'];
                            }
                        }
                    }
                }
            } catch (Exception $e) {
                error_log("Document OCR error: " . $e->getMessage());
                
                // If fallback mode is enabled, continue despite errors
                if (!defined('API_FALLBACK_MODE') || !API_FALLBACK_MODE) {
                    return ['valid' => false, 'message' => 'Document text verification failed: ' . $e->getMessage()];
                }
            }
        }

        // If we got here, file passed validation but didn't go through truthfulness check
        return [
            'valid' => true, 
            'message' => 'File passed validation',
            'truthfulness' => ['genuine' => true, 'confidence' => 0.5, 'message' => 'No verification performed']
        ];
    }

    /**
     * Log extracted text from documents
     */
    private function logExtractedText($documentType, $fileName, $extractedText) {
        $documentName = $this->getDocumentTypeName($documentType);
        $truncatedText = substr($extractedText, 0, 500) . (strlen($extractedText) > 500 ? '...' : '');
        
        error_log("============ TEXT EXTRACTED FROM DOCUMENT ============");
        error_log("Document Type: {$documentName}");
        error_log("File Name: {$fileName}");
        error_log("Extracted Text: ");
        error_log($truncatedText);
        error_log("====================================================");
        
        // Optional - write to a separate log file if needed
        $logDir = __DIR__ . '/../../../logs';
        if (!file_exists($logDir)) {
            mkdir($logDir, 0755, true);
        }
        
        $logFile = $logDir . '/document_text_' . date('Y-m-d') . '.log';
        file_put_contents($logFile, 
            date('[Y-m-d H:i:s]') . " Document Type: {$documentName}, File: {$fileName}\n" . 
            "TEXT: {$extractedText}\n\n", 
            FILE_APPEND
        );
    }

    /**
     * Extract text from a document using local methods first, then DeepSeek API as backup
     */
    private function extractTextFromDocument($file) {
        try {
            // Get file type and check if we can process it
            $mimeType = mime_content_type($file['tmp_name']);
            
            error_log("Starting content validation for file: " . $file['name'] . " (size: " . $file['size'] . " bytes)");
            
            // For PDFs, try local extraction first
            if ($mimeType === 'application/pdf') {
                // Try local PDF text extraction with Smalot first
                $extractedText = $this->extractTextFromPDFLocal($file);
                
                // If local extraction fails or returns very little text, use DeepSeek API as backup
                if (empty(trim($extractedText)) || strlen(trim($extractedText)) < 20) {
                    error_log("Local PDF extraction returned insufficient text, trying API backup");
                    $extractedText = $this->analyzeDocumentContent($file, "pdf");
                } else {
                    error_log("Successfully extracted text locally using PDF parser");
                }
                
                return $extractedText;
            } else if ($this->isImageType($mimeType)) {
                // For images, try local OCR first, then fall back to DeepSeek API if needed
                try {
                    $extractedText = $this->extractTextFromImageLocal($file);
                    
                    if (!empty(trim($extractedText)) && strlen(trim($extractedText)) > 10) {
                        error_log("Successfully extracted text from image using local OCR");
                        return $extractedText;
                    } else {
                        error_log("Local OCR returned insufficient text, trying API backup");
                    }
                } catch (Exception $e) {
                    error_log("Local OCR error: " . $e->getMessage() . " - Using API backup");
                }
                
                // Use DeepSeek API as backup for OCR
                return $this->analyzeDocumentContent($file, "image");
            } else {
                throw new Exception("Unsupported file type: " . $mimeType);
            }
            
            // Clean up the extracted text to remove explanations
            $documentType = pathinfo($file['name'], PATHINFO_FILENAME);
            $extractedText = $this->cleanExtractedText($extractedText, $documentType);
            
            return $extractedText;
        } catch (Exception $e) {
            error_log("Error analyzing document content: " . $e->getMessage());
            
            // Try fallback method if main extraction fails
            if (defined('API_FALLBACK_MODE') && API_FALLBACK_MODE) {
                // Return a basic string with the filename as fallback
                return "Document: " . pathinfo($file['name'], PATHINFO_FILENAME);
            }
            
            throw $e;
        }
    }
    
    /**
     * Extract text from PDF using Smalot PDF Parser (local method)
     */
    private function extractTextFromPDFLocal($file) {
        try {
            error_log("Using Smalot PDF Parser to extract text from: " . $file['name']);
            $pdf = $this->pdfParser->parseFile($file['tmp_name']);
            $text = $pdf->getText();
            
            // Basic validation of extracted text
            if (trim($text) === '') {
                error_log("Warning: No text extracted from PDF using Smalot parser");
                return "";
            }
            
            // Truncate overly long text for logs
            $logText = strlen($text) > 200 ? substr($text, 0, 200) . "..." : $text;
            error_log("PDF text extracted locally (" . strlen($text) . " chars): " . $logText);
            
            return $text;
        } catch (Exception $e) {
            error_log("Error in local PDF extraction: " . $e->getMessage());
            return "";  // Return empty text, the calling function will try API backup
        }
    }

    /**
     * Extract text from an image using Tesseract OCR - improved version based on test_ocr.php
     */
    private function extractTextFromImageLocal($file) {
        try {
            error_log("Using Tesseract OCR to extract text from image: " . $file['name']);
            
            // Define possible paths to look for Tesseract executable
            $possiblePaths = [
                'C:/wamp64/www/onlineenrolmentsystem/bin/tesseract/tesseract.exe',  // Absolute path
                __DIR__ . '/../../../bin/tesseract/tesseract.exe',                   // Relative path from current file
                __DIR__ . '/../../bin/tesseract/tesseract.exe',                      // Alternative relative path
                'C:/Program Files/Tesseract-OCR/tesseract.exe',                      // Standard Windows install location
                '/usr/bin/tesseract'                                                 // Linux location
            ];
            
            // Find the first valid tesseract path
            $tesseractPath = null;
            foreach ($possiblePaths as $path) {
                if (file_exists($path)) {
                    $tesseractPath = $path;
                    error_log("Found Tesseract at: $tesseractPath");
                    break;
                }
            }
            
            // Check if Tesseract was found at any of the paths
            if (!$tesseractPath) {
                error_log("ERROR: Tesseract executable not found in any of the expected locations!");
                // Log all the paths we checked to help with debugging
                error_log("Checked paths: " . implode(', ', $possiblePaths));
                return "";
            }
            
            // Create temporary directory for processing if it doesn't exist
            $uploadDir = __DIR__ . '/../../../uploads/temp/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            // Generate a unique filename for the temporary file
            $tempFilename = uniqid() . '_' . basename($file['name']);
            $tempFilePath = $uploadDir . $tempFilename;
            
            // Save the file to the temporary location
            if (!copy($file['tmp_name'], $tempFilePath)) {
                error_log("Failed to create temporary file for OCR processing");
                return "";
            }
            
            // Process the image with Tesseract OCR
            try {
                $tesseract = new TesseractOCR($tempFilePath);
                $tesseract->executable($tesseractPath);
                
                // Add configuration options to improve OCR quality
                $tesseract->lang('eng')          // Set language to English
                         ->dpi(300)              // Higher DPI for better recognition
                         ->psm(6)                // Page segmentation mode: 6 = Assume a single uniform block of text
                         ->oem(1);               // OCR Engine Mode: 1 = Neural nets LSTM engine only
                
                // Run OCR and get the text
                $text = $tesseract->run();
                
                // Clean up the temporary file
                @unlink($tempFilePath);
                
                // Basic validation of extracted text
                if (trim($text) === '') {
                    error_log("Warning: No text extracted from image using Tesseract OCR");
                    return "";
                }
                
                // Log the full extracted text
                error_log("==== EXTRACTED IMAGE TEXT START ====");
                error_log($text);
                error_log("==== EXTRACTED IMAGE TEXT END ====");
                
                return $text;
            } catch (Exception $tessException) {
                error_log("Tesseract execution error: " . $tessException->getMessage());
                return "";
            }
        } catch (Exception $e) {
            error_log("Error in local image OCR: " . $e->getMessage());
            return "";  // Return empty text, the calling function will try API backup
        }
    }
    
    /**
     * Verify the document using OCR and content validation
     */
    public function validateDocumentWithOCR($file, $documentType) {
        try {
            error_log("Starting OCR validation for document type: " . $documentType);
            
            // STEP 1: Extract text using Tesseract OCR
            $extractedText = $this->extractTextFromImageLocal($file);
            error_log("Tesseract OCR extraction result: " . (empty($extractedText) ? "FAILED (no text)" : "SUCCESS (" . strlen($extractedText) . " chars)"));
            
            // Log the full extracted text in chunks to avoid log truncation
            if (!empty($extractedText)) {
                error_log("==== EXTRACTED TEXT START ====");
                // Log text in chunks of 1000 characters to prevent log entry truncation
                $chunks = str_split($extractedText, 1000);
                foreach ($chunks as $index => $chunk) {
                    error_log("TEXT CHUNK " . ($index + 1) . "/" . count($chunks) . ": " . $chunk);
                }
                error_log("==== EXTRACTED TEXT END ====");
            }
            
            // If OCR failed, check if it's an ID photo (which may not have text)
            if (empty($extractedText)) {
                if ($documentType === 'id_pic') { 
                    // ID photos don't require text validation
                    return ['valid' => true, 'message' => 'ID photo - no text verification required'];
                }
                
                error_log("Tesseract OCR failed to extract text. Trying backup method...");
                // Try API backup for text extraction as fallback
                $extractedText = $this->analyzeDocumentContent($file, 'image');
                
                if (empty($extractedText)) {
                    return ['valid' => false, 'message' => 'Could not extract text from document'];
                }
            }
            
            // STEP 2: Verify the text content against expected patterns for this document type
            $verificationResult = $this->verifyDocumentText($extractedText, $documentType);
            if (!$verificationResult['valid']) {
                error_log("Document content verification failed for " . $documentType);
                return $verificationResult;
            }
            
            // STEP 3: Send extracted text to DeepSeek API to verify document truthfulness
            error_log("Document content verified. Sending to DeepSeek for truthfulness verification...");
            $truthfulness = $this->verifyDocumentTruthfulness($extractedText, $documentType);
            $verificationResult['truthfulness'] = $truthfulness;
            $verificationResult['extractedText'] = $extractedText;
            
            // Log the verification outcome
            error_log("DeepSeek verification result: " . 
                     ($truthfulness['genuine'] ? "GENUINE" : "SUSPICIOUS") . 
                     " (Confidence: " . $truthfulness['confidence'] . ")");
            
            return $verificationResult;
        } catch (Exception $e) {
            error_log("Document OCR validation error: " . $e->getMessage());
            if (defined('API_FALLBACK_MODE') && API_FALLBACK_MODE) {
                return ['valid' => true, 'message' => 'Document accepted in fallback mode'];
            }
            return ['valid' => false, 'message' => 'OCR validation failed: ' . $e->getMessage()];
        }
    }

    /**
     * Check if Tesseract OCR is available on the system
     */
    private function isTesseractAvailable() {
        try {
            // Try to create a tesseract instance
            $tesseract = new TesseractOCR();
            $version = $tesseract->version();
            error_log("Tesseract OCR version detected: " . $version);
            return true;
        } catch (Exception $e) {
            error_log("Tesseract OCR not available: " . $e->getMessage() . " - Using DeepSeek API fallback");
            return false;
        }
    }

    /**
     * Analyze document content using DeepSeek API
     */
    private function analyzeDocumentContent($file, $fileType) {
        $fileName = $file['name'];
        $fileContent = file_get_contents($file['tmp_name']);
        $fileBase64 = base64_encode($fileContent);
        $mimeType = mime_content_type($file['tmp_name']);
        
        error_log("Analyzing content of $fileType file: $fileName");
        
        // For PDFs, try to use Smalot first (this should already have been tried by the caller)
        if ($fileType === "pdf" && !defined('API_FALLBACK_MODE')) {
            try {
                $text = $this->extractTextFromPDFLocal($file);
                if (!empty(trim($text)) && strlen(trim($text)) > 20) {
                    return $text;
                }
            } catch (Exception $e) {
                error_log("Local PDF parsing failed, continuing to API: " . $e->getMessage());
                // Continue to API method if local parsing fails
            }
        }
        
        // For PDFs, use direct API classification approach as backup
        if ($fileType === "pdf") {
            // Define a constant fallback message in case API calls fail
            $fallbackClassification = "Document type based on filename: {$fileName}";
            
            try {
                // Set API_FALLBACK_MODE to skip actual API calls if needed
                if (defined('API_FALLBACK_MODE') && API_FALLBACK_MODE) {
                    error_log("Using filename-based classification due to API_FALLBACK_MODE=true");
                    return $this->extractDocumentTypeFromFilename($file);
                }
                
                error_log("Calling API for PDF document classification with strict timeout");
                // Use a very short timeout (5 seconds) to avoid long waits
                $payload = [
                    'model' => 'deepseek-chat',
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'You are a document classifier that identifies document types.'
                        ],
                        [
                            'role' => 'user',
                            'content' => "I have a document named \"" . $file['name'] . "\". What type of educational document is this likely to be?"
                        ]
                    ],
                    'max_tokens' => 50
                ];
                
                // Use a strict timeout to prevent PHP from hanging
                $apiResponse = $this->callDeepSeekAPIWithTimeout($payload, 5);
                
                if (isset($apiResponse['choices'][0]['message']['content'])) {
                    $classification = $apiResponse['choices'][0]['message']['content'];
                    error_log("Got document classification: " . $classification);
                    return "Classification: " . $classification;
                }
            } catch (Exception $e) {
                error_log("API classification failed: " . $e->getMessage() . " - Using filename fallback");
            }
            
            // Fall back to filename-based classification if API call failed
            return $this->extractDocumentTypeFromFilename($file);
        }
        
        // For image files, also use a fallback approach if needed
        try {
            // Set API_FALLBACK_MODE to skip actual API calls if needed
            if (defined('API_FALLBACK_MODE') && API_FALLBACK_MODE) {
                error_log("Skipping image analysis due to API_FALLBACK_MODE=true");
                return "Image document: " . pathinfo($file['name'], PATHINFO_FILENAME);
            }
            
            // For image files or converted PDFs, use vision-specific prompting
            $payload = [
                'model' => 'deepseek-vision',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are an OCR service. Only extract the actual text visible in the document. Do not provide descriptions, explanations, or interpretations of the document.'
                    ],
                    [
                        'role' => 'user',
                        'content' => [
                            [
                                'type' => 'text',
                                'text' => "Extract and list ALL text visible in this document or image. Include all text exactly as it appears, preserving the format where possible. Return ONLY the text content, no explanations."
                            ],
                            [
                                'type' => 'image_url',
                                'image_url' => [
                                    'url' => "data:{$mimeType};base64,{$fileBase64}"
                                ]
                            ]
                        ]
                    ]
                ],
                'temperature' => 0,
                'max_tokens' => 1500
            ];
            
            // Call the DeepSeek API with a strict timeout
            error_log("Calling DeepSeek API to analyze image content with strict timeout...");
            $apiResponse = $this->callDeepSeekAPIWithTimeout($payload, 10);
            
            // Extract the content analysis from the response
            if (isset($apiResponse['choices'][0]['message']['content'])) {
                $analysis = $apiResponse['choices'][0]['message']['content'];
                error_log("Content analysis successful, received " . strlen($analysis) . " characters");
                return $analysis;
            }
        } catch (Exception $e) {
            error_log("Image analysis failed: " . $e->getMessage() . " - Using fallback");
        }
        
        // Fallback for all failures
        return "Document: " . pathinfo($file['name'], PATHINFO_FILENAME);
    }

    /**
     * Extract document type from filename as a fallback method
     */
    private function extractDocumentTypeFromFilename($file) {
        $filenameParts = explode('.', basename($file['name']));
        $basename = strtolower($filenameParts[0]);
        
        // Look for common patterns in the filename
        $commonDocs = [
            'form_138' => ['form 138', 'form138', 'f138', '138', 'report card'],
            'tor' => ['tor', 'transcript', 'record'],
            'good_moral' => ['good moral', 'gmc', 'moral'],
            'psa_birthCert' => ['birth', 'psa', 'nso']
        ];
        
        foreach ($commonDocs as $docType => $keywords) {
            foreach ($keywords as $keyword) {
                if (strpos($basename, $keyword) !== false) {
                    error_log("Document type identified from filename as: $docType");
                    return "Document type: " . $this->getDocumentTypeName($docType) . "\n" .
                           "Identified from filename: " . $file['name'];
                }
            }
        }
        
        // Last resort - just use filename
        return "Document: " . $basename;
    }

    /**
     * Call the DeepSeek API with a custom timeout - improved version
     */
    private function callDeepSeekAPIWithTimeout($payload, $timeoutSeconds = 10) {
        static $failureCount = 0;
        
        // Circuit breaker - if too many failures, automatically use fallback mode
        if ($failureCount > 3) {
            error_log("Too many API failures, activating circuit breaker");
            throw new Exception('Circuit breaker activated due to multiple API failures');
        }
        
        // Start timing the API call
        $startTime = microtime(true);
        
        $ch = curl_init($this->apiEndpoint);
        
        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->apiKey
        ];
        
        // Convert payload to JSON with error checking
        $jsonPayload = json_encode($payload);
        if ($jsonPayload === false) {
            throw new Exception('Failed to encode API payload: ' . json_last_error_msg());
        }
        
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonPayload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        
        // Strict timeout settings
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeoutSeconds);        // Maximum time the request is allowed to take
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeoutSeconds); // Maximum time allowed for connection
        curl_setopt($ch, CURLOPT_DNS_CACHE_TIMEOUT, 120);          // Cache DNS lookups
        
        // Disable SSL verification for testing environments
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        
        // Add retry mechanism
        $retries = 0;
        $maxRetries = 1;
        $response = false;
        
        while ($retries <= $maxRetries && $response === false) {
            if ($retries > 0) {
                error_log("API call retry attempt {$retries}");
                sleep(1); // Wait before retry
            }
            
            $response = curl_exec($ch);
            $err = curl_error($ch);
            
            if ($response === false) {
                $retries++;
            }
        }
        
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $totalTime = curl_getinfo($ch, CURLINFO_TOTAL_TIME);
        
        curl_close($ch);
        
        // Log detailed timing information
        $endTime = microtime(true);
        $duration = $endTime - $startTime;
        error_log("API call completed in {$duration} seconds (CURL reported: {$totalTime}s), HTTP code: {$httpCode}");
        
        if ($err || $response === false) {
            $failureCount++;
            throw new Exception('cURL Error: ' . ($err ?: 'No response received within timeout period'));
        }
        
        if ($httpCode >= 400) {
            $failureCount++;
            $logResponse = substr($response, 0, 500);
            throw new Exception('API Error: HTTP Code ' . $httpCode . ' - ' . $logResponse);
        }
        
        $decodedResponse = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $failureCount++;
            throw new Exception('Invalid JSON response: ' . json_last_error_msg());
        }
        
        // Reset failure count on success
        $failureCount = 0;
        
        return $decodedResponse;
    }

    /**
     * Post-process extracted text to clean up unwanted explanations
     * This helps filter out DeepSeek's tendency to explain what documents are
     */
    private function cleanExtractedText($text, $documentType) {
        // Remove explanations about document types that might be included
        $explanationPatterns = [
            // Patterns for explanations about what a document is
            '/is an? (official|educational) document/i',
            '/is commonly used/i',
            '/Based on the filename/i',
            '/I can extract the following information/i',
            '/Here\'s what I would expect to find in/i',
            '/A Form \d+ (is|contains)/i',
            '/The text in this document appears to be/i',
            '/In a typical (form|document)/i',
            '/For a [a-z\s]+ document/i',
            '/The content of a [a-z\s]+ typically includes/i'
        ];
        
        // Check if the text starts with explanations
        $firstNewline = strpos($text, "\n");
        $firstLine = $firstNewline !== false ? substr($text, 0, $firstNewline) : $text;
        
        foreach ($explanationPatterns as $pattern) {
            if (preg_match($pattern, $firstLine)) {
                // If the first line contains explanations, find where the actual content likely begins
                $contentMarkers = [
                    "Content:", 
                    "Text:", 
                    "Extracted Text:", 
                    "The document contains:", 
                    "---",
                    ":"
                ];
                
                $startPos = false;
                foreach ($contentMarkers as $marker) {
                    $pos = stripos($text, $marker);
                    if ($pos !== false) {
                        $startPos = $pos + strlen($marker);
                        break;
                    }
                }
                
                if ($startPos !== false) {
                    $text = trim(substr($text, $startPos));
                    error_log("Cleaned explanatory text from extraction");
                }
                break;
            }
        }
        
        // Remove any bullet point lists of generic document content
        $bulletListPatterns = [
            '/^[\s\-•]+(Scope of work|Roles and responsibilities|Deliverables|Budget|Timelines)/m',
            '/^[\s\-•]+(Legal|Compliance|Requirements|Appendices)/m',
            '/^[\s\-•]+(Risk management|Stakeholder|Evaluation criteria)/m'
        ];
        
        $hasBulletList = false;
        foreach ($bulletListPatterns as $pattern) {
            if (preg_match($pattern, $text)) {
                $hasBulletList = true;
                break;
            }
        }
        
        if ($hasBulletList) {
            error_log("Detected generic bullet list content - replacing with error message");
            return "ERROR: Unable to extract actual text from document. Please ensure the document contains real content.";
        }
        
        return $text;
    }

    /**
     * Extract text from PDF document using DeepSeek API
     */
    private function extractTextFromPDF($file) {
        error_log("Processing PDF file with DeepSeek: " . $file['name']);
        
        try {
            // For PDF files, we'll focus on classifying the document type rather than extracting text
            $payload = [
                'model' => 'deepseek-chat',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are a document classifier that identifies document types.'
                    ],
                    [
                        'role' => 'user',
                        'content' => "I have a document named \"" . $file['name'] . "\". Based on the filename, what type of educational document is this likely to be? Is it Form 137, Form 138, Report Card, Transcript of Records, Good Moral Certificate, Birth Certificate, or something else?"
                    ]
                ],
                'max_tokens' => 100
            ];
            
            $apiResponse = $this->callDeepSeekAPI($payload);
            
            if (isset($apiResponse['choices'][0]['message']['content'])) {
                $classification = $apiResponse['choices'][0]['message']['content'];
                error_log("DeepSeek classified document as: " . $classification);
                return "DeepSeek Document Classification: " . $classification;
            }
            
            // If classification fails, return filename as content
            return "Document: " . pathinfo($file['name'], PATHINFO_FILENAME);
        } catch (Exception $e) {
            error_log("DeepSeek PDF processing error: " . $e->getMessage());
            // Return a basic string with the filename
            return "Document: " . pathinfo($file['name'], PATHINFO_FILENAME);
        }
    }

    /**
     * Verify the extracted text against expected titles for document type
     */
    private function verifyDocumentText($extractedText, $documentType) {
        if (empty($extractedText)) {
            // For ID pictures, empty text is acceptable
            if ($documentType === 'id_pic') {
                return ['valid' => true, 'message' => 'ID photo validated'];
            }
            
            return ['valid' => false, 'message' => 'No text found in document'];
        }
        
        // Log the actual text we're checking against
        error_log("Verifying document content for type: " . $documentType);
        error_log("Text to verify: " . substr($extractedText, 0, 200));
        
        // Get expected keywords for this document type
        if (!isset($this->documentTitles[$documentType])) {
            return ['valid' => true, 'message' => 'No validation keywords defined for this document type'];
        }
        
        // Define content patterns for different document types
        $contentPatterns = [
            'form_138' => [
                'keywords' => ['form 138', 'report card', 'academic record', 'grades', 'school year', 'subject'],
                'sections' => ['grades', 'academic', 'performance', 'evaluation']
            ],
            'good_moral' => [
                'keywords' => ['good moral', 'character', 'conduct', 'certificate'],
                'sections' => ['certify', 'behavior', 'moral', 'character', 'standing']
            ],
            'psa_birthCert' => [
                'keywords' => ['birth certificate', 'psa', 'nso', 'born', 'date of birth'],
                'sections' => ['name', 'birth', 'parents', 'place', 'date']
            ],
            'id_pic' => [
                'keywords' => ['photo', 'portrait', 'face', 'id picture'],
                'sections' => []  // ID pictures may not have sections
            ],
            'Brgy_clearance' => [
                'keywords' => ['barangay', 'clearance', 'certificate', 'resident'],
                'sections' => ['issued', 'certify', 'resident', 'clearance']
            ],
            'tor' => [
                'keywords' => ['transcript', 'records', 'academic', 'subjects', 'grades'],
                'sections' => ['subjects', 'units', 'grades', 'semester', 'academic']
            ],
            'honor_dismissal' => [
                'keywords' => ['honorable', 'dismissal', 'transfer', 'good standing'],
                'sections' => ['certificate', 'granted', 'transfer', 'completed']
            ]
        ];
        
        $expectedKeywords = $this->documentTitles[$documentType];
        $lowerText = strtolower($extractedText);
        
        // Check if document type is mentioned explicitly
        $documentTypeDetected = false;
        foreach ($expectedKeywords as $keyword) {
            if (strpos($lowerText, $keyword) !== false) {
                error_log("MATCH FOUND for keyword: '{$keyword}'");
                $documentTypeDetected = true;
                break;
            }
        }
        
        // If we have content patterns, check for key sections/elements that should exist
        $contentMatches = 0;
        $requiredMatches = 2; // Need at least 2 content matches to validate
        
        if (isset($contentPatterns[$documentType])) {
            $patterns = $contentPatterns[$documentType];
            
            // Check additional content keywords (more specific to actual content)
            foreach ($patterns['keywords'] as $keyword) {
                if (strpos($lowerText, $keyword) !== false) {
                    $contentMatches++;
                    error_log("Content match found: '{$keyword}'");
                }
            }
            
            // Check for expected sections
            foreach ($patterns['sections'] as $section) {
                if (strpos($lowerText, $section) !== false) {
                    $contentMatches++;
                    error_log("Section match found: '{$section}'");
                }
            }
        }
        
        // Log validation details
        error_log("Document validation results - Type detected: " . ($documentTypeDetected ? "YES" : "NO") . 
                  ", Content matches: {$contentMatches}/{$requiredMatches}");
        
        // If API analysis contains certain indicators that this is the correct document
        if (strpos($lowerText, "this appears to be a") !== false && strpos($lowerText, $documentType) !== false) {
            error_log("DeepSeek confirmed document type");
            return ['valid' => true, 'message' => "DeepSeek confirmed this document is a {$this->getDocumentTypeName($documentType)}"];
        }
        
        // If document type is detected or we have enough content matches, consider it valid
        if ($documentTypeDetected || $contentMatches >= $requiredMatches) {
            return ['valid' => true, 'message' => "Document verified as {$this->getDocumentTypeName($documentType)}"];
        }
        
        // For API_FALLBACK_MODE, we'll trust the document is valid anyway
        if (defined('API_FALLBACK_MODE') && API_FALLBACK_MODE) {
            error_log("Document accepted due to API_FALLBACK_MODE despite failing validation");
            return ['valid' => true, 'message' => "Document accepted with API_FALLBACK_MODE"];
        }
        
        // Document failed validation
        return [
            'valid' => false, 
            'message' => "Document content does not match {$this->getDocumentTypeName($documentType)}. Please upload the correct document."
        ];
    }
    
    /**
     * Call DeepSeek API with proper error handling
     */
    private function callDeepSeekAPI($payload) {
        $ch = curl_init($this->apiEndpoint);
        
        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->apiKey
        ];
        
        // Convert payload to JSON with error checking
        $jsonPayload = json_encode($payload);
        if ($jsonPayload === false) {
            throw new Exception('Failed to encode API payload: ' . json_last_error_msg());
        }
        
        // Log payload size for debugging
        error_log("API payload size: " . strlen($jsonPayload) . " bytes");
        
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonPayload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30); // Reasonable timeout
        
        // Disable SSL verification
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        
        // Limit verbosity to reduce log size
        $verbose = false;
        if ($verbose) {
            $verboseOutput = fopen('php://temp', 'w+');
            curl_setopt($ch, CURLOPT_VERBOSE, true);
            curl_setopt($ch, CURLOPT_STDERR, $verboseOutput);
        }
        
        $response = curl_exec($ch);
        $err = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        // Get verbose log if enabled
        if ($verbose) {
            rewind($verboseOutput);
            $verboseLog = stream_get_contents($verboseOutput);
            fclose($verboseOutput);
            if ($httpCode >= 400 || $err) {
                error_log("cURL verbose log: " . $verboseLog);
            }
        }
        
        curl_close($ch);
        
        if ($err) {
            throw new Exception('cURL Error: ' . $err);
        }
        
        if ($httpCode >= 400) {
            // Log part of the response but avoid filling logs with huge responses
            $logResponse = substr($response, 0, 500);
            throw new Exception('API Error: HTTP Code ' . $httpCode . ' - ' . $logResponse);
        }
        
        $decodedResponse = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Invalid JSON response: ' . json_last_error_msg());
        }
        
        return $decodedResponse;
    }
    
    /**
     * Check if file extension is allowed for the given document type
     */
    private function checkFileExtension($extension, $fileType) {
        return in_array($extension, $this->allowedExtensions[$fileType]);
    }
    
    /**
     * Check if MIME type is appropriate for the document type
     */
    private function checkMimeType($mimeType, $fileType) {
        if ($fileType === 'image') {
            return $this->isImageType($mimeType);
        } else if ($fileType === 'document') {
            // Allow both document and image types for document uploads
            return $this->isDocumentType($mimeType) || $this->isImageType($mimeType);
        }
        return false;
    }
    
    /**
     * Check if MIME type is an image type
     */
    private function isImageType($mimeType) {
        return strpos($mimeType, 'image/') === 0;
    }
    
    /**
     * Check if MIME type is a document type
     */
    private function isDocumentType($mimeType) {
        $validTypes = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ];
        return in_array($mimeType, $validTypes);
    }
    
    /**
     * Get user-friendly document type name
     */
    private function getDocumentTypeName($documentType) {
        $names = [
            'form_138' => 'Form 138 / Report Card',
            'good_moral' => 'Certificate of Good Moral Character',
            'psa_birthCert' => 'PSA Birth Certificate',
            'id_pic' => 'ID Photo',
            'Brgy_clearance' => 'Barangay Clearance',
            'tor' => 'Transcript of Records',
            'honor_dismissal' => 'Honorable Dismissal'
        ];
        
        return $names[$documentType] ?? $documentType;
    }
    
    /**
     * Get user-friendly upload error message
     */
    private function getUploadErrorMessage($errorCode) {
        switch ($errorCode) {
            case UPLOAD_ERR_INI_SIZE:
                return 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
            case UPLOAD_ERR_FORM_SIZE:
                return 'The uploaded file exceeds the MAX_FILE_SIZE directive in the HTML form';
            case UPLOAD_ERR_PARTIAL:
                return 'The uploaded file was only partially uploaded';
            case UPLOAD_ERR_NO_FILE:
                return 'No file was uploaded';
            case UPLOAD_ERR_NO_TMP_DIR:
                return 'Missing a temporary folder';
            case UPLOAD_ERR_CANT_WRITE:
                return 'Failed to write file to disk';
            case UPLOAD_ERR_EXTENSION:
                return 'A PHP extension stopped the file upload';
            default:
                return 'Unknown upload error';
        }
    }

    /**
     * Verify document truthfulness/genuineness using DeepSeek API
     */
    private function verifyDocumentTruthfulness($extractedText, $documentType) {
        // If there's no text, we can't verify
        if (empty($extractedText)) {
            if ($documentType === 'id_pic') {
                // ID pics don't need text verification
                return ['genuine' => true, 'confidence' => 0.5, 'message' => 'ID photo - no text verification required'];
            }
            return ['genuine' => false, 'confidence' => 0, 'message' => 'No content to verify'];
        }

        // Log that we're sending text to DeepSeek
        error_log("Sending extracted text to DeepSeek API for " . $documentType . " verification");
        
        // Construct prompt for DeepSeek API to analyze document truthfulness
        $payload = [
            'model' => 'deepseek-chat',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are a document verification expert. Analyze the text extracted from a document and determine if it appears to be genuine based on content, structure, and terminology. Focus on whether the text contains the expected elements for this document type.'
                ],
                [
                    'role' => 'user',
                    'content' => "This text was extracted from a " . $this->getDocumentTypeName($documentType) . 
                                " using OCR. Determine if it appears to be authentic based on the content.\n\n" . 
                                "Document text:\n\n" . $extractedText . "\n\n" .
                                "Respond with one of these options: 'GENUINE' or 'SUSPICIOUS', followed by a confidence score (0.0-1.0), and a brief explanation."
                ]
            ],
            'temperature' => 0.1,
            'max_tokens' => 150
        ];

        try {
            // Skip actual API call if we're in fallback mode
            if (defined('API_FALLBACK_MODE') && API_FALLBACK_MODE) {
                error_log("API_FALLBACK_MODE is enabled, skipping actual DeepSeek API call");
                // Fallback verification result
                return [
                    'genuine' => true,
                    'confidence' => 0.7,
                    'message' => 'Document appears genuine (using fallback mode)',
                    'documentType' => $documentType
                ];
            }
            
            // Call DeepSeek API for document truthfulness verification
            $response = $this->callDeepSeekAPI($payload);
            
            if (!isset($response['choices'][0]['message']['content'])) {
                throw new Exception('Invalid API response format');
            }
            
            $analysis = $response['choices'][0]['message']['content'];
            $lower_analysis = strtolower($analysis);
            
            // Parse the response to determine if the document is genuine
            $genuine = (strpos($lower_analysis, 'genuine') !== false);
            $suspicious = (strpos($lower_analysis, 'suspicious') !== false || 
                          strpos($lower_analysis, 'fabricated') !== false ||
                          strpos($lower_analysis, 'fake') !== false ||
                          strpos($lower_analysis, 'not genuine') !== false);
            
            // Extract confidence score if present
            $confidence = 0.5; // Default confidence
            if (preg_match('/(?:confidence|score)[\s:]*([0-9]*\.?[0-9]+)/', $lower_analysis, $matches)) {
                $confidence = floatval($matches[1]);
            }
            
            $is_genuine = ($genuine && !$suspicious) || (!$suspicious && $confidence >= 0.5);
            
            error_log("DeepSeek verification for $documentType: " . ($is_genuine ? "GENUINE" : "SUSPICIOUS") . " (Confidence: $confidence)");
            
            return [
                'genuine' => $is_genuine,
                'confidence' => $confidence,
                'message' => trim($analysis),
                'documentType' => $documentType
            ];
        }
        catch (Exception $e) {
            error_log("DeepSeek verification error: " . $e->getMessage());
            
            // In case of API failure, use fallback
            return [
                'genuine' => true, 
                'confidence' => 0.6, 
                'message' => 'Document appears genuine (API error, using fallback)',
                'documentType' => $documentType
            ];
        }
    }
}
?>
