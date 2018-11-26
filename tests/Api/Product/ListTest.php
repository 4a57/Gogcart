<?php

namespace Test\Api\Product;

use App\DataFixtures\FixtureLoadable;
use App\DataFixtures\ProductFixture;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Serializer\Serializer;

class ListTest extends WebTestCase
{
    use FixtureLoadable;

    const LIST_PRODUCT_URI = '/products';
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
        $product4 = [
            'id' => 4,
            'title' => 'Icewind Dale',
            'price' => '4.99',
        ];
        $product5 = [
            'id' => 5,
            'title' => 'Bloodborne',
            'price' => '5.99',
        ];


        return [
            '#1 should get 3 products (default)' => [
                'page' => null,
                'itemsPerPage' => null,
                'expected' => [
                    $product1,
                    $product2,
                    $product3,
                ],
                'statusCode' => 200,
            ],
            '#2 should get 2 products on 2. page' => [
                'page' => 2,
                'itemsPerPage' => null,
                'expected' => [
                    $product4,
                    $product5,
                ],
                'statusCode' => 200,
            ],
            '#3 should get 2 products' => [
                'page' => null,
                'itemsPerPage' => 2,
                'expected' => [
                    $product1,
                    $product2,
                ],
                'statusCode' => 200,
            ],
            '#4 should get 3 products (max items per page is 3)' => [
                'page' => null,
                'itemsPerPage' => 5,
                'expected' => [
                    $product1,
                    $product2,
                    $product3,
                ],
                'statusCode' => 200,
            ],
            '#5 should get 1 product on 3. page' => [
                'page' => 3,
                'itemsPerPage' => 2,
                'expected' => [
                    $product5,
                ],
                'statusCode' => 200,
            ],
            '#6 should not get products' => [
                'page' => 3,
                'itemsPerPage' => null,
                'expected' => [],
                'statusCode' => 200,
            ],
        ];
    }

    /**
     * @test
     * @dataProvider getData
     *
     * @param int $page
     * @param int $itemsPerPage
     * @param array $expected
     * @param int $statusCode
     */
    public function it_should_assert_equal(?int $page, ?int $itemsPerPage, array $expected, int $statusCode)
    {
        $queryData = ['page' => $page, 'itemsPerPage' => $itemsPerPage];
        $uri = sprintf('%s?%s', self::LIST_PRODUCT_URI, http_build_query($queryData));

        $this->client->request(
            'GET',
            $uri,
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
}
