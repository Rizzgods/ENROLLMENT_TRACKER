<?php
require_once __DIR__ . '/../config.php';

class FileValidator {
    private $apiKey;
    private $apiEndpoint;
    private $allowedExtensions;
    private $maxFileSize;
    private $documentTitles;

    public function __construct() {
        global $apiKey, $apiEndpoint;
        
        $this->apiKey = $apiKey;
        $this->apiEndpoint = $apiEndpoint;
        
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

        // If we got here, file passed validation
        return ['valid' => true, 'message' => 'File passed validation'];
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
     * Extract text from a document using DeepSeek API
     */
    private function extractTextFromDocument($file) {
        try {
            // Get file type and check if we can process it
            $mimeType = mime_content_type($file['tmp_name']);
            
            error_log("Starting content validation for file: " . $file['name'] . " (size: " . $file['size'] . " bytes)");
            
            // Different handling based on file type
            if ($mimeType === 'application/pdf') {
                $extractedText = $this->analyzeDocumentContent($file, "pdf");
            } else if ($this->isImageType($mimeType)) {
                $extractedText = $this->analyzeDocumentContent($file, "image");
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
     * Analyze document content using DeepSeek API
     */
    private function analyzeDocumentContent($file, $fileType) {
        $fileName = $file['name'];
        $fileContent = file_get_contents($file['tmp_name']);
        $fileBase64 = base64_encode($fileContent);
        $mimeType = mime_content_type($file['tmp_name']);
        
        error_log("Analyzing content of $fileType file: $fileName");
        
        // For PDFs, try local extraction first before API calls
        if ($fileType === "pdf") {
            // Try all available extraction methods for PDFs
            $pdfText = $this->extractPdfTextLocally($file['tmp_name']);
            
            if (!empty($pdfText)) {
                error_log("Successfully extracted text from PDF using local method");
                return $pdfText;
            }
            
            // If local extraction failed, now try converting to image if possible
            if (extension_loaded('imagick')) {
                // Create temporary file for the image
                $tempImagePath = tempnam(sys_get_temp_dir(), 'pdf_img_') . '.jpg';
                
                // Convert first page of PDF to image
                $imagick = new Imagick();
                $imagick->setResolution(300, 300); // High resolution for better text recognition
                $imagick->readImage($file['tmp_name'] . '[0]'); // Read first page
                $imagick->setImageFormat('jpg');
                $imagick->setCompressionQuality(95);
                $imagick->writeImage($tempImagePath);
                
                // Now read this image file
                $fileContent = file_get_contents($tempImagePath);
                $fileBase64 = base64_encode($fileContent);
                $mimeType = 'image/jpeg';
                
                error_log("PDF converted to image: " . $tempImagePath);
                
                // Clean up
                unlink($tempImagePath);
                
                // Now process as image
                $fileType = "image";
            } else {
                error_log("Imagick extension not available for PDF conversion");
                
                // Try PdfParser if available via composer
                if (class_exists('\\Smalot\\PdfParser\\Parser')) {
                    try {
                        error_log("Attempting PDF extraction with Smalot/PdfParser");
                        $parser = new \Smalot\PdfParser\Parser();
                        $pdf = $parser->parseFile($file['tmp_name']);
                        $text = $pdf->getText();
                        
                        if (!empty($text)) {
                            error_log("Text successfully extracted from PDF using PdfParser");
                            return $text;
                        }
                    } catch (Exception $e) {
                        error_log("PdfParser extraction failed: " . $e->getMessage());
                    }
                }
            }
            
            // If we get here, try a lower timeout API call to avoid long delays
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
                'max_tokens' => 50,
                'timeout' => 10  // Custom timeout parameter
            ];
            
            try {
                error_log("Trying faster API call with reduced timeout");
                $apiResponse = $this->callDeepSeekAPIWithTimeout($payload, 10);  // 10 second timeout
                
                if (isset($apiResponse['choices'][0]['message']['content'])) {
                    $classification = $apiResponse['choices'][0]['message']['content'];
                    error_log("Got quick document classification: " . $classification);
                    return "Classification: " . $classification;
                }
            } catch (Exception $e) {
                error_log("Quick API call failed too: " . $e->getMessage());
            }
            
            // As a last resort, extract meaningful data from the filename
            $filenameParts = explode('.', basename($file['name']));
            $basename = $filenameParts[0];
            
            // Look for common patterns in the filename
            $commonDocs = [
                'form_138' => ['form 138', 'form138', 'f138', '138', 'report card'],
                'tor' => ['tor', 'transcript', 'record'],
                'good_moral' => ['good moral', 'gmc', 'moral'],
                'psa_birthCert' => ['birth', 'psa', 'nso']
            ];
            
            $basename = strtolower($basename);
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
        
        // For image files, continue with existing API call approach
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
                            'text' => "Extract and list ALL text visible in this document or  image. Include all text exactly as it appears, preserving the format where possible. Return ONLY the text content, no explanations."
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
        
        // Call the DeepSeek API
        error_log("Calling DeepSeek API to analyze document content...");
        $apiResponse = $this->callDeepSeekAPI($payload);
        
        // Extract the content analysis from the response
        if (isset($apiResponse['choices'][0]['message']['content'])) {
            $analysis = $apiResponse['choices'][0]['message']['content'];
            error_log("Content analysis successful, received " . strlen($analysis) . " characters");
            return $analysis;
        } else {
            error_log("Unexpected API response format: " . json_encode(array_slice($apiResponse, 0, 3)));
            throw new Exception("Failed to get content analysis from API");
        }
    }

    /**
     * Extract PDF text using local methods (no API calls)
     */
    private function extractPdfTextLocally($pdfPath) {
        // First check if PdfParser is available and load it if possible
        $pdfparserInstalled = false;
        if (file_exists(__DIR__ . '/../../vendor/autoload.php')) {
            require_once __DIR__ . '/../../vendor/autoload.php';
            $pdfparserInstalled = class_exists('\\Smalot\\PdfParser\\Parser');
            if ($pdfparserInstalled) {
                try {
                    error_log("Using Smalot PDF Parser library");
                    $parser = new \Smalot\PdfParser\Parser();
                    $pdf = $parser->parseFile($pdfPath);
                    $text = $pdf->getText();
                    
                    if (!empty(trim($text))) {
                        error_log("Successfully extracted text using PdfParser: " . substr($text, 0, 100) . "...");
                        return $text;
                    }
                } catch (Exception $e) {
                    error_log("PdfParser error: " . $e->getMessage());
                }
            } else {
                error_log("Smalot PdfParser not available. Run 'composer install' to install it.");
            }
        }
        
        // Method 1: Try pdftotext command if available (common on Linux/Unix)
        if (function_exists('exec')) {
            $output = [];
            $returnVar = -1;
            
            // Check if pdftotext exists
            exec('which pdftotext', $output, $returnVar);
            if ($returnVar === 0) {
                error_log("Found pdftotext utility, trying to extract text");
                $tempOutputFile = tempnam(sys_get_temp_dir(), 'pdf_txt_');
                exec("pdftotext -layout \"$pdfPath\" \"$tempOutputFile\"", $output, $returnVar);
                
                if ($returnVar === 0 && file_exists($tempOutputFile)) {
                    $text = file_get_contents($tempOutputFile);
                    unlink($tempOutputFile); // Clean up
                    
                    if (!empty(trim($text))) {
                        error_log("Text extracted via pdftotext: " . substr($text, 0, 100) . "...");
                        return $text;
                    }
                }
            }
        }
        
        // Method 2: Try to extract text directly with PHP
        try {
            // Check if we have the pdf_text() function from the PDF extension
            if (function_exists('pdf_open_file')) {
                error_log("Using PHP PECL PDF functions");
                $pdf = pdf_open_file($pdfPath);
                $pages = pdf_get_numimages($pdf);
                
                $text = '';
                for ($i = 1; $i <= $pages; $i++) {
                    $text .= pdf_get_text($pdf, $i) . "\n\n";
                }
                
                pdf_close($pdf);
                
                if (!empty(trim($text))) {
                    return $text;
                }
            }
        } catch (Exception $e) {
            error_log("PHP PDF extension extraction failed: " . $e->getMessage());
        }
        
        // Method 3: Try a simple regex-based approach to extract text
        // This is very limited but might catch some text
        $content = file_get_contents($pdfPath);
        if (!empty($content)) {
            // Look for text patterns in the raw PDF
            $text = '';
            
            // Extract text between BT and ET tags (basic PDF text blocks)
            $pattern = '/BT.*?\((.*?)\).*?ET/s';
            if (preg_match_all($pattern, $content, $matches)) {
                foreach ($matches[1] as $match) {
                    $text .= $match . "\n";
                }
            }
            
            // Look for plain text patterns
            $pattern = '/\/(F\d+) \d+ Tf.*?\((.*?)\)/s';
            if (preg_match_all($pattern, $content, $matches)) {
                foreach ($matches[2] as $match) {
                    $text .= $match . " ";
                }
            }
            
            if (!empty(trim($text))) {
                error_log("Extracted some text with regex: " . substr($text, 0, 100) . "...");
                return $text;
            }
        }
        
        // No text could be extracted
        return '';
    }

    /**
     * Call the DeepSeek API with a custom timeout
     */
    private function callDeepSeekAPIWithTimeout($payload, $timeoutSeconds = 10) {
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
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeoutSeconds); // Use custom timeout
        
        // Disable SSL verification
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        
        $response = curl_exec($ch);
        $err = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        curl_close($ch);
        
        if ($err) {
            throw new Exception('cURL Error: ' . $err);
        }
        
        if ($httpCode >= 400) {
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
}
?>
