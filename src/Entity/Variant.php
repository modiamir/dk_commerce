<?php

namespace Digikala\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity(repositoryClass="Digikala\Repository\VariantRepository")
 * @ORM\Table(name="variants")
 */
class Variant
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=0)
     */
    private $price;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $color;

    /**
     * @ORM\ManyToOne(targetEntity="Digikala\Entity\Product", inversedBy="variants")
     * @ORM\JoinColumn(nullable=false)
     */
    private $product;

    public function getId()
    {
        return $this->id;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(string $color = null): self
    {
        $this->color = $color;

        return $this;
    }


    public function getPrice()
    {
        return $this->price;
    }

    public function setPrice($price = null): self
    {
        $this->price = $price;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;

        return $this;
    }
}
