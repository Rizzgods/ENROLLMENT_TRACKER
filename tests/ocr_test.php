<?php
require_once __DIR__ . '/../chatbot/config.php';
require_once __DIR__ . '/../chatbot/utils/file_validator.php';

// Simple OCR test script
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Test function to extract text from a file
function testOCR($imagePath) {
    if (!file_exists($imagePath)) {
        echo "Error: File not found - $imagePath<br>";
        return;
    }
    
    echo "<h2>Testing OCR on: " . basename($imagePath) . "</h2>";
    
    // Create mock file array like $_FILES provides
    $file = [
        'name' => basename($imagePath),
        'type' => mime_content_type($imagePath),
        'tmp_name' => $imagePath,
        'error' => 0,
        'size' => filesize($imagePath)
    ];
    
    try {
        // Create validator
        $validator = new FileValidator();
        
        // Use reflection to access the private method
        $reflectionMethod = new ReflectionMethod('FileValidator', 'extractTextFromDocument');
        $reflectionMethod->setAccessible(true);
        
        echo "<p>Starting text extraction...</p>";
        
        // Start timer
        $startTime = microtime(true);
        
        // Call the method
        $extractedText = $reflectionMethod->invoke($validator, $file);
        
        // End timer
        $endTime = microtime(true);
        $execTime = round($endTime - $startTime, 2);
        
        echo "<p>Extraction completed in $execTime seconds</p>";
        
        // Display results
        if (!empty($extractedText)) {
            // Check if there are explanations at the beginning
            if (strpos($extractedText, "Form 138") === 0 || 
                strpos($extractedText, "A Form 138") === 0 ||
                strpos($extractedText, "This document") === 0) {
                echo '<div class="warning" style="background-color:#ffe8d6; padding:10px; border-left:4px solid orange; margin-bottom:15px;">
                    <strong>Warning:</strong> DeepSeek returned an explanation instead of text extraction.
                    The system should be configured to extract actual document text only.
                </div>';
            }
            
            echo "<h3>Extracted Text:</h3>";
            echo "<pre>" . htmlspecialchars($extractedText) . "</pre>";
            
            // Check for specific keywords
            $keywords = ['form 138', 'form138', 'report card'];
            echo "<h3>Keyword Check:</h3>";
            echo "<ul>";
            foreach ($keywords as $keyword) {
                $found = stripos($extractedText, $keyword) !== false;
                echo "<li>" . htmlspecialchars($keyword) . ": " . ($found ? "FOUND" : "NOT FOUND") . "</li>";
            }
            echo "</ul>";
        } else {
            echo "<p>No text extracted</p>";
        }
    } catch (Exception $e) {
        echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>OCR Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 2em; }
        pre { background: #f5f5f5; padding: 1em; border: 1px solid #ddd; white-space: pre-wrap; }
        .success { color: green; }
        .error { color: red; }
    </style>
</head>
<body>
    <h1>OCR Test Tool</h1>
    
    <form action="" method="post" enctype="multipart/form-data">
        <p>
            <label for="testImage">Upload an image to test:</label><br>
            <input type="file" name="testImage" id="testImage">
        </p>
        <p>
            <button type="submit" name="submitTest">Test OCR</button>
        </p>
    </form>
    
    <hr>
    
    <?php
    // Process uploaded test file
    if (isset($_POST['submitTest']) && isset($_FILES['testImage']) && $_FILES['testImage']['error'] === UPLOAD_ERR_OK) {
        testOCR($_FILES['testImage']['tmp_name']);
    }
    ?>
</body>
</html>
