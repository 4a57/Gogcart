<?php

namespace Test\Api\Cart;

use App\DataFixtures\CartFixture;
use App\DataFixtures\FixtureLoadable;
use App\DataFixtures\ProductFixture;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Serializer\Serializer;

class CreateTest extends WebTestCase
{
    use FixtureLoadable;

    const CREATE_CART_URI = '/carts';
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
            '#1 should create new cart' => [
                'expected' => [
                    'id' => 4,
                    'products' => [],
                    'totalPrice' => '0'
                ],
                'statusCode' => 201,
            ],
        ];
    }

    /**
     * @test
     * @dataProvider getData
     *
     * @param array $expected
     * @param int $statusCode
     */
    public function it_should_assert_equal(array $expected, int $statusCode)
    {
        $this->client->request(
            'POST',
            self::CREATE_CART_URI,
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_ACCEPT' => 'application/json',
            ],
            $this->serializer->serialize([], self::DATA_FORMAT)
        );

        $response = $this->client->getResponse();
        $this->assertEquals($statusCode, $response->getStatusCode());
        $this->assertEquals(
            $expected,
            $this->serializer->decode($response->getContent(), self::DATA_FORMAT)
        );
    }
}
