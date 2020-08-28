<?php

use Illuminate\Support\Facades\Route;

Route::get('mailup', 'SettingsController@edit')->name('mailup.settings.edit');
Route::post('mailup', 'SettingsController@update')->name('mailup.settings.update');
