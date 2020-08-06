<?php

namespace SlytherinCz\ApiClient\Service\ListObject;

use SlytherinCz\Contracts\ApiClient\DataObjectFactoryInterface;
use SlytherinCz\Contracts\ApiClient\ListInterface;

class ListFactory
{
    private array $options;

    public const TOTAL_ITEMS = 'totalItems';
    public const TOTAL_PAGES = 'totalPages';
    public const CURRENT_PAGE = 'currentPage';
    public const NEXT_PAGE = 'nextPage';
    public const PREVIOUS_PAGE = 'previousPage';
    public const ITEMS = 'items';

    /**
     * ListFactory constructor.
     * @param array $options
     */
    public function __construct(array $options)
    {
        $this->options = $options;
    }

    /**
     * @param array $input
     * @param DataObjectFactoryInterface $factory
     * @return ListInterface
     */
    public function create(array $input, DataObjectFactoryInterface $factory): ListInterface
    {
        return new ListObject(
            $this->extractField($input,self::TOTAL_PAGES),
            $this->extractField($input , self::CURRENT_PAGE),
            $this->extractField($input, self::TOTAL_ITEMS),
            $this->extractField($input, self::NEXT_PAGE) ?? null,
            $this->extractField($input,self::PREVIOUS_PAGE) ?? null,
            $this->createItems($input, $factory)
        );
    }

    private function extractField(array $input, string $field)
    {
        if(!empty($this->options['listFields']) && !empty($this->options['listFields'][$field])) {
            return $input[$this->options['listFields'][$field]] ?? null;
        }
        return $input[$field] ?? null;
    }

    /**
     * @param array $input
     * @param DataObjectFactoryInterface $factory
     * @return array
     */
    private function createItems(array $input, DataObjectFactoryInterface $factory)
    {
        $items = [];
        $inputItems = $this->extractField($input, self::ITEMS);
        foreach ($inputItems as $itemInput) {
            $items[] = $factory->create($itemInput);
        }
        return $items;
    }
}