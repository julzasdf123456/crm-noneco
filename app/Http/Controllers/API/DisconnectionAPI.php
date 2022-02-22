<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request; 
use App\Http\Controllers\Controller; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\CreateReadingsRequest;

class DisconnectionAPI extends Controller {
    public $successStatus = 200;

    public function getDisconnectionList(Request $request) {
        
    }
}