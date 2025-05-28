<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Visitor;
use App\Models\Employee;
use Illuminate\Support\Str;
use App\Models\VisitorGuest;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;


class FrontendController extends Controller
{
    /**
     * Get visitor data by phone number for autofill functionality
     */
    public function getVisitorByPhone(Request $request)
    {
        try {
            // Validate the phone number
            $validated = $request->validate([
                'phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:5'
            ], [
                'phone.required' => 'Phone number is required',
                'phone.regex' => 'Please enter a valid phone number',
                'phone.min' => 'Phone number is too short'
            ]);

            $phone = $validated['phone'];

            $visitor = Visitor::where('phone', $phone)
                        ->orderBy('created_at', 'desc')
                        ->first();

            if ($visitor) {
                return response()->json([
                    'status' => 'success',
                    'data' => $visitor
                ]);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'No visitor found with this phone number'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation Error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error in getVisitorByPhone: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while fetching visitor data'
            ], 500);
        }
    }

    public function index()
    {
        $employees = Employee::all();
        return view('frontend.visitor-new-2', compact('employees'));
        //  return view('frontend.visitor', compact('employees'));
    }

    public function store(Request $request)
    {
        try {
            // Implementing proper validation with custom messages
            $request->validate([
                'employee' => 'required|integer|exists:employees,id',
                'name' => 'required|max:255',
                'visitor_type' => 'required|max:255',
                'organization' => 'required|max:255',
                'phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10',
                'address' => 'required',
                'visitor_card_id' => 'required|unique:visitors,visitor_card_id',
                'email' => 'nullable|email',
                'image' => 'nullable',
                'reason' => 'required',
            ], [
                'employee.required' => 'Please select an employee to meet',
                'employee.exists' => 'Selected employee does not exist',
                'name.required' => 'Visitor name is required',
                'visitor_type.required' => 'Visitor type is required',
                'organization.required' => 'Organization name is required',
                'phone.required' => 'Phone number is required',
                'phone.regex' => 'Please enter a valid phone number',
                'phone.min' => 'Phone number must be at least 10 digits',
                'address.required' => 'Address is required',
                'visitor_card_id.required' => 'Visitor card ID is required',
                'visitor_card_id.unique' => 'This visitor card ID is already in use',
                'email.email' => 'Please enter a valid email address',
                'reason.required' => 'Reason for visit is required',
            ]);

            $data = $request->all();
            $slug = Str::slug($request->name);
            $img = $request->image;
            if ($img) {
                try {
                    $folderPath = "uploads/visitors/";

                    $image_parts = explode(";base64,", $img);
                    $image_type_aux = explode("image/", $image_parts[0]);
                    $image_type = $image_type_aux[1];

                    $image_base64 = base64_decode($image_parts[1]);
                    $fileName = $slug . "-" . time() . '.png';

                    $file = $folderPath . $fileName;
                    file_put_contents($file, $image_base64);
                } catch (\Exception $e) {
                    // Handle image processing error
                    $file = 'no-image.png';
                    \Log::error('Image processing error for visitor: ' . $request->name . ' (' . $request->phone . '). Error: ' . $e->getMessage());
                }
            } else {
                $file = 'no-image.png';
            }

            $employee = Employee::find($request->employee);
            //return $request->all();

            // Begin transaction
            \DB::beginTransaction();
            try {
                $data["image"] = $file;
                $data["employee_id"] = $request->employee;
                $data["in_time"] = Carbon::now();
                $data["department_id"] = $employee->department_id;
                $data["visitor_card_id"] = $request->visitor_card_id;
                $visitor = Visitor::create($data);

                if ($data['is_guest'] == 1 ) {
                    // Validate guest data
                    if (!isset($data['guest_name']) || !is_array($data['guest_name']) || count($data['guest_name']) === 0) {
                        throw new \Illuminate\Validation\ValidationException(
                            \Illuminate\Validation\Validator::make([], ['guest' => 'required'], ['guest.required' => 'Guest information is required'])
                        );
                    }

                    for ($i = 0; $i < count($data['guest_name']); $i++) {
                        // Validate each guest's data
                        if (empty($data['guest_name'][$i]) || empty($data['guest_card_no'][$i])) {
                            throw new \Illuminate\Validation\ValidationException(
                                \Illuminate\Validation\Validator::make([],
                                ['guest_details' => 'required'],
                                ['guest_details.required' => 'Guest name and card number are required for all guests'])
                            );
                        }

                        // Check for duplicate guest card numbers
                        if (VisitorGuest::where('visitor_card_id', $data['guest_card_no'][$i])
                                        ->where('is_checkin', true)
                                        ->exists()) {
                            throw new \Illuminate\Validation\ValidationException(
                                \Illuminate\Validation\Validator::make([],
                                ['guest_card' => 'unique'],
                                ['guest_card.unique' => 'Guest card number ' . $data['guest_card_no'][$i] . ' is already in use'])
                            );
                        }

                        $guest = new VisitorGuest();
                        $guest->visitor_id = $visitor->id;
                        $guest->name = $data['guest_name'][$i];
                        $guest->visitor_card_id = $data['guest_card_no'][$i];
                        $guest->organization = $data['guest_organization'][$i] ?? '';
                        $guest->phone = $data['guest_phone'][$i] ?? '';
                        $guest->email = $data['guest_email'][$i] ?? '';
                        $guest->address = $data['guest_address'][$i] ?? '';
                        $guest->is_checkin = true;
                        $guest->save();
                    }
                }

                // Commit transaction if everything is successful
                \DB::commit();

                if ($request->expectsJson()) {
                    return response()->json([
                        "message" => "Visitor registration successful",
                        "status" => 201
                    ]);
                } else {
                    Toastr::success('Visitor registration successful', 'Success');
                    return redirect()->route('home');
                }
            } catch (\Exception $e) {
                // Roll back transaction on any error
                \DB::rollBack();
                throw $e; // Re-throw the exception to be caught by the outer try-catch block
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 422,
                    'message' => 'Validation Error',
                    'errors' => $e->errors()
                ], 422);
            }

            return redirect()->back()->withErrors($e->errors())->withInput();

        } catch (\Illuminate\Database\QueryException $e) {
            // Log the database error
            \Log::error('Database error while creating visitor: ' . $e->getMessage());

            // Check for duplicate entry (unique constraint violation)
            $errorCode = $e->errorInfo[1] ?? '';
            if ($errorCode == 1062) {
                $errorMessage = 'A visitor with the same card ID already exists.';
            } else {
                $errorMessage = 'A database error occurred. Please try again.';
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 500,
                    'message' => 'Database Error',
                    'error' => $errorMessage
                ], 500);
            }

            Toastr::error($errorMessage, 'Error');
            return redirect()->back()->withInput();

        } catch (\Exception $e) {
            // Log the error
            \Log::error('Visitor creation error: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 500,
                    'message' => 'Server Error',
                    'error' => 'An unexpected error occurred while processing your request.'
                ], 500);
            }

            Toastr::error('An error occurred while saving the visitor. Please try again.', 'Error');
            return redirect()->back()->withInput();
        }
    }

    /**
     * Check if a visitor card ID is already in use
     */
    public function checkCardId(Request $request)
    {
        try {
            // Validate the card ID
            $validated = $request->validate([
                'card_id' => 'required'
            ]);

            $cardId = $validated['card_id'];

            // Check for active visitor (not checked out)
            $visitor = Visitor::where('visitor_card_id', $cardId)
                        ->whereNull('out_time')
                        ->first();

            if ($visitor) {
                return response()->json([
                    'status' => 'error',
                    'available' => false,
                    'message' => 'This card ID is currently in use by another visitor'
                ]);
            }

            // Also check guest visitors
            $guestVisitor = VisitorGuest::where('visitor_card_id', $cardId)
                            ->where('is_checkin', true)
                            ->first();

            if ($guestVisitor) {
                return response()->json([
                    'status' => 'error',
                    'available' => false,
                    'message' => 'This card ID is currently in use by a guest visitor'
                ]);
            }

            return response()->json([
                'status' => 'success',
                'available' => true,
                'message' => 'Card ID is available'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in checkCardId: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'available' => false,
                'message' => 'An error occurred while checking the card ID'
            ], 500);
        }
    }
}
