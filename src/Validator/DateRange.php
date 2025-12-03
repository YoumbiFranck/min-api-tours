<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class DateRange extends Constraint
{
    public string $message = 'La date de fin doit être postérieure à la date de début';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}