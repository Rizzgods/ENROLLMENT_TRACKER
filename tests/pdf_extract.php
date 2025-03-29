<?php
require_once __DIR__ . '/../chatbot/config.php';
require_once __DIR__ . '/../chatbot/utils/file_validator.php';
require_once __DIR__ . '/../vendor/autoload.php'; // Ensure Tesseract OCR is autoloaded
use thiagoalessio\TesseractOCR\TesseractOCR;

// Image text extraction testing tool
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Function to extract text from an image
function extractTextFromImage($filePath) {
    if (!file_exists($filePath)) {
        return "Error: File not found - $filePath";
    }

    // Validate MIME type to ensure it's an image
    $mimeType = mime_content_type($filePath);
    $validMimeTypes = ['image/png', 'image/jpeg', 'image/jpg', 'image/bmp', 'image/tiff'];
    if (!in_array($mimeType, $validMimeTypes)) {
        return "Error: Not a valid image file (detected MIME type: $mimeType)";
    }

    try {
        // Use Tesseract OCR to extract text
        $ocr = new TesseractOCR($filePath);
        $extractedText = $ocr->run();

        return [
            'success' => true,
            'raw_text' => $extractedText,
            'cleaned_text' => cleanExtractedText($extractedText), // Implement cleaning logic if needed
            'exec_time' => round(microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"], 2)
        ];
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

// Helper function to clean extracted text
function cleanExtractedText($text) {
    // Implement text cleaning logic if needed
    return trim($text);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Image Text Extraction</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 2em; line-height: 1.6; }
        h1, h2, h3 { color: #2874a6; }
        pre { background: #f5f5f5; padding: 1em; border: 1px solid #ddd; overflow-x: auto; white-space: pre-wrap; }
        .container { max-width: 900px; margin: 0 auto; }
        .card { border: 1px solid #ddd; border-radius: 8px; padding: 20px; margin-bottom: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .info { background-color: #e8f4fd; border-left: 4px solid #2196F3; padding: 12px; margin: 10px 0; }
        .warning { background-color: #fff3e0; border-left: 4px solid #FF9800; padding: 12px; margin: 10px 0; }
        .error { background-color: #ffebee; border-left: 4px solid #F44336; padding: 12px; margin: 10px 0; }
        .success { background-color: #e8f5e9; border-left: 4px solid #4CAF50; padding: 12px; margin: 10px 0; }
        .btn { background: #4CAF50; color: white; border: none; padding: 10px 15px; cursor: pointer; border-radius: 4px; font-size: 16px; }
        .btn:hover { background: #388E3C; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Image Text Extraction Test</h1>
        
        <div class="card">
            <h2>Upload an Image</h2>
            <div class="info">
                <p>This tool will extract text from your uploaded image using Tesseract OCR.</p>
            </div>
            
            <form method="post" enctype="multipart/form-data">
                <p>
                    <input type="file" name="imageFile" accept="image/*">
                </p>
                <p>
                    <button type="submit" name="extract" class="btn">Extract Text</button>
                </p>
            </form>
        </div>
        
        <?php
        // Process uploaded image file
        if (isset($_POST['extract'])) {
            if (isset($_FILES['imageFile']) && $_FILES['imageFile']['error'] === UPLOAD_ERR_OK) {
                echo '<div class="card">';
                echo '<h2>Extraction Results</h2>';
                
                echo '<div class="info">';
                echo '<p>File: <strong>' . htmlspecialchars($_FILES['imageFile']['name']) . '</strong></p>';
                echo '<p>Size: ' . round($_FILES['imageFile']['size'] / 1024, 2) . ' KB</p>';
                echo '</div>';
                
                $result = extractTextFromImage($_FILES['imageFile']['tmp_name']);
                
                if (is_array($result) && isset($result['success']) && $result['success']) {
                    echo '<div class="success">';
                    echo '<p>Extraction successful! (completed in ' . $result['exec_time'] . ' seconds)</p>';
                    echo '</div>';
                    
                    echo '<h3>Extracted Text:</h3>';
                    echo '<pre>' . htmlspecialchars($result['cleaned_text']) . '</pre>';
                    
                    echo '<h3>Raw API Response:</h3>';
                    echo '<pre>' . htmlspecialchars($result['raw_text']) . '</pre>';
                } else {
                    echo '<div class="error">';
                    echo '<p>Extraction failed: ' . (is_array($result) ? htmlspecialchars($result['error']) : htmlspecialchars($result)) . '</p>';
                    echo '</div>';
                }
                
                echo '</div>';
            } else {
                echo '<div class="error">';
                echo '<p>Error: No file uploaded or an error occurred during upload.</p>';
                echo '</div>';
            }
        }
        ?>
    </div>
</body>
</html>
