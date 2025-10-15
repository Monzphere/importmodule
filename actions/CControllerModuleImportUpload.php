<?php declare(strict_types = 0);
/*
** Zabbix Module Importer
** Copyright (C) 2025
**
** This program is free software; you can redistribute it and/or modify
** it under the terms of the GNU General Public License as published by
** the Free Software Foundation; either version 3 of the License, or
** (at your option) any later version.
**
** This program is distributed in the hope that it will be useful,
** but WITHOUT ANY WARRANTY; without even the implied warranty of
** MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
** GNU General Public License for more details.
**
** You should have received a copy of the GNU General Public License
** along with this program; if not, write to the Free Software
** Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
**/

namespace Modules\ModuleImporter\Actions;

use CController,
    CControllerResponseData,
    CControllerResponseFatal,
    CWebUser,
    Exception,
    PharData;

/**
 * Controller for handling module upload and installation
 */
class CControllerModuleImportUpload extends CController {

    private const ALLOWED_EXTENSIONS = ['tar.gz', 'tgz'];
    private const MODULES_PATH = '/usr/share/zabbix/modules/';
    private const TEMP_EXTRACT_PATH = '/tmp/zabbix_module_import/';
    private const MAX_FILE_SIZE = 52428800; // 50MB in bytes

    protected function init(): void {
        $this->disableCsrfValidation();
    }

    protected function checkInput(): bool {
        return true;
    }

    protected function checkPermissions(): bool {
        return $this->getUserType() === USER_TYPE_SUPER_ADMIN;
    }

    protected function doAction(): void {
        header('Content-Type: application/json');

        $result = [
            'success' => false,
            'message' => '',
            'module_info' => null
        ];

        try {
            if (!isset($_FILES['module_file']) || $_FILES['module_file']['error'] !== UPLOAD_ERR_OK) {
                throw new Exception(_('Failed to upload file. Please try again.'));
            }

            $file = $_FILES['module_file'];

            $this->validateFileExtension($file['name']);
            $this->validateMimeType($file['tmp_name']);
            $this->validateFileSize($file['size']);

            $temp_dir = $this->createTempDirectory();
            $extracted_path = $this->extractArchive($file['tmp_name'], $temp_dir);
            $manifest = $this->validateModuleStructure($extracted_path);
            $this->validateManifest($manifest);
            $this->checkModuleExists($manifest['id']);

            $module_dir_name = $this->sanitizeDirectoryName($manifest['id']);
            $final_path = self::MODULES_PATH . $module_dir_name;

            if (!rename($extracted_path, $final_path)) {
                throw new Exception(_('Failed to move module to final destination.'));
            }

            $this->setDirectoryPermissions($final_path);
            $this->cleanupTempDirectory($temp_dir);

            $result = [
                'success' => true,
                'message' => sprintf(_('Module "%s" imported successfully!'), $manifest['name']),
                'module_info' => [
                    'id' => $manifest['id'],
                    'name' => $manifest['name'],
                    'version' => $manifest['version'],
                    'author' => $manifest['author'] ?? 'N/A'
                ]
            ];

        } catch (Exception $e) {
            if (isset($temp_dir) && is_dir($temp_dir)) {
                $this->cleanupTempDirectory($temp_dir);
            }

            $result = [
                'success' => false,
                'message' => $e->getMessage()
            ];
        } catch (\Throwable $e) {
            $result = [
                'success' => false,
                'message' => 'Fatal error: ' . $e->getMessage()
            ];
        }

        echo json_encode($result);
        exit;
    }

    /**
     * Validate uploaded file extension
     *
     * @param string $filename Original filename
     * @throws Exception If extension is not allowed
     */
    private function validateFileExtension(string $filename): void {
        $valid = false;

        foreach (self::ALLOWED_EXTENSIONS as $ext) {
            if (substr($filename, -strlen($ext)) === $ext) {
                $valid = true;
                break;
            }
        }

        if (!$valid) {
            throw new Exception(_('Invalid file extension. Only .tar.gz and .tgz files are allowed.'));
        }
    }

    /**
     * Validate file MIME type
     *
     * @param string $filepath Path to uploaded file
     * @throws Exception If MIME type is not allowed
     */
    private function validateMimeType(string $filepath): void {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $filepath);
        finfo_close($finfo);

        $allowed_mimes = [
            'application/x-gzip',
            'application/gzip',
            'application/x-tar',
            'application/x-compressed-tar'
        ];

        if (!in_array($mime, $allowed_mimes, true)) {
            throw new Exception(_('Invalid file type. Only tar.gz archives are allowed.'));
        }
    }

    /**
     * Validate file size
     *
     * @param int $size File size in bytes
     * @throws Exception If file exceeds maximum size
     */
    private function validateFileSize(int $size): void {
        if ($size > self::MAX_FILE_SIZE) {
            throw new Exception(_('File size exceeds maximum allowed size of 50MB.'));
        }
    }

    /**
     * Create temporary extraction directory
     *
     * @return string Path to temporary directory
     * @throws Exception If directory creation fails
     */
    private function createTempDirectory(): string {
        $temp_dir = self::TEMP_EXTRACT_PATH . uniqid('module_', true);

        if (!mkdir($temp_dir, 0755, true)) {
            throw new Exception(_('Failed to create temporary directory.'));
        }

        return $temp_dir;
    }

    /**
     * Extract tar.gz archive to destination
     *
     * @param string $archive_path Path to archive file
     * @param string $destination Extraction destination
     * @return string Path to extracted module directory
     * @throws Exception If extraction fails
     */
    private function extractArchive(string $archive_path, string $destination): string {
        try {
            $temp_archive = $destination . '/upload.tar.gz';
            if (!copy($archive_path, $temp_archive)) {
                throw new Exception(_('Failed to copy uploaded file.'));
            }

            $phar = new PharData($temp_archive);
            $phar->extractTo($destination, null, true);

            unlink($temp_archive);

            $items = scandir($destination);
            $module_dir = null;

            foreach ($items as $item) {
                if ($item !== '.' && $item !== '..' && is_dir($destination . '/' . $item)) {
                    $module_dir = $destination . '/' . $item;
                    break;
                }
            }

            if ($module_dir === null) {
                $module_dir = $destination;
            }

            return $module_dir;

        } catch (Exception $e) {
            throw new Exception(_('Failed to extract archive: ') . $e->getMessage());
        }
    }

    /**
     * Validate module structure and load manifest
     *
     * @param string $module_path Path to extracted module
     * @return array Manifest data
     * @throws Exception If structure is invalid
     */
    private function validateModuleStructure(string $module_path): array {
        $manifest_path = $module_path . '/manifest.json';

        if (!file_exists($manifest_path)) {
            throw new Exception(_('Invalid module structure: manifest.json not found.'));
        }

        $manifest_content = file_get_contents($manifest_path);
        $manifest = json_decode($manifest_content, true);

        if ($manifest === null) {
            throw new Exception(_('Invalid manifest.json: unable to parse JSON.'));
        }

        return $manifest;
    }

    /**
     * Validate manifest.json content
     *
     * @param array $manifest Parsed manifest data
     * @throws Exception If manifest is invalid
     */
    private function validateManifest(array $manifest): void {
        $required_fields = ['manifest_version', 'id', 'name', 'version', 'namespace'];

        foreach ($required_fields as $field) {
            if (!isset($manifest[$field]) || empty($manifest[$field])) {
                throw new Exception(sprintf(_('Invalid manifest.json: missing required field "%s".'), $field));
            }
        }

        if (!preg_match('/^[a-z0-9_-]+$/i', $manifest['id'])) {
            throw new Exception(_('Invalid module ID: only alphanumeric characters, hyphens and underscores are allowed.'));
        }

        if (!preg_match('/^[A-Z][a-zA-Z0-9]*$/', $manifest['namespace'])) {
            throw new Exception(_('Invalid namespace: must start with uppercase letter and contain only alphanumeric characters.'));
        }
    }

    /**
     * Check if module with same ID already exists
     *
     * @param string $module_id Module identifier
     * @throws Exception If module already exists
     */
    private function checkModuleExists(string $module_id): void {
        $module_path = self::MODULES_PATH . $module_id;

        if (is_dir($module_path)) {
            throw new Exception(sprintf(_('Module with ID "%s" already exists. Please remove it before importing.'), $module_id));
        }
    }

    /**
     * Sanitize directory name for security
     *
     * @param string $name Directory name to sanitize
     * @return string Sanitized name
     */
    private function sanitizeDirectoryName(string $name): string {
        $name = preg_replace('/[^a-z0-9_-]/i', '', $name);
        $name = str_replace(['..', './'], '', $name);

        return $name;
    }

    /**
     * Set correct filesystem permissions recursively
     *
     * @param string $path Directory path
     */
    private function setDirectoryPermissions(string $path): void {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $item) {
            if ($item->isDir()) {
                chmod($item->getPathname(), 0755);
            } else {
                chmod($item->getPathname(), 0644);
            }
        }

        chmod($path, 0755);
    }

    /**
     * Remove temporary directory and its contents
     *
     * @param string $dir Directory path to remove
     */
    private function cleanupTempDirectory(string $dir): void {
        if (!is_dir($dir)) {
            return;
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($iterator as $item) {
            if ($item->isDir()) {
                rmdir($item->getPathname());
            } else {
                unlink($item->getPathname());
            }
        }

        rmdir($dir);
    }
}
