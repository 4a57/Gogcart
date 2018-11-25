<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class MaxProductsInCart extends Constraint
{
    public $limit = 3;
    public $message = 'You cannot add another products to your cart. Limit is {{ limit }}';
}
