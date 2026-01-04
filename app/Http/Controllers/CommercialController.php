<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Client;


class CommercialController extends Controller
{
    public function index()
    {
        $user = auth()->user();
    
        if (!$user->isCommercial()) {
            abort(403, 'Accès non autorisé.');
        }
    
        return view('commercial.home', compact('user'));
    }
}