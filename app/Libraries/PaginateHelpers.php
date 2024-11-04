<?php

namespace App\Libraries;

use Illuminate\Container\Container;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

class PaginateHelpers
{
    public static function paginate(Collection $results, $pageSize)
    {
        $page = Paginator::resolveCurrentPage('page');
        $total = $results->count();

        // Convert data to a standard array
        $data = $results->values()->all();

        // Transform the data into objects
        $data = array_map(function ($item) {
            return (object) $item;
        }, $data);

        // Calculate the slice of data for the current page
        $pageData = array_slice($data, ($page - 1) * $pageSize, $pageSize);

        // Paginate the transformed data
        return self::paginator($pageData, $total, $pageSize, $page, [
            'path' => Paginator::resolveCurrentPath(),
            'pageName' => 'page',
        ]);
    }

    protected static function paginator($items, $total, $perPage, $currentPage, $options)
    {
        return Container::getInstance()->makeWith(LengthAwarePaginator::class, compact(
            'items',
            'total',
            'perPage',
            'currentPage',
            'options'
        ));
    }
}
