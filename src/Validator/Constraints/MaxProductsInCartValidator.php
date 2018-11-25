<?php

namespace App\Validator\Constraints;

use App\Entity\Cart;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * @Annotation
 */
class MaxProductsInCartValidator extends ConstraintValidator
{
    public function validate($cart, Constraint $constraint)
    {
        if (!$constraint instanceof MaxProductsInCart) {
            throw new UnexpectedTypeException($constraint, MaxProductsInCart::class);
        }

        if (!$cart instanceof Cart) {
            throw new UnexpectedTypeException($cart, Cart::class);
        }

        if ($cart->getCartProducts()->count() > $constraint->limit) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ limit }}', $constraint->limit)
                ->addViolation();
        }
    }
}
