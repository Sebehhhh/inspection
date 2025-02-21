<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\Indicator;
use App\Models\Problem;
use App\Models\Rule;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $equipmentCount = Equipment::count();
        $indicatorCount = Indicator::count();
        $problemCount   = Problem::count();
        $ruleCount      = Rule::count();
        $userCount      = User::count();
    
        return view('c_panel.dashboard', compact('equipmentCount', 'indicatorCount', 'problemCount', 'ruleCount',  'userCount'));
    }
    
}
