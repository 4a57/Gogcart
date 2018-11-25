<?php

namespace App\Tests\Validator\Constraints;

use App\Entity\Cart;
use App\Entity\Product;
use App\Validator\Constraints\MaxProductsInCart;
use App\Validator\Constraints\MaxProductsInCartValidator;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

class MaxProductsInCartValidatorTest extends ConstraintValidatorTestCase
{
    protected function createValidator()
    {
        return new MaxProductsInCartValidator();
    }

    public function getCartWithProducts(): array
    {
        $cart = new Cart();
        $cart
            ->addProduct($this->createProduct(1, 'G'))
            ->addProduct($this->createProduct(2, 'G'))
            ->addProduct($this->createProduct(3, 'G'));

        return [[$cart]];
    }

    /**
     * @test
     * @dataProvider getCartWithProducts
     *
     * @param Cart $cart
     */
    public function it_should_be_valid_when_add_less_products_than_limit(Cart $cart)
    {
        $this->validator->validate($cart, new MaxProductsInCart(['limit' => 4]));

        $this->assertNoViolation();
    }

    /**
     * @test
     * @dataProvider getCartWithProducts
     *
     * @param Cart $cart
     */
    public function it_should_be_valid_when_add_exact_products_than_limit(Cart $cart)
    {
        $this->validator->validate($cart, new MaxProductsInCart(['limit' => 3]));

        $this->assertNoViolation();
    }

    /**
     * @test
     * @dataProvider getCartWithProducts
     *
     * @param Cart $cart
     */
    public function it_should_be_invalid_when_add_more_products_than_limit(Cart $cart)
    {
        $this->validator->validate($cart, new MaxProductsInCart(['limit' => 2, 'message' => 'test']));

        $this->buildViolation('test')
            ->setParameter('{{ limit }}', 2)
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
