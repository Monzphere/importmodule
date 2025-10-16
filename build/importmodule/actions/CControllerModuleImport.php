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
    CRoleHelper,
    API;

/**
 * Controller for displaying the module import interface
 */
class CControllerModuleImport extends CController {

    protected function init(): void {
        $this->disableCsrfValidation();
    }

    protected function checkInput(): bool {
        $fields = [
            'ajax' => 'in 1'
        ];

        $ret = $this->validateInput($fields);

        if (!$ret) {
            $this->setResponse(new CControllerResponseFatal());
        }

        return $ret;
    }

    protected function checkPermissions(): bool {
        return $this->getUserType() === USER_TYPE_SUPER_ADMIN;
    }

    protected function doAction(): void {
        $data = [
            'title' => _('Module Importer'),
            'user' => [
                'debug_mode' => $this->getDebugMode()
            ],
            'allowed_extensions' => ['tar.gz', 'tgz'],
            'max_upload_size' => $this->getMaxUploadSize(),
            'modules_path' => '/usr/share/zabbix/modules/'
        ];

        $modules_dir = '/usr/share/zabbix/modules/';
        $existing_modules = [];

        if (is_dir($modules_dir)) {
            $dirs = scandir($modules_dir);
            foreach ($dirs as $dir) {
                if ($dir !== '.' && $dir !== '..' && is_dir($modules_dir . $dir)) {
                    $manifest_path = $modules_dir . $dir . '/manifest.json';
                    if (file_exists($manifest_path)) {
                        $manifest = json_decode(file_get_contents($manifest_path), true);
                        $existing_modules[] = [
                            'id' => $manifest['id'] ?? $dir,
                            'name' => $manifest['name'] ?? $dir,
                            'version' => $manifest['version'] ?? 'N/A',
                            'author' => $manifest['author'] ?? 'N/A'
                        ];
                    }
                }
            }
        }

        $data['existing_modules'] = $existing_modules;

        $response = new CControllerResponseData($data);
        $response->setTitle(_('Module Importer'));
        $this->setResponse($response);
    }

    /**
     * Get maximum upload size from php.ini configuration
     *
     * @return string Formatted size string
     */
    private function getMaxUploadSize(): string {
        $upload_max = ini_get('upload_max_filesize');
        $post_max = ini_get('post_max_size');

        $upload_bytes = $this->convertToBytes($upload_max);
        $post_bytes = $this->convertToBytes($post_max);

        $max_bytes = min($upload_bytes, $post_bytes);

        return $this->formatBytes($max_bytes);
    }

    /**
     * Convert PHP ini size notation to bytes
     *
     * @param string $size Size string (e.g., "2M", "1G")
     * @return int Size in bytes
     */
    private function convertToBytes(string $size): int {
        $size = trim($size);
        $last = strtolower($size[strlen($size)-1]);
        $size = (int)$size;

        switch($last) {
            case 'g':
                $size *= 1024;
            case 'm':
                $size *= 1024;
            case 'k':
                $size *= 1024;
        }

        return $size;
    }

    /**
     * Format bytes to human-readable string
     *
     * @param int $bytes Number of bytes
     * @return string Formatted size string
     */
    private function formatBytes(int $bytes): string {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;

        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }
}
