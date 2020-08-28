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
                        'display' => __('mailup::settings.endpoint'),
                        'instructions' => __('mailup::settings.endpoint_descr'),
                        'validate' => 'required|url',
                    ],
                    'forms' => [
                        'type' => 'form',
                        'display' => __('mailup::settings.forms'),
                        'instructions' => __('mailup::settings.forms_descr'),
                        'max_items' => 100,
                    ],
                ],
            ],
        ]);
    }
}
