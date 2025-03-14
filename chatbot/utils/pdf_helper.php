<?php
/**
 * PDF Helper Functions
 * 
 * This file contains helper functions for working with PDF files
 * when standard extensions like Imagick are not available.
 */

// Try to load composer autoloader if it exists
$autoloaderPaths = [
    __DIR__ . '/../../vendor/autoload.php', // From this directory
    __DIR__ . '/../vendor/autoload.php',    // From chatbot directory
    __DIR__ . '/../../../../vendor/autoload.php' // From project root
];

foreach ($autoloaderPaths as $autoloader) {
    if (file_exists($autoloader)) {
        require_once $autoloader;
        break;
    }
}

// Check and log available PDF tools
if (!function_exists('check_pdf_tools')) {
    function check_pdf_tools() {
        $tools = [];
        
        // Check for PDF Parser library
        $tools['pdfparser'] = class_exists('\\Smalot\\PdfParser\\Parser');
        
        // Check for Imagick
        $tools['imagick'] = extension_loaded('imagick');
        
        // Check for pdftotext
        $tools['pdftotext'] = false;
        if (function_exists('exec')) {
            exec('which pdftotext 2>/dev/null', $output, $returnVar);
            $tools['pdftotext'] = ($returnVar === 0);
        }
        
        error_log("PDF Tools available: " . 
                 "PdfParser: " . ($tools['pdfparser'] ? "Yes" : "No") . ", " . 
                 "Imagick: " . ($tools['imagick'] ? "Yes" : "No") . ", " . 
                 "pdftotext: " . ($tools['pdftotext'] ? "Yes" : "No"));
                 
        return $tools;
    }
    
    // Log available tools on first load
    check_pdf_tools();
}

/**
 * Extract text from a PDF file using multiple fallback methods
 * 
 * @param string $pdfPath Path to the PDF file
 * @return string Extracted text or empty string if extraction fails
 */
function extractTextFromPDF($pdfPath) {
    if (!file_exists($pdfPath)) {
        error_log("PDF file not found: $pdfPath");
        return '';
    }
    
    // Try PdfParser library if available
    if (class_exists('\\Smalot\\PdfParser\\Parser')) {
        try {
            $parser = new \Smalot\PdfParser\Parser();
            $pdf = $parser->parseFile($pdfPath);
            $text = $pdf->getText();
            
            if (!empty($text)) {
                return $text;
            }
        } catch (Exception $e) {
            error_log("PdfParser extraction failed: " . $e->getMessage());
        }
    }
    
    // Try pdftotext command if available
    if (function_exists('exec')) {
        $output = [];
        $returnVar = -1;
        
        // Check if pdftotext exists (common on Linux)
        exec('which pdftotext 2>/dev/null', $output, $returnVar);
        if ($returnVar === 0) {
            $tempOutputFile = tempnam(sys_get_temp_dir(), 'pdf_txt_');
            exec("pdftotext -layout \"$pdfPath\" \"$tempOutputFile\" 2>/dev/null", $output, $returnVar);
            
            if ($returnVar === 0 && file_exists($tempOutputFile)) {
                $text = file_get_contents($tempOutputFile);
                unlink($tempOutputFile); // Clean up
                
                if (!empty(trim($text))) {
                    return $text;
                }
            }
        }
    }
    
    // Try simple regex pattern matching on raw PDF content
    $content = file_get_contents($pdfPath);
    if (!empty($content)) {
        $text = '';
        
        // Look for text patterns in PDF content
        $patterns = [
            '/\/Text\s*?\[(.*?)\]/s',
            '/BT.*?\((.*?)\).*?ET/s',
            '/\/(F\d+) \d+ Tf.*?\((.*?)\)/s',
            '/\(\s*(.*?)\s*\) Tj/s'
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match_all($pattern, $content, $matches)) {
                foreach ($matches[1] as $match) {
                    // Clean up common PDF encoding artifacts
                    $cleaned = str_replace(['\\)', '\\(', '\\\\', '\r', '\n'], [')', '(', '\\', "\r", "\n"], $match);
                    $text .= $cleaned . "\n";
                }
            }
        }
        
        // Remove non-printable characters
        $text = preg_replace('/[^\x20-\x7E\r\n]/', '', $text);
        
        if (!empty(trim($text))) {
            return $text;
        }
    }
    
    return '';
}

/**
 * Get metadata about a PDF without extracting all text
 * 
 * @param string $pdfPath Path to the PDF file
 * @return array Metadata including title, author, page count
 */
function getPdfMetadata($pdfPath) {
    $metadata = [
        'title' => '',
        'author' => '',
        'creator' => '',
        'producer' => '',
        'creation_date' => '',
        'modified_date' => '',
        'page_count' => 0
    ];
    
    // Try using PdfParser
    if (class_exists('\\Smalot\\PdfParser\\Parser')) {
        try {
            $parser = new \Smalot\PdfParser\Parser();
            $pdf = $parser->parseFile($pdfPath);
            $details = $pdf->getDetails();
            
            // Map standard PDF metadata keys
            $mapping = [
                'Title' => 'title',
                'Author' => 'author', 
                'Creator' => 'creator',
                'Producer' => 'producer',
                'CreationDate' => 'creation_date',
                'ModDate' => 'modified_date'
            ];
            
            foreach ($mapping as $pdfKey => $metaKey) {
                if (isset($details[$pdfKey])) {
                    $metadata[$metaKey] = $details[$pdfKey];
                }
            }
            
            // Get page count
            $metadata['page_count'] = count($pdf->getPages());
            
            return $metadata;
        } catch (Exception $e) {
            error_log("PdfParser metadata extraction failed: " . $e->getMessage());
        }
    }
    
    // Fallback: try to determine page count from raw PDF
    $content = file_get_contents($pdfPath);
    if (preg_match_all('/\/Pages?\s+/', $content, $matches)) {
        $metadata['page_count'] = count($matches[0]);
    }
    
    return $metadata;
}
?>
