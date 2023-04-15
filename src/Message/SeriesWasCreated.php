<?php
declare(strict_types=1);

namespace App\Message;

use App\Entity\Series;

class SeriesWasCreated
{
    public function __construct(
        public Series $series
    ){
        
    }
}