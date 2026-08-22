<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

trait HandlesDataTable
{
    /**
     * Apply search, filters, sorting and pagination.
     */
    protected function dataTable(
        Builder $query,
        Request $request,
        array $searchable = [],
        array $filterable = [],
        array $sortable = [],
        int $defaultPerPage = 10,
        array $perPageOptions = [10, 25, 50, 100],
    ) {
        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {
            $search = trim(
                $request->input('search')
            );

            if ($search !== '') {
                $query->where(
                    function ($q) use (
                        $search,
                        $searchable
                    ) {
                        foreach (
                            $searchable
                            as $index => $column
                        ) {
                            if ($index === 0) {
                                $q->where(
                                    $column,
                                    'like',
                                    "%{$search}%"
                                );
                            } else {
                                $q->orWhere(
                                    $column,
                                    'like',
                                    "%{$search}%"
                                );
                            }
                        }
                    }
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Filters
        |--------------------------------------------------------------------------
        */

        $filters = $request->input(
            'filters',
            []
        );

        if (
            is_array($filters) &&
            !empty($filters)
        ) {
            foreach (
                $filters as $column => $value
            ) {
                /*
                |--------------------------------------------------------------------------
                | Ignore invalid filters
                |--------------------------------------------------------------------------
                */

                if (
                    !in_array(
                        $column,
                        $filterable,
                        true
                    )
                ) {
                    continue;
                }

                /*
                |--------------------------------------------------------------------------
                | Ignore empty
                |--------------------------------------------------------------------------
                */

                if (
                    $value === null ||
                    $value === ''
                ) {
                    continue;
                }

                /*
                |--------------------------------------------------------------------------
                | Array / multiple select
                |--------------------------------------------------------------------------
                */

                if (
                    is_array($value)
                ) {
                    $value =
                        array_values(
                            array_filter(
                                $value,
                                fn ($item) =>
                                    $item !==
                                        null &&
                                    $item !==
                                        ''
                            )
                        );

                    if (
                        empty($value)
                    ) {
                        continue;
                    }

                    $query->whereIn(
                        $column,
                        $value
                    );

                    continue;
                }

                /*
                |--------------------------------------------------------------------------
                | Normal select
                |--------------------------------------------------------------------------
                */

                $query->where(
                    $column,
                    $value
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Sorting
        |--------------------------------------------------------------------------
        */

        $sort = $request->input(
            'sort',
            'created_at'
        );

        $direction = strtolower(
            $request->input(
                'direction',
                'desc'
            )
        );

        /*
        |--------------------------------------------------------------------------
        | Prevent SQL injection
        |--------------------------------------------------------------------------
        */

        if (
            !in_array(
                $sort,
                $sortable,
                true
            )
        ) {
            $sort =
                $sortable[0]
                ?? 'created_at';
        }

        if (
            !in_array(
                $direction,
                [
                    'asc',
                    'desc',
                ],
                true
            )
        ) {
            $direction =
                'desc';
        }

        $query->orderBy(
            $sort,
            $direction
        );

        /*
        |--------------------------------------------------------------------------
        | Pagination
        |--------------------------------------------------------------------------
        */

        $perPage = (int) $request->input(
            'per_page',
            $defaultPerPage
        );

        if (
            !in_array(
                $perPage,
                $perPageOptions,
                true
            )
        ) {
            $perPage =
                $defaultPerPage;
        }

        /*
        |--------------------------------------------------------------------------
        | Return paginator
        |--------------------------------------------------------------------------
        */

        return $query
            ->paginate(
                $perPage
            )
            ->withQueryString();
    }
}