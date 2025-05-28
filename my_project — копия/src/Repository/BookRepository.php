<?php

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    public function save(string $title, ?string $genre, ?string $author, ?int $price, int $quantity = 1): void
    {
        // Проверяем есть ли уже такая книга
        $existingBook = $this->findOneBy([
            'book_title' => $title,
            'book_genre' => $genre,
            'book_author' => $author,
            'book_price' => $price
        ]);

        if ($existingBook) {
            $existingBook->setQuantity($existingBook->getQuantity() + $quantity);
            $this->getEntityManager()->flush();
        } else {
            $book = new Book();
            $book->setBookTitle($title);
            $book->setBookGenre($genre);
            $book->setBookAuthor($author);
            $book->setBookPrice($price);
            $book->setQuantity($quantity);

            $this->getEntityManager()->persist($book);
            $this->getEntityManager()->flush();
        }
    }

    public function getFiltered(string $filter = ''): array
    {
        $qb = $this->createQueryBuilder('b');

        if ($filter) {
            $qb->where('b.book_title LIKE :filter OR b.book_genre LIKE :filter')
               ->setParameter('filter', '%'.$filter.'%');
        }

        return $qb->getQuery()->getResult();
    }

    public function getAll(): array
    {
        return $this->findAll();
    }

    public function findAvailableBooks(): array
    {
        return $this->createQueryBuilder('b')
            ->where('b.quantity > 0')
            ->getQuery()
            ->getResult();
    }
}