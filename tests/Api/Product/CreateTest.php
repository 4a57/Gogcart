<?php

namespace Test\Api\Product;

use App\DataFixtures\FixtureLoadable;
use App\DataFixtures\ProductFixture;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Serializer\Serializer;

class CreateTest extends WebTestCase
{
    use FixtureLoadable;

    const CREATE_PRODUCT_URI = '/api/products';
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
            '#1 should create new product with full data' => [
                'input' => [
                    'title' => 'Test product',
                    'price' => '12.34',
                ],
                'expected' => [
                    'id' => 6,
                    'title' => 'Test product',
                    'price' => '12.34',
                ],
                'statusCode' => 201,
            ],
            '#2 should validation fail (invalid price)' => [
                'input' => [
                    'title' => 'Test product',
                ],
                'expected' => [
                    'title' => 'An error occurred',
                    'type' => 'https://tools.ietf.org/html/rfc2616#section-10',
                    'detail' => 'price: This value should not be blank.',
                    'violations' => [
                        [
                            'propertyPath' => 'price',
                            'message' => 'This value should not be blank.',
                        ],
                    ],
                ],
                'statusCode' => 400,
            ],
            '#3 should validation fail (invalid price)' => [
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
            '#4 should validation fail (invalid title)' => [
                'input' => [
                    'price' => '12.34',
                ],
                'expected' => [
                    'title' => 'An error occurred',
                    'type' => 'https://tools.ietf.org/html/rfc2616#section-10',
                    'detail' => 'title: This value should not be blank.',
                    'violations' => [
                        [
                            'propertyPath' => 'title',
                            'message' => 'This value should not be blank.',
                        ],
                    ],
                ],
                'statusCode' => 400,
            ],
            '#5 should validation fail (invalid title)' => [
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
            '#6 should validation fail (empty form)' => [
                'input' => [],
                'expected' => [
                    'title' => 'An error occurred',
                    'type' => 'https://tools.ietf.org/html/rfc2616#section-10',
                    'detail' => 'title: This value should not be blank.'
                        . "\n"
                        . 'price: This value should not be blank.',
                    'violations' => [
                        [
                            'propertyPath' => 'title',
                            'message' => 'This value should not be blank.',
                        ],
                        [
                            'propertyPath' => 'price',
                            'message' => 'This value should not be blank.',
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
     * @param array $input
     * @param array $expected
     * @param int $statusCode
     */
    public function it_should_assert_equal(array $input, array $expected, int $statusCode)
    {
        $this->client->request(
            'POST',
            self::CREATE_PRODUCT_URI,
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
