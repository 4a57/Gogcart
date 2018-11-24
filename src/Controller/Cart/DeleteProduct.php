<?php

namespace App\Controller\Cart;

use ApiPlatform\Core\Exception\ItemNotFoundException;
use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Entity\Cart;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Component\HttpFoundation\Response;

class DeleteProduct
{
    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(ValidatorInterface $validator, EntityManagerInterface $entityManager)
    {
        $this->validator = $validator;
        $this->entityManager = $entityManager;
    }

    /**
     * @Entity("product", expr="repository.find(product_id)")
     */
    public function __invoke(Cart $data, Product $product)
    {
        $cardProduct = $data->findCartProductByProduct($product);
        if ($cardProduct === null) {
            throw new ItemNotFoundException();
        }

        $this->entityManager->remove($cardProduct);
        $this->entityManager->flush();

        return new Response(null, 204);
    }
}
