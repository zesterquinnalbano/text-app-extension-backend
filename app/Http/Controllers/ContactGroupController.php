<?php

namespace App\Http\Controllers;

use App\ContactGroup;

class ContactGroupController extends Controller
{
    public function __invoke()
    {
        $contactGroups = ContactGroup::get();

        return response()->json([
            'result' => true,
            'data' => $contactGroups,
        ]);
    }
}
