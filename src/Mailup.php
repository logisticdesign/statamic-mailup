<?php

namespace Logisticdesign\Mailup;

use Illuminate\Support\Facades\Http;
use Logisticdesign\Mailup\Exceptions\MailupException;

class Mailup
{
    protected $settings = [];

    protected $status = [
        0 => 'success',
        1 => 'generic_error',
        2 => 'invalid_email',
        3 => 'already_subscribed',
        -1011 => 'ip_not_registered',
    ];

    public function __construct()
    {
        $this->settings = Settings::load();
    }

    public function settings()
    {
        return $this->settings;
    }

    public function endpoint()
    {
        return rtrim($this->settings->get('endpoint'), '/');
    }

    public function forms()
    {
        return $this->settings->get('forms');
    }

    public function allStatus()
    {
        return $this->status;
    }

    public function status($code, $default = null)
    {
        return $this->status[$code] ?? $default;
    }

    public function subscribe($email, $options = [])
    {
        $listId = $options['list_id'] ?? null;
        $source = $options['source'] ?? 'website';
        $doubleOptIn = $options['double_optin'] ?? true;

        $param = [
            'list' => $listId,
            'email' => $email,
            'source' => $source,
            'confirm' => $doubleOptIn,
            'retCode' => 1,
        ];

        $response = Http::post(
            $this->endpoint().'/frontend/xmlsubscribe.aspx?'.http_build_query($param)
        );

        $statusCode = $response->json();

        if (! in_array($statusCode, $this->validStatusCode())) {
            $message = $this->status($statusCode, __('mailup::messages.generic_error'));

            throw new MailupException(__("mailup::messages.{$message}"), $statusCode);
        }

        return $statusCode;
    }

    public function validStatusCode()
    {
        return [0, 3];
    }
}
