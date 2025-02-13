<?php

namespace App\Enums;

enum NotificationTypesEnum: string
{
    case Email = 'mail';
    case SMS = 'vonage';
}
