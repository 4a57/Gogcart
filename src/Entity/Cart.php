<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\Cart\AddProduct;
use App\Controller\Cart\DeleteProduct;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(
 *     attributes={
 *         "normalization_context"={"groups"={"output"}},
 *         "denormalization_context"={"groups"={"input"}}
 *     },
 *     collectionOperations={"post"},
 *     itemOperations={
 *         "get",
 *         "add_product"={
 *             "method"="POST",
 *             "path"="/carts/{id}/products.{_format}",
 *             "controller"=AddProduct::class,
 *             "defaults": {"_api_receive": false},
 *             "swagger_context": {
 *                 "summary": "Add product to cart",
 *                 "parameters": {
 *                     { "name": "id", "in": "path", "required": "true", "type": "integer" },
 *                     { "in": "body", "required": "true", "type": "object", "properties": {
 *                             "id": {"type": "string"}
 *                         }
 *                     }
 *                 }
 *             }
 *         },
 *         "delete_product"={
 *             "method"="DELETE",
 *             "path"="/carts/{id}/products/{product_id}.{_format}",
 *             "controller"=DeleteProduct::class,
 *             "defaults": {"_api_receive": false},
 *             "swagger_context": {
 *                 "summary": "Delete product from cart",
 *                 "parameters": {
 *                     { "name": "id", "in": "path", "required": "true", "type": "integer" },
 *                     { "name": "productId", "in": "path", "required": "true", "type": "integer" }
 *                 }
 *             }
 *         }
 *     }
 * )
 * @ORM\Entity()
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=false)
 */
class Cart
{
    use TimestampableEntity, SoftDeleteableEntity;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"input", "output"})
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity="CartProduct", mappedBy="cart", orphanRemoval=true, cascade={"remove", "persist"})
     * @Assert\Valid()
     * @var Collection|CartProduct[]
     */
    private $cartProducts;

    /**
     * @ORM\ManyToMany(targetEntity="Product", fetch="EAGER")
     * @Groups({"output"})
     * @var Collection|Product[]
     */
    private $products;

    public function __construct()
    {
        $this->cartProducts = new ArrayCollection();
        $this->products = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @Groups({"input", "output"})
     */
    public function getTotalPrice(): string
    {
        $totalPrice = 0;
        foreach ($this->products as $product) {
            $totalPrice += $product->getPrice() * 100;
        }

        return (string)($totalPrice / 100);
    }

    /**
     * @return Collection|CartProduct[]
     */
    public function getCartProducts(): Collection
    {
        return $this->cartProducts;
    }

    /**
     * @return Collection|Product[]
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): self
    {
        $cartProduct = new CartProduct();
        $cartProduct
            ->setCart($this)
            ->setProduct($product);

        $this->cartProducts[] = $cartProduct;

        return $this;
    }

    public function findCartProductByProduct(Product $product): ?CartProduct
    {
        foreach ($this->cartProducts as $cartProduct) {
            if ($cartProduct->getProduct() == $product) {
                return $cartProduct;
            }
        }

        return null;
    }
}
