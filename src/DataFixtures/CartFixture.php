<?php

namespace App\DataFixtures;

use App\Entity\Cart;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class CartFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $productRepository = $manager->getRepository(Product::class);

        $manager->persist($this->createCart());

        $manager->persist($this->createCart([
            $productRepository->find(1),
        ]));

        $manager->persist($this->createCart([
            $productRepository->find(1),
            $productRepository->find(2),
            $productRepository->find(3),
        ]));

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            ProductFixture::class,
        ];
    }

    /**
     * @param Product[] $products
     * @return Cart
     */
    private function createCart(array $products = []): Cart
    {
        $cart = new Cart();
        foreach ($products as $product) {
            $cart->addProduct($product);
        }

        return $cart;
    }
}
