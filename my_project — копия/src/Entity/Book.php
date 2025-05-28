<?php

namespace App\Entity;

use App\Repository\BookRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BookRepository::class)]
class Book
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $book_title = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $book_genre = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $book_author = null;

    #[ORM\Column(nullable: true)]
    private ?int $book_price = null;

    #[ORM\Column]
    private ?int $quantity = 0;

    #[ORM\OneToMany(targetEntity: Orders::class, mappedBy: 'book')]
    private Collection $orders;

    public function __construct()
    {
        $this->orders = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBookTitle(): ?string
    {
        return $this->book_title;
    }

    public function setBookTitle(string $book_title): static
    {
        $this->book_title = $book_title;

        return $this;
    }

    public function getBookGenre(): ?string
    {
        return $this->book_genre;
    }

    public function setBookGenre(?string $book_genre): static
    {
        $this->book_genre = $book_genre;

        return $this;
    }

    public function getBookAuthor(): ?string
    {
        return $this->book_author;
    }

    public function setBookAuthor(?string $book_author): static
    {
        $this->book_author = $book_author;

        return $this;
    }

    public function getBookPrice(): ?int
    {
        return $this->book_price;
    }

    public function setBookPrice(?int $book_price): static
    {
        $this->book_price = $book_price;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * @return Collection<int, Order>
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Orders $order): static
    {
        if (!$this->orders->contains($order)) {
            $this->orders->add($order);
            $order->setBook($this);
        }

        return $this;
    }

    public function removeOrder(Orders $order): static
    {
        if ($this->orders->removeElement($order)) {
            if ($order->getBook() === $this) {
                $order->setBook(null);
            }
        }

        return $this;
    }
}