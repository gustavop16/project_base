<?php

namespace App\Services;

use App\Models\Bay;
use App\Models\Company;
use App\Models\CompanyBayLocation;
use App\Models\CompanyFacility;
use App\Models\CompanyStation;
use App\Models\Location;
use App\Models\Part;
use App\Models\PartReceiving;
use App\Models\User;
use InvalidArgumentException;

class AttachmentService
{

    public function instanceModel(string $type): object
    {        
        return match ($type) {
            'users'         => new User(),
            'companies'     => new Company(),
            'stations'      => new CompanyStation(),
            'facilities'    => new CompanyFacility(),
            'bay'           => new Bay(),
            'location'      => new Location(),
            'parts'         => new Part(),
            'part_receiving'=> new PartReceiving(),
            default   => throw new InvalidArgumentException("Tipo de modelo inválido: $type"),
        };
    }
  

}
