<?php

use App\Contact;
use Illuminate\Database\Seeder;

class ContactsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Contact::insert([
            [
                'firstname' => 'Zester2',
                'lastname' => 'Albano',
                'contact_number' => '8006226233',
            ],
            [
                'firstname' => 'Zester3',
                'lastname' => 'Albano',
                'contact_number' => '8006226235',
            ],
            [
                'firstname' => 'Zester4',
                'lastname' => 'Albano',
                'contact_number' => '8006226236',
            ],
            [
                'firstname' => 'Zester5',
                'lastname' => 'Albano',
                'contact_number' => '8006226237',
            ],
        ]);
    }
}
