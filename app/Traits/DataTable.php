<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

trait DataTable
{

    public function scopeDataTablePaginate(
        Builder $query,
        Request $request,
        array $searchableColumns = [],
        array $sortableColumns = [],
        array $filters = [],
        int $defaultPerPage = 10,
        array $allowedPerPage = [10, 25, 50, 100],
    ) {
        /*
    |--------------------------------------------------------------------------
    | Automatically get configuration from model
    |--------------------------------------------------------------------------
    */

        if (
            empty($searchableColumns) &&
            method_exists(
                $this,
                'dataTableSearchable'
            )
        ) {
            $searchableColumns =
                $this->dataTableSearchable();
        }

        if (
            empty($sortableColumns) &&
            method_exists(
                $this,
                'dataTableSortable'
            )
        ) {
            $sortableColumns =
                $this->dataTableSortable();
        }

        if (
            empty($filters) &&
            method_exists(
                $this,
                'dataTableFilters'
            )
        ) {
            $filters =
                $this->dataTableFilters();
        }

        /*
    |--------------------------------------------------------------------------
    | Search
    |--------------------------------------------------------------------------
    */

        $this->applyDataTableSearch(
            $query,
            $request,
            $searchableColumns
        );

        /*
    |--------------------------------------------------------------------------
    | Filters
    |--------------------------------------------------------------------------
    */

        $this->applyDataTableFilters(
            $query,
            $request,
            $filters
        );

        /*
    |--------------------------------------------------------------------------
    | Sorting
    |--------------------------------------------------------------------------
    */

        $this->applyDataTableSort(
            $query,
            $request,
            $sortableColumns
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

        if (! in_array(
            $perPage,
            $allowedPerPage,
            true
        )) {
            $perPage = $defaultPerPage;
        }

        return $query
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * Search.
     */
    protected function applyDataTableSearch(
        Builder $query,
        Request $request,
        array $allowedColumns
    ): void {
        if (! $request->filled('search')) {
            return;
        }

        $search = trim(
            $request->input('search')
        );

        if ($search === '') {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Columns requested by frontend
        |--------------------------------------------------------------------------
        */

        $requestedColumns = $request->input(
            'search_columns',
            []
        );

        if (! is_array($requestedColumns)) {
            $requestedColumns = [];
        }

        /*
        |--------------------------------------------------------------------------
        | Only use columns allowed by backend
        |--------------------------------------------------------------------------
        */

        $columns = array_values(
            array_intersect(
                $requestedColumns,
                $allowedColumns
            )
        );

        /*
        |--------------------------------------------------------------------------
        | If frontend doesn't send columns,
        | use all backend-approved columns.
        |--------------------------------------------------------------------------
        */

        if (empty($columns)) {
            $columns = $allowedColumns;
        }

        /*
        |--------------------------------------------------------------------------
        | Nothing searchable
        |--------------------------------------------------------------------------
        */

        if (empty($columns)) {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Search query
        |--------------------------------------------------------------------------
        */

        $query->where(function (Builder $q) use (
            $columns,
            $search
        ) {
            foreach ($columns as $index => $column) {
                /*
                |--------------------------------------------------------------------------
                | Relationship search
                |--------------------------------------------------------------------------
                |
                | Example:
                |
                | parent.name
                | user.email
                |
                */

                if (
                    str_contains(
                        $column,
                        '.'
                    )
                ) {
                    $parts = explode(
                        '.',
                        $column
                    );

                    $relationColumn =
                        array_pop($parts);

                    $relation =
                        implode(
                            '.',
                            $parts
                        );

                    $method =
                        $index === 0
                        ? 'whereHas'
                        : 'orWhereHas';

                    $q->{$method}(
                        $relation,
                        function (
                            Builder $relationQuery
                        ) use (
                            $relationColumn,
                            $search
                        ) {
                            $relationQuery->where(
                                $relationColumn,
                                'like',
                                '%' .
                                    $search .
                                    '%'
                            );
                        }
                    );

                    continue;
                }

                /*
                |--------------------------------------------------------------------------
                | Normal column
                |--------------------------------------------------------------------------
                */

                $method =
                    $index === 0
                    ? 'where'
                    : 'orWhere';

                $q->{$method}(
                    $column,
                    'like',
                    '%' .
                        $search .
                        '%'
                );
            }
        });
    }

    /**
     * Filters.
     */
    protected function applyDataTableFilters(
        Builder $query,
        Request $request,
        array $allowedFilters
    ): void {
        foreach (
            $allowedFilters
            as $filter
        ) {
            /*
            |--------------------------------------------------------------------------
            | Normal filter
            |--------------------------------------------------------------------------
            |
            | filters[status]=active
            |
            */

            $value = $request->input(
                "filters.$filter"
            );

            if (
                $value === null ||
                $value === ''
            ) {
                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | Multiple values
            |--------------------------------------------------------------------------
            |
            | filters[status][]=active
            | filters[status][]=pending
            |
            */

            if (is_array($value)) {
                $query->whereIn(
                    $filter,
                    $value
                );

                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | Relationship filter
            |--------------------------------------------------------------------------
            */

            if (
                str_contains(
                    $filter,
                    '.'
                )
            ) {
                $parts = explode(
                    '.',
                    $filter
                );

                $column =
                    array_pop($parts);

                $relation =
                    implode(
                        '.',
                        $parts
                    );

                $query->whereHas(
                    $relation,
                    function (
                        Builder $relationQuery
                    ) use (
                        $column,
                        $value
                    ) {
                        $relationQuery->where(
                            $column,
                            $value
                        );
                    }
                );

                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | Normal filter
            |--------------------------------------------------------------------------
            */

            $query->where(
                $filter,
                $value
            );
        }
    }

    /**
     * Sorting.
     */
    protected function applyDataTableSort(
        Builder $query,
        Request $request,
        array $allowedColumns
    ): void {
        $defaultSort =
            $allowedColumns[0]
            ?? 'created_at';

        $sort = $request->input(
            'sort',
            $defaultSort
        );

        $direction = $request->input(
            'direction',
            'desc'
        );

        /*
        |--------------------------------------------------------------------------
        | Validate sort column
        |--------------------------------------------------------------------------
        */

        if (
            ! in_array(
                $sort,
                $allowedColumns,
                true
            )
        ) {
            $sort = $defaultSort;
        }

        /*
        |--------------------------------------------------------------------------
        | Validate direction
        |--------------------------------------------------------------------------
        */

        if (
            ! in_array(
                $direction,
                [
                    'asc',
                    'desc',
                ],
                true
            )
        ) {
            $direction = 'desc';
        }

        /*
        |--------------------------------------------------------------------------
        | Relationship sorting
        |--------------------------------------------------------------------------
        |
        | Relationship sorting requires a join/subquery.
        |
        | For normal DataTable usage, keep sortable columns
        | as columns of the main table.
        |
        */

        if (
            str_contains(
                $sort,
                '.'
            )
        ) {
            return;
        }

        $query->orderBy(
            $sort,
            $direction
        );
    }
}
