<?php
require_once __DIR__ . '/../chatbot/config.php';
require_once __DIR__ . '/../chatbot/utils/file_validator.php';

// PDF text extraction testing tool
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Function to extract text from PDF using our file validator
function extractTextFromPDF($filePath) {
    if (!file_exists($filePath)) {
        return "Error: File not found - $filePath";
    }
    
    if (strtolower(pathinfo($filePath, PATHINFO_EXTENSION)) != 'pdf') {
        return "Error: Not a PDF file";
    }
    
    // Create mock file array like $_FILES provides
    $file = [
        'name' => basename($filePath),
        'type' => mime_content_type($filePath),
        'tmp_name' => $filePath,
        'error' => 0,
        'size' => filesize($filePath)
    ];
    
    // Create validator
    $validator = new FileValidator();
    
    // Use reflection to access the private method for PDF analysis
    $reflectionMethod = new ReflectionMethod('FileValidator', 'analyzeDocumentContent');
    $reflectionMethod->setAccessible(true);
    
    try {
        // Start timer
        $startTime = microtime(true);
        
        // Call the method
        $extractedText = $reflectionMethod->invoke($validator, $file, "pdf");
        
        // End timer
        $endTime = microtime(true);
        $execTime = round($endTime - $startTime, 2);
        
        // Clean up text if needed
        $reflectionCleanMethod = new ReflectionMethod('FileValidator', 'cleanExtractedText');
        $reflectionCleanMethod->setAccessible(true);
        $cleanedText = $reflectionCleanMethod->invoke($validator, $extractedText, pathinfo($file['name'], PATHINFO_FILENAME));
        
        return [
            'success' => true,
            'raw_text' => $extractedText,
            'cleaned_text' => $cleanedText,
            'exec_time' => $execTime
        ];
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>PDF Text Extraction</title>
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
        <h1>PDF Text Extraction Test</h1>
        
        <div class="card">
            <h2>Upload a PDF</h2>
            <div class="info">
                <p>This tool will convert your PDF to an image and then extract text using DeepSeek.</p>
            </div>
            
            <form method="post" enctype="multipart/form-data">
                <p>
                    <input type="file" name="pdfFile" accept=".pdf">
                </p>
                <p>
                    <button type="submit" name="extract" class="btn">Extract Text</button>
                </p>
            </form>
        </div>
        
        <?php
        // Process uploaded PDF file
        if (isset($_POST['extract']) && isset($_FILES['pdfFile']) && $_FILES['pdfFile']['error'] === UPLOAD_ERR_OK) {
            echo '<div class="card">';
            echo '<h2>Extraction Results</h2>';
            
            echo '<div class="info">';
            echo '<p>File: <strong>' . htmlspecialchars($_FILES['pdfFile']['name']) . '</strong></p>';
            echo '<p>Size: ' . round($_FILES['pdfFile']['size'] / 1024, 2) . ' KB</p>';
            echo '</div>';
            
            $result = extractTextFromPDF($_FILES['pdfFile']['tmp_name']);
            
            if (isset($result['success']) && $result['success']) {
                echo '<div class="success">';
                echo '<p>Extraction successful! (completed in ' . $result['exec_time'] . ' seconds)</p>';
                echo '</div>';
                
                // Compare raw and cleaned text
                if ($result['raw_text'] !== $result['cleaned_text']) {
                    echo '<div class="warning">';
                    echo '<p>The text was cleaned because explanatory content was detected.</p>';
                    echo '</div>';
                }
                
                echo '<h3>Extracted Text:</h3>';
                echo '<pre>' . htmlspecialchars($result['cleaned_text']) . '</pre>';
                
                echo '<h3>Raw API Response:</h3>';
                echo '<pre>' . htmlspecialchars($result['raw_text']) . '</pre>';
            } else {
                echo '<div class="error">';
                echo '<p>Extraction failed: ' . htmlspecialchars($result['error']) . '</p>';
                echo '</div>';
            }
            
            echo '</div>';
        }
        ?>
    </div>
</body>
</html>
