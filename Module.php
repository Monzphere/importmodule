<?php
/*
** Zabbix Module Importer by MonZphere
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

namespace Modules\ModuleImporter;

use Zabbix\Core\CModule,
    APP,
    CMenu,
    CMenuItem,
    CWebUser;

/**
 * Zabbix Module Importer main class
 */
class Module extends CModule {

    /**
     * Initialize module and register menu items
     */
    public function init(): void {
        if (CWebUser::getType() === USER_TYPE_SUPER_ADMIN) {
            APP::Component()->get('menu.main')
                ->findOrAdd(_('Administration'))
                ->getSubmenu()
                ->insertAfter(_('General'),
                    (new CMenuItem(_('Import Module')))
                        ->setAction('module.import.view')
                        ->setAliases(['module.import.upload'])
                );
        }
    }

    /**
     * Event handler triggered on module installation
     */
    public function onInstall(): void {
        $temp_dir = '/tmp/zabbix_module_import';
        if (!is_dir($temp_dir)) {
            mkdir($temp_dir, 0755, true);
        }
    }

    /**
     * Event handler triggered on module uninstallation
     */
    public function onUninstall(): void {
        $temp_dir = '/tmp/zabbix_module_import';
        if (is_dir($temp_dir)) {
            $this->removeDirectory($temp_dir);
        }
    }

    /**
     * Recursively remove directory and its contents
     *
     * @param string $dir Directory path
     */
    private function removeDirectory(string $dir): void {
        if (!is_dir($dir)) {
            return;
        }

        $items = scandir($dir);
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $path = $dir . '/' . $item;
            if (is_dir($path)) {
                $this->removeDirectory($path);
            } else {
                unlink($path);
            }
        }

        rmdir($dir);
    }
}
