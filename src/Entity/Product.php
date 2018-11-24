<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource(
 *     attributes={
 *         "pagination_items_per_page"=30,
 *         "pagination_client_items_per_page"=true,
 *         "maximum_items_per_page"=3,
 *         "normalization_context"={"groups"={"output"}},
 *         "denormalization_context"={"groups"={"input"}}
 *     },
 *     collectionOperations={"get", "post"},
 *     itemOperations={"get", "put", "delete"}
 * )
 * @ORM\Entity()
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=false)
 */
class Product
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
     * @ORM\OneToMany(targetEntity="CartProduct", mappedBy="cart", orphanRemoval=true, cascade={"remove"})
     */
    private $cartProducts;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"input", "output"})
     */
    private $title;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     * @Groups({"input", "output"})
     */
    private $price;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function setPrice($price): self
    {
        $this->price = $price;

        return $this;
    }
}
