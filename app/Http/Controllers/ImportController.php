<?php

namespace App\Http\Controllers;

use App\Contacts;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class ImportController extends Controller implements ToCollection
{
    public function __invoke(Collection $rows)
    {
        $contacts = [];
        foreach ($rows as $row) {
            $pos = strpos($row['2'], '+');

            if ($pos === false) {
                $contacts[] = [
                    'firstname' => $row[0],
                    'lastname' => $row[1],
                    'contact_number' => '+' . $row[2],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            } else {
                $contacts[] = [
                    'firstname' => $row[0],
                    'lastname' => $row[1],
                    'contact_number' => $row[2],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            }
        }

        Contacts::insert($contacts);

        return response()->json([
            'result' => true,
        ]);
    }
}
