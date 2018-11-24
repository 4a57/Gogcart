<?php

namespace App\Controller\Cart;

use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Entity\Cart;
use App\Entity\Product;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;

class AddProduct
{
    private $serializer;
    private $validator;

    public function __construct(SerializerInterface $serializer, ValidatorInterface $validator)
    {
        $this->serializer = $serializer;
        $this->validator = $validator;
    }

    public function __invoke(Request $request, Cart $data): Cart
    {
        /**
         * @var Product $product
         */
        $product = $this->serializer->deserialize($request->getContent(), Product::class, 'json');
        $data->addProduct($product);
        $this->validator->validate($data);

        return $data;
    }
}
