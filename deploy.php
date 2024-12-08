<?php
if(isset($_GET['action'])) {
    if($_GET['action'] == 'backup') {
        // Recursive function to delete files and directories
        // Helper function to delete a directory recursively
        function deleteDirectory($dir) {
            $items = scandir($dir);
            foreach ($items as $item) {
                if ($item === '.' || $item === '..') {
                    continue;
                }

                $fullPath = $dir . DIRECTORY_SEPARATOR . $item;
                if (is_dir($fullPath)) {
                    deleteDirectory($fullPath);
                } else {
                    unlink($fullPath);
                }
            }

            // Remove the directory itself
            rmdir($dir);
        }


        function cleanDirectory($dir, $excludedItems) {
            $items = scandir($dir);
            
            foreach ($items as $item) {
                // Skip current and parent directory references
                if ($item === '.' || $item === '..') {
                    continue;
                }

                $fullPath = $dir . DIRECTORY_SEPARATOR . $item;

                // Skip excluded files and directories
                if (in_array($item, $excludedItems)) {
                    continue;
                }

                // If it's a directory, recursively clean it
                if (is_dir($fullPath)) {
                    deleteDirectory($fullPath);
                } else {
                    // Otherwise, delete the file
                    unlink($fullPath);
                }
            }
        }


        // Define the directory to clean
        $directory = '.';

        // Exclude these items
        $excludedItems = ['config.inc.php', 'data', 'tmp', 'deploy.php'];

        /**
         * Recursively zip a directory.
         *
         * @param string $source Path to the directory to zip.
         * @param string $destination Path to the output ZIP file.
         * @return bool True if successful, false otherwise.
         */
        function zipDirectory($source, $destination)
        {
            if (!extension_loaded('zip') || !file_exists($source)) {
                return false;
            }

            $zip = new ZipArchive();
            if (!$zip->open($destination, ZipArchive::CREATE | ZipArchive::OVERWRITE)) {
                return false;
            }

            $source = realpath($source);

            if (is_dir($source)) {
                $iterator = new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS);
                $files = new RecursiveIteratorIterator($iterator, RecursiveIteratorIterator::SELF_FIRST);

                foreach ($files as $file) {
                    $filePath = $file->getRealPath();
                    $relativePath = substr($filePath, strlen($source) + 1);

                    if ($file->isDir()) {
                        $zip->addEmptyDir($relativePath);
                    } elseif ($file->isFile() && basename($filePath) != 'git_latest_deploy.zip') {
                        echo "Backupping-up {$filePath}";
                        $zip->addFile($filePath, $relativePath);
                    }
                }
            } elseif (is_file($source)) {
                $zip->addFile($source, basename($source));
            }

            return $zip->close();
        }

        // Example usage
        $sourceDir = __DIR__; // Change this to your target directory
        $outputZip = './data/tmp/git_latest_deploy.zip';

        if (zipDirectory($sourceDir, $outputZip)) {
            cleanDirectory($directory, $excludedItems);
        } else {
            echo "Failed to zip directory.\n";
        }

        echo "Directory cleaned successfully!";
    }

    else if($_GET['action'] == 'restore') {
        $zipFile = './data/tmp/git_latest_deploy.zip';
        $extractTo = './'; // Extract to the current directory

        // Create a ZipArchive object
        $zip = new ZipArchive();

        // Open the zip file
        if ($zip->open($zipFile) === TRUE) {
            // Loop through all files in the zip
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $zipEntry = $zip->getNameIndex($i);
                
                // Extract the file
                $zip->extractTo($extractTo, $zipEntry);
            }
            // Close the zip file
            $zip->close();
            echo "Unzip successful, with 'data' folder excluded.\n";
        } else {
            echo "Failed to open the zip file.\n";
        }
    }
}

mkdir("info");