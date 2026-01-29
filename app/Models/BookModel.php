<?php

namespace App\Models;

class BookModel
{
    private ?int $id = null;
    private string $title;
    private int $authorId;
    private int $categoryId;
    private float $price;
    private int $stock;
    private string $description;
    private string $publishedDate;

    public function __construct(
        string $title,
        int $authorId,
        int $categoryId,
        float $price,
        int $stock,
        string $description,
        string $publishedDate
    ) {
        $this->title         = $title;
        $this->authorId      = $authorId;
        $this->categoryId    = $categoryId;
        $this->price         = $price;
        $this->stock         = $stock;
        $this->description   = $description;
        $this->publishedDate = $publishedDate;
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getAuthorId(): int
    {
        return $this->authorId;
    }

    public function getCategoryId(): int
    {
        return $this->categoryId;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function getStock(): int
    {
        return $this->stock;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getPublishedDate(): string
    {
        return $this->publishedDate;
    }
}
