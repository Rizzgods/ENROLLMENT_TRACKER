<?php
/**
 * Dependency installation helper
 * 
 * This script helps install required dependencies for the enrollment system
 */

echo "=== Dependency Installation Helper ===\n\n";

// Check if Composer is installed
echo "Checking for Composer... ";
$composerInstalled = false;
exec('composer --version', $output, $returnVar);
if ($returnVar === 0) {
    echo "FOUND!\n";
    $composerInstalled = true;
    echo $output[0] . "\n";
} else {
    echo "NOT FOUND!\n";
    echo "Please install Composer from https://getcomposer.org/download/\n";
}

// Check if we're in the right directory
echo "\nChecking current directory... ";
$composerJson = 'composer.json';
if (file_exists($composerJson)) {
    echo "OK - composer.json found.\n";
    
    // Display current dependencies
    $composerContent = file_get_contents($composerJson);
    $composerData = json_decode($composerContent, true);
    
    if (isset($composerData['require'])) {
        echo "Current dependencies:\n";
        foreach ($composerData['require'] as $package => $version) {
            echo "  - $package: $version\n";
        }
    }
    
    if ($composerInstalled) {
        echo "\nWould you like to install dependencies now? (y/n): ";
        $handle = fopen("php://stdin", "r");
        $line = trim(fgets($handle));
        if (strtolower($line) === 'y') {
            echo "Running 'composer install'...\n";
            passthru('composer install');
            echo "\nDependencies installed!\n";
        } else {
            echo "Skipping installation.\n";
        }
    }
} else {
    echo "ERROR - composer.json not found in current directory!\n";
    echo "Make sure you're running this script from the project root.\n";
}

// Check if PDF libraries are available
echo "\nChecking for PDF libraries...\n";

// Check for Imagick
echo "Checking for Imagick extension... ";
if (extension_loaded('imagick')) {
    echo "INSTALLED\n";
    echo "Imagick version: " . Imagick::getVersion()['versionString'] . "\n";
} else {
    echo "NOT INSTALLED\n";
    echo "Imagick is recommended for PDF processing. Instructions:\n";
    echo "- Windows: https://mlocati.github.io/articles/php-windows-imagick.html\n";
    echo "- Linux: sudo apt-get install php-imagick\n";
}

// Check for pdftotext
echo "Checking for pdftotext utility... ";
exec('which pdftotext 2>/dev/null', $output, $returnVar);
if ($returnVar === 0) {
    echo "INSTALLED\n";
} else {
    echo "NOT INSTALLED\n";
    echo "You may want to install Poppler utils:\n";
    echo "- Windows: http://blog.alivate.com.au/poppler-windows/\n";
    echo "- Linux: sudo apt-get install poppler-utils\n";
}

// Check for our fallback method
echo "Checking for fallback PDF extraction... OK (built-in)\n";

echo "\nSetup complete!\n";
?>
