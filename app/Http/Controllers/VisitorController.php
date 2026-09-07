<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Visitor;
use App\Models\VisitorGuest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
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
        $query = Visitor::with('employee')->withCount([
            'guests',
            'guests as pending_guests_count' => function ($q) {
                $q->where('is_checkout', false);
            }
        ]);

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

        // Column ordering (col 9 = guests_count, col 10 = to whom, col 11 = reason, col 12 = action)
        $columnMap = [
            2 => 'visitor_card_id',
            3 => 'name',
            4 => 'organization',
            5 => 'phone',
            6 => 'in_time',
            7 => 'in_time',
            8 => 'out_time',
            11 => 'reason',
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
            $guestCount = $v->guests_count ?? 0;
            $pendingGuestCount = $v->pending_guests_count ?? 0;
            $guestBadge = '<span class="badge" style="background:#337ab7;color:#fff;padding:3px 7px;border-radius:10px;">T: ' . $guestCount . '</span>';
            $guestBadge .= ' <span class="badge" style="background:' . ($pendingGuestCount > 0 ? '#d9534f' : '#777') . ';color:#fff;padding:3px 7px;border-radius:10px;">P: ' . $pendingGuestCount . '</span>';

            $actions = '<a href="' . route('visitors.show', $v->id) . '" class="btn btn-info waves-effect btn-sm" title="View"><i class="material-icons">visibility</i> View</a>';
            if ($guestCount > 0) {
                $actions .= ' <button type="button" class="btn btn-primary waves-effect btn-sm guest-list-btn"
                    data-visitor-id="' . $v->id . '"
                    data-visitor-name="' . e($v->name) . '"
                    title="Guests"><i class="material-icons">group</i> Guests</button>';
            }
            if (!$v->checkout) {
                $actions .= ' <button type="button" class="btn btn-danger waves-effect btn-sm checkout-btn"
                    data-delete-id="' . $v->id . '"
                    data-guest-count="' . $guestCount . '"
                    data-visitor-name="' . e($v->name) . '"
                    title="Checkout"><i class="material-icons">exit_to_app</i> Checkout</button>';
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
                $guestBadge,
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
        $departments = Department::orderBy('name')->get();
        $employees   = Employee::where('status', 1)->orderBy('name')->get();

        return view("backend.admin.visitor.pending", compact("departments", "employees"));
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
            'employee' => 'required|integer|exists:employees,id',
            'name' => 'required|max:255',
            'organization' => 'required|max:255',
            'visitor_type' => ['required', Rule::in(['brand', 'factory', 'trade-union', 'official'])],
            'phone' => 'required',
            'address' => 'required',
            'visitor_card' => ['required', Rule::unique('visitors', 'visitor_card_id')->whereNull('out_time')],
            'email' => 'nullable|email',
            'reason' => 'required',
            'image' => 'nullable',
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

        try {
            $visitor = Visitor::create($data);
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('Error creating visitor from admin form: ' . $e->getMessage());
            Toastr::error('Failed to save visitor. Please check the submitted data and try again.', 'Error');
            return redirect()->back()->withInput();
        }

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

    public function checkout($id, Request $request)
    {
        $visitor = Visitor::find($id);
        if (!$visitor) {
            return response()->json(["message" => "Visitor not found", "status" => 404], 404);
        }

        if ($visitor->checkout) {
            return response()->json(["message" => "Visitor already checked out", "status" => 200]);
        }

        $now = Carbon::now();
        $visitor->out_time = $now;
        $visitor->checkout = 1;
        $visitor->save();

        // Checkout associated guests only if checkbox was checked (default: true)
        if ($request->boolean('with_guests', true)) {
            $visitor->guests()
                ->where('is_checkout', false)
                ->update(['is_checkout' => true, 'out_time' => $now]);
        }

        return response()->json([
            "message" => "Success",
            "status" => 201
        ]);
    }

    public function checkoutGuest($id)
    {
        $guest = VisitorGuest::find($id);
        if (!$guest) {
            return response()->json(["message" => "Guest not found", "status" => 404], 404);
        }

        if ($guest->is_checkout) {
            return response()->json(["message" => "Guest already checked out", "status" => 200]);
        }

        $guest->is_checkout = true;
        $guest->out_time = Carbon::now();
        $guest->save();

        return response()->json([
            "message" => "Guest checkout successful",
            "status" => 201
        ]);
    }

    public function guestList($id)
    {
        $visitor = Visitor::with('guests')->find($id);
        if (!$visitor) {
            return response()->json(["message" => "Visitor not found", "status" => 404], 404);
        }

        return response()->json([
            "status" => 200,
            "visitor" => [
                "id" => $visitor->id,
                "name" => $visitor->name,
                "checkout" => (bool) $visitor->checkout,
            ],
            "guests" => $visitor->guests->map(function ($guest) {
                return [
                    "id" => $guest->id,
                    "name" => $guest->name,
                    "visitor_card_id" => $guest->visitor_card_id,
                    "organization" => $guest->organization,
                    "phone" => $guest->phone,
                    "is_checkout" => (bool) $guest->is_checkout,
                    "in_time" => $guest->in_time ? date('d-m-Y h:i a', strtotime($guest->in_time)) : null,
                    "out_time" => $guest->out_time ? date('d-m-Y h:i a', strtotime($guest->out_time)) : null,
                ];
            }),
        ]);
    }

}
