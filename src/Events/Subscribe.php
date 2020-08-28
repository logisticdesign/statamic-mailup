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
        if (! $this->requireSubscription($event)) {
            return false;
        }

        $mailupData = $this->mailupData();
        $submissionData = $event->submission->data();

        $email = $mailupData['email'] ?? $submissionData['email'] ?? null;

        if (! $listId = $mailupData['list_id'] ?? null) {
            throw new MailupException('Missing "list_id" value');
        }

        try {
            Mailup::subscribe($email, [
                'list_id' => $listId,
                'double_optin' => $mailupData['double_optin'],
            ]);
        } catch (MailupException $e) {
            throw ValidationException::withMessages(['mailup' => $e->getMessage()]);
        }
    }

    protected function requireSubscription($event)
    {
        if (! strlen(Mailup::endpoint())) {
            return false;
        }

        $forms = Mailup::forms();
        $handle = optional($event->submission)->form->handle() ?? null;

        return $this->request->has('mailup') and in_array($handle, $forms);
    }

    protected function mailupData()
    {
        return $this->request->input('mailup', []);
    }
}
