<?php

namespace Test\Api\Cart;

use App\DataFixtures\CartFixture;
use App\DataFixtures\FixtureLoadable;
use App\DataFixtures\ProductFixture;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Serializer\Serializer;

class GetTest extends WebTestCase
{
    use FixtureLoadable;

    const GET_CART_URI = '/carts/%s';
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

    public function getCartData(): array
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
        $product3 = [
            'id' => 3,
            'title' => 'Baldur\'s Gate',
            'price' => '3.99',
        ];

        return [
            '#1 should get empty cart' => [
                'id' => 1,
                'expected' => [
                    'id' => 1,
                    'totalPrice' => '0',
                    'products' => []
                ],
                'statusCode' => 200,
            ],
            '#2 should get cart with 2 product' => [
                'id' => 2,
                'expected' => [
                    'id' => 2,
                    'totalPrice' => '1.99',
                    'products' => [
                        $product1,
                    ]
                ],
                'statusCode' => 200,
            ],
            '#3 should get cart with 3 products' => [
                'id' => 3,
                'expected' => [
                    'id' => 3,
                    'totalPrice' => '8.97',
                    'products' => [
                        $product1,
                        $product2,
                        $product3,
                    ]
                ],
                'statusCode' => 200,
            ],
        ];
    }

    /**
     * @test
     * @dataProvider getCartData
     *
     * @param int $id
     * @param array $expected
     * @param int $statusCode
     */
    public function it_should_get_cart(int $id, array $expected, int $statusCode)
    {
        $this->client->request(
            'GET',
            sprintf(self::GET_CART_URI, $id),
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_ACCEPT' => 'application/json',
            ]
        );

        $response = $this->client->getResponse();
        $this->assertEquals($statusCode, $response->getStatusCode());
        $this->assertEquals(
            $expected,
            $this->serializer->decode($response->getContent(), self::DATA_FORMAT)
        );
    }

    public function getNotFoundCartData(): array
    {
        return [
            '#1 should throw 404 on not exist product' => [
                'id' => 999,
                'statusCode' => 404,
            ],
        ];
    }

    /**
     * @test
     * @dataProvider getNotFoundCartData
     *
     * @param int $id
     * @param int $statusCode
     */
    public function it_should_throw_404(int $id, int $statusCode)
    {
        $this->client->request(
            'GET',
            sprintf(self::GET_CART_URI, $id),
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_ACCEPT' => 'application/json',
            ]
        );

        $response = $this->client->getResponse();
        $this->assertEquals($statusCode, $response->getStatusCode());
    }
}
