<?php

namespace App\PackTrack\Application\Query;

use Symfony\Component\Validator\Constraints as Assert;

class ListOrdersWithPackagesQuery
{
    #[Assert\Positive]
    #[Assert\GreaterThan(0)]
    private int $page;

    #[Assert\Positive]
    private int $pageSize;

    public function __construct(int $page = 1, int $pageSize = 10)
    {
        $this->page = $page;
        $this->pageSize = $pageSize;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function getPageSize(): int
    {
        return $this->pageSize;
    }
}
