<?php

namespace App\Models;

use App\Traits\PaginateWithSearch;

use App\Models\BaseModel;
use App\Traits\TraitBuilder;
use App\Traits\TraitCollection;
class Profile extends BaseModel
{
    use PaginateWithSearch, TraitCollection, TraitBuilder;
}
