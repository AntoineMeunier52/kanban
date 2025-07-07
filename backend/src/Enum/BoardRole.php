<?php

namespace App\Enum;

enum BoardRole: string
{
    case ADMIN = 'admin';
    case WRITE = 'write';
    case READ = 'read';
}
