<?php

namespace App\Imports;

use App\Contact;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class ContactImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        $contacts = [];
        $rowContactNumbers = collect($rows)->pluck(2);
        $contact = Contact::select('id', 'contact_number')->get();

        foreach ($rows as $row) {
            if ($contact->where('contact_number', "+{$row[2]}")->count() == 0) {
                var_dump($row);
                $pos = strpos($row['2'], '+');

                if ($pos === false) {
                    $contacts[] = [
                        'firstname' => $row[0],
                        'lastname' => $row[1],
                        'contact_number' => '+' . $row[2],
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                        'created_by' => Auth::id(),
                    ];
                } else {
                    $contacts[] = [
                        'firstname' => $row[0],
                        'lastname' => $row[1],
                        'contact_number' => $row[2],
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                        'created_by' => Auth::id(),
                    ];
                }
            }
        }

        Contact::insert($contacts);
    }
}
