<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

trait HasDataTable
{
    public function scopeDataTable(
        Builder $query,
        ?Request $request = null
    ): Builder {
        $request ??= request();

        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        $search = $request->input('search');

        if (
            filled($search) &&
            property_exists($this, 'dataTableSearchable')
        ) {
            $query->where(function (Builder $query) use ($search) {
                foreach (
                    $this->dataTableSearchable
                    as $index => $column
                ) {
                    if ($index === 0) {
                        $query->where(
                            $column,
                            'like',
                            "%{$search}%"
                        );
                    } else {
                        $query->orWhere(
                            $column,
                            'like',
                            "%{$search}%"
                        );
                    }
                }
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Sorting
        |--------------------------------------------------------------------------
        */

        $allowedSorts =
            property_exists(
                $this,
                'dataTableSortable'
            )
                ? $this->dataTableSortable
                : [];

        $sort = $request->input(
            'sort',
            'created_at'
        );

        $direction = $request->input(
            'direction',
            'desc'
        );

        if (
            ! empty($allowedSorts) &&
            ! in_array(
                $sort,
                $allowedSorts,
                true
            )
        ) {
            $sort = 'created_at';
        }

        if (
            ! in_array(
                $direction,
                ['asc', 'desc'],
                true
            )
        ) {
            $direction = 'desc';
        }

        $query->orderBy(
            $sort,
            $direction
        );

        return $query;
    }

    /*
    |--------------------------------------------------------------------------
    | DataTable Pagination
    |--------------------------------------------------------------------------
    */

    public function scopeDataTablePaginate(
        Builder $query,
        ?Request $request = null
    ) {
        $request ??= request();

        $perPage = (int) $request->input(
            'per_page',
            10
        );

        $allowedPerPage = [
            10,
            25,
            50,
            100,
        ];

        if (
            ! in_array(
                $perPage,
                $allowedPerPage,
                true
            )
        ) {
            $perPage = 10;
        }

        return $query
            ->dataTable($request)
            ->paginate($perPage)
            ->withQueryString();
    }
}