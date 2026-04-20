<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Country;
use App\Models\Skill;
use App\Models\Tag;
use App\Traits\ApiResponse;

class MetadataController extends Controller
{
    use ApiResponse;

    public function skills() {
        return $this->success(Skill::select('id', 'name')->orderBy('name', 'asc')->get());
    }

    public function tags() {
        return $this->success(Tag::select('id', 'name')->orderBy('name', 'asc')->get());
    }

    public function cities() {
        return $this->success(City::with('country')->get());
    }
    public function countries() {
        return $this->success(Country::select('id','name')->orderBy('name','asc')->get());
    }
}
