<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class IsProductNotAdded extends Constraint
{
    public $message = 'This product is already in your cart';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
