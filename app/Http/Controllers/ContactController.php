<?php

namespace App\Http\Controllers;

use App\Contact;
use App\ContactGroup;
use App\Imports\ContactImport;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

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

        $contact = $contact->with('contactGroup')
            ->whereCreatedBy(Auth::id())
            ->orderBy('firstname')
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
            'contact_group' => 'nullable',
        ]);

        $pos = strpos($validatedInput['contact_number'], '+');

        if ($pos === false) {
            $validatedInput['contact_number'] = '+' . $validatedInput['contact_number'];
        }

        $contactGroup = ContactGroup::firstOrCreate(['name' => $validatedInput['contact_group']]);

        $validatedInput['contact_group_id'] = $contactGroup->id;
        $validatedInput['created_by'] = Auth::id();

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
        $contact = Contact::find($id)->load('contactGroup');

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
            'contact_group' => 'nullable',
        ]);

        $pos = strpos($validatedInput['contact_number'], '+');

        if ($pos === false) {
            $validatedInput['contact_number'] = '+' . $validatedInput['contact_number'];
        }

        $contactGroup = ContactGroup::firstOrCreate(['name' => $validatedInput['contact_group']]);

        $validatedInput['contact_group_id'] = $contactGroup->id;
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

    public function search(Request $request)
    {
        $param = json_decode($request->q);
        $contact = Contact::query();

        $contact->when(isset($param->query), function ($query) use ($param) {
            $query->where(function ($query) use ($param) {
                $query->where('firstname', 'like', "%$param->query%")
                    ->orWhere('lastname', 'like', "%$param->query%");
            });
        });

        $contact = $contact->with('contactGroup')
            ->whereCreatedBy(Auth::id())
            ->orderBy('firstname')
            ->limit($param->limit)
            ->offset($param->offset)
            ->get();

        if ($param->query == "") {
            return response()->json([
                'result' => true,
                'data' => [],
            ]);
        }

        return response()->json([
            'result' => true,
            'data' => $contact,
        ]);
    }

    public function import(Request $request)
    {
        if ($request->hasFile('file')) {
            Excel::import(new ContactImport, $request->file('file'));

            return response()->json([
                'result' => true,
                'message' => 'success',
            ]);
        }
    }
}
