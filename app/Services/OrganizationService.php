<?php
namespace App\Services;

use App\Models\Organization;

class OrganizationService{
    public function current(){
        $org=Organization::where('id',1)->first();

        return $org;
    }
} 