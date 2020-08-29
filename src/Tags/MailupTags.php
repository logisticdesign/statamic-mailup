<?php

namespace Logisticdesign\Mailup\Tags;

use Statamic\Tags\Tags;

class MailupTags extends Tags
{
    protected static $handle = 'mailup';

    /**
     * The {{ mailup:status }} tag.
     *
     * @return string
     */
    public function status()
    {
        if (! session()->has('mailup::subscription')) {
            return false;
        }

        return session()->get('mailup::subscription');
    }
}
