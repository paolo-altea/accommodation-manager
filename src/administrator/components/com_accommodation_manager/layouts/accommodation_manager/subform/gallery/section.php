<?php
/**
 * @package     Com_Accommodation_manager
 * @subpackage  Layout
 * @copyright   Copyright (C) 2019. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 *
 * Custom gallery subform section - two-row layout
 * Row 1: image, image_mobile, action buttons
 * Row 2: alt_de, alt_it, alt_en, alt_fr, alt_es
 */

defined('_JEXEC') or die;

use Joomla\CMS\Form\Form;
use Joomla\CMS\Language\Text;

extract($displayData);

/**
 * Layout variables
 * -----------------
 * @var   Form    $form       The form instance for render the section
 * @var   string  $basegroup  The base group name
 * @var   string  $group      Current group name
 * @var   array   $buttons    Array of the buttons that will be rendered
 */

// Get all fields
$fields = $form->getGroup('');

// Separate fields into two groups
$imageFields = [];
$altFields = [];

foreach ($fields as $fieldName => $field) {
    $name = $field->fieldname;
    if (in_array($name, ['image', 'image_mobile'])) {
        $imageFields[$name] = $field;
    } elseif (strpos($name, 'alt_') === 0) {
        $altFields[$name] = $field;
    }
}
?>

<div class="subform-repeatable-group card mb-3" data-base-name="<?php echo $basegroup; ?>" data-group="<?php echo $group; ?>">
    <div class="card-body">
        <!-- Row 1: Images + Buttons -->
        <div class="row align-items-end mb-3">
            <div class="col-md-5">
                <?php if (isset($imageFields['image'])) : ?>
                <div class="form-vertical">
                    <?php echo $imageFields['image']->renderField(); ?>
                </div>
                <?php endif; ?>
            </div>
            <div class="col-md-5">
                <?php if (isset($imageFields['image_mobile'])) : ?>
                <div class="form-vertical">
                    <?php echo $imageFields['image_mobile']->renderField(); ?>
                </div>
                <?php endif; ?>
            </div>
            <div class="col-md-2 text-end">
                <?php if (!empty($buttons)) : ?>
                <div class="btn-group mb-3">
                    <?php if (!empty($buttons['add'])) : ?>
                        <button type="button" class="group-add btn btn-sm btn-success" aria-label="<?php echo Text::_('JGLOBAL_FIELD_ADD'); ?>">
                            <span class="icon-plus" aria-hidden="true"></span>
                        </button>
                    <?php endif; ?>
                    <?php if (!empty($buttons['remove'])) : ?>
                        <button type="button" class="group-remove btn btn-sm btn-danger" aria-label="<?php echo Text::_('JGLOBAL_FIELD_REMOVE'); ?>">
                            <span class="icon-minus" aria-hidden="true"></span>
                        </button>
                    <?php endif; ?>
                    <?php if (!empty($buttons['move'])) : ?>
                        <button type="button" class="group-move btn btn-sm btn-primary" aria-label="<?php echo Text::_('JGLOBAL_FIELD_MOVE'); ?>">
                            <span class="icon-arrows-alt" aria-hidden="true"></span>
                        </button>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Row 2: Alt texts -->
        <?php if (!empty($altFields)) : ?>
        <div class="row">
            <?php foreach ($altFields as $field) : ?>
            <div class="col">
                <div class="form-vertical">
                    <?php echo $field->renderField(); ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>
