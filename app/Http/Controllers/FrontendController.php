<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Visitor;
use App\Models\Employee;
use Illuminate\Support\Str;
use App\Models\VisitorGuest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
            Log::error('Error in getVisitorByPhone: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while fetching visitor data'
            ], 500);
        }
    }

    public function index()
    {
        $employees = Employee::all();
        return view('frontend.visitor-register', compact('employees'));
    }

    public function store(Request $request)
    {
        try {
            $allowedVisitorTypes = ['brand', 'factory', 'trade-union', 'official'];

            // Implementing proper validation with custom messages
            $request->validate([
                'employee' => 'required|integer|exists:employees,id',
                'name' => 'required|max:255',
                'visitor_type' => ['required', Rule::in($allowedVisitorTypes)],
                'organization' => 'required|max:255',
                'phone' => ['required', 'regex:/^[0-9\s\-\+\(\)]*$/', 'min:10', function ($attribute, $value, $fail) {
                    if (strlen(preg_replace('/\D/', '', $value)) < 7) {
                        $fail('Please enter a valid phone number.');
                    }
                }],
                'address' => 'required|max:1000',
                'visitor_card_id' => [
                    'required',
                    'max:255',
                    Rule::unique('visitors', 'visitor_card_id')->whereNull('out_time'),
                    function ($attribute, $value, $fail) {
                        if (VisitorGuest::where('visitor_card_id', $value)->where('is_checkin', true)->where('is_checkout', false)->exists()) {
                            $fail('This card ID is currently in use by a guest visitor.');
                        }
                    },
                ],
                'email' => 'required_if:visitor_type,brand|nullable|email',
                'image' => ['nullable', 'regex:/^data:image\/(png|jpe?g);base64,[A-Za-z0-9+\/=]+$/', 'max:6000000'],
                'reason' => 'required|max:1000',

                'is_guest' => 'nullable|in:1,2',
                'guest_name' => 'required_if:is_guest,1|array',
                'guest_name.*' => 'required|max:255',
                'guest_card_no' => 'required_if:is_guest,1|array',
                'guest_card_no.*' => [
                    'required',
                    'max:255',
                    'distinct',
                    function ($attribute, $value, $fail) use ($request) {
                        if ($value === $request->input('visitor_card_id')) {
                            $fail('Guest card ID cannot be the same as the main visitor card ID.');
                            return;
                        }
                        if (VisitorGuest::where('visitor_card_id', $value)->where('is_checkin', true)->where('is_checkout', false)->exists()) {
                            $fail('Guest card ID "' . $value . '" is already in use by another guest.');
                            return;
                        }
                        if (Visitor::where('visitor_card_id', $value)->whereNull('out_time')->exists()) {
                            $fail('Guest card ID "' . $value . '" is already in use by another visitor.');
                        }
                    },
                ],
                'guest_organization.*' => 'required|max:255',
                'guest_phone.*' => 'required|regex:/^[0-9\s\-\+\(\)]*$/',
                'guest_email.*' => 'nullable|email',
                'guest_address.*' => 'required',
            ], [
                'employee.required' => 'Please select an employee to meet',
                'employee.exists' => 'Selected employee does not exist',
                'name.required' => 'Visitor name is required',
                'visitor_type.required' => 'Visitor type is required',
                'visitor_type.in' => 'Please select a valid visitor type',
                'organization.required' => 'Organization name is required',
                'phone.required' => 'Phone number is required',
                'phone.regex' => 'Please enter a valid phone number',
                'phone.min' => 'Phone number must be at least 10 digits',
                'address.required' => 'Address is required',
                'visitor_card_id.required' => 'Visitor card ID is required',
                'visitor_card_id.unique' => 'This visitor card ID is already in use',
                'email.required_if' => 'Email is required for brand visitors',
                'email.email' => 'Please enter a valid email address',
                'image.regex' => 'Visitor photo is invalid, please retake it',
                'reason.required' => 'Reason for visit is required',

                'guest_name.required_if' => 'Guest information is required',
                'guest_name.*.required' => 'Guest name is required for all guests',
                'guest_card_no.required_if' => 'Guest card ID is required',
                'guest_card_no.*.required' => 'Guest card ID is required for all guests',
                'guest_card_no.*.distinct' => 'Duplicate guest card ID in the submitted guest list',
                'guest_organization.*.required' => 'Guest organization is required for all guests',
                'guest_phone.*.required' => 'Guest phone is required for all guests',
                'guest_phone.*.regex' => 'Please enter a valid guest phone number',
                'guest_email.*.email' => 'Please enter a valid guest email address',
                'guest_address.*.required' => 'Guest address is required for all guests',
            ]);



            $data = $request->all();
            $slug = Str::slug($request->name);
            $img = $request->image;
            if ($img) {
                try {
                    $folderPath = "uploads/visitors/";

                    // Check if directory exists and is writable
                    if (!is_dir($folderPath)) {
                        // Try to create directory if it doesn't exist
                        if (!mkdir($folderPath, 0755, true)) {
                            throw new \Exception("Failed to create upload directory: Permission denied", 13);
                        }
                    } else if (!is_writable($folderPath)) {
                        throw new \Exception("Upload directory is not writable: Permission denied", 13);
                    }

                    $image_parts = explode(";base64,", $img);
                    $image_type_aux = explode("image/", $image_parts[0]);
                    $image_type = $image_type_aux[1];

                    $image_base64 = base64_decode($image_parts[1]);
                    $fileName = $slug . "-" . time() . '.png';

                    $file = $folderPath . $fileName;
                    if (!file_put_contents($file, $image_base64)) {
                        $error_code = error_get_last()['type'] ?? 0;
                        throw new \Exception("Failed to save image file: " . error_get_last()['message'] ?? 'Unknown error', $error_code);
                    }

                    // Set proper permissions for the file in Linux/Ubuntu environments
                    if (PHP_OS !== 'WINNT') {
                        chmod($file, 0644);
                    }
                } catch (\Exception $e) {
                    // Handle image processing error with more detailed logging
                    $file = 'no-image.png';
                    $errorCode = $e->getCode();

                    // Log specific error information based on error code
                    if ($errorCode == 13) {
                        // Permission error (common in Linux/Ubuntu)
                        Log::error('Image upload permission error: ' . $e->getMessage(), [
                            'visitor' => $request->name,
                            'phone' => $request->phone,
                            'folder' => $folderPath,
                            'server_os' => PHP_OS,
                            'user' => get_current_user()
                        ]);
                    } else if ($errorCode == 28) {
                        // Disk full error (common in Linux/Ubuntu)
                        Log::error('Disk full error during image upload: ' . $e->getMessage(), [
                            'visitor' => $request->name,
                            'phone' => $request->phone,
                            'server_os' => PHP_OS
                        ]);
                    } else {
                        // General error
                        Log::error('Image processing error: ' . $e->getMessage(), [
                            'visitor' => $request->name,
                            'phone' => $request->phone,
                            'error_code' => $errorCode,
                            'server_os' => PHP_OS
                        ]);
                    }
                }
            } else {
                $file = 'no-image.png';
            }



            $employee = Employee::find($request->employee);

            // Begin transaction
            DB::beginTransaction();
            try {
                $data["image"] = $file;
                $data["employee_id"] = $request->employee;
                $data["in_time"] = Carbon::now();
                $data["department_id"] = $employee->department_id;
                $data["visitor_card_id"] = $request->visitor_card_id;
                $visitor = Visitor::create($data);

                if ($request->input('is_guest') == 1) {
                    // Guest data is already fully validated above, so just persist each row
                    foreach ($data['guest_name'] as $i => $name) {
                        $guest = new VisitorGuest();
                        $guest->visitor_id = $visitor->id;
                        $guest->name = $name;
                        $guest->visitor_card_id = $data['guest_card_no'][$i];
                        $guest->organization = $data['guest_organization'][$i] ?? '';
                        $guest->phone = $data['guest_phone'][$i] ?? '';
                        $guest->email = $data['guest_email'][$i] ?? '';
                        $guest->address = $data['guest_address'][$i] ?? '';
                        $guest->is_checkin = true;
                        $guest->save();
                    }
                }

                // Commt transaction if everything is successful
                DB::commit();

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
                DB::rollBack();
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
            // Get the full error information
            $errorInfo = $e->errorInfo ?? [];
            $errorCode = $errorInfo[1] ?? '';
            $sqlState = $errorInfo[0] ?? '';
            $driverErrorMessage = $errorInfo[2] ?? '';

            // Log detailed database error information
            Log::error('Database error while creating visitor: ' . $e->getMessage(), [
                'sql_state' => $sqlState,
                'error_code' => $errorCode,
                'driver_message' => $driverErrorMessage,
                'server_os' => PHP_OS,
                'php_version' => PHP_VERSION,
                'stack_trace' => $e->getTraceAsString()
            ]);

            // Determine user-friendly error message based on error code
            if ($errorCode == 1062) {
                $errorMessage = 'A visitor with the same card ID already exists.';
            } else if ($errorCode == 1045) {
                $errorMessage = 'Database access denied. Please contact administrator.';
                Log::critical('Database access denied: ' . $e->getMessage());
            } else if ($errorCode == 2002) {
                $errorMessage = 'Cannot connect to database server. Please try again later.';
                Log::critical('Database connection failed: ' . $e->getMessage());
            } else if ($errorCode == 1044) {
                $errorMessage = 'Database permission error. Please contact administrator.';
                Log::critical('Database permission error: ' . $e->getMessage());
            } else if ($errorCode == 1049) {
                $errorMessage = 'Unknown database. Please contact administrator.';
                Log::critical('Unknown database error: ' . $e->getMessage());
            } else if ($errorCode == 13) {
                // Linux permission error for database files
                $errorMessage = 'Database file permission error. Please contact administrator.';
                Log::critical('Database file permission error: ' . $e->getMessage());
            } else if ($errorCode == 1146) {
                // Table doesn't exist
                $errorMessage = 'Database structure issue. Please contact administrator.';
                Log::critical('Missing table error: ' . $e->getMessage());
            } else if ($errorCode == 1054) {
                // Unknown column
                $errorMessage = 'Database structure issue. Please contact administrator.';
                Log::critical('Unknown column error: ' . $e->getMessage());
            } else if ($errorCode == 1452) {
                // Foreign key constraint fails
                $errorMessage = 'The selected employee does not exist. Please select a valid employee.';

                // Check if the message specifically mentions 'employee_id'
                if (strpos($driverErrorMessage, 'employee_id') !== false) {
                    $errorMessage = 'The selected employee does not exist. Please select a valid employee.';
                } else if (strpos($driverErrorMessage, 'department_id') !== false) {
                    $errorMessage = 'The selected department does not exist. Please contact administrator.';
                } else {
                    $errorMessage = 'A reference error occurred. Please verify your selection and try again.';
                }

                Log::critical('Foreign key constraint error: ' . $e->getMessage(), [
                    'driver_message' => $driverErrorMessage,
                    'table' => 'visitors'
                ]);
            } else if ($errorCode == 1040) {
                // Too many connections
                $errorMessage = 'Database is busy. Please try again later.';
                Log::critical('Too many connections error: ' . $e->getMessage());
            } else if ($errorCode == 1153) {
                // Got a packet bigger than 'max_allowed_packet' bytes
                $errorMessage = 'Data size too large for database. Please try again with smaller image.';
                Log::critical('Max allowed packet error: ' . $e->getMessage());
            } else if ($errorCode == 1129) {
                // Host is blocked after multiple connection errors
                $errorMessage = 'Database connection temporarily blocked. Please try again in a few minutes.';
                Log::critical('Host blocked error: ' . $e->getMessage());
            } else if ($errorCode == 1213) {
                // Deadlock found when trying to get lock
                $errorMessage = 'Database conflict occurred. Please try again.';
                Log::critical('Deadlock detected: ' . $e->getMessage());
            } else if ($errorCode == 17) {
                // Linux specific - file exists but cannot be created
                $errorMessage = 'Database file access error. Please contact administrator.';
                Log::critical('Linux file exists error: ' . $e->getMessage());
            } else if ($errorCode == 23) {
                // Linux specific - error on write
                $errorMessage = 'Database storage error. Please contact administrator.';
                Log::critical('Linux disk write error: ' . $e->getMessage());
            } else if ($errorCode == 28) {
                // Linux specific - no space left on device
                $errorMessage = 'Server storage full. Please contact administrator.';
                Log::critical('Linux disk full error: ' . $e->getMessage());
            } else if ($sqlState == 'HY000') {
                // General error state that could be filesystem related in Ubuntu
                $errorMessage = 'Database system error. Please contact administrator.';
                Log::critical('General database error (possibly filesystem): ' . $e->getMessage());
            } else {
                $errorMessage = 'A database error occurred. Please try again.';

                // For non-production environments, add more details
                if (app()->environment() !== 'production') {
                    $errorMessage .= ' (Error: ' . $errorCode . ' - ' . $driverErrorMessage . ')';
                }

                // Log with stack trace for unhandled error codes
                Log::critical('Unhandled database error: ' . $e->getMessage(), [
                    'error_code' => $errorCode,
                    'sql_state' => $sqlState,
                    'driver_message' => $driverErrorMessage,
                    'stack_trace' => $e->getTraceAsString()
                ]);
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
            // Log the error with more detailed information for debugging
            Log::error('Visitor creation error: ' . $e->getMessage(), [
                'stack_trace' => $e->getTraceAsString(),
                'server_os' => PHP_OS,
                'php_version' => PHP_VERSION
            ]);

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
                        ->where('checkout', false) // Assuming 'checkout' is a boolean indicating if the visitor has checked out
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
                            ->where('is_checkout', false)
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
            Log::error('Error in checkCardId: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'available' => false,
                'message' => 'An error occurred while checking the card ID'
            ], 500);
        }
    }
}
