<?php
/**
 * @package		OpenCart
 * @author		Tecziq Solutions Pvt Ltd
 * @copyright	Copyright (c) 2019, Tecziq Solutions, Ltd. (https://www.tecziq.com/)
 * @license		https://opensource.org/licenses/GPL-3.0
 * @link		https://www.tecziq.com
*/

require_once('aws/aws-autoloader.php');

use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;
use Aws\S3\Transfer as S3Transfer;

/**
* S3 class
*/
class S3 {
    private $PDF_DIRECTORY;
    private $SDS_DIRECTORY;
    private $SDS_PARTS_PATH_PREFIX;
    private $SDS_TEMPORARY_DIRECTORY;
    private static $BUCKET_NAME;
    private $log;
    private $s3Client;
    private $config;
    private $allObjects;

    public function __construct($registry) {
        $this->PDF_DIRECTORY = 'pdfs/';
        $this->SDS_DIRECTORY = $this->PDF_DIRECTORY . 'msds/';
        $this->SDS_PARTS_PATH_PREFIX = $this->SDS_DIRECTORY . 'parts/';
        $this->SDS_TEMPORARY_DIRECTORY = DIR_IMAGE . $this->PDF_DIRECTORY . 'temp/';

        $this->config = $registry->get('config');

        $this->allObjects = [
                'folders' => [], 'files' => []
        ];

        $config_bucket = $this->config->get('config_s3_bucket');
        self::$BUCKET_NAME = $config_bucket ? $config_bucket : DEFAULT_BUCKET;

        $config_region = $this->config->get('config_s3_bucket_region');
        $this->s3Client = new S3Client([
                'version' => '2006-03-01',
                'region'  => $config_region ? $config_region : DEFAULT_REGION,
                'scheme' => $this->config->get('config_secure') ? 'https' : 'http'
        ]);

        // $this->s3Client->registerStreamWrapper();

        $this->log = new Log('s3.log');
    }

    function getAllBuckets() {
            try {
                    $result = $this->s3Client->listBuckets();
            } catch (S3Exception $e) {
                    $this->writeToLog($e->getMessage());
            }

            return $result ? $result['Buckets'] : [];
    }

    function getObjectAcl($key) {
            return $this->s3Client->getObjectAcl([
                    'Bucket' => self::$BUCKET_NAME,
                    'Key' => $key,
            ])['Grants'];
    }

    function getAllObjects($prefix = '', $search_key = '', $marker = '', $maxKeys = 0) {
            try {
                    $options = [
                        'Bucket' => self::$BUCKET_NAME,
                        'Delimiter' => '/',
                        'Prefix' => $prefix . $search_key
                    ];

                    if (!empty($marker)) {
                            $options['Marker'] = $marker;
                    }
                    
                    if ($maxKeys) {
                            $options['MaxKeys'] = $maxKeys;
                    }

                    $objectList = $this->s3Client->listObjects($options);

                    if ($objectList) {
                            if (isset($objectList['CommonPrefixes'])) {
                                    foreach ($objectList['CommonPrefixes'] as $folder) {
                                            $this->allObjects['folders'][] = str_replace($prefix, '', $folder['Prefix']);
                                    }
                            }

                            foreach ($objectList['Contents'] as $file) {
                                    $key = str_replace($prefix, '', $file['Key']);
                                    !$key || strstr($key, '.html') !== false ?: $this->allObjects['files'][] = $key;
                            }

                            if ($objectList['IsTruncated']) {
                                    return $this->getAllObjects($prefix, $search_key, $objectList['NextMarker']);
                            }
                    }
            } catch (S3Exception $e) {
                    $this->writeToLog($e->getMessage());
            }

            return $this->allObjects;
    }

    function createDirectoryObject($prefix, $directory) {
            try {
                    $result = $this->s3Client->putObject([
                            'ACL' => 'bucket-owner-full-control',
                            'Bucket' => self::$BUCKET_NAME,
                            'Key' => $prefix . $directory
                    ]);
            } catch (S3Exception $e) {
                    $this->writeToLog($e->getMessage());
            }

            return $result ?? false;
    }

        /**
         * Upload original + generate all 4 variants (std, w, h, p)
         */
        public function putObject($prefix, $file, $file_name = '') {
        // remove old upload log at the very beginning
        @unlink(DIR_LOGS . 's3_upload.log');

        $result = false;

        try {
        $logFile = DIR_LOGS . 's3_upload.log';
        $key     = $prefix . ($file_name ? $file_name : $file['name']);
        $path    = $file['tmp_name']; 
        $type    = $file['type'];

        // --- 1. Upload original to S3 ---
        $result = $this->s3Client->putObject([
                'ACL'    => 'bucket-owner-full-control',
                'Bucket' => self::$BUCKET_NAME,
                'Key'    => $key,
                'ContentType' => $type,
                'Body'   => file_get_contents($path),
        ]);

        $msg  = date('Y-m-d H:i:s') . " | putObject() original uploaded\n";
        $msg .= "Bucket: " . self::$BUCKET_NAME . "\nKey: " . $key . "\n";
        $msg .= str_repeat('-', 60) . "\n";
        file_put_contents($logFile, $msg, FILE_APPEND);

        // --- 2. Copy original to DIR_IMAGE for variant generation ---
        $targetRelative = $prefix . ($file_name ? $file_name : $file['name']);
        $targetFull     = DIR_IMAGE . $targetRelative;

        if (!is_dir(dirname($targetFull))) {
                @mkdir(dirname($targetFull), 0755, true);
        }

        if ($path !== $targetFull) {
                @copy($path, $targetFull);
        }

        // --- 3. Generate eager 4‑variants for all static sizes ---
        foreach ($this->getEagerSizes() as $dim) {
                list($w, $h) = $dim;
                if ($w && $h) {
                $this->forceAllVariants($targetRelative, $w, $h, $type, $logFile);
                }
        }

        // --- 4. Remove local originals ---
        @unlink($targetFull);
        $this->cleanupEmptyDirs($targetFull);
        

        } catch (\Throwable $e) {
        file_put_contents(
                DIR_LOGS . 's3_error.log',
                date('Y-m-d H:i:s') . ' ' . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n",
                FILE_APPEND
        );
        throw $e;
        }

        return $result;
        }



        /**
         * Force creation of 4 variants (std, w, h, p)
         */
        private function forceAllVariants($relativePath, $w, $h, $type, $logFile) {

        // --- Prevent recursion: skip already-cache images ---
        if (strpos($relativePath, 'cache/') === 0) {
                return;
        }

        file_put_contents($logFile,
                date('Y-m-d H:i:s') . " | ENTERED forceAllVariants for {$relativePath} ({$w}x{$h})\n",
                FILE_APPEND
        );        

        $sourceFile = DIR_IMAGE . $relativePath;
        if (!is_file($sourceFile)) {
                file_put_contents($logFile, "DEBUG: sourceFile missing $sourceFile\n", FILE_APPEND);
                return;
        }

        $ext = strtolower(pathinfo($sourceFile, PATHINFO_EXTENSION));
        switch ($ext) {
                case 'gif': $src = imagecreatefromgif($sourceFile); break;
                case 'png': $src = imagecreatefrompng($sourceFile); break;
                default:    $src = imagecreatefromjpeg($sourceFile); break;
        }
        if (!$src) {
                file_put_contents($logFile, "DEBUG: GD could not open $sourceFile\n", FILE_APPEND);
                return;
        }

        $orig_w = imagesx($src);
        $orig_h = imagesy($src);
        file_put_contents($logFile, "DEBUG: orig size={$orig_w}x{$orig_h}\n", FILE_APPEND);

        $modes = ['', 'w', 'h', 'p'];

        foreach ($modes as $mode) {
                // --- Clean filename (no .jpg-300x300.jpg bug) ---
                $basename = pathinfo($relativePath, PATHINFO_FILENAME);
                $dirname  = dirname($relativePath);
                if ($dirname === '.') {
                $dirname = '';
                } else {
                $dirname .= '/';
                }

                $cacheRel = 'cache/' . $dirname . $basename . '-' . $w . 'x' . $h;
                if ($mode !== '') {
                $cacheRel .= $mode;
                }
                $cacheRel .= '.' . $ext;

                $cacheFull = DIR_IMAGE . $cacheRel;

                if (!is_dir(dirname($cacheFull))) {
                mkdir(dirname($cacheFull), 0755, true);
                }

                // --- Calculate target dimensions ---
                $dst_w = $w; 
                $dst_h = $h;
                if ($mode === 'w') {
                $dst_h = ($orig_w > 0) ? max(1, intval(($orig_h / $orig_w) * $w)) : 1;
                } elseif ($mode === 'h') {
                $dst_w = ($orig_h > 0) ? max(1, intval(($orig_w / $orig_h) * $h)) : 1;
                } elseif ($mode === 'p') {
                if ($orig_w > 0 && $orig_h > 0) {
                        $ratio = min($w / $orig_w, $h / $orig_h);
                        $dst_w = max(1, intval($orig_w * $ratio));
                        $dst_h = max(1, intval($orig_h * $ratio));
                }
                }

                file_put_contents($logFile, "DEBUG: mode=$mode => {$dst_w}x{$dst_h}, cacheRel=$cacheRel\n", FILE_APPEND);

                $dst = imagecreatetruecolor($dst_w, $dst_h);
                imagealphablending($dst, false);
                imagesavealpha($dst, true);

                if (!imagecopyresampled($dst, $src, 0, 0, 0, 0, $dst_w, $dst_h, $orig_w, $orig_h)) {
                file_put_contents($logFile, "DEBUG: imagecopyresampled failed mode=$mode\n", FILE_APPEND);
                imagedestroy($dst);
                continue;
                }

                // --- Save to disk ---
                if ($ext === 'gif')      imagegif($dst, $cacheFull);
                elseif ($ext === 'png')  imagepng($dst, $cacheFull, 9);
                else                     imagejpeg($dst, $cacheFull, 90);

                imagedestroy($dst);

                if (is_file($cacheFull)) {
                $this->s3Client->putObject([
                        'ACL'    => 'bucket-owner-full-control',
                        'Bucket' => self::$BUCKET_NAME,
                        'Key'    => $cacheRel,
                        'ContentType' => $type,
                        'Body'   => file_get_contents($cacheFull),
                ]);
                file_put_contents($logFile, "DEBUG: uploaded $cacheRel\n", FILE_APPEND);
                // Remove temp file
                @unlink($cacheFull);
                } else {
                file_put_contents($logFile, "DEBUG: cacheFull missing $cacheFull\n", FILE_APPEND);
                }
        }

        imagedestroy($src);
        $this->cleanupEmptyDirs(dirname($cacheFull));
        }

    /**
     * Return all configured image sizes (core + Journal)
     */
        private function getEagerSizes() {
        // All eager sizes we want (width × height)
        return [
                [40, 40],
                [60, 60],
                [80, 80],
                [90, 90],
                [100, 100],
                [500, 500],
                [1000, 1000]
        ];
        }

        private function cleanupEmptyDirs($path) {
                $dir = is_dir($path) ? $path : dirname($path);
                $root = rtrim(DIR_IMAGE, '/\\');

                while (strpos($dir, $root) === 0 && $dir !== $root) {
                        @rmdir($dir); // only works if empty
                        $dir = dirname($dir);
                }
        }


    function getObject($prefix, $file_name, $source, $only_details = false) {
                if (empty($prefix . $file_name)) {
                        return false;
                }

            try {
                    $options = $only_details ? [
                            'Bucket' => self::$BUCKET_NAME,
                            'Key' => $prefix . $file_name
                    ] : [
                            'Bucket' => self::$BUCKET_NAME,
                            'Key' => $prefix . $file_name,
                            'SaveAs' => $source . $file_name
                    ];

                    $result = $this->s3Client->getObject($options);
            } catch (S3Exception $e) {
                    $this->writeToLog($e->getMessage());
            }

            return $result ?? false;
    }

    function headObject($prefix, $file_name) {
            if (empty($prefix . $file_name)) {
                return false;
            }

            try {
                    $options = [
                            'Bucket' => self::$BUCKET_NAME,
                            'Key' => $prefix . $file_name
                    ];

                    $result = $this->s3Client->headObject($options);
            } catch (S3Exception $e) {
                    $this->writeToLog($e->getMessage());
            }

            return $result ?? false;
    }

    function deleteObjects($keys, $prefix = '') {
            try {
                    $options = [
                            'Bucket' => self::$BUCKET_NAME
                    ];

                    for ($i = 0; $i < count($keys); $i++) {
                            $options['Delete']['Objects'][]['Key'] = $prefix . $keys[$i];
                    }

                    $result = $this->s3Client->deleteObjects($options);
            } catch (S3Exception $e) {
                    $this->writeToLog($e->getMessage());
            }

            return $result ?? false;
    }

    function copyFile($source, $destination) {
            return shell_exec("aws s3 cp {$source} {$destination}");
    }

    function removeFileFromTemp($location) {
            if (file_exists($location) && unlink($location)) {
                    $this->writeToLog("Operation success: File remove: {$location}");
            } else {
                    $this->writeToLog("Operation failed: File remove: {$location}");
            }
    }

    function removeFilesFromTemp($files) {
            for($i = 0; $i < count($files); $i++) {
                    $this->removeFileFromTemp("{$this->SDS_TEMPORARY_DIRECTORY}{$files[$i]}");
            }
    }

    function writeToLog($exception = '') {
            $this->log->write($exception);
    }

    function emptyTempDirectory() {
            $tempFiles = glob("{$this->SDS_TEMPORARY_DIRECTORY}*");
            foreach($tempFiles as $file){
                    echo "<pre>"; print_r($file); echo "</pre>"; //die;
                    if(is_file($file)) {
                            unlink($file);
                    }
            }
    }

    function createTempDirectory() {
            mkdir($this->SDS_TEMPORARY_DIRECTORY);
    }

    function isTempExists() {
            return file_exists($this->SDS_TEMPORARY_DIRECTORY);
    }

    /*
     * SDS Merging functions
     */
    function copyFileToTemp($prefix, $file_name) {
            return $this->getObject($prefix, $file_name, $this->SDS_TEMPORARY_DIRECTORY);
    }

    function copyFileToS3($source, $file_name, $destination, $file_type = '') {
            $file = array(
                'name' => $file_name,
                'tmp_name' => $source . $file_name,
                'type' => $file_type ? $file_type : filetype($source . $file_name)
            );

            return $this->putObject($destination, $file);
    }

    function copyFilesToTemp($files) {
            if (!$this->isTempExists()) {
                    $this->createTempDirectory();
            }

            for ($i = 0; $i < count($files); $i++) {
                    $copy_result = $this->copyFileToTemp($this->SDS_PARTS_PATH_PREFIX, $files[$i]);

                    $log_string = "(s3://gbiosciences/{$this->SDS_PARTS_PATH_PREFIX}{$files[$i]} -> {$this->SDS_TEMPORARY_DIRECTORY})";
                    if ($copy_result !== NULL && $copy_result['@metadata']['statusCode'] === 200 && file_exists($this->SDS_TEMPORARY_DIRECTORY . $files[$i])) {
                            $this->writeToLog("Operation success: File copy: {$log_string}");
                    } else {
                            $this->writeToLog("Operation failed: File copy: {$log_string}");
                    }
            }
    }

    function addFilesToQueue(&$PDFMergerObj, $files) {
            for ($i = 0; $i < count($files); $i++) {
                    if (is_file("{$this->SDS_TEMPORARY_DIRECTORY}{$files[$i]}")) {
                            $PDFMergerObj->addPdf("{$this->SDS_TEMPORARY_DIRECTORY}{$files[$i]}");
                    } else {
                            $this->log->write("Merging Logs: File not present - {$this->SDS_TEMPORARY_DIRECTORY}{$files[$i]}");
                    }
            }
    }

    function mergeFilesInQueue(&$PDFMergerObj, $newFile) {
            $merge_result = $PDFMergerObj->merge('file', $this->SDS_TEMPORARY_DIRECTORY . $newFile);

            if (file_exists($this->SDS_TEMPORARY_DIRECTORY . $newFile)) {
                    $this->writeToLog("Merging Logs: Merging Success - {$this->SDS_TEMPORARY_DIRECTORY}{$newFile}");

                    $copy_result = $this->copyFileToS3($this->SDS_TEMPORARY_DIRECTORY, $newFile, $this->SDS_DIRECTORY, 'application/pdf');

                    $log_string = "({$this->SDS_TEMPORARY_DIRECTORY}{$newFile} -> s3://gbiosciences/{$this->SDS_DIRECTORY}{$newFile})";
                    if ($copy_result !== NULL && $copy_result['@metadata']['statusCode'] === 200) {
                            $this->writeToLog("Operation success: File copy: {$log_string}");
                    } else {
                            $this->writeToLog("Operation failed: File copy: {$log_string}");
                    }

                    unset($copy_result);
                    $this->removeFileFromTemp($this->SDS_TEMPORARY_DIRECTORY . $newFile);
            } else {
                    $this->writeToLog("Merging Logs: Merging Failed - {$this->SDS_TEMPORARY_DIRECTORY}{$newFile}");
            }

            return $merge_result;
    }
}
