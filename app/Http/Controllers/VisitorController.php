<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Visitor;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Brian2694\Toastr\Facades\Toastr;
use App\Http\Resources\VisitorResource;
use Storage;


class VisitorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $visitors = VisitorResource::collection(Visitor::all());
        return view("backend.admin.visitor.index", compact("visitors"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $employees = Employee::where('status', 1)->get();
        return view('backend.admin.visitor.create', compact('employees'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $request->validate([
            'employee' => 'required|integer',
            'name' => 'required|max:255',
            'factory_name' => 'required|max:255',
            'phone' => 'required',
            'address' => 'required',
            'visitor_card' => 'required',
            'email' => '',
            'about' => '',
            'image' => '',
        ]);

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

        // Storage::put($file, $image_base64);

        // if ($request->hasFile('image')) {

        //     $manager = new ImageManager(new Driver());

        //     $image = $request->file('image');
        //     $filename = $slug . "-" . time() . '.' . $image->getClientOriginalExtension();
        //     $img = $manager->read($image);
        //     $img = $img->resize(400, 400);
        //     $img->save('images/visitors/' . $filename);

        // } else {
        //     $filename = "no-image.png";
        // }
        $employee = Employee::find($request->employee);

        $data = $request->all();
        $data["image"] = $file;
        $data["employee_id"] = $request->employee;
        $data["in_time"] = Carbon::now();
        $data["department_id"] = $employee->department_id;
        $data["visitor_card_id"] = $request->visitor_card;
        $visitor = Visitor::create($data);

        Toastr::success(' Succesfully Saved ', 'Success');
        return redirect()->route('visitors.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $visitor = Visitor::find($id);
        return view('backend.admin.visitor.show', compact('visitor'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function checkout($id)
    {
        $visitor = Visitor::find($id);
        $visitor->out_time = Carbon::now();
        $visitor->checkout = 1;
        $visitor->save();
        Toastr::success(' Status Updated ', 'Success');
        return redirect()->back();
    }
}
