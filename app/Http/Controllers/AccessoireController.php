<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AccessoireController extends Controller
{
    <?php

namespace App\Http\Controllers;

use App\Models\Accessoire;
use Illuminate\Http\Request;

class AccessoireController extends Controller
{
    public function index()
    {
        $accessoires = Accessoire::with('type')->get();

        return response()->json($accessoires);
    }
}
}
