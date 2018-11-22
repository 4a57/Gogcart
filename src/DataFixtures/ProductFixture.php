<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class ProductFixture extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $products = [
            ['title' => 'Fallout', 'price' => '1.99'],
            ['title' => 'Don\'t Starve', 'price' => '2.99'],
            ['title' => 'Baldur\'s Gate', 'price' => '3.99'],
            ['title' => 'Icewind Dale', 'price' => '4.99'],
            ['title' => 'Bloodborne', 'price' => '5.99'],
        ];

        foreach ($products as $product) {
            $manager->persist($this->createProduct($product['title'], $product['price']));
        }

        $manager->flush();
    }

    private function createProduct(string $name, string $price): Product
    {
        $product = new Product();
        $product
            ->setTitle($name)
            ->setPrice($price);
        
        return $product;
    }
}
