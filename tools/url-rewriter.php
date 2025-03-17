<?php
// filepath: /home/sarbada/Desktop/quiz-application (Copy)/tools/url-rewriter.php

/**
 * URL Rewriter Script
 * This script scans all view files and replaces hardcoded URLs with URL helper function calls
 */

// Configuration
$viewsDirectory = __DIR__ . '/../src/Views';
$backupDirectory = __DIR__ . '/../backups/views_' . date('Y-m-d_H-i-s');

// Create backup directory
if (!is_dir($backupDirectory)) {
    mkdir($backupDirectory, 0755, true);
    echo "Created backup directory: $backupDirectory\n";
}

// Get all PHP files in views directory recursively
$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($viewsDirectory)
);

$viewFiles = [];
foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $viewFiles[] = $file->getPathname();
    }
}

echo "Found " . count($viewFiles) . " view files to process.\n";

// Regular expressions for finding URLs
$patterns = [
    // href="/some/path"
    '~href\s*=\s*["\'](\/)([^"\']+)["\']~i' => 'href="<?= $url(\'$2\') ?>"',
    
    // src="/some/path"
    '~src\s*=\s*["\'](\/)([^"\']+)["\']~i' => 'src="<?= $url(\'$2\') ?>"',
    
    // action="/some/path"
    '~action\s*=\s*["\'](\/)([^"\']+)["\']~i' => 'action="<?= $url(\'$2\') ?>"',
    
    // url: '/some/path'
    '~url\s*:\s*["\'](\/)([^"\']+)["\']~i' => 'url: \'<?= $url(\'$2\') ?>\'',
    
    // xmlhttp.open("GET", "/some/path"
    '~xmlhttp\.open\(["\'](?:GET|POST)["\'],\s*["\'](\/)([^"\']+)["\']~i' => 'xmlhttp.open("$1", "<?= $url(\'$2\') ?>"',
    
    // fetch('/some/path')
    '~fetch\(["\'](\/)([^"\']+)["\']~i' => 'fetch(\'<?= $url(\'$2\') ?>\'',
    
    // $.ajax({ url: '/some/path'
    '~\$\.ajax\(\{\s*url\s*:\s*["\'](\/)([^"\']+)["\']~i' => '$.ajax({ url: \'<?= $url(\'$2\') ?>\'',

    // form.attr('action', '/some/path')
    '~\.attr\([\'"]action[\'"]\s*,\s*[\'"](\/)([^"\']+)[\'"]\)~i' => '.attr(\'action\', \'<?= $url(\'$2\') ?>\')',
    
    // window.location = '/some/path'
    '~window\.location(?:\.href)?\s*=\s*["\'](\/)([^"\']+)["\']~i' => 'window.location = \'<?= $url(\'$2\') ?>\'',
    
    // location.href = '/some/path'
    '~location\.href\s*=\s*["\'](\/)([^"\']+)["\']~i' => 'location.href = \'<?= $url(\'$2\') ?>\'',
    
    // axios.get('/some/path')
    '~axios\.(?:get|post|put|delete)\(["\'](\/)([^"\']+)["\']~i' => 'axios.$1(\'<?= $url(\'$2\') ?>\'',
];

$filesModified = 0;
$totalReplacements = 0;

foreach ($viewFiles as $file) {
    $content = file_get_contents($file);
    $originalContent = $content;
    $replacementsInFile = 0;
    
    foreach ($patterns as $pattern => $replacement) {
        $matches = [];
        if (preg_match_all($pattern, $content, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                // Skip if it already looks like it's using the URL helper
                if (strpos($match[0], '$url(') !== false || strpos($match[0], 'url(') !== false) {
                    continue;
                }
                $replacementsInFile++;
            }
        }
        
        $content = preg_replace($pattern, $replacement, $content);
    }
    
    if ($originalContent !== $content) {
        // Create backup of original file
        $relativePath = str_replace($viewsDirectory, '', $file);
        $backupPath = $backupDirectory . $relativePath;
        $backupDir = dirname($backupPath);
        
        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }
        
        file_put_contents($backupPath, $originalContent);
        
        // Save modified content
        file_put_contents($file, $content);
        
        $filesModified++;
        $totalReplacements += $replacementsInFile;
        echo "Modified: $file ($replacementsInFile replacements)\n";
    }
}

echo "\nSummary:\n";
echo "Files modified: $filesModified\n";
echo "Total URL replacements: $totalReplacements\n";
echo "Backups stored in: $backupDirectory\n";
