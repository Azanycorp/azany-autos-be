<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('process:emails')
    ->everyMinute();
