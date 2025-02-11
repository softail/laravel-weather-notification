<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        auth()->user()->locations()->get();

        return Inertia::render('Dashboard');
    }
}
