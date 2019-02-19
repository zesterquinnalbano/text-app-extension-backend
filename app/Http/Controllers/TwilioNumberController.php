<?php

namespace App\Http\Controllers;

use App\TwilioNumber;

class TwilioNumberController extends Controller
{
    public function __invoke()
    {
        $twilio = TwilioNumber::select('id', 'contact_number')->get();

        return response()->json([
            'result' => true,
            'data' => $twilio,
        ]);

    }
}
