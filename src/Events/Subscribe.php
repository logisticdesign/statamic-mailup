<?php

namespace Logisticdesign\Mailup\Events;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
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

        try {
            Mailup::subscribe($event->submission->data());
        } catch (MailupException $e) {
            throw ValidationException::withMessages([
                'mailup' => $e->getMessage(),
            ]);
        }
    }

    protected function canHandleSubscription($event)
    {
        $forms = Mailup::forms();
        $handle = optional($event->submission)->form->handle() ?? null;

        return Mailup::validConfigForSubscription() and in_array($handle, $forms);
    }
}
