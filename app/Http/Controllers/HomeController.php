<?php

namespace App\Http\Controllers;

use App\Services\ProgressStatsService;

class HomeController extends Controller
{
    public function __construct(private ProgressStatsService $progressStats) {}

    public function beranda()
    {
        return view('public.beranda', [
            'services' => $this->progressStats->serviceCards(),
        ]);
    }

    public function about()
    {
        return view('public.about');
    }
}
