<?php

namespace SlytherinCz\ApiClient\Service\ListObject;

use SlytherinCz\Contracts\ApiClient\ListInterface;

class ListObject implements ListInterface, \JsonSerializable
{
    private int $totalPages;
    private int $currentPage;
    private int $totalItems;
    private ?int $nextPage;
    private ?int $previousPage;
    private array $items;

    /**
     * ListObject constructor.
     * @param int $totalPages
     * @param int $currentPage
     * @param int $totalItems
     * @param int|null $nextPage
     * @param int|null $previousPage
     * @param array $items
     */
    public function __construct(int $totalPages, int $currentPage, int $totalItems, ?int $nextPage, ?int $previousPage, array $items)
    {
        $this->totalPages = $totalPages;
        $this->currentPage = $currentPage;
        $this->totalItems = $totalItems;
        $this->nextPage = $nextPage;
        $this->previousPage = $previousPage;
        $this->items = $items;
    }

    /**
     * @return int
     */
    public function getTotalPages(): int
    {
        return $this->totalPages;
    }

    /**
     * @return int
     */
    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    /**
     * @return int
     */
    public function getTotalItems(): int
    {
        return $this->totalItems;
    }

    /**
     * @return int|null
     */
    public function getNextPage(): ?int
    {
        return $this->nextPage;
    }

    /**
     * @return int|null
     */
    public function getPreviousPage(): ?int
    {
        return $this->previousPage;
    }

    /**
     * @return array
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @return array
     */
    private function toArray()
    {
        return [
            'totalPages' => $this->totalPages,
            'currentPage' => $this->currentPage,
            'totalItems' => $this->totalItems,
            'nextPage' => $this->nextPage,
            'previousPage' => $this->previousPage,
            'items' => $this->items
        ];
    }

    /**
     * @return array|mixed
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}