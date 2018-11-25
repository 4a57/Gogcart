<?php

namespace Test\Api\Product;

use App\DataFixtures\FixtureLoadable;
use App\DataFixtures\ProductFixture;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Serializer\Serializer;

class GetTest extends WebTestCase
{
    use FixtureLoadable;

    const GET_PRODUCT_URI = '/api/products/%s';
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
        ]);

        $this->serializer = self::$container->get('serializer');
    }

    public function getProductData(): array
    {
        return [
            '#1 should get one product' => [
                'id' => 1,
                'expected' => [
                    'id' => 1,
                    'title' => 'Fallout',
                    'price' => '1.99',
                ],
                'statusCode' => 200,
            ],
        ];
    }

    /**
     * @test
     * @dataProvider getProductData
     *
     * @param int $id
     * @param array $expected
     * @param int $statusCode
     */
    public function it_should_get_product(int $id, array $expected, int $statusCode)
    {
        $this->client->request(
            'GET',
            sprintf(self::GET_PRODUCT_URI, $id),
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

    public function getNotFoundProductData(): array
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
     * @dataProvider getNotFoundProductData
     *
     * @param int $id
     * @param int $statusCode
     */
    public function it_should_throw_404(int $id, int $statusCode)
    {
        $this->client->request(
            'GET',
            sprintf(self::GET_PRODUCT_URI, $id),
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
