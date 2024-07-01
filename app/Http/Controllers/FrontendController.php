<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Visitor;
use App\Models\VisitorGuest;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Str;
use Carbon\Carbon;


class FrontendController extends Controller
{
    public function index()
    {
        $employees = Employee::all();
        // return view('frontend.visitor-new', compact('employees'));
        return view('frontend.visitor', compact('employees'));
    }

    public function store(Request $request)
    {

        // $request->validate([
        //     'employee' => 'required|integer',
        //     'name' => 'required|max:255',
        //     'organization-type' => 'required|max:255',
        //     'organization' => 'required|max:255',
        //     'phone' => 'required',
        //     'address' => 'required',
        //     // 'visitor_card' => 'required',
        //     'email' => '',
        //     'image' => '',
        // ]);

        $data = $request->all();
        $slug = Str::slug($request->name);
        $img = $request->image;
        if ($img) {
            $folderPath = "uploads/visitors/";

            $image_parts = explode(";base64,", $img);
            $image_type_aux = explode("image/", $image_parts[0]);
            $image_type = $image_type_aux[1];

            $image_base64 = base64_decode($image_parts[1]);
            $fileName = $slug . "-" . time() . '.png';

            $file = $folderPath . $fileName;
            file_put_contents($file, $image_base64);
        } else {
            $file = 'no-image.png';
        }


        $employee = Employee::find($request->employee);
        // return $data;


        $data["image"] = $file;
        $data["employee_id"] = $request->employee;
        $data["in_time"] = Carbon::now();
        $data["department_id"] = $employee->department_id;
        $data["visitor_card_id"] = $request->visitor_card;
        $visitor = Visitor::create($data);


        if ($data['is_guest'] == 1 ) {
            for ($i = 0; $i < count($data['guest_name']); $i++) {
                $guest = new VisitorGuest();

                $guest->visitor_id = $visitor->id;
                $guest->name = $data['guest_name'][$i];
                $guest->organization = $data['guest_organization'][$i];
                $guest->phone = $data['guest_phone'][$i];
                $guest->email = $data['guest_email'][$i];
                $guest->address = $data['guest_address'][$i];
                $guest->is_checkin = true;
                $guest->save();

            }
        }
        Toastr::success('Succesfully Saved ', 'Success');
        return redirect()->route('home');
    }
}
