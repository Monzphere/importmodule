<?php
/**
 * @var CView $this
 * @var array $data
 */

$this->includeJsFile('module.import.view.js.php');

$html_page = (new CHtmlPage())
    ->setTitle(_('Module Importer'))
    ->setControls(
        (new CTag('nav', true, (new CList())
            ->addItem(new CRedirectButton(_('Back to modules'), (new CUrl('zabbix.php'))
                ->setArgument('action', 'module.list')
            ))
        ))
            ->setAttribute('aria-label', _('Content controls'))
    );

// Upload form
$upload_form = (new CForm('post', null, 'multipart/form-data'))
    ->setId('module-upload-form')
    ->setName('module_upload_form');

$upload_table = (new CFormList())
    ->addRow(
        (new CLabel(_('Module package'), 'module_file'))->setAsteriskMark(),
        (new CFile('module_file'))
            ->setWidth(ZBX_TEXTAREA_STANDARD_WIDTH)
            ->setAriaRequired()
            ->setAttribute('accept', '.tar.gz,.tgz')
    )
    ->addRow('',
        (new CDiv([
            (new CDiv())
                ->addClass('upload-progress')
                ->addStyle('display: none;'),
            (new CDiv())
                ->addClass('upload-status')
        ]))
    )
    ->addInfo(
        (new CDiv([
            (new CDiv(_('Allowed file types: .tar.gz, .tgz')))->addClass('info-item'),
            (new CDiv(sprintf(_('Maximum upload size: %s'), $data['max_upload_size'])))->addClass('info-item'),
            (new CDiv(_('Only Super Admin users can upload modules')))->addClass('info-item warning'),
        ]))->addClass('upload-info')
    );

$upload_form->addItem([
    (new CFormFieldset())
        ->addClass('module-upload-section')
        ->addItem($upload_table)
]);

$upload_form->addItem(
    (new CSubmit('upload', _('Upload and Import')))
        ->addClass('js-upload-module')
);

// Existing modules table
$modules_table = (new CTableInfo())->setHeader([
    _('Module ID'),
    _('Name'),
    _('Version'),
    _('Author')
]);

if (!empty($data['existing_modules'])) {
    foreach ($data['existing_modules'] as $module) {
        $modules_table->addRow([
            $module['id'],
            $module['name'],
            $module['version'],
            $module['author']
        ]);
    }
} else {
    $modules_table->setNoDataMessage(_('No modules installed yet.'));
}

$html_page->addItem(
    (new CTabView())
        ->addTab('upload', _('Upload Module'), $upload_form)
        ->addTab('existing', _('Installed Modules'), $modules_table)
);

$html_page->show();
