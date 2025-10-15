<?php
/**
 * @var CView $this
 */
?>

<script>
const view = new class {
    constructor() {
        this.upload_form = null;
        this.file_input = null;
        this.upload_button = null;
        this.progress_div = null;
        this.status_div = null;
    }

    init() {
        this.upload_form = document.getElementById('module-upload-form');
        this.file_input = document.querySelector('input[name="module_file"]');
        this.upload_button = document.querySelector('.js-upload-module');
        this.progress_div = document.querySelector('.upload-progress');
        this.status_div = document.querySelector('.upload-status');

        this._initEventListeners();
    }

    _initEventListeners() {
        // Form submit
        this.upload_form.addEventListener('submit', (e) => {
            e.preventDefault();
            this._handleUpload();
        });

        // File input change
        this.file_input.addEventListener('change', (e) => {
            this._validateFile(e.target.files[0]);
        });

        // Drag and drop (optional enhancement)
        this._initDragAndDrop();
    }

    _validateFile(file) {
        if (!file) {
            return false;
        }

        // Validar extensão
        const allowed_extensions = ['tar.gz', 'tgz'];
        const filename = file.name.toLowerCase();
        let valid_extension = false;

        for (const ext of allowed_extensions) {
            if (filename.endsWith('.' + ext)) {
                valid_extension = true;
                break;
            }
        }

        if (!valid_extension) {
            this._showError(<?= json_encode(_('Invalid file extension. Only .tar.gz and .tgz files are allowed.')) ?>);
            this.file_input.value = '';
            return false;
        }

        // Validar tamanho (50MB)
        const max_size = 50 * 1024 * 1024;
        if (file.size > max_size) {
            this._showError(<?= json_encode(_('File size exceeds maximum allowed size of 50MB.')) ?>);
            this.file_input.value = '';
            return false;
        }

        this._clearStatus();
        return true;
    }

    _handleUpload() {
        const file = this.file_input.files[0];

        if (!file) {
            this._showError(<?= json_encode(_('Please select a file to upload.')) ?>);
            return;
        }

        if (!this._validateFile(file)) {
            return;
        }

        // Confirmar upload
        const confirmation = <?= json_encode(_('Are you sure you want to import this module? Make sure it comes from a trusted source.')) ?>;
        if (!confirm(confirmation)) {
            return;
        }

        this._startUpload(file);
    }

    _startUpload(file) {
        this.upload_button.disabled = true;
        this.file_input.disabled = true;

        this._showProgress();

        const formData = new FormData();
        formData.append('module_file', file);

        const xhr = new XMLHttpRequest();

        xhr.upload.addEventListener('progress', (e) => {
            if (e.lengthComputable) {
                const percent = (e.loaded / e.total) * 100;
                this._updateProgress(percent);
            }
        });

        xhr.addEventListener('load', () => {
            this._hideProgress();

            if (xhr.status === 200) {
                try {
                    const response = JSON.parse(xhr.responseText);

                    if (response.success) {
                        this._showSuccess(response.message);

                        // Recarregar página após 2 segundos
                        setTimeout(() => {
                            location.reload();
                        }, 2000);
                    } else {
                        this._showError(response.message);
                        this._resetForm();
                    }
                } catch (e) {
                    this._showError(<?= json_encode(_('Failed to parse server response.')) ?>);
                    this._resetForm();
                }
            } else {
                this._showError(<?= json_encode(_('Upload failed. Please try again.')) ?>);
                this._resetForm();
            }
        });

        xhr.addEventListener('error', () => {
            this._hideProgress();
            this._showError(<?= json_encode(_('Network error occurred. Please try again.')) ?>);
            this._resetForm();
        });

        xhr.addEventListener('abort', () => {
            this._hideProgress();
            this._showError(<?= json_encode(_('Upload cancelled.')) ?>);
            this._resetForm();
        });

        const url = new Curl('zabbix.php');
        url.setArgument('action', 'module.import.upload');

        xhr.open('POST', url.getUrl());
        xhr.send(formData);
    }

    _showProgress() {
        this.progress_div.style.display = 'block';
        this._updateProgress(0);
    }

    _hideProgress() {
        this.progress_div.style.display = 'none';
    }

    _updateProgress(percent) {
        percent = Math.round(percent);
        this.progress_div.innerHTML = `
            <div class="progress-bar">
                <div class="progress-fill" style="width: ${percent}%"></div>
            </div>
            <div class="progress-text">${percent}%</div>
        `;
    }

    _showError(message) {
        this.status_div.innerHTML = `
            <div class="msg-bad">
                <span class="msg-icon"></span>
                ${this._escapeHtml(message)}
            </div>
        `;
    }

    _showSuccess(message) {
        this.status_div.innerHTML = `
            <div class="msg-good">
                <span class="msg-icon"></span>
                ${this._escapeHtml(message)}
            </div>
        `;
    }

    _clearStatus() {
        this.status_div.innerHTML = '';
    }

    _resetForm() {
        this.upload_button.disabled = false;
        this.file_input.disabled = false;
        this.file_input.value = '';
    }

    _initDragAndDrop() {
        const dropZone = this.upload_form.querySelector('.form-list');

        if (!dropZone) {
            return;
        }

        dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropZone.classList.add('drag-over');
        });

        dropZone.addEventListener('dragleave', () => {
            dropZone.classList.remove('drag-over');
        });

        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.classList.remove('drag-over');

            const files = e.dataTransfer.files;
            if (files.length > 0) {
                this.file_input.files = files;
                this._validateFile(files[0]);
            }
        });
    }

    _escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
};

document.addEventListener('DOMContentLoaded', () => {
    view.init();
});
</script>
