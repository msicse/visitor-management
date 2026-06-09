<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Visitor;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Brian2694\Toastr\Facades\Toastr;
use Storage;


class VisitorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $departments  = Department::orderBy('name')->get();
        $employees    = Employee::where('status', 1)->orderBy('name')->get();
        $visitorTypes = Visitor::select('visitor_type')->distinct()->pluck('visitor_type');

        return view("backend.admin.visitor.index", compact("departments", "employees", "visitorTypes"));
    }

    public function getData(Request $request)
    {
        $query = Visitor::with('employee');

        // Custom filters passed from the filter form via DataTables ajax.data
        if ($request->filled('date_from')) {
            $query->whereDate('in_time', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('in_time', '<=', $request->date_to);
        }
        if ($request->filled('status') && $request->status !== '') {
            $query->where('checkout', $request->status);
        }
        if ($request->filled('visitor_type')) {
            $query->where('visitor_type', $request->visitor_type);
        }
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        $totalData     = Visitor::count();
        $totalFiltered = $query->count();

        // DataTables global search box
        $searchValue = $request->input('search.value');
        if (!empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('name', 'like', "%{$searchValue}%")
                  ->orWhere('phone', 'like', "%{$searchValue}%")
                  ->orWhere('organization', 'like', "%{$searchValue}%")
                  ->orWhere('visitor_card_id', 'like', "%{$searchValue}%");
            });
            $totalFiltered = $query->count();
        }

        // Column ordering
        $columnMap = [
            2 => 'visitor_card_id',
            3 => 'name',
            4 => 'organization',
            5 => 'phone',
            6 => 'in_time',
            7 => 'in_time',
            8 => 'out_time',
            10 => 'reason',
        ];
        $orderColIdx = (int) $request->input('order.0.column', 6);
        $orderDir    = $request->input('order.0.dir', 'desc') === 'asc' ? 'asc' : 'desc';
        $orderCol    = $columnMap[$orderColIdx] ?? 'in_time';
        $query->orderBy($orderCol, $orderDir);

        $start   = (int) $request->input('start', 0);
        $length  = (int) $request->input('length', 25);
        $records = $query->skip($start)->take($length)->get();

        $data = [];
        foreach ($records as $i => $v) {
            $image = $v->image
                ? '<img src="' . asset($v->image) . '" style="height:60px;">'
                : '';
            $actions = '<a href="' . route('visitors.show', $v->id) . '" class="btn btn-info waves-effect btn-sm" title="View"><i class="material-icons">visibility</i> View</a>';
            if (!$v->checkout) {
                $actions .= ' <button type="button" class="btn btn-danger waves-effect btn-sm delete" data-delete-id="' . $v->id . '" title="Checkout"><i class="material-icons">exit_to_app</i> Checkout</button>';
            }
            $data[] = [
                $start + $i + 1,
                $image,
                e($v->visitor_card_id ?? ''),
                e($v->name),
                e($v->organization),
                e($v->phone ?? ''),
                $v->in_time ? date('d-m-Y', strtotime($v->in_time)) : '',
                $v->in_time ? date('h:i a', strtotime($v->in_time)) : '',
                $v->out_time
                    ? date('d-m-Y h:i a', strtotime($v->out_time))
                    : "<span class='text-danger'>Pending Checkout</span>",
                $v->employee ? e($v->employee->name) : '',
                e($v->reason ?? ''),
                $actions,
            ];
        }

        return response()->json([
            'draw'            => intval($request->input('draw')),
            'recordsTotal'    => $totalData,
            'recordsFiltered' => $totalFiltered,
            'data'            => $data,
        ]);
    }

    public function pending(Request $request)
    {
        $departments  = Department::orderBy('name')->get();
        $employees    = Employee::where('status', 1)->orderBy('name')->get();
        $visitorTypes = Visitor::select('visitor_type')->distinct()->pluck('visitor_type');

        return view("backend.admin.visitor.index", compact("departments", "employees", "visitorTypes"));
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
        // Toastr::success(' Status Updated ', 'Success');


        return response()->json([
            "message" => "Success",
            "status" => 201
        ]);

        return redirect()->back();
    }

}
