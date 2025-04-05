<?php

namespace App\Commands;
use Illuminate\Pagination\LengthAwarePaginator;


class PaginationInfoCommand
{
    public static function execute(LengthAwarePaginator $collection, int $perPage)
    {
        return [
            "current_page" => $collection->currentPage(),
            "total_pages" => $collection->lastPage(),
            "per_page" => $perPage,
            "next_page_url" => $collection->nextPageUrl(),
            "previous_page_url" => $collection->previousPageUrl(),
            "total" => $collection->total(),
        ];
    }
}
