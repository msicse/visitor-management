<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use App\Models\Department;
use App\Models\Employee;
use Str;

class DepartmentController extends Controller
{
    public function index()
    {
         $departments = Department::all();
         return view('backend.admin.departments')->with(compact('departments'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'name'          => 'required|max:255',
            'short_name'    => 'required|max:255',
        ]);

        $slug  = Str::slug($request->name);
        $department = new Department();
        $department->name         = $request->name;
        $department->short_name   = $request->short_name;
        $department->slug        = $slug;
        $department->save();

        Toastr::success('Department Created', 'Saved');
        return redirect()->back();
    }

    public function edit($id)
    {
        $department = Department::find($id);
        return $department;   
    }

    public function update(Request $request, $id)
    {
         $request->validate([
            'name'          => 'required|max:255',
            'short_name'    => 'required|max:255',
        ]);
        
        $department = Department::find($id);
        $department->name         = $request->name;
        $department->short_name   = $request->short_name;
        //$department->slug        = $slug;
        $department->save();
        Toastr::success(' Succesfully Updated ', 'Success');
            
            

        // $employees = Employee::where('department_id', '=', $id )->exists();

        // if( $employees ){
            
        //     Toastr::error('Delete Resticted  ', 'Error');
        // } else {
        //     //$slug  = str_slug($request->name);
        //     $department = Department::find($id);
        //     $department->name         = $request->name;
        //     $department->short_name   = $request->short_name;
        //     //$department->slug        = $slug;
        //     $department->save();
        //     Toastr::success(' Succesfully Updated ', 'Success');
        // }

    
        return redirect()->back();
    }

    public function destroy($id)
    {
        $department = Department::find($id);

        $employees = Employee::where('department_id', '=', $id )->exists();


        if( $employees ){
            
            Toastr::error('Delete Resticted  ', 'Error');
        } else {
            $department->delete();
            Toastr::success('Succesfully Deleted  ', 'Success');
        }

        return redirect()->back();

        
    }
}
