<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class DateRangeValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof DateRange) {
            throw new UnexpectedTypeException($constraint, DateRange::class);
        }

        // Si la valeur est null, on laisse passer (NotNull s'en occupe)
        if (null === $value) {
            return;
        }

        // Récupérer l'objet Tour complet
        $tour = $this->context->getObject();

        // Vérifier que endDate > startDate
        if ($tour->getEndDate() && $tour->getStartDate()) {
            if ($tour->getEndDate() <= $tour->getStartDate()) {
                $this->context->buildViolation($constraint->message)
                    ->atPath('endDate')
                    ->addViolation();
            }
        }
    }
}