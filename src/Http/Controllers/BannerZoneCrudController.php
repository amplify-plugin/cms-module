<?php

namespace Amplify\System\Cms\Http\Controllers;

use Amplify\System\Cms\Http\Requests\BannerZoneRequest;
use Amplify\System\Cms\Models\BannerZone;
use Amplify\System\Abstracts\BackpackCustomCrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Backpack\CRUD\app\Library\Widget;

/**
 * Class BannerZoneCrudController
 *
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class BannerZoneCrudController extends BackpackCustomCrudController
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
        CRUD::setModel(BannerZone::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/banner-zone');
        CRUD::setEntityNameStrings('banner-zone', 'banner zones');
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
        CRUD::column('id')->type('number')->thousands_sep('');
        CRUD::column('name');
        CRUD::column('code');
        CRUD::column('fetch_data_from_easyask')->label('Fetch From EasyAsk')->type('boolean');
    }

    public function setupShowOperation()
    {
        CRUD::column('id')->type('number')->thousands_sep('');
        CRUD::column('name');
        CRUD::column('code');
        CRUD::column('fetch_data_from_easyask')->label('Fetch From EasyAsk')->type('boolean');
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
        CRUD::setValidation(BannerZoneRequest::class);

        Widget::add([
            'type' => 'inline_script',
            'stack' => 'after_scripts',
            'content' => <<<JS
crud.field('fetch_data_from_easyask').onChange(function (field) {
    crud.field('banners').hide(field.value == 1);
}).change();
JS
        ]);

        CRUD::addFields([
            [
                'type' => 'text',
                'name' => 'name',
                'label' => 'Name',
                'required' => true
            ],

            [
                'type' => 'text',
                'name' => 'code',
                'label' => 'Code',
                'required' => true
            ],

            [
                'type' => 'boolean',
                'name' => 'fetch_data_from_easyask',
                'label' => 'Managed By EasyAsk',
                'hint' => 'If enabled contents will be pull from EasyAsk.'
            ],

            [
                'name' => 'banners',
                'label' => 'Banner Items',
                'type' => 'relationship',
                'entity' => 'banners',
                'attribute' => 'name',
                'reorder' => true,
                'new_item_label' => 'Add Banner Item',
                'subfields' => [
                    //Name
                    [
                        'name' => 'name',
                        'label' => 'Heading',
                        'type' => 'text',
                    ],
                    [
                        'name' => 'has_heading',
                        'label' => 'Show heading text on slider item',
                        'type' => 'boolean',
                    ],
                    //Background Type
                    [
                        'name' => 'background_type',
                        'type' => 'select_from_array',
                        'label' => 'Slider Media Type',
                        'default' => 'image',
                        'options' => [
                            'image' => 'Image',
                            'video' => 'Video',
                        ]
                    ],
                    //Background Image
                    [
                        'label' => 'Slider Media',
                        'name' => 'background_image',
                        'type' => 'browse',
                        'readonly' => false,
                        'attributes' => [
                            'id' => 'background_image',
                        ],
                    ],
                    [
                        'name' => 'button_link',
                        'label' => 'Link',
                        'type' => 'url',
                    ],
                    [
                        'name' => 'open_new_tab',
                        'label' => 'Open link in new tab',
                        'type' => 'boolean',
                    ],
                    [
                        'name' => 'has_advance_option',
                        'label' => 'Use Advance Option',
                        'type' => 'boolean',
                        'default' => false,
                        'fake' => true,
                    ],
                    //Foreground Image
                    [
                        'label' => 'Foreground',
                        'name' => 'image',
                        'type' => 'browse',
                        'readonly' => false,
                        'attributes' => [
                            'id' => 'image',
                        ],
                    ],
                    [
                        'name' => 'has_content',
                        'label' => 'Display Content',
                        'type' => 'boolean',
                    ],
                    [
                        'name' => 'content',
                        'label' => 'Content',
                        'type' => 'ckeditor',
                    ],
                    [
                        'name' => 'has_button',
                        'label' => 'Display Button',
                        'type' => 'boolean',
                    ],
                    [
                        'name' => 'button_title',
                        'label' => 'Button Label',
                        'type' => 'text',
                    ],
                    [
                        'name' => 'button_style',
                        'label' => 'Button Style',
                        'type' => 'select_from_array',
                        'default' => 'primary',
                        'options' => [
                            'primary' => 'Primary',
                            'success' => 'Success',
                            'warning' => 'Warning',
                            'danger' => 'Danger',
                            'info' => 'Info',
                            'light' => 'Light',
                            'dark' => 'Dark',
                        ]
                    ],

                    [
                        'label' => 'Foreground Text Alignment',
                        'name' => 'text_alignment',
                        'type' => 'radio',
                        'wrapper' => ['class' => 'form-group col-md-3'],
                        'options' => [
                            'left' => 'Left',
                            'center' => 'Center',
                            'right' => 'Right',
                        ]
                    ],
                    [
                        'label' => 'Foreground Image Alignment',
                        'name' => 'image_alignment',
                        'type' => 'radio',
                        'wrapper' => ['class' => 'form-group col-md-3'],
                        'options' => [
                            'left' => 'Left',
                            'center' => 'Center',
                            'right' => 'Right',
                        ]
                    ],
                    [
                        'label' => 'Foreground Text/Image Position',
                        'name' => 'alignment',
                        'type' => 'radio',
                        'wrapper' => ['class' => 'form-group col-md-3'],
                        'options' => [
                            'left' => '<img src="'
                                . mix('img/img-right.png', 'vendor/backend')
                                . '" alt="Text Left & Image Right" width="80"> Left',
                            'right' => '<img src="'
                                . mix('img/img-left.png', 'vendor/backend')
                                . '" alt="Text Left & Image Right" width="80"> Right'
                        ],
                        'escape' => true,
                    ],
                    [
                        'name' => 'slider_ratio',
                        'label' => 'Foreground Section Ratio',
                        'type' => 'radio',
                        'wrapper' => ['class' => 'form-group col-md-3'],
                        'options' => [
                            '3' => '30% - 70%',
                            '4' => '40% - 60%',
                            '6' => '50% - 50%',
                            '8' => '60% - 40%',
                            '9' => '70% - 30%',
                        ]
                    ],
                    [
                        'name' => 'enabled',
                        'label' => 'Enabled',
                        'type' => 'boolean',
                        'hint' => 'If enabled slider items will appear on site.'
                    ],
                ]
            ]
        ]);


        Widget::add([
            'type' => 'inline_script',
            'stack' => 'after_scripts',
            'content' => <<<JS
crud.field('banners').subfield('has_advance_option').onChange(function (field) {
    let value = field.value;
    let rowNumber = field.rowNumber;
    crud.field('banners').subfield('image', rowNumber).show(value == 1);
    crud.field('banners').subfield('has_content', rowNumber).show(value == 1);
    crud.field('banners').subfield('content', rowNumber).show(value == 1);
    crud.field('banners').subfield('has_button', rowNumber).show(value == 1);
    crud.field('banners').subfield('button_title', rowNumber).show(value == 1);
    crud.field('banners').subfield('button_style', rowNumber).show(value == 1);
    crud.field('banners').subfield('text_alignment', rowNumber).show(value == 1);
    crud.field('banners').subfield('image_alignment', rowNumber).show(value == 1);
    crud.field('banners').subfield('alignment', rowNumber).show(value == 1);
    crud.field('banners').subfield('slider_ratio', rowNumber).show(value == 1);
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
    }
}
