<?php

namespace Logisticdesign\Mailup\Events;

use Illuminate\Http\Request;
use Logisticdesign\Mailup\Exceptions\MailupException;
use Logisticdesign\Mailup\Facades\Mailup;
use Statamic\Events\FormSubmitted;

class Subscribe
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function handle(FormSubmitted $event)
    {
        if (! $this->canHandleSubscription($event)) {
            return false;
        }

        $code = Mailup::subscribe($event->submission->data());

        $status = Mailup::status($code, 'generic_error');

        session()->flash('mailup::subscription', [
            'code' => $code,
            'message' => __("mailup::messages.{$status}"),
            'success' => Mailup::isValidStatusCode($code),
        ]);
    }

    protected function canHandleSubscription($event)
    {
        if (! Mailup::endpoint()) {
            throw new MailupException('Missing endpoint');
        }

        if (! Mailup::listId()) {
            throw new MailupException('Missing List ID');
        }

        if (! Mailup::emailField()) {
            throw new MailupException('Missing Email field name');
        }

        $forms = Mailup::forms();
        $handle = optional($event->submission)->form->handle() ?? null;

        return in_array($handle, $forms);
    }
}
