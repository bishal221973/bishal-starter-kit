<?php

namespace Birta\Licence\Middleware;

use Birta\Licence\Models\Licence;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckLicence
{
    public function handle(
        Request $request,
        Closure $next
    ): Response {
        /*
        |--------------------------------------------------------------------------
        | Multiple Branch Mode
        |--------------------------------------------------------------------------
        */

        if (
            config(
                'licence.allow_multiple_branches'
            )
        ) {
            $branchId = $request
                ->user()
                ?->branch_id
                ?? session('branch_id');

            if (!$branchId) {
                abort(
                    403,
                    'No branch is selected.'
                );
            }

            $licence = app('licence')
                ->current($branchId);

            if (!$licence) {
                abort(
                    403,
                    'No licence found for this branch.'
                );
            }

            if (!$licence->isValid()) {
                abort(
                    403,
                    'The branch licence is invalid or expired.'
                );
            }
        } else {
            /*
            |--------------------------------------------------------------------------
            | Single Branch Mode
            |--------------------------------------------------------------------------
            */

            $licence = Licence::first();

            if (!$licence) {
                abort(
                    403,
                    'No licence found.'
                );
            }

            if (!$licence->isValid()) {
                abort(
                    403,
                    'The licence is invalid or expired.'
                );
            }
        }

        return $next($request);
    }
}