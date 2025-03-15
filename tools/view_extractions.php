<?php
/**
 * Document Text Extraction Viewer
 * 
 * This tool displays extracted text from uploaded documents,
 * allowing easy review and debugging of text extraction results.
 */

// Set up the page
$title = "Document Text Extraction Viewer";
require_once __DIR__ . '/../include/initialize.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Document Text Extraction Viewer</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; padding: 0; background: #f9f9f9; }
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        h1 { color: #2c3e50; border-bottom: 2px solid #3498db; padding-bottom: 10px; }
        .filters { background: #fff; padding: 15px; border-radius: 4px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); margin-bottom: 20px; }
        select, input[type="date"] { padding: 8px; margin-right: 10px; border: 1px solid #ddd; border-radius: 4px; }
        button { background: #3498db; color: white; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer; }
        button:hover { background: #2980b9; }
        .extraction-list { margin-top: 20px; }
        .extraction-item { background: white; margin-bottom: 15px; padding: 15px; border-radius: 4px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .extraction-header { display: flex; justify-content: space-between; margin-bottom: 10px; }
        .extraction-header h3 { margin: 0; color: #3498db; }
        .extraction-header .date { color: #7f8c8d; font-size: 0.9em; }
        .document-info { background: #f5f5f5; padding: 10px; border-left: 3px solid #3498db; margin-bottom: 10px; font-size: 0.9em; }
        .document-info span { margin-right: 15px; }
        pre { background: #f8f9fa; padding: 15px; border-radius: 4px; overflow-x: auto; white-space: pre-wrap; font-size: 14px; line-height: 1.5; }
        .text-content { max-height: 300px; overflow-y: auto; border: 1px solid #eee; }
        .empty { color: #7f8c8d; text-align: center; padding: 30px; }
        .keyword { background: #ffe6b3; padding: 2px 5px; border-radius: 3px; font-size: 0.85em; margin-right: 5px; }
        .toggle-button { background: #95a5a6; font-size: 12px; padding: 3px 8px; margin-left: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Document Text Extraction Viewer</h1>
        
        <div class="filters">
            <form method="GET">
                <label for="doc_type">Document Type:</label>
                <select name="doc_type" id="doc_type">
                    <option value="">All Types</option>
                    <option value="form_138" <?php echo isset($_GET['doc_type']) && $_GET['doc_type'] == 'form_138' ? 'selected' : ''; ?>>Form 138 / Report Card</option>
                    <option value="good_moral" <?php echo isset($_GET['doc_type']) && $_GET['doc_type'] == 'good_moral' ? 'selected' : ''; ?>>Good Moral Certificate</option>
                    <option value="psa_birthCert" <?php echo isset($_GET['doc_type']) && $_GET['doc_type'] == 'psa_birthCert' ? 'selected' : ''; ?>>PSA Birth Certificate</option>
                    <option value="tor" <?php echo isset($_GET['doc_type']) && $_GET['doc_type'] == 'tor' ? 'selected' : ''; ?>>Transcript of Records</option>
                    <option value="id_pic" <?php echo isset($_GET['doc_type']) && $_GET['doc_type'] == 'id_pic' ? 'selected' : ''; ?>>ID Picture</option>
                    <option value="Brgy_clearance" <?php echo isset($_GET['doc_type']) && $_GET['doc_type'] == 'Brgy_clearance' ? 'selected' : ''; ?>>Barangay Clearance</option>
                    <option value="honor_dismissal" <?php echo isset($_GET['doc_type']) && $_GET['doc_type'] == 'honor_dismissal' ? 'selected' : ''; ?>>Honor Dismissal</option>
                </select>
                
                <label for="date">Date:</label>
                <input type="date" name="date" id="date" value="<?php echo isset($_GET['date']) ? $_GET['date'] : date('Y-m-d'); ?>">
                
                <button type="submit">Filter</button>
                <button type="submit" name="recent" value="1">Show Recent (10)</button>
            </form>
        </div>

        <div class="extraction-list">
            <?php
            // Get filter values
            $docType = isset($_GET['doc_type']) ? $_GET['doc_type'] : '';
            $date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
            $showRecent = isset($_GET['recent']) && $_GET['recent'] == 1;
            
            // Determine which log file to read based on filters
            if (!empty($docType) && !$showRecent) {
                // Show logs for a specific document type
                $logFile = __DIR__ . '/../logs/extractions/' . $docType . '_extractions.log';
            } else {
                // Show general daily log
                $logFile = __DIR__ . '/../logs/document_text_' . $date . '.log';
            }
            
            if (file_exists($logFile)) {
                // Read the log file
                $content = file_get_contents($logFile);
                
                // Split into individual extraction records
                $pattern = $showRecent ? '/\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\](.*?)(?=\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\]|$)/s' : 
                                        '/\[' . str_replace('-', '\-', $date) . ' \d{2}:\d{2}:\d{2}\](.*?)(?=\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\]|$)/s';
                
                preg_match_all($pattern, $content, $matches);
                
                // Process matches
                $extractions = [];
                foreach ($matches[0] as $match) {
                    // Extract the date/time
                    preg_match('/\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\]/', $match, $timeMatch);
                    $time = isset($timeMatch[1]) ? $timeMatch[1] : '';
                    
                    // Extract document type and filename
                    preg_match('/Document Type: (.*?), File: (.*?)$/m', $match, $docMatch);
                    $docTypeName = isset($docMatch[1]) ? $docMatch[1] : 'Unknown';
                    $fileName = isset($docMatch[2]) ? $docMatch[2] : 'Unknown';
                    
                    // Extract the text content
                    preg_match('/TEXT:(.*?)(?=\n\n|$)/s', $match, $textMatch);
                    $text = isset($textMatch[1]) ? trim($textMatch[1]) : '';
                    
                    // Add to extractions array
                    $extractions[] = [
                        'time' => $time,
                        'docType' => $docTypeName,
                        'fileName' => $fileName,
                        'text' => $text
                    ];
                }
                
                // Sort by most recent first
                usort($extractions, function($a, $b) {
                    return strtotime($b['time']) - strtotime($a['time']);
                });
                
                // Limit to 10 if showing recent
                if ($showRecent) {
                    $extractions = array_slice($extractions, 0, 10);
                }
                
                // Output extractions
                if (count($extractions) > 0) {
                    foreach ($extractions as $i => $extraction) {
                        echo '<div class="extraction-item">';
                        echo '<div class="extraction-header">';
                        echo '<h3>' . htmlspecialchars($extraction['docType']) . '</h3>';
                        echo '<div class="date">' . htmlspecialchars($extraction['time']) . '</div>';
                        echo '</div>';
                        
                        echo '<div class="document-info">';
                        echo '<span><strong>File:</strong> ' . htmlspecialchars($extraction['fileName']) . '</span>';
                        echo '</div>';
                        
                        echo '<div class="text-content">';
                        echo '<pre>' . htmlspecialchars($extraction['text']) . '</pre>';
                        echo '</div>';
                        echo '</div>';
                    }
                } else {
                    echo '<div class="empty">No extraction records found for the selected criteria.</div>';
                }
            } else {
                echo '<div class="empty">No log file found for the selected date.</div>';
            }
            ?>
        </div>
    </div>

    <script>
        // Add JavaScript for any interactive features
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-submit form when date or document type changes
            document.getElementById('doc_type').addEventListener('change', function() {
                this.form.submit();
            });
            
            document.getElementById('date').addEventListener('change', function() {
                this.form.submit();
            });
        });
    </script>
</body>
</html>
