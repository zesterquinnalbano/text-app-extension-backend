<?php

namespace App\Imports;

use App\Contact;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\ContactGroup;

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

                $contactGroup = ContactGroup::firstOrCreate([
                    'name' => $row[3]
                ]);

                if ($pos === false) {
                    $contacts[] = [
                        'firstname' => $row[0],
                        'lastname' => $row[1],
                        'contact_number' => '+' . $row[2],
                        'contact_group_id' => $contactGroup->id,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                        'created_by' => Auth::id(),
                    ];
                } else {
                    $contacts[] = [
                        'firstname' => $row[0],
                        'lastname' => $row[1],
                        'contact_number' => $row[2],
                        'contact_group_id' => $contactGroup->id,
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
