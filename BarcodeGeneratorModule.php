<?php

namespace Modules\BarcodeGenerator;

use App\Classes\Hook;
use App\Services\Module;
use Illuminate\Support\Facades\Event;

class BarcodeGeneratorModule extends Module
{
    public function __construct()
    {
        parent::__construct(__FILE__);
        Hook::addFilter('ns-dashboard-menus', function ($menus) {
            $menus    =   array_insert_after($menus, 'inventory', [
                'foobar'    =>    [
                    'label'   =>    __('Bulk Label Print'),
                    'icon'    =>    'las la-print',
                    'href'    =>    route('bc.print-labels'),
                ]
            ]);

            return $menus; // <= do not forget
        });
    }
}
