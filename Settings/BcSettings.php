<?php
/**
 * Product Barcode &amp; Generator Settings
 * @since 1.0
**/

namespace Modules\BarcodeGenerator\Settings;

use App\Services\SettingsPage;
use App\Services\ModulesService;
use App\Services\Helper;

class BcSettings extends SettingsPage
{
    protected $form;
    public const identifier      =   'bcsettings';

    public function __construct()
    {
        /**
         * @var ModulesService
         */
        $module     =   app()->make(ModulesService::class);

        /**
         * Settings Form definition.
         */
        $this->form     =   [
            'title'         =>  __m('Settings', 'BarcodeGenerator'),
            'description'   =>  __m('No description has been provided.', 'BarcodeGenerator'),
            'tabs'      =>  [
                'general'   =>  [
                    'label'     =>  __m('General Settings', 'BarcodeGenerator'),
                    'fields'    =>  [
                        // ...
                    ]
                ]
            ]
        ];
    }
}
