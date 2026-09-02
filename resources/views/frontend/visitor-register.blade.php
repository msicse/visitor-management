<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Visitor Registration</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="{{ asset('backend/select2/select2.min.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('css/toastr.min.css')  }}">
    <style>
        html, body { height: 100%; }
        * { font-family: 'Inter', ui-sans-serif, system-ui, sans-serif; }

        .error { color: #ef4444; font-size: .7rem; }

        .error-border { border-color: #ef4444 !important; box-shadow: 0 0 0 3px rgba(239,68,68,.12) !important; }

        table td, table td * { vertical-align: top; }

        /* Shared form control styling so inputs / selects / textareas look consistent */
        .form-control {
            width: 100%;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            padding: 0.45rem 0.7rem;
            font-size: 0.8125rem;
            color: #111827;
            background-color: #fff;
            transition: border-color .15s ease, box-shadow .15s ease;
        }
        .form-control:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37,99,235,.15);
        }
        .form-control::placeholder { color: #9ca3af; }
        textarea.form-control { resize: none; }

        .form-label {
            display: flex;
            align-items: center;
            gap: .25rem;
            font-size: 0.72rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.15rem;
        }
        .required-star { color: #ef4444; }

        .section-card { background: #fff; border: 1px solid #e5e7eb; border-radius: 0.75rem; box-shadow: 0 1px 2px rgba(0,0,0,.03); }
        .section-head { display:flex; align-items:center; gap:.5rem; padding: .45rem .75rem; border-bottom:1px solid #f1f5f9; }
        .section-badge {
            display:flex; align-items:center; justify-content:center;
            width:1.5rem; height:1.5rem; border-radius:9999px;
            background:#eff6ff; color:#2563eb; font-weight:700; font-size:.7rem; flex-shrink:0;
        }

        /* Camera frame */
        #my_camera, #results { width: 152px; height: 116px; }
        #results img { width: 100%; height: 100%; object-fit: cover; }

        /* Select2 to match form-control */
        .select2-container { width: 100% !important; }
        .select2-container--default .select2-selection--single {
            height: 34px !important;
            border-radius: 0.5rem !important;
            border: 1px solid #d1d5db !important;
            display: flex;
            align-items: center;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 34px !important; padding-left: 0.7rem !important; color:#111827; font-size:.8125rem;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow { height: 32px !important; }
        .select2-container--default.select2-container--focus .select2-selection--single,
        .select2-container--default.select2-container--open .select2-selection--single {
            border-color: #2563eb !important;
            box-shadow: 0 0 0 3px rgba(37,99,235,.15);
        }
        .select2-dropdown { font-size: .8125rem; }

        /* Guest table */
        #vtable thead th {
            position: sticky; top: 0;
            background: #f8fafc; color:#64748b; font-size:.6rem; font-weight:700;
            text-transform: uppercase; letter-spacing:.04em; padding:.25rem .5rem; text-align:left;
            border-bottom:1px solid #e5e7eb;
        }
        #vtable td { padding:.2rem .4rem; border-bottom:1px solid #f1f5f9; }
        #vtable tbody tr:hover { background:#f8fafc; }

        ::-webkit-scrollbar { height: 7px; width: 7px; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 9999px; }
        ::-webkit-scrollbar-track { background: transparent; }
    </style>
</head>

<body class="h-screen flex flex-col overflow-hidden bg-gray-50 text-gray-800">

    <!-- Header -->
    <header class="shrink-0 bg-white border-b border-gray-200">
        <div class="max-w-screen-2xl mx-auto flex items-center justify-between px-4 sm:px-6 py-2">
            <img src="{{ asset('images/rsc.png') }}" class="h-8">
            <div class="text-center">
                <h1 class="text-base sm:text-xl font-bold text-gray-900 tracking-tight leading-none">Visitor Registration</h1>

            </div>
            <a href="{{ route('login') }}"
                class="inline-flex items-center gap-1.5 rounded-lg bg-gray-900 hover:bg-gray-800 text-white text-xs font-medium px-3 py-1.5 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l3 3m0 0-3 3m3-3H3" />
                </svg>
                <span class="hidden sm:inline">Admin Login</span>
            </a>
        </div>
    </header>

    <form action="#" id="addVisitorForm" class="flex-1 flex flex-col overflow-hidden">
        <input type="hidden" name="image" id="image" class="image-tag" value="" required>
        <input type="hidden" id="isGuest" name="is_guest" value="2">

        <main class="flex-1 flex overflow-hidden gap-3 max-w-screen-2xl mx-auto w-full px-3 sm:px-4 pt-2 pb-1.5">

            <!-- Left column: photo -->
            <aside class="w-40 xl:w-44 shrink-0">
                <div class="section-card p-2.5">
                    <div class="flex items-center gap-1.5 mb-2">
                        <span class="section-badge">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.174C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.174 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0ZM18.75 10.5h.008v.008h-.008V10.5Z" />
                            </svg>
                        </span>
                        <h2 class="text-[11px] font-semibold text-gray-900 leading-tight">Visitor Photo <span class="required-star">*</span></h2>
                    </div>

                    <div class="relative mx-auto rounded-lg overflow-hidden border-2 border-dashed border-gray-300 bg-gray-50 flex items-center justify-center" style="width:152px;height:116px;">
                        <span id="my_camera" class="block"></span>
                        <span id="results" class="absolute top-0 left-0 w-full h-full"></span>
                    </div>

                    <input type="button" id="snapshotBtn"
                        class="mt-2 w-full inline-flex items-center justify-center gap-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-[11px] font-semibold py-1.5 transition cursor-pointer"
                        value="Take Photo" onClick="take_snapshot()">
                    <p class="text-[10px] text-gray-400 mt-1.5 text-center leading-snug">Face the camera clearly before capturing</p>
                </div>
            </aside>

            <!-- Right column: form -->
            <div class="flex-1 flex flex-col overflow-hidden gap-2">

                <div class="flex-1 overflow-y-auto space-y-2 pr-1">

                    <!-- Visitor + meeting details -->
                    <div class="section-card">
                        <div class="section-head">
                            <span class="section-badge">1</span>
                            <h2 class="text-xs font-semibold text-gray-900">Visitor &amp; Meeting Details</h2>
                        </div>
                        <div class="p-3 grid grid-cols-1 md:grid-cols-3 gap-x-3 gap-y-2">
                            <div>
                                <label for="phone" class="form-label">Visitor Phone <span class="required-star">*</span></label>
                                <input type="text" name="phone" id="phone" placeholder="e.g. 01XXXXXXXXX" required class="form-control" />
                            </div>
                            <div>
                                <label for="organization-type" class="form-label">Visitor From <span class="required-star">*</span></label>
                                <select id="organization-type" name="visitor_type" class="form-control" required>
                                    <option value="">Select</option>
                                    <option value="brand">Brand</option>
                                    <option value="factory">Factory</option>
                                    <option value="trade-union">Trade Union</option>
                                    <option value="official">Others</option>
                                </select>
                                <label id="organization-type-error" class="error" for="organization-type"></label>
                            </div>
                            <div>
                                <label for="visitor_card_id" class="form-label">Visitor Card ID <span class="required-star">*</span></label>
                                <input type="text" name="visitor_card_id" id="visitor_card_id" placeholder="Enter card ID" required class="form-control" />
                                <div id="cardIdFeedback" class="text-[11px] mt-0.5 hidden"></div>
                            </div>

                            <div>
                                <label for="name" class="form-label">Visitor Name <span class="required-star">*</span></label>
                                <input type="text" name="name" id="name" placeholder="Enter full name" required class="form-control" />
                            </div>
                            <div>
                                <label for="organization" class="form-label">Visitor Organization <span class="required-star">*</span></label>
                                <input id="organization" type="text" name="organization" class="form-control" placeholder="Organization name" required />
                            </div>
                            <div>
                                <label for="email" class="form-label">Visitor Email <span class="required-star" id="emailReq">*</span></label>
                                <input id="email" type="text" name="email" placeholder="name@example.com" class="form-control" />
                            </div>


                             <div>
                                <label for="address" class="form-label">Visitor Address <span class="required-star">*</span></label>
                                <textarea class="form-control" name="address" placeholder="Visitor address" id="address" rows="1" required></textarea>
                            </div>
                            <div>
                                <label for="reason" class="form-label">Reason <span class="required-star">*</span></label>
                                <textarea class="form-control" name="reason" placeholder="Purpose of visit" id="reason" rows="1" required></textarea>
                            </div>
                             <div>
                                <label for="employee" class="form-label">Whom to Meet <span class="required-star">*</span></label>
                                <select id="employee" name="employee" class="form-control" required>
                                    <option value="">Select RSC Employee</option>
                                    @foreach ($employees as $data)
                                        <option value="{{ $data->id }}" {{ $data->id == old('employee') ? 'selected' : '' }}>
                                            {{ $data->name . ' - ' . $data->department->short_name }}
                                        </option>
                                    @endforeach
                                </select>
                                <label id="employee-error" class="error" for="employee"></label>
                            </div>

                        </div>
                    </div>

                    <!-- Accompanying guests -->
                    <div class="section-card">
                        <div class="section-head justify-between !py-1.5 !px-3">
                            <div class="flex items-center gap-1.5">
                                <span class="section-badge">2</span>
                                <h2 class="text-[11px] font-semibold text-gray-900">Accompanying Guests <span class="text-gray-400 font-normal">(optional)</span></h2>
                                <span id="guestCount" class="inline-flex items-center justify-center min-w-[1.1rem] h-[1.1rem] px-1 rounded-full bg-gray-100 text-gray-600 text-[10px] font-semibold">0</span>
                            </div>
                            <button type="button" id="addVisitor"
                                class="inline-flex items-center gap-1 rounded-full bg-blue-600 hover:bg-blue-700 text-white text-[10.5px] font-semibold px-2.5 py-1 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                </svg>
                                Add Guest
                            </button>
                        </div>
                        <div class="p-1.5">
                            <div class="overflow-auto rounded-lg border border-gray-200 max-h-56">
                                <table class="w-full text-sm" id="vtable">
                                    <thead>
                                        <tr>
                                            <th style="width:85px;">Card No.</th>
                                            <th>Name</th>
                                            <th>Organization</th>
                                            <th>Phone</th>
                                            <th>Email</th>
                                            <th>Address</th>
                                            <th style="width:40px;">Action</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Actions (always visible, never scrolls away) -->
                <div class="shrink-0 flex justify-end gap-2 pt-1.5 border-t border-gray-200">
                    <button type="button" id="resetForm"
                        class="inline-flex items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white hover:bg-gray-50 text-gray-700 text-xs font-semibold px-5 py-2 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                        </svg>
                        Reset
                    </button>
                    <button type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold px-7 py-2 shadow-sm transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                        </svg>
                        Submit Registration
                    </button>
                </div>

            </div>
        </main>
    </form>

    <!-- Spinner overlay -->
    <div id="spinnerModal" class="fixed inset-0 items-center justify-center bg-gray-900/40 backdrop-blur-sm z-50 hidden">
        <div class="bg-white p-8 rounded-2xl shadow-xl flex flex-col items-center gap-3">
            <svg class="w-12 h-12 animate-spin text-blue-600" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M32 3C35.8083 3 39.5794 3.75011 43.0978 5.20749C46.6163 6.66488 49.8132 8.80101 52.5061 11.4939C55.199 14.1868 57.3351 17.3837 58.7925 20.9022C60.2499 24.4206 61 28.1917 61 32C61 35.8083 60.2499 39.5794 58.7925 43.0978C57.3351 46.6163 55.199 49.8132 52.5061 52.5061C49.8132 55.199 46.6163 57.3351 43.0978 58.7925C39.5794 60.2499 35.8083 61 32 61C28.1917 61 24.4206 60.2499 20.9022 58.7925C17.3837 57.3351 14.1868 55.199 11.4939 52.5061C8.801 49.8132 6.66487 46.6163 5.20749 43.0978C3.7501 39.5794 3 35.8083 3 32C3 28.1917 3.75011 24.4206 5.2075 20.9022C6.66489 17.3837 8.80101 14.1868 11.4939 11.4939C14.1868 8.80099 17.3838 6.66487 20.9022 5.20749C24.4206 3.7501 28.1917 3 32 3L32 3Z" stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round" opacity=".25"></path>
                <path d="M32 3C36.5778 3 41.0906 4.08374 45.1692 6.16256C49.2477 8.24138 52.7762 11.2562 55.466 14.9605C58.1558 18.6647 59.9304 22.9531 60.6448 27.4748C61.3591 31.9965 60.9928 36.6232 59.5759 40.9762" stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"></path>
            </svg>
            <p class="text-sm font-medium text-gray-700">Processing, please wait…</p>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.25/webcam.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.20.0/dist/jquery.validate.min.js"></script>
    <script src="{{ asset('backend/select2/select2.min.js') }}"></script>
    <script src="{{ asset('js/toastr.min.js') }}"></script>

    <script>
        // Function to check if a guest card ID is available
        function checkGuestCardId(input) {
            const cardId = $(input).val().trim();
            if (cardId.length > 0) {
                // Check for duplicates within the form first
                let isDuplicate = false;
                $('.guest-card-id').not(input).each(function() {
                    if ($(this).val() === cardId) {
                        isDuplicate = true;
                        return false; // break the loop
                    }
                });

                if (isDuplicate) {
                    toastr.error('This card ID is already used for another guest', 'Duplicate Card ID');
                    $(input).addClass('border-red-500 error-border');
                    return;
                }

                // Also check if it matches the main visitor's card
                if ($('#visitor_card_id').val() === cardId) {
                    toastr.error('This card ID is already used for the main visitor', 'Duplicate Card ID');
                    $(input).addClass('border-red-500 error-border');
                    return;
                }

                // Then check against the server
                $.ajax({
                    url: "{{ route('visitor.checkCardId') }}",
                    data: { card_id: cardId },
                    type: "POST",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (!response.available) {
                            toastr.error(response.message, 'Card ID Not Available');
                            $(input).addClass('border-red-500 error-border');
                        } else {
                            $(input).removeClass('border-red-500 error-border');
                        }
                    },
                    error: function() {
                        toastr.warning('Could not verify card ID', 'Warning');
                    }
                });
            }
        }

        function take_snapshot() {
            Webcam.snap(function (data_uri) {
                $(".image-tag").val(data_uri);
                document.getElementById('results').innerHTML = '<img height="116px" width="152px" src="' + data_uri + '"/>';

                // Hide camera and show result
                $("#my_camera").css("visibility", "hidden");
                $("#results").css("visibility", "visible");

                // Change button text to "Retake Photo"
                $("#snapshotBtn").val("Retake Photo").attr("onClick", "retake_photo()");
            });
        }

        function retake_photo() {
            // Clear the result and show camera again
            document.getElementById('results').innerHTML = '';
            $("#my_camera").css("visibility", "visible");
            $("#results").css("visibility", "hidden");

            // Change button text back to "Take Photo"
            $("#snapshotBtn").val("Take Photo").attr("onClick", "take_snapshot()");
        }

        Webcam.set({
            width: 152,
            height: 116,
            image_format: 'jpeg',
            jpeg_quality: 90
        });

        Webcam.attach('#my_camera');

        // Initially hide the results container
        $(document).ready(function() {
            $("#results").css("visibility", "hidden");
        });

        $("#organization-type").change(function () {
            let id = $(this).val();

            if (id === 'brand') {
                $('#email').attr('required', 'required');
                $("#emailReq").removeClass('hidden');
            } else {
                $("#email").removeAttr('required');
                $("#emailReq").addClass('hidden');
            }
        });

        // Soft-reset the whole kiosk form (fields, photo, guest rows) without reloading the page
        function resetVisitorForm() {
            $('#employee').val(null).trigger('change');
            $('#addVisitorForm')[0].reset();

            $('.form-error').remove();
            $('.error-border').removeClass('error-border');

            $(".image-tag").val("");
            document.getElementById('results').innerHTML = "";
            $("#my_camera").css("visibility", "visible");
            $("#results").css("visibility", "hidden");
            $("#snapshotBtn").val("Take Photo").attr("onClick", "take_snapshot()");

            $('#cardIdFeedback').removeClass('text-green-600 text-red-600').addClass('hidden');

            // Clear any accompanying guest rows
            $('#vtable tr').not(':first').remove();
            $('#isGuest').val(2);
            updateGuestCount();

            $('#phone').trigger('focus');
        }

        $(document).ready(function () {
            $('#employee').select2();
            $("#addVisitorForm").validate({ onsubmit: false });

            // Check if employee selection has valid options
            if ($('#employee option').length <= 1) {
                // Only the default "Select" option exists
                toastr.warning('No employees found in the system. Please contact administrator.', 'Warning');
            }

            // Add custom validation for employee field
            $('#employee').on('change', function() {
                const employeeId = $(this).val();
                if (employeeId) {
                    // Verify the selected employee exists in the database
                    const optionExists = $('#employee option[value="' + employeeId + '"]').length > 0;
                    if (!optionExists) {
                        toastr.error('The selected employee does not exist. Please select a valid employee.', 'Error');
                        $(this).val('');
                    }
                }
            });

            // Card ID validation
            let cardIdTimer;
            $('#visitor_card_id').on('input', function() {
                const cardId = $(this).val().trim();
                clearTimeout(cardIdTimer);

                // Always clear any form-error divs and the jQuery Validate label near this field on every keystroke
                $(this).nextAll('.form-error').remove();
                $(this).removeClass('border-red-500 error-border');
                $('#visitor_card_id-error').text('');

                if (cardId.length === 0) {
                    $('#cardIdFeedback').addClass('hidden').removeClass('text-green-600 text-red-600').text('');
                    return;
                }

                if (cardId.length > 0) {
                    // Clear previous feedback
                    $('#cardIdFeedback').removeClass('text-green-600 text-red-600').addClass('hidden');

                    // Wait for user to finish typing
                    cardIdTimer = setTimeout(function() {
                        $.ajax({
                            url: "{{ route('visitor.checkCardId') }}",
                            data: { card_id: cardId },
                            type: "POST",
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                $('#cardIdFeedback').removeClass('hidden');

                                if (response.available) {
                                    $('#cardIdFeedback').text('Card ID is available').addClass('text-green-600');
                                } else {
                                    $('#cardIdFeedback').text(response.message).addClass('text-red-600');
                                }
                            },
                            error: function() {
                                $('#cardIdFeedback').removeClass('hidden').text('Could not verify card ID').addClass('text-red-600');
                            }
                        });
                    }, 500); // 500ms delay
                }
            });

            // Phone number lookup functionality
            $('#phone').on('blur', function() {
                const phoneNumber = $(this).val().trim();
                if (phoneNumber.length > 0) {
                    // Show loading indicator
                    const spinnerModal = document.getElementById('spinnerModal');
                    spinnerModal.classList.remove('hidden');
                    spinnerModal.classList.add('flex');

                    $.ajax({
                        url: "{{ route('visitor.getByPhone') }}",
                        data: { phone: phoneNumber },
                        type: "POST",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            const spinnerModal = document.getElementById('spinnerModal');
                            spinnerModal.classList.add('hidden');
                            spinnerModal.classList.remove('flex');

                            if (response.status === 'success') {
                                const visitor = response.data;

                                // Only populate fields that are currently empty
                                if (!$('#name').val()) $('#name').val(visitor.name);
                                if (!$('#email').val()) $('#email').val(visitor.email);
                                if (!$('#organization').val()) $('#organization').val(visitor.organization);
                                if (!$('#address').val()) $('#address').val(visitor.address);
                                if (!$('#organization-type').val()) $('#organization-type').val(visitor.visitor_type);

                                // Notify user
                                toastr.info('Previous visitor data loaded. You can modify if needed.', 'Info');
                            }
                        },
                        error: function(xhr, status, errorThrown) {
                            const spinnerModal = document.getElementById('spinnerModal');
                            spinnerModal.classList.add('hidden');
                            spinnerModal.classList.remove('flex');

                            if (xhr.status === 422) {
                                // Validation errors
                                let errorMsg = 'Please provide a valid phone number';
                                if (xhr.responseJSON && xhr.responseJSON.errors) {
                                    errorMsg = xhr.responseJSON.errors.phone[0] || errorMsg;
                                }
                                toastr.warning(errorMsg, 'Warning');
                            } else if (xhr.status !== 0) {
                                // Server error but not a network issue
                                toastr.error('Error contacting server. Please try again.', 'Error');
                            }
                            // Network errors (status 0) are silently ignored for better UX
                        }
                    });
                }
            });

            $("#addVisitorForm").on("submit", function (e) {
                e.preventDefault();

                // Reset previous error highlights
                $('.form-error').remove();
                $('.error-border').removeClass('error-border');
                $('#cardIdFeedback').addClass('hidden').removeClass('text-green-600 text-red-600').text('');

                // Specifically check employee field
                const employeeId = $('#employee').val();
                if (!employeeId) {
                    $('#employee').next('.select2-container').find('.select2-selection').addClass('border-red-500 error-border');
                    $('<div class="text-red-500 text-xs mt-1 form-error">Please select an employee to meet</div>')
                        .insertAfter($('#employee-error'));
                    toastr.error('Please select an employee to meet', 'Validation Error');
                    return;
                }

                // Verify the selected employee exists in the database
                const optionExists = $('#employee option[value="' + employeeId + '"]').length > 0;
                if (!optionExists) {
                    toastr.error('The selected employee does not exist', 'Validation Error');
                    return;
                }

                // Check if there are any duplicate guest card IDs
                let hasDuplicateGuests = false;
                let guestCardIds = [];

                // Check if there are guests and validate them
                if ($("#isGuest").val() === "1") {
                    // Collect all guest card IDs
                    $('.guest-card-id').each(function() {
                        const cardId = $(this).val().trim();
                        if (cardId) {
                            if (guestCardIds.includes(cardId)) {
                                hasDuplicateGuests = true;
                                $(this).addClass('border-red-500 error-border');
                                $('<div class="text-red-500 text-xs mt-1 form-error">Duplicate card ID</div>')
                                    .insertAfter($(this));
                            } else {
                                guestCardIds.push(cardId);
                            }

                            // Check against main visitor card
                            if ($('#visitor_card_id').val() === cardId) {
                                hasDuplicateGuests = true;
                                $(this).addClass('border-red-500 error-border');
                                $('<div class="text-red-500 text-xs mt-1 form-error">Same as main visitor card ID</div>')
                                    .insertAfter($(this));
                            }
                        }
                    });
                }

                if (hasDuplicateGuests) {
                    toastr.error('Please fix the duplicate card IDs', 'Validation Error');
                    return;
                }

                if ($("#addVisitorForm").valid()) {
                    if ($(".image-tag").val() === "") {
                        toastr.warning("Please take a photo of the visitor", "Photo Required");
                    } else {
                        const spinnerModal = document.getElementById('spinnerModal');
                        spinnerModal.classList.remove('hidden');
                        spinnerModal.classList.add('flex');

                        $.ajax({
                            url: "{{ route('visitor.store') }}",
                            data: $(this).serialize(),
                            type: "POST",
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function (result) {

                                if (result.status === 201) {
                                    resetVisitorForm();

                                    const spinnerModal = document.getElementById('spinnerModal');
                                    spinnerModal.classList.add('hidden');
                                    spinnerModal.classList.remove('flex');
                                    toastr.success(result.message || 'Successfully saved', 'Success');

                                } else {
                                    const spinnerModal = document.getElementById('spinnerModal');
                                    spinnerModal.classList.add('hidden');
                                    spinnerModal.classList.remove('flex');
                                    toastr.error('Server not response', 'Error');
                                }
                            },
                            error: function(xhr, status, error) {
                                const spinnerModal = document.getElementById('spinnerModal');
                                spinnerModal.classList.add('hidden');
                                spinnerModal.classList.remove('flex');

                                if (xhr.status === 422) {
                                    // Validation errors
                                    let errors = xhr.responseJSON.errors;
                                    for (let field in errors) {
                                        // Display error message
                                        toastr.error(errors[field][0], 'Validation Error');

                                        // Highlight the field with error
                                        const inputField = $('#' + field);
                                        if (inputField.length) {
                                            inputField.addClass('border-red-500 error-border');
                                            // For visitor_card_id, insert before #cardIdFeedback so only one message shows
                                            if (field === 'visitor_card_id') {
                                                $('<div class="text-red-500 text-xs mt-1 form-error">' + errors[field][0] + '</div>')
                                                    .insertBefore($('#cardIdFeedback'));
                                            } else {
                                                $('<div class="text-red-500 text-xs mt-1 form-error">' + errors[field][0] + '</div>')
                                                    .insertAfter(inputField);
                                            }
                                        }
                                    }
                                } else if (xhr.status === 500) {
                                    // Server errors
                                    let errorMessage = 'An error occurred. Please try again.';

                                    if (xhr.responseJSON && xhr.responseJSON.error) {
                                        errorMessage = xhr.responseJSON.error;

                                        // Check for common foreign key constraint messages
                                        if (errorMessage.includes('employee does not exist') ||
                                            errorMessage.includes('reference error') ||
                                            errorMessage.includes('foreign key constraint')) {

                                            // Reset the employee dropdown
                                            $('#employee').val('').trigger('change');
                                        }
                                    }

                                    toastr.error(errorMessage, 'Server Error');
                                } else {
                                    // Other HTTP errors
                                    toastr.error('Connection issue. Please check your network and try again.', 'Error');
                                }
                            }
                        });
                    }

                } else {
                    // Form validation failed
                    toastr.warning('Please fill in all required fields correctly', 'Form Validation');
                }
            });

            // Reset button functionality
            $('#resetForm').on('click', function() {
                resetVisitorForm();
                toastr.info('Form has been reset', 'Info');
            });
        });

        $('#addVisitor').click(function (e) {
            e.preventDefault();

            $("#isGuest").val(1);
            let table = document.getElementById("vtable");
            let rowCount = table.rows.length;
            var row = table.insertRow(rowCount);

            // Generate a unique row ID for reference
            const rowId = 'guest-row-' + Date.now();
            row.id = rowId;

            for (j = 0; j <= rowCount; j++) {
                row.innerHTML = `<td><input type='text' name="guest_card_no[]" class="form-control !py-1.5 !px-1.5 !text-xs guest-card-id" placeholder="ID" required onblur="checkGuestCardId(this)" /></td>
                <td><input type='text' name="guest_name[]" class="form-control !py-1.5 !text-xs" placeholder="Name" required /></td>
                <td><input type='text' name="guest_organization[]" class="form-control !py-1.5 !text-xs" placeholder="Organization" required/></td>
                <td><input type='text' name="guest_phone[]" class="form-control !py-1.5 !text-xs" placeholder="Phone" required/></td>
                <td><input type='text' name="guest_email[]" class="form-control !py-1.5 !text-xs" placeholder="Email" /></td>
                <td><input type='text' name="guest_address[]" class="form-control !py-1.5 !text-xs" placeholder="Address" required/></td>
                <td>
                    <button type="button" class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-red-50 hover:bg-red-100 text-red-600 delete" onclick="delete_row($(this))" title="Remove guest">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                        </svg>
                    </button>
                </td>
                `;
            }

            updateGuestCount();
        });

        function delete_row(row) {

            row.closest('tr').remove();
            let table = document.getElementById("vtable");
            let rowCount = table.rows.length;

            if (rowCount < 2) {
                $("#isGuest").val(2);
            }

            updateGuestCount();
        }

        function updateGuestCount() {
            const table = document.getElementById("vtable");
            const count = table.rows.length - 1; // exclude the header row
            $('#guestCount').text(count);
        }

    </script>


    @if ($errors->any())
        @foreach ($errors->all() as $error)
            <script>
                toastr.error('{{ $error }}', {
                    closeButton: true,
                    progressBar: true,
                });

            </script>
        @endforeach
    @endif

</body>

</html>
