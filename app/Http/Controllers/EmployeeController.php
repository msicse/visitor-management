<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Department;
use Brian2694\Toastr\Facades\Toastr;
use Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $employees = Employee::all();
        return view('backend.admin.employee.index')->with(compact('employees'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $departments = Department::all();
        return view('backend.admin.employee.create')->with(compact('departments'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'department'    => 'required|integer',
            'name'          => 'required|max:255',
            'designation'   => 'required|max:255',
            'date_of_join'  => '',
            'phone'         => '',
            'email'         => '',
            'about'         => '',
            'employee_id'   => 'required',
            'image'         => 'image',
        ]);

        
        //return $request->all();
        $slug = Str::slug($request->name);

        if($request->hasFile('image')) {
            $manager = new ImageManager(new Driver());

            $image       = $request->file('image');
            $filename    = $slug . "-". time().'.'.$image->getClientOriginalExtension();
            $img = $manager->read($image);
            $img = $img->resize(400, 400);
            $img->save('images/employee/' .$filename);

        }else{
            $filename = 'no-image.png';
        }

        $employee = new Employee();

        $employee->name     = $request->name;
        $employee->department_id     = $request->department;
        $employee->designation     = $request->designation;
        $employee->emply_id     = $request->employee_id;
        $employee->date_of_join     = $request->date_of_join;
        $employee->phone     = $request->phone;
        $employee->status     = 1;
        $employee->email     = $request->email;
        $employee->about     = $request->about;
        $employee->image     = $filename;
        $employee->save();

        Toastr::success(' Succesfully Saved ', 'Success');
        return redirect()->route('employees.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $employee = Employee::find($id);
        return view('backend.admin.employee.show')->with(compact('employee'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        
        $departments = Department::all();
        $employee = Employee::find($id);
        return view('backend.admin.employee.edit')->with(compact('employee', 'departments'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'department'    => 'required|integer',
            'name'          => 'required|max:255',
            'designation'   => 'required|max:255',
            'date_of_join'  => '',
            'date_of_resign'  => '',
            'phone'         => '',
            'email'         => '',
            'about'         => '',
            'image'         => 'image',
        ]);
    

        $transections = false;//Transection::where('employee_id', '=', $id )->exists();

        if( $transections ) {
            Toastr::error('Update Resticted  ', 'Error');
        } else {
        
            
            $employee = Employee::find($id);
            $slug = Str::slug($request->name);

            if($request->hasFile('image')) {
   
                $manager = new ImageManager(new Driver());

                $image       = $request->file('image');
                $filename    = $slug . "-". time().'.'.$image->getClientOriginalExtension();
                $img = $manager->read($image);
                $img = $img->resize(400, 400);
                $img->save('images/employee/' .$filename);

                
                if( file_exists('images/employee/' .$employee->image) ){
                    unlink('images/employee/' .$employee->image);
                }
                
            }else{
                $filename = $employee->image;
            }


            

            $employee->name             = $request->name;
            $employee->department_id    = $request->department;
            $employee->designation      = $request->designation;
            $employee->date_of_join     = $request->date_of_join;
            $employee->resign_date      = $request->date_of_resign;
            $employee->phone            = $request->phone;
            $employee->status           = 1;
            $employee->email            = $request->email;
            $employee->about            = $request->about;
            $employee->image            = $filename;
            $employee->save();

            Toastr::success('Succesfully Saved ', 'Success');
        }

        return redirect()->route('employees.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
