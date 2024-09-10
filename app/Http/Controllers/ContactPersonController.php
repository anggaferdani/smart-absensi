<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ContactPerson;

class ContactPersonController extends Controller
{
    public function index() {
        $contactPersons = ContactPerson::where('status', 1)->paginate(10);
        return view('admin.contact-person.index', compact('contactPersons'));
    }

    public function update(Request $request, $id) {
        $contactPerson = ContactPerson::find($id);

        $request->validate([
            'name' => 'required',
            'phone' => 'required',
        ]);

        try {
            $array = [
                'name' => $request['name'],
                'phone' => $request['phone'],
            ];

            $contactPerson->update($array);
    
            return redirect()->back()->with('success', 'Success.');
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }
}
