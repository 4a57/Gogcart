<?php

namespace App\Tests\Validator\Constraints;

use App\Entity\Cart;
use App\Entity\CartProduct;
use App\Entity\Product;
use App\Validator\Constraints\IsProductNotAdded;
use App\Validator\Constraints\IsProductNotAddedValidator;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

class IsProductNotAddedValidatorTest extends ConstraintValidatorTestCase
{
    protected function createValidator()
    {
        return new IsProductNotAddedValidator();
    }

    public function getCartWithProducts(): array
    {
        $cart = new Cart();
        $cart
            ->addProduct($this->createProduct(1, 'a'))
            ->addProduct($this->createProduct(2, 'b'))
            ->addProduct($this->createProduct(3, 'c'));

        return [[$cart]];
    }

    /**
     * @test
     * @dataProvider getCartWithProducts
     *
     * @param Cart $cart
     */
    public function it_should_be_valid_when_product_is_not_duplicated(Cart $cart)
    {
        $product = $this->createProduct(4, 'asd');

        $cartProduct = new CartProduct();
        $cartProduct
            ->setCart($cart)
            ->setProduct($product);

        $cart->addProduct($product);

        $this->validator->validate($cartProduct, new IsProductNotAdded());

        $this->assertNoViolation();
    }

    /**
     * @test
     * @dataProvider getCartWithProducts
     *
     * @param Cart $cart
     */
    public function it_should_be_valid_when_product_is_duplicated(Cart $cart)
    {
        $product = $this->createProduct(4, 'asd');

        $cartProduct = new CartProduct();
        $cartProduct
            ->setCart($cart)
            ->setProduct($product);
        $this->setPrivateId($cartProduct, 1);

        $cart->addCartProduct($cartProduct);

        $cartProductDuplicated = new CartProduct();
        $cartProductDuplicated
            ->setCart($cart)
            ->setProduct($product);

        $cart->addCartProduct($cartProductDuplicated);

        $this->validator->validate($cartProductDuplicated, new IsProductNotAdded(['message' => 'test']));

        $this->buildViolation('test')
            ->assertRaised();
    }

    private function createProduct(int $id, string $title): Product
    {
        $product = new Product();
        $product->setTitle($title);
        $this->setPrivateId($product, $id);

        return $product;
    }

    private function setPrivateId($entity, $id): void
    {
        $idReflector = new \ReflectionProperty(get_class($entity), 'id');
        $idReflector->setAccessible(true);
        $idReflector->setValue($entity, $id);
    }
}
