<?php

namespace Test\Api\Cart;

use App\DataFixtures\CartFixture;
use App\DataFixtures\FixtureLoadable;
use App\DataFixtures\ProductFixture;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Serializer\Serializer;

class RemoveProductTest extends WebTestCase
{
    use FixtureLoadable;

    const REMOVE_PRODUCT_URI = '/api/carts/%s/products/%s';
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

    public function getData(): array
    {
        return [
            '#1 should delete product' => [
                'cartId' => 3,
                'productId' => 1,
                'statusCode' => 204,
            ],
            '#2 should not found cart' => [
                'cartId' => 999,
                'productId' => 1,
                'statusCode' => 404,
            ],
            '#3 should not found product' => [
                'cartId' => 3,
                'productId' => 999,
                'statusCode' => 404,
            ],
            '#4 should not found existing product in cart' => [
                'cartId' => 1,
                'productId' => 3,
                'statusCode' => 400,
            ],
        ];
    }

    /**
     * @test
     * @dataProvider getData
     *
     * @param int $cartId
     * @param int $productId
     * @param int $statusCode
     */
    public function it_should_assert_equal(int $cartId, int $productId, int $statusCode)
    {
        $this->client->request(
            'DELETE',
            sprintf(self::REMOVE_PRODUCT_URI, $cartId, $productId),
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
