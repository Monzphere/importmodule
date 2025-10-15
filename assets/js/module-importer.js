/**
 * Module Importer - Additional JavaScript utilities
 * Provides helper functions for module import functionality
 */

(function() {
    'use strict';

    /**
     * Module Importer utilities
     */
    window.ModuleImporter = {

        /**
         * Validate tar.gz file structure
         *
         * @param {File} file - The file to validate
         * @returns {boolean}
         */
        validateFileType: function(file) {
            if (!file) {
                return false;
            }

            const validExtensions = ['.tar.gz', '.tgz'];
            const fileName = file.name.toLowerCase();

            return validExtensions.some(ext => fileName.endsWith(ext));
        },

        /**
         * Format file size to human readable
         *
         * @param {number} bytes - Size in bytes
         * @returns {string}
         */
        formatFileSize: function(bytes) {
            if (bytes === 0) return '0 Bytes';

            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));

            return Math.round((bytes / Math.pow(k, i)) * 100) / 100 + ' ' + sizes[i];
        },

        /**
         * Show confirmation dialog
         *
         * @param {string} message - Message to display
         * @returns {boolean}
         */
        confirm: function(message) {
            return window.confirm(message);
        },

        /**
         * Sanitize filename for display
         *
         * @param {string} filename - Filename to sanitize
         * @returns {string}
         */
        sanitizeFilename: function(filename) {
            return filename.replace(/[^a-zA-Z0-9._-]/g, '_');
        },

        /**
         * Check if user has required permissions
         *
         * @returns {boolean}
         */
        checkPermissions: function() {
            // This would be replaced with actual permission check
            // For now, the backend handles this
            return true;
        },

        /**
         * Display notification
         *
         * @param {string} message - Message to display
         * @param {string} type - Type of notification (success, error, warning, info)
         */
        notify: function(message, type) {
            type = type || 'info';

            // Use Zabbix native notification if available
            if (typeof addMessage === 'function') {
                addMessage({
                    type: type === 'success' ? 'success' : 'error',
                    message: message
                });
            } else {
                console.log('[' + type.toUpperCase() + '] ' + message);
            }
        },

        /**
         * Debounce function for event handlers
         *
         * @param {Function} func - Function to debounce
         * @param {number} wait - Wait time in milliseconds
         * @returns {Function}
         */
        debounce: function(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        },

        /**
         * Validate module manifest structure
         *
         * @param {Object} manifest - Manifest object to validate
         * @returns {Object} Validation result with success flag and errors array
         */
        validateManifest: function(manifest) {
            const errors = [];
            const requiredFields = ['manifest_version', 'id', 'name', 'version', 'namespace'];

            requiredFields.forEach(field => {
                if (!manifest.hasOwnProperty(field) || !manifest[field]) {
                    errors.push('Missing required field: ' + field);
                }
            });

            // Validate ID format
            if (manifest.id && !/^[a-z0-9_-]+$/i.test(manifest.id)) {
                errors.push('Invalid module ID format');
            }

            // Validate namespace format
            if (manifest.namespace && !/^[A-Z][a-zA-Z0-9]*$/.test(manifest.namespace)) {
                errors.push('Invalid namespace format');
            }

            return {
                success: errors.length === 0,
                errors: errors
            };
        },

        /**
         * Parse security headers for XSS protection
         *
         * @param {string} content - Content to sanitize
         * @returns {string}
         */
        sanitizeContent: function(content) {
            const temp = document.createElement('div');
            temp.textContent = content;
            return temp.innerHTML;
        },

        /**
         * Check if tar.gz archive is valid
         *
         * @param {ArrayBuffer} buffer - File buffer
         * @returns {boolean}
         */
        isValidTarGz: function(buffer) {
            const arr = new Uint8Array(buffer).subarray(0, 3);
            // Check for gzip magic number (1f 8b 08)
            return arr[0] === 0x1f && arr[1] === 0x8b && arr[2] === 0x08;
        },

        /**
         * Initialize security measures
         */
        initSecurity: function() {
            // Prevent drag and drop on body (only allow on designated areas)
            document.body.addEventListener('dragover', function(e) {
                if (!e.target.closest('.module-upload-section')) {
                    e.preventDefault();
                    e.dataTransfer.effectAllowed = 'none';
                    e.dataTransfer.dropEffect = 'none';
                }
            }, false);

            document.body.addEventListener('drop', function(e) {
                if (!e.target.closest('.module-upload-section')) {
                    e.preventDefault();
                }
            }, false);
        }
    };

    // Initialize security measures when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            ModuleImporter.initSecurity();
        });
    } else {
        ModuleImporter.initSecurity();
    }

})();
