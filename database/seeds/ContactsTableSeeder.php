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
                'firstname' => '[test]:',
                'lastname' => '16479311820',
                'contact_number' => '+16479311820',
            ],
        ]);
    }
}
