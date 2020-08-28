<?php

namespace Logisticdesign\Mailup\Facades;

use Illuminate\Support\Facades\Facade;
use Logisticdesign\Mailup\Mailup as MailupService;

class Mailup extends Facade
{
    protected static function getFacadeAccessor()
    {
        return MailupService::class;
    }
}
