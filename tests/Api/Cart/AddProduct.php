<?php

namespace Test\Api\Cart;

use App\DataFixtures\CartFixture;
use App\DataFixtures\FixtureLoadable;
use App\DataFixtures\ProductFixture;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Serializer\Serializer;

class AddProduct extends WebTestCase
{
    use FixtureLoadable;

    const ADD_PRODUCT_URI = '/carts/%s/products';
    const GET_PRODUCT_URI = '/products/%s';
    const DATA_FORMAT = 'json';

    /**
     * @var \Symfony\Bundle\FrameworkBundle\Client
     */
    private $client;

    /**
     * @var Serializer
     */
    private $serializer;

    protected function setUp()
    {
        parent::setUp();
        $this->client = self::createClient();

        $entityManager = self::$container->get('doctrine')->getManager();
        $this->loadFixtures($entityManager, [
            ProductFixture::class,
            CartFixture::class,
        ]);

        $this->serializer = self::$container->get('serializer');
    }

    public function addProductData(): array
    {
        $product1 = [
            'id' => 1,
            'title' => 'Fallout',
            'price' => '1.99',
        ];
        $product2 = [
            'id' => 2,
            'title' => 'Don\'t Starve',
            'price' => '2.99',
        ];

        return [
            '#1 should add product to empty cart' => [
                'id' => 1,
                'input' => [
                    'id' => sprintf(self::GET_PRODUCT_URI, 1)
                ],
                'expected' => [
                    'id' => 1,
                    'totalPrice' => '1.99',
                    'products' => [
                        $product1
                    ],
                ],
                'statusCode' => 201,
            ],
            '#2 should add product to not empty cart' => [
                'id' => 2,
                'input' => [
                    'id' => sprintf(self::GET_PRODUCT_URI, 2)
                ],
                'expected' => [
                    'id' => 2,
                    'totalPrice' => '4.98',
                    'products' => [
                        $product1,
                        $product2
                    ],
                ],
                'statusCode' => 201,
            ],
        ];
    }

    /**
     * @test
     * @dataProvider addProductData
     *
     * @param int $id
     * @param array $input
     * @param array $expected
     * @param int $statusCode
     */
    public function it_should_add_product_to_cart(int $id, array $input, array $expected, int $statusCode)
    {
        $this->client->request(
            'POST',
            sprintf(self::ADD_PRODUCT_URI, $id),
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_ACCEPT' => 'application/json',
            ],
            $this->serializer->serialize($input, self::DATA_FORMAT)
        );

        $response = $this->client->getResponse();
        $this->assertEquals($statusCode, $response->getStatusCode());
        $this->assertEquals(
            $expected,
            $this->serializer->decode($response->getContent(), self::DATA_FORMAT)
        );
    }

    public function notAddProductData(): array
    {
        return [
            '#1 should not add product to cart (duplicate)' => [
                'id' => 2,
                'input' => [
                    'id' => sprintf(self::GET_PRODUCT_URI, 1)
                ],
                'statusCode' => 400,
            ],
            '#2 should not add product to cart (full)' => [
                'id' => 3,
                'input' => [
                    'id' => sprintf(self::GET_PRODUCT_URI, 4)
                ],
                'statusCode' => 400,
            ],
        ];
    }

    /**
     * @test
     * @dataProvider notAddProductData
     *
     * @param int $id
     * @param array $input
     * @param int $statusCode
     */

    public function it_should_not_add_product_to_cart(int $id, array $input, int $statusCode)
    {
        $this->client->request(
            'POST',
            sprintf(self::ADD_PRODUCT_URI, $id),
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_ACCEPT' => 'application/json',
            ],
            $this->serializer->serialize($input, self::DATA_FORMAT)
        );

        $response = $this->client->getResponse();
        $this->assertEquals($statusCode, $response->getStatusCode());
    }
}
