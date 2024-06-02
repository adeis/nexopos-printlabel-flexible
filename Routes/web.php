<?php

use Illuminate\Support\Facades\Route;

Route::get('/dashboard/barcode-generator', [
    'middleware' => 'auth',
 'uses' => 'Modules\BarcodeGenerator\Http\Controllers\MainController@index'
 ])->name('bc.print-labels');
