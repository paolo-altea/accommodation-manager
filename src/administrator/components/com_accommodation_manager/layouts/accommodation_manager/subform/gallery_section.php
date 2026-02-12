<?php
/**
 * @package     Com_Accommodation_manager
 * @subpackage  Layout
 * @copyright   Copyright (C) 2024. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 *
 * Gallery subform section - each repeatable row
 * Row 1: image fields + action buttons
 * Row 2: alt text fields
 */

defined('_JEXEC') or die;

use Joomla\CMS\Form\Form;
use Joomla\CMS\Language\Text;

extract($displayData);

/**
 * @var   Form    $form       The form instance for render the section
 * @var   string  $basegroup  The base group name
 * @var   string  $group      Current group name
 * @var   array   $buttons    Array of the buttons that will be rendered
 */

$imageFields = [];
$altFields   = [];

foreach ($form->getGroup('') as $field) {
    $fieldName = $field->fieldname;
    if (str_starts_with($fieldName, 'alt_')) {
        $altFields[] = $field;
    } else {
        $imageFields[] = $field;
    }
}
?>

<div class="subform-repeatable-group card mb-3" data-base-name="<?php echo $basegroup; ?>" data-group="<?php echo $group; ?>">
    <div class="card-body p-2">
        <div class="row align-items-end">
            <?php foreach ($imageFields as $field) : ?>
                <div class="col-md">
                    <?php echo $field->renderField(); ?>
                </div>
            <?php endforeach; ?>
            <?php if (!empty($buttons)) : ?>
                <div class="col-md-auto mb-3">
                    <div class="btn-group">
                        <?php if (!empty($buttons['move'])) : ?>
                            <button type="button" class="group-move btn btn-sm btn-primary" aria-label="<?php echo Text::_('JGLOBAL_FIELD_MOVE'); ?>">
                                <span class="icon-arrows-alt" aria-hidden="true"></span>
                            </button>
                        <?php endif; ?>
                        <?php if (!empty($buttons['remove'])) : ?>
                            <button type="button" class="group-remove btn btn-sm btn-danger" aria-label="<?php echo Text::_('JGLOBAL_FIELD_REMOVE'); ?>">
                                <span class="icon-minus" aria-hidden="true"></span>
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <?php if (!empty($altFields)) : ?>
            <div class="row">
                <?php foreach ($altFields as $field) : ?>
                    <div class="col">
                        <?php echo $field->renderField(); ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
