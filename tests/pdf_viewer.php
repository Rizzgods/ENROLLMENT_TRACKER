<?php
require_once __DIR__ . '/../chatbot/config.php';
require_once __DIR__ . '/../chatbot/utils/file_validator.php';

// PDF viewer and analyzer for testing purposes
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Function to analyze PDF filename
function analyzePdfFilename($filename) {
    // Remove extension and convert to lowercase
    $name = strtolower(pathinfo($filename, PATHINFO_FILENAME));
    
    // Debug information
    $debug = [];
    $debug[] = "Analyzing filename: '{$name}'";
    
    // Define document type patterns - expanded with more variations
    $patterns = [
        'Form 138 / Report Card' => ['form138', 'form 138', 'form_138', 'report', 'card', 'f138', '138'],
        'Form 137 / Permanent Record' => ['form137', 'form 137', 'form_137', 'permanent', 'record', 'f137', '137'],  // Added Form 137
        'Certificate of Good Moral Character' => ['good moral', 'good_moral', 'goodmoral', 'moral', 'character', 'gmc'],
        'PSA Birth Certificate' => ['birth', 'certificate', 'psa', 'nso', 'birth_certificate'],
        'ID Photo' => ['id', 'photo', 'picture', 'id_pic', 'idpic'],
        'Transcript of Records' => ['transcript', 'tor', 'records', 'academic record', 'scholastic'],
        'Barangay Clearance' => ['brgy', 'barangay', 'clearance', 'brgy_clearance'],
        'Honorable Dismissal' => ['honor', 'dismissal', 'honorable', 'transfer', 'dismiss']
    ];
    
    $matches = [];
    $wordMatches = [];
    
    // Break filename into words for better matching
    $words = preg_split('/[\s_\-\.]+/', $name);
    $debug[] = "Words extracted: " . implode(', ', $words);
    
    // Check for each pattern
    foreach ($patterns as $docType => $keywords) {
        $docMatches = 0;
        $matchedKeywords = [];
        
        foreach ($keywords as $keyword) {
            // Check for direct keyword in filename
            if (strpos($name, $keyword) !== false) {
                $docMatches++;
                $matchedKeywords[] = $keyword;
            }
            
            // Check for word matches
            foreach ($words as $word) {
                // Full word match
                if ($word === $keyword) {
                    $docMatches += 2; // Give more weight to exact matches
                    $matchedKeywords[] = "$keyword (exact)";
                }
                // Partial word match for shorter keywords
                elseif (strlen($keyword) > 3 && strpos($word, $keyword) !== false) {
                    $docMatches++;
                    $matchedKeywords[] = "$keyword (in $word)";
                }
                // Number matching (e.g., "137" in "form137")
                elseif (is_numeric($keyword) && strpos($word, $keyword) !== false) {
                    $docMatches += 2;
                    $matchedKeywords[] = "$keyword (number in $word)";
                }
            }
        }
        
        if ($docMatches > 0) {
            $matches[$docType] = $docMatches;
            $wordMatches[$docType] = $matchedKeywords;
        }
    }
    
    return [
        'matches' => $matches,
        'wordMatches' => $wordMatches,
        'debug' => $debug
    ];
}

// Function to extract text from PDF using our file validator
function extractPdfText($filePath) {
    if (!file_exists($filePath)) {
        return [
            'success' => false,
            'error' => "Error: File not found - $filePath"
        ];
    }
    
    $fileExtension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
    $mimeType = mime_content_type($filePath);
    
    // More permissive check - accept files with PDF extension or PDF mime type
    if ($fileExtension != 'pdf' && $mimeType != 'application/pdf') {
        return [
            'success' => false,
            'error' => "Error: Not a PDF file (Detected mime type: $mimeType)"
        ];
    }
    
    // Create mock file array like $_FILES provides
    $file = [
        'name' => basename($filePath),
        'type' => $mimeType,
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
    <title>PDF Analyzer & Content Viewer</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 2em; line-height: 1.6; }
        .container { max-width: 1000px; margin: 0 auto; }
        .card { border: 1px solid #ddd; border-radius: 8px; padding: 20px; margin-bottom: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .results { border-top: 1px solid #eee; margin-top: 20px; padding-top: 10px; }
        .match { background-color: #e9f7ef; padding: 10px; margin-bottom: 5px; border-radius: 4px; }
        .match-keywords { font-size: 0.9em; color: #666; margin-top: 5px; }
        .match-keyword { background: #f0f0f0; padding: 2px 5px; margin: 2px; border-radius: 3px; display: inline-block; }
        .debug { background-color: #f8f9fa; padding: 10px; border-left: 4px solid #ddd; margin: 10px 0; font-family: monospace; }
        h2 { color: #2874A6; }
        .tabs { display: flex; margin-bottom: -1px; }
        .tab { padding: 10px 20px; cursor: pointer; background: #f1f1f1; border: 1px solid #ddd; border-bottom: none; margin-right: 5px; border-radius: 5px 5px 0 0; }
        .tab.active { background: white; border-bottom: 1px solid white; }
        .tab-content { border: 1px solid #ddd; padding: 20px; border-radius: 0 5px 5px 5px; }
        pre { background: #f5f5f5; padding: 1em; border: 1px solid #ddd; overflow-x: auto; white-space: pre-wrap; max-height: 400px; overflow-y: auto; }
        .info { background-color: #e8f4fd; border-left: 4px solid #2196F3; padding: 12px; margin: 10px 0; }
        .warning { background-color: #fff3e0; border-left: 4px solid #FF9800; padding: 12px; margin: 10px 0; }
        .error { background-color: #ffebee; border-left: 4px solid #F44336; padding: 12px; margin: 10px 0; }
        .success { background-color: #e8f5e9; border-left: 4px solid #4CAF50; padding: 12px; margin: 10px 0; }
        .btn { background: #4CAF50; color: white; border: none; padding: 10px 15px; cursor: pointer; border-radius: 4px; font-size: 16px; }
        .btn:hover { background: #388E3C; }
        .btn-blue { background: #2196F3; }
        .btn-blue:hover { background: #0b7dda; }
    </style>
    <script>
        // Simple tab functionality
        function showTab(tabId) {
            // Hide all tab content
            const tabContents = document.querySelectorAll('.tab-content');
            tabContents.forEach(content => { content.style.display = 'none'; });
            
            // Remove active class from all tabs
            const tabs = document.querySelectorAll('.tab');
            tabs.forEach(tab => { tab.classList.remove('active'); });
            
            // Show the selected tab content and mark tab as active
            document.getElementById(tabId).style.display = 'block';
            document.getElementById('tab-' + tabId).classList.add('active');
        }
        
        // Initialize tabs when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            showTab('filename-analysis');
        });
    </script>
</head>
<body>
    <div class="container">
        <h1>PDF Analyzer & Content Viewer</h1>
        <p>This tool helps analyze PDFs by extracting text content and analyzing filenames.</p>
        
        <div class="card">
            <h2>Upload a PDF</h2>
            <div class="info">
                <p>Upload a PDF file to analyze its content and filename.</p>
            </div>
            
            <form method="post" enctype="multipart/form-data">
                <p>
                    <input type="file" name="pdfFile" accept=".pdf">
                </p>
                <p>
                    <button type="submit" name="analyze" class="btn">Analyze PDF</button>
                </p>
            </form>
            
            <p>OR</p>
            
            <form method="post" enctype="multipart/form-data">
                <div></div>
                    <h3>Enter a filename to test (filename analysis only):</h3>
                    <input type="text" name="filename" placeholder="Form 137.pdf" style="width: 300px; padding: 8px;">
                </div>
                <p></p>
                    <button type="submit" name="analyzeFilename" class="btn btn-blue">Analyze Filename</button>
                </p>
            </form>
        </div>

        <?php
        // Process uploaded PDF file or filename
        if (isset($_POST['analyze']) && isset($_FILES['pdfFile']) && $_FILES['pdfFile']['error'] === UPLOAD_ERR_OK) {
            $filename = $_FILES['pdfFile']['name'];
            $filePath = $_FILES['pdfFile']['tmp_name'];
            
            // Get both content and filename analysis
            $textResult = extractPdfText($filePath);
            $filenameResult = analyzePdfFilename($filename);
            
            // Display results with tabs
            echo '<div class="card results">';
            echo '<h2>Analysis Results for: ' . htmlspecialchars($filename) . '</h2>';
            
            echo '<div class="tabs">';
            echo '<div id="tab-content-extraction" class="tab active" onclick="showTab(\'content-extraction\')">Content Extraction</div>';
            echo '<div id="tab-filename-analysis" class="tab" onclick="showTab(\'filename-analysis\')">Filename Analysis</div>';
            echo '</div>';
            
            // Content Extraction Tab
            echo '<div id="content-extraction" class="tab-content">';
            
            if ($textResult['success']) {
                echo '<div class="success">';
                echo '<p>Text extraction successful! (completed in ' . $textResult['exec_time'] . ' seconds)</p>';
                echo '</div>';
                
                // Show if content was cleaned
                if ($textResult['raw_text'] !== $textResult['cleaned_text']) {
                    echo '<div class="warning">';
                    echo '<p>The text was cleaned because explanatory content was detected.</p>';
                    echo '</div>';
                }
                
                echo '<h3>Extracted Text:</h3>';
                echo '<pre>' . htmlspecialchars($textResult['cleaned_text']) . '</pre>';
                
                // Only show raw text if different
                if ($textResult['raw_text'] !== $textResult['cleaned_text']) {
                    echo '<details>';
                    echo '<summary>Show Raw API Response</summary>';
                    echo '<pre>' . htmlspecialchars($textResult['raw_text']) . '</pre>';
                    echo '</details>';
                }
            } else {
                echo '<div class="error">';
                echo '<p>Text extraction failed: ' . htmlspecialchars($textResult['error']) . '</p>';
                echo '</div>';
            }
            
            echo '</div>'; // End content extraction tab
            
            // Filename Analysis Tab
            echo '<div id="filename-analysis" class="tab-content" style="display:none;">';
            
            // Show debug info
            echo '<div class="debug">';
            echo '<h3>Debug Information</h3>';
            foreach ($filenameResult['debug'] as $line) {
                echo htmlspecialchars($line) . "<br>";
            }
            echo '</div>';
            
            if (empty($filenameResult['matches'])) {
                echo "<p>No document type patterns matched. Try renaming your file to include keywords like 'form137' or 'report card'.</p>";
            } else {
                echo "<p>Possible document types:</p>";
                arsort($filenameResult['matches']); // Sort by match count
                foreach ($filenameResult['matches'] as $docType => $count) {
                    echo "<div class='match'>";
                    echo "<strong>{$docType}</strong> (confidence score: {$count})";
                    
                    // Show matched keywords
                    if (!empty($filenameResult['wordMatches'][$docType])) {
                        echo "<div class='match-keywords'>Matched keywords: ";
                        foreach ($filenameResult['wordMatches'][$docType] as $keyword) {
                            echo "<span class='match-keyword'>" . htmlspecialchars($keyword) . "</span>";
                        }
                        echo "</div>";
                    }
                    
                    echo "</div>";
                }
            }
            
            echo '</div>'; // End filename analysis tab
            
            echo '</div>'; // End card
            
        } elseif (isset($_POST['analyzeFilename']) && !empty($_POST['filename'])) {
            // Filename-only analysis
            $filename = $_POST['filename'];
            $filenameResult = analyzePdfFilename($filename);
            
            echo '<div class="card results">';
            echo '<h2>Filename Analysis Results for: ' . htmlspecialchars($filename) . '</h2>';
            
            // Show debug info
            echo '<div class="debug">';
            echo '<h3>Debug Information</h3>';
            foreach ($filenameResult['debug'] as $line) {
                echo htmlspecialchars($line) . "<br>";
            }
            echo '</div>';
            
            if (empty($filenameResult['matches'])) {
                echo "<p>No document type patterns matched. Try renaming your file to include keywords like 'form137' or 'report card'.</p>";
            } else {
                echo "<p>Possible document types:</p>";
                arsort($filenameResult['matches']); // Sort by match count
                foreach ($filenameResult['matches'] as $docType => $count) {
                    echo "<div class='match'>";
                    echo "<strong>{$docType}</strong> (confidence score: {$count})";
                    
                    // Show matched keywords
                    if (!empty($filenameResult['wordMatches'][$docType])) {
                        echo "<div class='match-keywords'>Matched keywords: ";
                        foreach ($filenameResult['wordMatches'][$docType] as $keyword) {
                            echo "<span class='match-keyword'>" . htmlspecialchars($keyword) . "</span>";
                        }
                        echo "</div>";
                    }
                    
                    echo "</div>";
                }
            }
            
            echo '</div>'; // End card
        }
        ?>
    </div>
</body>
</html>
