<?php
// filepath: c:\wamp64\www\onlineenrolmentsystem\test_ocr.php
require_once 'vendor/autoload.php';
use thiagoalessio\TesseractOCR\TesseractOCR;

// Process uploaded image
$extractedText = '';
$error = '';
$success = false;
$uploadedFile = '';

// Define path to local Tesseract executable - With explicit full path
$tesseractPath = 'C:/wamp64/www/onlineenrolmentsystem/bin/tesseract/tesseract.exe';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    try {
        $uploadDir = __DIR__ . '/uploads/temp/';
        
        // Create directory if it doesn't exist
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        // Generate unique filename
        $filename = uniqid() . '_' . basename($_FILES['image']['name']);
        $filePath = $uploadDir . $filename;
        $uploadedFile = $filename;
        
        // Move uploaded file
        if (move_uploaded_file($_FILES['image']['tmp_name'], $filePath)) {
            // Run OCR on the image with explicit executable path
            $tesseract = new TesseractOCR($filePath);
            $tesseract->executable($tesseractPath);
            
            // Check if the executable exists
            if (!file_exists($tesseractPath)) {
                throw new Exception("Tesseract executable not found at: $tesseractPath");
            }
            
            // Add additional configurations if needed
            $tesseract->lang('eng');
            
            // Get the text
            $extractedText = $tesseract->run();
            $success = true;
        } else {
            $error = "Failed to upload file.";
        }
    } catch (Exception $e) {
        $error = "OCR Error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OCR Test Page</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            line-height: 1.6;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
        }
        .success {
            color: green;
            font-weight: bold;
        }
        .error {
            color: red;
            font-weight: bold;
        }
        .panel {
            border: 1px solid #ddd;
            padding: 15px;
            margin: 15px 0;
            border-radius: 5px;
        }
        .image-preview {
            max-width: 400px;
            max-height: 400px;
            margin: 10px 0;
        }
        textarea {
            width: 100%;
            min-height: 200px;
            margin: 10px 0;
            padding: 8px;
            font-family: monospace;
        }
        code {
            background-color: #f5f5f5;
            padding: 2px 5px;
            border-radius: 3px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>OCR Test Page</h1>
        <p>This page tests if Tesseract OCR is properly installed and can extract text from images.</p>
        
        <?php
        try {
            echo "<h2>Tesseract Status:</h2>";
            
            // Check if tesseract executable exists
            if (file_exists($tesseractPath)) {
                echo "<p class='success'>✓ Tesseract executable found at: <code>" . htmlspecialchars($tesseractPath) . "</code></p>";
                
                // Check if tessdata directory exists (required for languages)
                $tessDataPath = dirname($tesseractPath) . '/tessdata';
                if (file_exists($tessDataPath)) {
                    echo "<p class='success'>✓ Tessdata directory found</p>";
                } else {
                    echo "<p class='error'>⚠ Tessdata directory missing! Language files must be in: <code>" . htmlspecialchars($tessDataPath) . "</code></p>";
                }
                
                // Try to get version info
                $tesseract = new TesseractOCR();
                $tesseract->executable($tesseractPath);
                try {
                    $version = $tesseract->version();
                    echo "<p class='success'>✓ Tesseract version: <code>" . htmlspecialchars($version) . "</code></p>";
                } catch (Exception $ve) {
                    echo "<p class='error'>⚠ Error getting version: " . $ve->getMessage() . "</p>";
                }
            } else {
                echo "<p class='error'>⚠ Tesseract executable NOT found at: <code>" . htmlspecialchars($tesseractPath) . "</code></p>";
                
                // Check if the directory exists
                if (!file_exists(dirname($tesseractPath))) {
                    echo "<p>The directory doesn't exist. Creating it now...</p>";
                    mkdir(dirname($tesseractPath), 0755, true);
                    echo "<p>Directory created at: <code>" . htmlspecialchars(dirname($tesseractPath)) . "</code></p>";
                    echo "<p>Please download Tesseract OCR and place the files in this directory.</p>";
                    echo "<p>You need at minimum:</p>";
                    echo "<ul>";
                    echo "<li><code>tesseract.exe</code> - The main executable</li>";
                    echo "<li><code>tessdata/</code> folder - With language data files</li>";
                    echo "<li>Required DLL files</li>";
                    echo "</ul>";
                } else {
                    echo "<p>The directory exists but tesseract.exe is missing.</p>";
                    echo "<h3>Files in the directory:</h3>";
                    echo "<ul>";
                    foreach (scandir(dirname($tesseractPath)) as $file) {
                        echo "<li>" . htmlspecialchars($file) . "</li>";
                    }
                    echo "</ul>";
                }
            }
        } catch (Exception $e) {
            echo "<h2>Error:</h2>";
            echo "<p class='error'>" . htmlspecialchars($e->getMessage()) . "</p>";
        }
        ?>
        
        <div class="panel">
            <h2>Upload an Image to Extract Text</h2>
            <form method="post" enctype="multipart/form-data">
                <input type="file" name="image" accept="image/*" required>
                <button type="submit">Extract Text</button>
            </form>
        </div>
        
        <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
            <div class="panel">
                <h2>OCR Results</h2>
                
                <?php if ($success): ?>
                    <h3>Uploaded Image:</h3>
                    <?php if (!empty($uploadedFile)): ?>
                        <img src="uploads/temp/<?php echo htmlspecialchars($uploadedFile); ?>" class="image-preview">
                    <?php endif; ?>
                    
                    <h3>Extracted Text:</h3>
                    <textarea readonly><?php echo htmlspecialchars($extractedText); ?></textarea>
                    
                    <p class="success">Text extraction completed successfully!</p>
                <?php else: ?>
                    <p class="error"><?php echo htmlspecialchars($error); ?></p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
        <div class="panel">
            <h3>Troubleshooting Tips:</h3>
            <ul>
                <li>Make sure Tesseract is properly installed in the specified path</li>
                <li>Tesseract requires language data files in a <code>tessdata/</code> folder in the same directory</li>
                <li>OCR works best on typed text rather than handwritten text</li>
                <li>For best results, use clear, high-contrast images</li>
            </ul>
        </div>
    </div>
</body>
</html>