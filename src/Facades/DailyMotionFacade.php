<?php

namespace Bakcay\DailyMotion\Facades;

use Illuminate\Support\Facades\Facade;

class DailyMotionFacade extends Facade {
    protected static function getFacadeAccessor() {
        return 'dailymotion';
    }
}
