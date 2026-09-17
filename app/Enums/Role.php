<?php

namespace App\Enums;

enum Role: string
{
    case ADMIN = 'admin';
    case MONITOR = 'monitor';
    case LOKET = 'loket';
    case ARSIP = 'arsip';
    case SEKSI1 = 'seksi1';
    case SEKSI2 = 'seksi2';
}
