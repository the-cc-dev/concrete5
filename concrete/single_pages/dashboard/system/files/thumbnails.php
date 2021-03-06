<?php

defined('C5_EXECUTE') or die('Access Denied.');

/* @var Concrete\Core\Page\View\PageView $view */
/* @var Concrete\Core\Form\Service\Form $form */
/* @var Concrete\Core\Validation\CSRF\Token $token */
/* @var Concrete\Controller\SinglePage\Dashboard\System\Files\Thumbnails $controller */

if (isset($type)) {
    /* @var Concrete\Core\Entity\File\Image\Thumbnail\Type\Type $type */
    /* @var array $sizingModes */
    /* @var array $sizingModeHelps */
    /* @var bool $allowConditionalThumbnails */
    /* @var array $fileSetOptions [if $allowConditionalThumbnails is true] */
    /* @var array $fileSets [if $allowConditionalThumbnails is true] */
    if ($type->getID() !== null && !$type->isRequired()) {
        ?>
        <div class="ccm-dashboard-header-buttons">
            <form method="post" action="<?= $view->action('delete', $type->getID())?> ">
                <?php $token->output('thumbnailtype-delete-' . $type->getID()) ?>
                <button type="button" class="btn btn-danger" data-action="delete-type"><?= t('Delete Type') ?></button>
            </form>
        </div>
        <?php
    }
    ?>
    <form method="POST" action="<?= $view->action('save', $type->getID() ?: 'new') ?>">
        <?php $token->output('thumbnailtype-save-' . ($type->getID() ?: 'new')) ?>
        <div class="form-group">
            <?= $form->label('ftTypeHandle', t('Handle')) ?>
            <div class="input-group">
                <?= $form->text('ftTypeHandle', $type->getHandle(), ['required' => 'required', 'maxlength' => '255'] + ($type->getID() !== null && $type->isRequired() ? ['readonly' => 'readonly'] : []) ) ?>
                <span class="input-group-addon"><i class="fa fa-asterisk"></i></span>
            </div>
        </div>
        <div class="form-group">
            <?= $form->label('ftTypeName', t('Name')) ?>
            <div class="input-group">
                <?=$form->text('ftTypeName', $type->getName(), ['required' => 'required', 'maxlength' => '255']) ?>
                <span class="input-group-addon"><i class="fa fa-asterisk"></i></span>
            </div>
        </div>
        <div class="form-group">
            <?= $form->label('ftTypeWidth', t('Width')) ?>
            <div class="input-group">
                <?= $form->number('ftTypeWidth', $type->getWidth() ?: '', ['min' => '1']) ?>
                <span class="input-group-addon"><?= t('px') ?></span>
            </div>
        </div>
        <div class="form-group">
            <?= $form->label('ftTypeHeight', t('Height')) ?>
            <div class="input-group">
                <?=$form->text('ftTypeHeight', $type->getHeight() ?: '', ['min' => '1']) ?>
                <span class="input-group-addon"><?= t('px') ?></span>
            </div>
        </div>
        <div class="form-group">
            <?= $form->label('ftTypeSizingMode', t('Sizing Mode')) ?>
            <?= $form->select('ftTypeSizingMode', $sizingModes, $type->getSizingMode()) ?>
            <p class="help-block" id="sizingmode-help"><span><?= $sizingModeHelps[$type->getSizingMode()] ?></span></p>
        </div>
        <?php
        if ($allowConditionalThumbnails) {
            $selectedFileSets = [];
            foreach ($type->getAssociatedFileSets() as $associatedFileSet) {
                $fileSetID = $associatedFileSet->getFileSetID();
                if (isset($fileSets[$fileSetID])) {
                    $selectedFileSets[] = $fileSetID;
                }
            }
            $fileSetAttributes = [];
            if (empty($selectedFileSets)) {
                $fileSetOption = $controller::FILESETOPTION_ALL;
                $fileSetAttributes['disabled'] = 'disabled';
            } else {
                $fileSetOption = $type->isLimitedToFileSets() ? $controller::FILESETOPTION_ONLY : $controller::FILESETOPTION_ALL_EXCEPT;
            }
            ?>
            <div class="form-group">
                <?= $form->label('fileSetOption', t('Conditional Thumbnails')) ?>
                <?= $form->select('fileSetOption', $fileSetOptions, $fileSetOption, ['required' => 'required']) ?>
            </div>
            <div class="form-group">
                <?= $form->label('fileSets', 'File Sets') ?>
                <div class="ccm-search-field-content">
                    <?= $form->selectMultiple('fileSets', $fileSets, $selectedFileSets, $fileSetAttributes) ?>
                </div>
            </div>
            <?php
        }
        ?>
        <div class="ccm-dashboard-form-actions-wrapper">
            <div class="ccm-dashboard-form-actions">
                <a href="<?= $view->action('') ?>" class="btn pull-left btn-default"><?= t('Back') ?></a>
                <?php
                if ($type->getID() !== null) {
                    ?>
                    <button type="submit" class="btn btn-primary pull-right"><?= t('Save') ?></button>
                    <?php
                } else {
                    ?>
                    <button type="submit" class="btn btn-primary pull-right"><?= t('Add') ?></button>
                    <?php
                }
                ?>
            </div>
        </div>
    </form>
    <script>
    $(document).ready(function() {
        $('button[data-action=delete-type]').on('click', function(e) {
            e.preventDefault();
            if (window.confirm(<?= json_encode(t('Delete this thumbnail type?')) ?>)) {
                $(this).closest('form').submit();
            }
        });
        var sizingModeHelps = <?= json_encode($sizingModeHelps)?>;
        $('#ftTypeSizingMode')
            .on('change', function(e) {
                var mode = $(this).val();
                $('#sizingmode-help span').html(mode in sizingModeHelps ? sizingModeHelps[mode] : '');
            })
            .trigger('change')
        ;
        <?php
        if ($allowConditionalThumbnails) {
            ?>
            var $fileSets = $('#fileSets');
            $fileSets.selectize({
                plugins: ['remove_button']
            });
            $('#fileSetOption')
                .on('change', function() {
                    if ($(this).val() === <?= json_encode($controller::FILESETOPTION_ALL) ?>) {
                        $fileSets[0].selectize.disable();
                    } else {
                        $fileSets[0].selectize.enable();
                    }
                })
                .trigger('change')
            ;
            <?php
        }
        ?>
    });
    </script>
    <?php
} else {
    /* @var Concrete\Core\Entity\File\Image\Thumbnail\Type\Type[] $types */
    ?>
    <div class="ccm-dashboard-header-buttons btn-group">
        <a href="<?= $view->action('options')?>" class="btn btn-default"><?= t('Options') ?></a>
        <a href="<?= $view->action('edit', 'new')?>" class="btn btn-primary"><?= t('Add Type') ?></a>
    </div>
    <table class="table">
        <thead>
            <tr>
                <th><?= t('Handle') ?></th>
                <th><?= t('Name') ?></th>
                <th><?= t('Width') ?></th>
                <th><?= t('Height') ?></th>
                <th><?= t('Sizing') ?></th>
                <th><?= t('Required') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($types as $type) {
                ?>
                <tr>
                    <td><a href="<?= $view->action('edit', $type->getID()) ?>"><?= h($type->getHandle()) ?></a></td>
                    <td><?= h($type->getDisplayName()) ?></td>
                    <td><?= $type->getWidth() ?: '<span class="text-muted">' . t('Automatic') . '</span>' ?></td>
                    <td><?= $type->getHeight() ?: '<span class="text-muted">' . t('Automatic') . '</span>' ?></td>
                    <td><?= h($type->getSizingModeDisplayName()) ?></td>
                    <td><?= $type->isRequired() ? t('Yes') : t('No') ?></td>
                </tr>
                <?php
            }
            ?>
        </tbody>
    </table>
    <?php
}
