<?php

namespace App\Service;

use App\Repository\PlayLogRepository;

class PlayLogService
{
    private $playLogRepo;

    public function __construct(PlayLogRepository $playLogRepo)
    {
        $this->playLogRepo = $playLogRepo;
    }


}
