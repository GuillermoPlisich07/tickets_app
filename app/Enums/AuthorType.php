<?php

namespace App\Enums;

enum AuthorType: string
{
    case Customer = 'customer';
    case Operator = 'operator';
}
