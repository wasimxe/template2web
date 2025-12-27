<?php
// Directories
$zipDir = 'zip/';
$baseDir = 'brands/tanxe.com/';
$projects = glob($zipDir . '*.zip');

// Function to check if text is valid (not only numbers or special characters)
function isValidText($text) {
    return preg_match('/[a-zA-Z]/', $text);
}

// Function to find existing key by value
function findKeyByValue($value, &$dictionary) {
    return array_search($value, $dictionary);
}

// Function to generate a unique key
function generateUniqueKey($baseKey, &$dictionary) {
    // Replace apostrophes with underscores or remove them
    $baseKey = str_replace("'", "", $baseKey);

    $key = $baseKey;
    $counter = 1;

    while (array_key_exists($key, $dictionary)) {
        $key = $baseKey . $counter;
        $counter++;
    }

    return $key;
}

// Function to replace text nodes with PHP variables
function replaceTextWithVariable($matches) {
    global $dictionary, $textCounter;

    $text = trim($matches[0]);

    // Skip if the text is empty
    if (empty($text)) {
        return $matches[0];
    }

    // Check for string length greater than 20
    if (strlen($text) > 20) {
        $key = 'text' . $textCounter++;
        $dictionary[$key] = $text;
    }
    // Check if it's an email
    elseif (filter_var($text, FILTER_VALIDATE_EMAIL)) {
        $key = 'email';
        $dictionary[$key] = $text;
    }
    // Check if it's a phone number (simple regex pattern for illustration)
    elseif (preg_match('/^\+?[0-9\s\-\(\)]+$/', $text)) {
        $key = 'phone_no';
        $dictionary[$key] = $text;
    }
    // Validate other text
    elseif (isValidText($text)) {
        $existingKey = findKeyByValue($text, $dictionary);

        if ($existingKey) {
            // Reuse the existing key
            $key = $existingKey;
        } else {
            $baseKey = strtolower(str_replace(' ', '_', $text));
            $key = generateUniqueKey($baseKey, $dictionary);

            if (!array_key_exists($key, $dictionary)) {
                $dictionary[$key] = $text;
            }
        }
    } else {
        // If it's not valid text, return the original match
        return $matches[0];
    }

    if (stripos($matches[0], '<title>') === false) {
        return "<span data-key='$key'><?php echo \$dictionary['$key']; ?></span>";
    } else {
        return "<?php echo \$dictionary['$key']; ?>";
    }
}

// Function to replace .html links with .php links
function replaceHtmlLinks($content) {
    return preg_replace('/<a\s+href="([^"]+)\.html"/i', '<a href="$1.php"', $content);
}

// Function to process .html files
function processHtmlFiles($dir) {
    global $dictionary, $textCounter;

    // Scan .html files in the directory and its subdirectories
    $htmlFiles = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    foreach ($htmlFiles as $htmlFile) {
        if ($htmlFile->isFile() && $htmlFile->getExtension() === 'html') {
            $htmlContent = file_get_contents($htmlFile);

            // Replace text nodes
           $pattern = '/(?<=>)([^<]+)(?=<)(?!<\/title>)/i';
            $modifiedContent = preg_replace_callback($pattern, 'replaceTextWithVariable', $htmlContent);

            // Replace .html links with .php links
            $modifiedContent = replaceHtmlLinks($modifiedContent);

            // Add the include statement for lang.php at the beginning of the output
            $includeStatement = "<?php session_start();\ninclude('lang.php'); ?>\n";
            $modifiedContent = $includeStatement . $modifiedContent;

            // Add the script inclusion before the closing body tag
            $modifiedContent = str_replace('</body>', "\n<?php if (isset(\$_SESSION['loggedin']) && \$_SESSION['loggedin'] === true) { ?>\n<script src=\"js/edittext.js\"></script>\n<?php } ?>\n</body>", $modifiedContent);

            // Determine the output filename
            $outputFile = str_replace('.html', '.php', $htmlFile);

            // Save the modified content as a PHP file
            file_put_contents($outputFile, $modifiedContent);

            // Delete the original .html file
            unlink($htmlFile);

            echo "Processed " . basename($htmlFile) . " to " . basename($outputFile) . "<hr>";
        }
    }

    // Generate lang.php file with dictionary array
    $langPhpContent = "<?php\n\$dictionary = " . var_export($dictionary, true) . ";\n?>";
    file_put_contents($dir . 'lang.php', $langPhpContent);

    // Copy edittext.js to js directory
    if (!file_exists($dir . 'js')) {
        mkdir($dir . 'js', 0777, true);
    }
    copy('edittext.js', $dir . 'js/edittext.js');

    // Copy admin.php and update_lang.php to the project directory
    copy('admin.php', $dir . 'admin.php');
    copy('logout.php', $dir . 'logout.php');
    copy('update_lang.php', $dir . 'update_lang.php');
    copy('upload_image.php', $dir . 'upload_image.php');
}

// Function to move files from a directory to another directory
function moveFilesToDirectory($sourceDir, $targetDir) {
    $files = scandir($sourceDir);
    foreach ($files as $file) {
        if ($file !== '.' && $file !== '..') {
            if (is_dir($sourceDir . $file)) {
                rename($sourceDir . $file, $targetDir . $file);
            } else {
                if (!file_exists($targetDir)) {
                    mkdir($targetDir, 0777, true);
                }
                rename($sourceDir . $file, $targetDir . $file);
            }
        }
    }
}

// Process each zip file
foreach ($projects as $zipFile) {
    $projectName = basename($zipFile, '.zip');
    $projectDir = $baseDir . $projectName . '/';
    $tempDir = $baseDir . $projectName . '_temp/';

    // Unzip the project
    $zip = new ZipArchive;
    if ($zip->open($zipFile) === TRUE) {
        // Create temporary directory for extraction
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0777, true);
        }

        // Extract files to the temporary directory
        $zip->extractTo($tempDir);
        $zip->close();

        echo "Unzipped $zipFile to $tempDir<hr>";

        // If there's only one directory inside tempDir, move its contents
        $filesInTempDir = scandir($tempDir);
        if (count($filesInTempDir) === 3 && is_dir($tempDir . $filesInTempDir[2])) { // [2] is the first actual directory or file
            $innerDir = $filesInTempDir[2];
            moveFilesToDirectory($tempDir . $innerDir . '/', $projectDir);
            rmdir($tempDir . $innerDir);
        } else {
            moveFilesToDirectory($tempDir, $projectDir);
        }

        // Remove the temporary directory
        rmdir($tempDir);

        // Initialize dictionary array for this project
        global $dictionary, $textCounter;
        $dictionary = [];
        $textCounter = 1;

        // Process HTML files in the project directory
        processHtmlFiles($projectDir);

        echo "Processed all .html files in $projectDir<hr>";
    } else {
        echo "Failed to unzip $zipFile<hr>";
    }
}

echo "Processing complete. Check the output directory for results.<hr>";
