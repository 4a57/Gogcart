<?php

namespace App\Validator\Constraints;

use App\Entity\CartProduct;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * @Annotation
 */
class IsProductNotAddedValidator extends ConstraintValidator
{
    public function validate($cartProduct, Constraint $constraint)
    {
        if (!$constraint instanceof IsProductNotAdded) {
            throw new UnexpectedTypeException($constraint, IsProductNotAdded::class);
        }

        if (!$cartProduct instanceof CartProduct) {
            throw new UnexpectedTypeException($cartProduct, CartProduct::class);
        }

        if ($this->isProductInCart($cartProduct)) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }

    private function isProductInCart(CartProduct $newCartProduct): bool
    {
        foreach ($newCartProduct->getCart()->getCartProducts() as $cartProduct) {
            if ($this->isProductDuplicated($cartProduct, $newCartProduct)) {
                return true;
            }
        }

        return false;
    }

    private function isProductDuplicated(CartProduct $a, CartProduct $b): bool
    {
        return $a->getProduct() == $b->getProduct() && $a->getId() != $b->getId();
    }
}
