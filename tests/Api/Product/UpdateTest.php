<?php

namespace Test\Api\Product;

use App\DataFixtures\FixtureLoadable;
use App\DataFixtures\ProductFixture;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Serializer\Serializer;

class UpdateTest extends WebTestCase
{
    use FixtureLoadable;

    const UPDATE_PRODUCT_URI = '/products/%s';
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
            '#1 should update product with full data' => [
                'id' => 1,
                'input' => [
                    'title' => 'Test product',
                    'price' => '12.34',
                ],
                'expected' => [
                    'id' => 1,
                    'title' => 'Test product',
                    'price' => '12.34',
                ],
                'statusCode' => 200,
            ],
            '#2 should update product with title' => [
                'id' => 1,
                'input' => [
                    'title' => 'Test product',
                ],
                'expected' => [
                    'id' => 1,
                    'title' => 'Test product',
                    'price' => '1.99',
                ],
                'statusCode' => 200,
            ],
            '#3 should update product with price' => [
                'id' => 1,
                'input' => [
                    'price' => '12.34',
                ],
                'expected' => [
                    'id' => 1,
                    'title' => 'Fallout',
                    'price' => '12.34',
                ],
                'statusCode' => 200,
            ],
            '#4 should validation fail (invalid price)' => [
                'id' => 1,
                'input' => [
                    'title' => 'Test product',
                    'price' => 'abc',
                ],
                'expected' => [
                    'title' => 'An error occurred',
                    'type' => 'https://tools.ietf.org/html/rfc2616#section-10',
                    'detail' => 'price: This value should be of type numeric.',
                    'violations' => [
                        [
                            'propertyPath' => 'price',
                            'message' => 'This value should be of type numeric.',
                        ],
                    ],
                ],
                'statusCode' => 400,
            ],
            '#5 should validation fail (invalid title)' => [
                'id' => 1,
                'input' => [
                    'title' => 'A',
                    'price' => '12.34',
                ],
                'expected' => [
                    'title' => 'An error occurred',
                    'type' => 'https://tools.ietf.org/html/rfc2616#section-10',
                    'detail' => 'title: This value is too short. It should have 3 characters or more.',
                    'violations' => [
                        [
                            'propertyPath' => 'title',
                            'message' => 'This value is too short. It should have 3 characters or more.',
                        ],
                    ],
                ],
                'statusCode' => 400,
            ],
        ];
    }

    /**
     * @test
     * @dataProvider getData
     *
     * @param int $id
     * @param array $input
     * @param array $expected
     * @param int $statusCode
     */
    public function it_should_assert_equal(int $id, array $input, array $expected, int $statusCode)
    {
        $this->client->request(
            'PUT',
            sprintf(self::UPDATE_PRODUCT_URI, $id),
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
}
