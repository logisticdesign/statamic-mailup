<?php

namespace Logisticdesign\Mailup\Http\Controllers;

use Illuminate\Http\Request;
use Logisticdesign\Mailup\Settings;
use Statamic\Facades\Blueprint;
use Statamic\Http\Controllers\CP\CpController;
use Statamic\Support\Arr;

class SettingsController extends CpController
{
    public function edit()
    {
        $values = Settings::load()->all();
        $blueprint = $this->blueprint();

        $fields = $blueprint->fields()->addValues($values)->preProcess();

        return view('mailup::settings', [
            'title' => 'MailUp',
            'action' => cp_route('mailup.settings.update'),
            'blueprint' => $blueprint->toPublishArray(),
            'values' => $fields->values(),
            'meta' => $fields->meta(),
        ]);
    }

    public function update(Request $request)
    {
        $blueprint = $this->blueprint();

        $fields = $blueprint->fields()->addValues($request->all());

        $fields->validate();

        $values = $fields->process()->values()->all();
        $values = Arr::removeNullValues($values);

        Settings::load($values)->save();
    }

    protected function blueprint()
    {
        return Blueprint::makeFromSections([
            'main' => [
                'display' => 'Main',
                'fields' => [
                    'endpoint' => [
                        'type' => 'text',
                        'width' => 50,
                        'display' => __('mailup::settings.endpoint'),
                        'instructions' => __('mailup::settings.endpoint_descr'),
                        'validate' => 'required|url',
                    ],
                    'list_id' => [
                        'type' => 'text',
                        'width' => 50,
                        'display' => __('mailup::settings.list_id'),
                        'instructions' => __('mailup::settings.list_id_descr'),
                        'validate' => 'required|integer',
                    ],
                    'email_field' => [
                        'type' => 'text',
                        'width' => 50,
                        'display' => __('mailup::settings.email_field'),
                        'instructions' => __('mailup::settings.email_field_descr'),
                        'validate' => 'required',
                    ],
                    'double_optin' => [
                        'type' => 'toggle',
                        'width' => 50,
                        'display' => __('mailup::settings.double_optin'),
                        'instructions' => __('mailup::settings.double_optin_descr'),
                    ],
                    'forms' => [
                        'type' => 'form',
                        'display' => __('mailup::settings.forms'),
                        'max_items' => 100,
                    ],
                    'custom_fields' => [
                        'type' => 'grid',
                        'mode' => 'table',
                        'add_row' => __('mailup::settings.custom_fields_add_row'),
                        'display' => __('mailup::settings.custom_fields'),
                        'instructions' => __('mailup::settings.custom_fields_descr'),
                        'fields' => [
                            [
                                'handle' => 'form_field',
                                'field' => [
                                    'type' => 'text',
                                    'display' => __('mailup::settings.custom_fields_form_field'),
                                ],
                            ],
                            [
                                'handle' => 'mailup_field',
                                'field' => [
                                    'type' => 'text',
                                    'display' => __('mailup::settings.custom_fields_mailup_field'),
                                    'instructions' => __('mailup::settings.custom_fields_mailup_field_descr'),
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ]);
    }
}
