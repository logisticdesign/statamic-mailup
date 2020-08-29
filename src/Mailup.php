<?php

namespace Logisticdesign\Mailup;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class Mailup
{
    /**
     * MailUp settings.
     *
     * @var \Illuminate\Support\Collection|array
     */
    protected $settings = [];

    /**
     * Subscription response status codes.
     *
     * @var array
     */
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

    /**
     * MailUp settings.
     *
     * @return \Illuminate\Support\Collection
     */
    public function settings()
    {
        return $this->settings;
    }

    /**
     * API endpoint.
     *
     * @return string
     */
    public function endpoint()
    {
        return rtrim($this->settings->get('endpoint'), '/');
    }

    /**
     * List ID.
     *
     * @return string
     */
    public function listId()
    {
        return $this->settings->get('list_id');
    }

    /**
     * Email field name.
     *
     * @return string
     */
    public function emailField()
    {
        return $this->settings->get('email_field');
    }

    /**
     * Double Opt-In.
     *
     * @return bool
     */
    public function doubleOptIn()
    {
        return (bool) $this->settings->get('double_optin', true);
    }

    /**
     * Forms used for subscription.
     *
     * @return array
     */
    public function forms()
    {
        return $this->settings->get('forms', []);
    }

    /**
     * Custom fields relationships.
     *
     * @return \Illuminate\Support\Collection
     */
    public function customFields()
    {
        return collect($this->settings->get('custom_fields'), []);
    }

    /**
     * Subscription response status codes.
     *
     * @return array
     */
    public function allStatus()
    {
        return $this->status;
    }

    /**
     * Fine subscription response status code.
     *
     * @param int $code
     * @param mixed $default
     * @return int|mixed
     */
    public function status($code, $default = null)
    {
        return $this->status[$code] ?? $default;
    }

    /**
     * Subscribe recipient.
     *
     * @param array $data
     * @return int
     */
    public function subscribe(array $data)
    {
        $params = [
            'list' => $this->listId(),
            'email' => $data[$this->emailField()] ?? null,
            'confirm' => $this->doubleOptIn(),
            'retCode' => 1,
        ];

        $customFields = $this->mapCustomFieldsWithSubmission($data);

        if ($customFields->count()) {
            $params['csvFldNames'] = $customFields->keys()->implode(';');

            $params['csvFldValues'] = $this->escapeCsvDelimiter(
                $customFields->values()
            )->implode(';');
        }

        $response = Http::post(
            $this->endpoint().'/frontend/xmlsubscribe.aspx?'.http_build_query($params)
        );

        return $response->json();
    }

    /**
     * Valid response status codes.
     *
     * @return array
     */
    public function validStatusCode()
    {
        return [0];
    }

    /**
     * Is a valid response status code.
     *
     * @return bool
     */
    public function isValidStatusCode($code)
    {
        return in_array($code, $this->validStatusCode());
    }

    /**
     * Map Custom Fields with submission data.
     *
     * @param array $submission
     * @return \Illuminate\Support\Collection
     */
    protected function mapCustomFieldsWithSubmission(array $submission)
    {
        return $this->customFields()->mapWithKeys(function ($item) use ($submission) {
            return [
                $item['mailup_field'] => $submission[$item['form_field']] ?? null,
            ];
        });
    }

    /**
     * Escape CSV delimiter from collection values.
     *
     * @param \Illuminate\Support\Collection $items
     * @return \Illuminate\Support\Collection
     */
    protected function escapeCsvDelimiter(Collection $items)
    {
        return $items->map(function ($value) {
            return str_replace(';', ' ', $value);
        });
    }
}
