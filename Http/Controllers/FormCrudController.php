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

        Widget::add()->type('script')->content('vendor/backend/js/forms/form-builder.js');

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
                        'label' => 'Input Type',
                        'type' => 'select_from_array',
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
                        ],
                        'wrapper' => [
                            'class' => 'form-group col-md-4',
                        ],
                        'allows_null' => false,
                    ],
                    [
                        'name' => 'name',
                        'label' => 'Field Name',
                        'type' => 'text',
                        'allows_null' => false,
                        'wrapper' => [
                            'class' => 'form-group col-md-4',
                        ],
                    ],
                    [
                        'name' => 'label',
                        'label' => 'Display Label',
                        'type' => 'text',
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
                        'name' => 'value',
                        'label' => 'Default Value',
                        'type' => 'textarea',
                    ],
                    [
                        'name' => 'is_required',
                        'label' => 'Field value is required?',
                        'type' => 'boolean',
                    ],
                    [
                        'name' => 'is_inline',
                        'label' => 'Display label and field in single line?',
                        'type' => 'boolean',
                    ],
                    [
                        'name' => 'validation',
                        'label' => 'Additional Validation',
                        'type' => 'textarea',
                        'hint' => 'Rules Docs (<a href="https://laravel.com/docs/10.x/validation#available-validation-rules" target="_blank">Available Validation Rules</a>).',
                    ],
                ],
            ],
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
