<?php

namespace App\Http\Controllers;

use App\Contact;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ContactController extends Controller
{
    public function index(Request $request)
    {
        $param = json_decode($request->q);
        $contact = Contact::query();

        $contact->when(isset($param->query), function ($query) use ($param) {
            $query->where(function ($query) use ($param) {
                $query->where('firstname', 'like', "%$param->query%")
                    ->orWhere('lastname', 'like', "%$param->query%");
            });
        });

        $contact = $contact->orderBy('firstname')
            ->limit($param->limit)
            ->offset($param->offset)
            ->get();

        return response()->json([
            'result' => true,
            'data' => $contact,
        ]);
    }

    public function store(Request $request)
    {
        $validatedInput = $this->validate($request, [
            'firstname' => 'required',
            'lastname' => 'required',
            'contact_number' => 'required|unique:contacts,contact_number',
        ]);

        $pos = strpos($validatedInput['contact_number'], '+');

        if ($pos === false) {
            $validatedInput['contact_number'] = '+' . $validatedInput['contact_number'];
        }

        $contact = Contact::create($validatedInput);

        return response()->json([
            'result' => true,
            'data' => [
                'id' => $contact->id,
            ],
        ]);
    }

    public function edit($id)
    {
        $contact = Contact::find($id);

        return response()->json([
            'result' => true,
            'data' => $contact,
        ]);
    }

    public function update(Request $request, $id)
    {
        $contact = Contact::find($id);

        $validatedInput = $this->validate($request, [
            'firstname' => 'required',
            'lastname' => 'required',
            'contact_number' => ['required', Rule::unique('contacts', 'contact_number')->ignore($contact->id)],
        ]);

        $pos = strpos($validatedInput['contact_number'], '+');

        if ($pos === false) {
            $validatedInput['contact_number'] = '+' . $validatedInput['contact_number'];
        }

        $contact = tap($contact)->update($validatedInput);

        return response()->json([
            'result' => true,
            'data' => [
                'id' => $contact->id,
            ],
        ]);
    }

    public function destroy($id)
    {
        Contact::find($id)->delete();

        return response()->json([
            'result' => true,
        ]);
    }
}
