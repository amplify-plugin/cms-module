<?php

namespace Amplify\System\Cms\Http\Controllers;

use Amplify\System\Cms\Http\Requests\FormRequest;
use Amplify\System\Abstracts\BackpackCustomCrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Backpack\CRUD\app\Library\Widget;

/**
 * Class FormCrudController
 *
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class FormCrudController extends BackpackCustomCrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\Amplify\System\Cms\Models\Form::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/form');
        CRUD::setEntityNameStrings('form', 'forms');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     *
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::addColumns([
            [
                'name' => 'name',
                'type' => 'text',
                'label' => 'Heading',
            ],
            [
                'name' => 'code',
                'type' => 'text',
                'label' => 'Unique Code',
            ],
            [
                'name' => 'allow_reset',
                'type' => 'boolean',
                'label' => 'Allow Reset?',
            ],
            [
                'name' => 'allow_captcha',
                'type' => 'boolean',
                'label' => 'Ask for Captcha?',
            ],
            [
                'name' => 'enabled',
                'type' => 'boolean',
                'label' => 'Accepting Repose?',
            ],
        ]);
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     *
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(FormRequest::class);

        CRUD::addFields([
            [
                'name' => 'name',
                'type' => 'text',
                'label' => 'Form Heading',
                'tab' => 'Basic',
            ],
            [
                'name' => 'code',
                'type' => 'slug',
                'label' => 'Form Unique Code',
                'target' => 'name',
                'tab' => 'Basic',
            ],
            [
                'name' => 'submit_button_title',
                'type' => 'text',
                'label' => 'Submit Button Label',
                'tab' => 'Basic',
                'value' => 'Submit',
            ],
            [
                'name' => 'reset_button_title',
                'type' => 'text',
                'label' => 'Form Content Clear Button Label',
                'tab' => 'Basic',
                'value' => 'Reset',
            ],
            [
                'name' => 'allow_reset',
                'type' => 'boolean',
                'label' => 'Allow Reset Form Data',
                'tab' => 'Basic',
                'value' => false,
            ],
            [
                'name' => 'allow_captcha',
                'type' => 'boolean',
                'label' => 'Ask for Captcha Verification',
                'tab' => 'Basic',
                'value' => true,
            ],
            [
                'name' => 'enabled',
                'type' => 'boolean',
                'label' => 'Accepting Repose',
                'tab' => 'Basic',
                'value' => true,
            ],
            [
                'name' => 'formFields',
                'label' => 'Fields',
                'type' => 'relationship',
                'tab' => 'Fields',
                'subfields' => [
                    [
                        'name' => 'type',
                        'label' => 'Type',
                        'type' => 'select_from_array',
                        'value' => 'rText',
                        'options' => [
                            'rText' => 'Plain Text',
                            'rNumber' => 'Number',
                            'rEmail' => 'Email Address',
                            'rUrl' => 'Website URL',
                            'rTel' => 'Phone Number',
                            'rSelect' => 'Dropdown',
                            'rTextarea' => 'Long Text',
                            'rCheckbox' => 'Checkbox (Multiple)',
                            'rRadio' => 'Checkbox (Single)',
                            'rRange' => 'Range Slider',
                            'rDate' => 'Date',
                            'rFile' => 'Upload File',
                            'rImage' => 'Upload Image(Preview)',
                            'rSelectRange' => 'Range Dropdown',
                            'rSelectMonth' => 'Month',
                            'rSelectYear' => 'Year',
                            'rHtml' => 'Raw HTML',
                        ],
                        'allows_null' => false,
                    ],
                    [
                        'name' => 'name',
                        'label' => 'Name',
                        'type' => 'text',
                        'allows_null' => false,
                        'wrapper' => [
                            'class' => 'form-group col-md-4',
                        ],
                    ],
                    [
                        'name' => 'label',
                        'label' => 'Label',
                        'type' => 'text',
                        'allows_null' => false,
                        'wrapper' => [
                            'class' => 'form-group col-md-4',
                        ],
                    ],
                    [
                        'name' => 'width',
                        'label' => 'Size',
                        'type' => 'select_from_array',
                        'options' => [
                            'col-md-12 col-12' => 'Full width',
                            'col-md-6 col-12' => 'Half width',
                            'col-md-4 col-12' => 'One-third',
                            'col-md-3 col-12' => 'One quarter',
                            'col-md-8 col-12' => 'Two thirds',
                            'col-md-9 col-12' => 'Three quarters',
                        ],
                        'default' => '12',
                        'allows_null' => false,
                        'wrapper' => [
                            'class' => 'form-group col-md-4',
                        ],
                    ],
                    [
                        'name' => 'options',
                        'type' => 'table',
                        'entity_singular' => 'option',
                        'columns' => [
                            'option' => 'Option',
                        ],
                    ],
                    [
                        'name' => 'minimum',
                        'type' => 'text',
                        'label' => 'Min Value',
                        'wrapper' => [
                            'class' => 'form-group col-md-6',
                        ],
                    ],
                    [
                        'name' => 'maximum',
                        'type' => 'text',
                        'label' => 'Max Value',
                        'wrapper' => [
                            'class' => 'form-group col-md-6',
                        ],
                    ],
                    [
                        'name' => 'is_required',
                        'label' => 'Value is required?',
                        'type' => 'boolean',
                    ],
                    [
                        'name' => 'is_inline',
                        'label' => 'Display label and field in single line?',
                        'type' => 'boolean',
                    ],
                    [
                        'name' => 'value',
                        'label' => 'Default Value',
                        'type' => 'textarea',
                        'attributes' => [
                            'rows' => 5,
                        ]
                    ],
                    [
                        'name' => 'validation',
                        'label' => 'Additional Validation',
                        'type' => 'text',
                        'hint' => 'Rules Docs (<a href="https://laravel.com/docs/10.x/validation#available-validation-rules" target="_blank">Available Validation Rules</a>).',
                    ],
                ],
            ],
        ]);

        Widget::add([
            'type' => 'inline_script',
            'stack' => 'after_scripts',
            'content' => <<<JS
crud.field('formFields').subfield('type').onChange(function (field) {
    let value = field.value;
    let rowNumber = field.rowNumber;
    crud.field('formFields').subfield('options', rowNumber).show((['rSelect', 'rCheckbox', 'rRadio']).includes(value));
    crud.field('formFields').subfield('minimum', rowNumber).show((['rRange', 'rSelectRange', 'rSelectYear']).includes(value));
    crud.field('formFields').subfield('maximum', rowNumber).show((['rRange', 'rSelectRange', 'rSelectYear']).includes(value));

    crud.field('formFields').subfield('name', rowNumber).hide(value === 'rHtml');
    crud.field('formFields').subfield('label', rowNumber).hide(value === 'rHtml');
    crud.field('formFields').subfield('is_required', rowNumber).hide(value === 'rHtml');
    crud.field('formFields').subfield('is_inline', rowNumber).hide(value === 'rHtml');
    crud.field('formFields').subfield('validation', rowNumber).hide(value === 'rHtml');
    crud.field('formFields').subfield('width', rowNumber).hide(value === 'rHtml');
}).change();

JS
        ]);

    }

    /**
     * Define what happens when the Update operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     *
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();

        $this->crud->removeField('code');
    }
}
