<?php

namespace Test\Api\Product;

use App\DataFixtures\FixtureLoadable;
use App\DataFixtures\ProductFixture;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Serializer\Serializer;

class DeleteTest extends WebTestCase
{
    use FixtureLoadable;

    const DELETE_PRODUCT_URI = '/products/%s';
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

    public function getData(): array
    {
        return [
            '#1 should delete product' => [
                'id' => 1,
                'statusCode' => 204,
            ],
            '#2 should not found product' => [
                'id' => 999,
                'statusCode' => 404,
            ],
        ];
    }

    /**
     * @test
     * @dataProvider getData
     *
     * @param int $id
     * @param int $statusCode
     */
    public function it_should_assert_equal(int $id, int $statusCode)
    {
        $this->client->request(
            'DELETE',
            sprintf(self::DELETE_PRODUCT_URI, $id),
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
