<!doctype html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="{{ asset('backend/select2/select2.min.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('css/toastr.min.css')  }}">
    <style>
        .error {
            color: red;
        }

        .error-border {
            border-color: #ef4444 !important;
        }

        table td,
        table td * {
            vertical-align: top;
        }

        #my_camera, #results {
            width: 220px;
            height: 180px;
        }

        #results img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
    </style>
</head>

<body class="h-screen">
    <header class="w-full mx-auto flex justify-between py-4 px-10 items-center sm:flex-row border-b  border-gray-600">
        <img src="{{ asset('images/rsc.png') }}" class=" h-10">
        <h1 class="text-6xl md:text-4xl text-center">Visitor Registration</h1>
        <a href="{{ route('login') }}" class=" py-2 px-3 bg-blue-700 text-white rounded-lg ">Admin Login</a>
    </header>

    <div class="flex flex-row h-[calc(100vh-5rem)] px-4">
        <aside class="w-1/6 overflow-y-auto">
            <div class="flex flex-col px-4 py-2 justify-between items-center gap-2 ">
                <div class="relative">
                    <span id="my_camera" class="px-6 snap"></span>
                    <span id="results" class="absolute top-0 left-0 w-full h-full"></span>
                </div>
                <input type="button" class="py-2 px-3 bg-green-700 text-white rounded-lg w-full" value="Take Photo"
                    onClick="take_snapshot()">
            </div>

        </aside>
        <main class="w-5/6 p-4 overflow-y-auto">
            <form action="#" id="addVisitorForm">
                <input type="hidden" name="image" id="image" class="image-tag" value="" required>
                <input type="hidden" id="isGuest" name="is_guest" value="2" class="">
                <div class="flex gap-16">
                    <div class="w-1/2 ">
                        <div class="flex flex-col mb-2">
                            <label for="phone" class="text-sm font-medium py-1 ">Visitor Phone <span
                                    class=" text-red-600 pl-1">*</span></label>
                            <input type="text" name="phone" id="phone" placeholder="Visitor phone" required
                                class="rounded-sm border border-slate-950 py-1 px-4 text-gray-800 text-sm" />
                        </div>
                        <div class="flex items-center grow mb-2 gap-4">
                            <div class="flex flex-col  basis-1/2">
                                <label for="organization-type" class="text-md font-medium py-2 ">Visitor From <span
                                        class=" text-red-600 pl-1">*</span></label>
                                <select id="organization-type" name="visitor_type"
                                    class="rounded-sm border border-slate-950 bg-transparent py-1 px-4 text-gray-800 sm:text-sm"
                                    required>
                                    <option value="">Select </option>
                                    <option value="brand">Brand </option>
                                    <option value="factory">Factory </option>
                                    <option value="trade-union">Trade Union </option>
                                    <option value="official">Others </option>
                                </select>
                                <label id="organization-type-error" class="error" for="organization-type"></label>
                            </div>
                            <div class="flex flex-col basis-1/2">
                                <label for="visitor_card_id" class="text-sm font-medium py-2 ">Visitor Card ID <span
                                        class=" text-red-600 pl-1">*</span></label>
                                <input type="text" name="visitor_card_id" id="visitor_card_id"
                                    placeholder="Enter Card ID" required
                                    class="rounded-sm border border-slate-950 py-1 px-4 text-gray-800 text-sm " />
                                <div id="cardIdFeedback" class="text-xs mt-1 hidden"></div>
                            </div>

                        </div>

                        <div class="flex flex-col mb-2">
                            <label for="employee" class="text-md font-medium ">Whom to Meet <span
                                    class=" text-red-600 pl-1">*</span></label>
                            <select id="employee" name="employee"
                                class=" rounded-sm border border-slate-950 bg-transparent py-1 px-4 text-gray-800 sm:text-sm"
                                required>
                                <option value="">Select RSC Employee</option>
                                @foreach ($employees as $data)
                                    <option value="{{ $data->id }}" {{ $data->id == old('employee') ? 'selected' : '' }}>
                                        {{ $data->name . ' - ' . $data->department->short_name }}
                                    </option>
                                @endforeach
                            </select>
                            <label id="employee-error" class="error" for="employee"></label>
                        </div>


                        <div class="flex flex-col mb-2">
                            <label for="reason" class="text-sm font-medium py-1 ">Reason <span
                                    class=" text-red-600 pl-1">*</span></label>
                            <textarea class="rounded-sm border border-slate-950 py-1 px-4 text-gray-800 text-sm "
                                name="reason" placeholder="Reason" id="reason" required></textarea>
                        </div>

                    </div>
                    <div class="w-1/2">


                        <div class="flex flex-col mb-2">
                            <label for="name" class="text-sm font-medium py-1 ">Visitor Name <span
                                    class=" text-red-600 pl-1">*</span></label>
                            <input type="text" name="name" id="name" placeholder="Enter Name" required
                                class="rounded-sm border border-slate-950 py-1 px-4 text-gray-800 text-sm " />
                        </div>
                        <div class="flex flex-col mb-2">
                            <label for="organization" class="text-sm font-medium py-1 ">Visitor Organization <span
                                    class=" text-red-600 pl-1">*</span></label>
                            <input id="organization" type="text" name="organization"
                                class="rounded-sm border border-slate-950 py-1 px-4 text-gray-800 text-sm "
                                placeholder="Enter Organization Name" required />
                        </div>

                        <div class="flex flex-col mb-2">
                            <label for="email" class="text-sm font-medium py-1 "> Visitor Email <span class=" text-red-600 pl-1"
                                    id="emailReq">*</span></label>
                            <input id="email" type="text" name="email" placeholder="Visitor Email"
                                class="rounded-sm border border-slate-950 py-1 px-4 text-gray-800 text-sm " />
                        </div>


                        <div class="flex flex-col mb-2">
                            <label for="address" class="text-sm font-medium py-1 ">Visitor Address <span
                                    class=" text-red-600 pl-1">*</span></label>
                            <textarea class="rounded-sm border border-slate-950 py-1 px-4 text-gray-800 text-sm "
                                name="address" placeholder="Visitor Address" id="address" required></textarea>
                        </div>
                    </div>
                </div>
                <div class="flex justify-end py-3">
                    <button type="button" id="addVisitor"
                        class="py-1 px-2 bg-blue-600 text-sm rounded-sm text-white flex items-center justify-center gap-1">
                        <span>Accopanied By</span>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-4 text-white">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                    </button>
                </div>

                <div class="block bg-transparent p-2 w-full overflow-x-auto">
                    <table class="w-full table-auto border" id="vtable">
                        <tr class="border border-solid border-l-0 text-sm ">
                            <th>SL</th>
                            <th class="w-1/12">Card No. </th>
                            <th>Name </th>
                            <th>Organization </th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Address </th>
                            <th>Action </th>
                        </tr>

                    </table>
                </div>
                <div class="text-center">
                    <button type="submit"
                        class=" bg-green-700 px-8 py-2 text-white rounded-md font-medium my-2">Submit</button>
                    <button type="button" id="resetForm"
                        class=" bg-gray-600 px-8 py-2 text-white rounded-md font-medium my-2 ml-2">Reset</button>
                </div>


            </form>
        </main>
    </div>


    <div id="spinnerModal"
        class="fixed inset-0 items-center justify-center bg-slate-200 bg-opacity-80 z-50 hidden">
        <div class="bg-transparent p-6 rounded-lg">
            <!-- Spinner -->
            <div class="flex items-center justify-center">
                <svg class="w-16 h-16 animate-spin text-gray-900/50" viewBox="0 0 64 64" fill="none"
                    xmlns="http://www.w3.org/2000/svg" width="24" height="24">
                    <path
                        d="M32 3C35.8083 3 39.5794 3.75011 43.0978 5.20749C46.6163 6.66488 49.8132 8.80101 52.5061 11.4939C55.199 14.1868 57.3351 17.3837 58.7925 20.9022C60.2499 24.4206 61 28.1917 61 32C61 35.8083 60.2499 39.5794 58.7925 43.0978C57.3351 46.6163 55.199 49.8132 52.5061 52.5061C49.8132 55.199 46.6163 57.3351 43.0978 58.7925C39.5794 60.2499 35.8083 61 32 61C28.1917 61 24.4206 60.2499 20.9022 58.7925C17.3837 57.3351 14.1868 55.199 11.4939 52.5061C8.801 49.8132 6.66487 46.6163 5.20749 43.0978C3.7501 39.5794 3 35.8083 3 32C3 28.1917 3.75011 24.4206 5.2075 20.9022C6.66489 17.3837 8.80101 14.1868 11.4939 11.4939C14.1868 8.80099 17.3838 6.66487 20.9022 5.20749C24.4206 3.7501 28.1917 3 32 3L32 3Z"
                        stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"></path>
                    <path
                        d="M32 3C36.5778 3 41.0906 4.08374 45.1692 6.16256C49.2477 8.24138 52.7762 11.2562 55.466 14.9605C58.1558 18.6647 59.9304 22.9531 60.6448 27.4748C61.3591 31.9965 60.9928 36.6232 59.5759 40.9762"
                        stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"
                        class="text-gray-950">
                    </path>
                </svg>
            </div>
            <!-- Modal Content (optional) -->
            <div class="mt-4 text-center">
                <p class=" text-black">Loading, please wait...</p>
            </div>
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
                document.getElementById('results').innerHTML = '<img height="180px" width="220px" src="' + data_uri + '"/>';

                // Hide camera and show result
                $("#my_camera").css("visibility", "hidden");
                $("#results").css("visibility", "visible");

                // Change button text to "Retake Photo"
                $("input[onClick='take_snapshot()']").val("Retake Photo").attr("onClick", "retake_photo()");
            });
        }

        function retake_photo() {
            // Clear the result and show camera again
            document.getElementById('results').innerHTML = '';
            $("#my_camera").css("visibility", "visible");
            $("#results").css("visibility", "hidden");

            // Change button text back to "Take Photo"
            $("input[onClick='retake_photo()']").val("Take Photo").attr("onClick", "take_snapshot()");
        }

        Webcam.set({
            width: 220,
            height: 180,
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

            if (id === 'official') {
                $("#email").removeAttr('required');
                $("#emailReq").addClass('hidden');
            } else {
                console.log(id);
                $('#email').attr('required', 'required');
                $("#emailReq").removeClass('');
            }

            // alert(id);
        });

        $(document).ready(function () {
            $('#employee').select2();
            $("#addVisitorForm").validate();

            // Function to clear visitor form fields
            function clearVisitorFormFields() {
                $('#name').val('');
                $('#email').val('');
                $('#organization').val('');
                $('#address').val('');
                $('#organization-type').val('');
                $('#visitor_card_id').val('');
                $('#reason').val('');
            }

            // Card ID validation
            let cardIdTimer;
            $('#visitor_card_id').on('input', function() {
                const cardId = $(this).val().trim();
                clearTimeout(cardIdTimer);

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

                    // Clear form fields before making the request
                    clearVisitorFormFields();

                    // Reset the camera/photo
                    $(".image-tag").val("");
                    document.getElementById('results').innerHTML = "";
                    $("#my_camera").css("visibility", "visible");
                    $("#results").css("visibility", "hidden");
                    $("input[onClick='retake_photo()']").val("Take Photo").attr("onClick", "take_snapshot()");

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

                                // Populate form fields with visitor data
                                $('#name').val(visitor.name);
                                $('#email').val(visitor.email);
                                $('#organization').val(visitor.organization);
                                $('#address').val(visitor.address);

                                // Set visitor type dropdown
                                $('#organization-type').val(visitor.visitor_type);

                                // Notify user
                                toastr.info('Previous visitor data loaded. You can modify if needed.', 'Info');
                            } else {
                                // If no visitor found, form fields remain cleared
                                // Optional: notify user that this is a new visitor
                                // toastr.info('New visitor registration', 'Info');
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
                                console.log(result);
                                if (result.status === 201) {
                                    $('#employee').val(null).trigger('change');
                                    $('#addVisitorForm')[0].reset();

                                    $(".image-tag").val("");
                                    document.getElementById('results').innerHTML = "";
                                    // Reset camera UI
                                    $("#my_camera").css("visibility", "visible");
                                    $("#results").css("visibility", "hidden");
                                    $("input[onClick='retake_photo()']").val("Take Photo").attr("onClick", "take_snapshot()");

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
                                            $('<div class="text-red-500 text-xs mt-1 form-error">' + errors[field][0] + '</div>')
                                                .insertAfter(inputField);
                                        }
                                    }
                                } else if (xhr.status === 500) {
                                    // Server errors
                                    let errorMessage = 'An error occurred. Please try again.';

                                    if (xhr.responseJSON && xhr.responseJSON.error) {
                                        errorMessage = xhr.responseJSON.error;
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
                // Reset the form
                $('#employee').val(null).trigger('change');
                $('#addVisitorForm')[0].reset();

                // Remove error highlights
                $('.form-error').remove();
                $('.error-border').removeClass('error-border');

                // Reset the camera/photo
                $(".image-tag").val("");
                document.getElementById('results').innerHTML = "";
                $("#my_camera").css("visibility", "visible");
                $("#results").css("visibility", "hidden");
                $("input[onClick='retake_photo()']").val("Take Photo").attr("onClick", "take_snapshot()");

                // Reset card ID feedback
                $('#cardIdFeedback').removeClass('text-green-600 text-red-600').addClass('hidden');

                // Notify user
                toastr.info('Form has been reset', 'Info');
            });
        });

        $('#addVisitor').click(function (e) {
            e.preventDefault();

            $("#isGuest").val(1);
            let table = document.getElementById("vtable");
            let rowCount = table.rows.length;
            console.log(rowCount);
            var row = table.insertRow(rowCount);

            // Generate a unique row ID for reference
            const rowId = 'guest-row-' + Date.now();
            row.id = rowId;

            for (j = 0; j <= rowCount; j++) {
                row.innerHTML = `<td class='' id=` + j + 1 + '>' + j + `</td>

                <td class="border "><input type='text' name="guest_card_no[]" class="rounded-sm border border-slate-950 p-1 text-gray-800 text-sm guest-card-id" required onblur="checkGuestCardId(this)" /></td>
                <td class="border"><input type='text' name="guest_name[]" class="rounded-sm border border-slate-950 p-1 text-gray-800 text-sm" required /></td>
                <td class="border"><input type='text' name="guest_organization[]" class="rounded-sm border border-slate-950 p-1 text-gray-800 text-sm"  required/></td>
                <td class="border"><input type='text' name="guest_phone[]" class="rounded-sm border border-slate-950 p-1 text-gray-800 text-sm"  required/></td>
                <td class="border"><input type='text' name="guest_email[]" class="rounded-sm border border-slate-950 p-1 text-gray-800 text-sm"  /></td>
                <td class="border"><input type='text' name="guest_address[]" class="rounded-sm border border-slate-950 p-1 text-gray-800 text-sm" required/></td>
                <td class="border">
                    <button type="button" class="px-2 py-1 bg-red-600 text-white text-sm delete" onclick ="delete_row($(this))">X</button>
                </td>


                `;
            }
        });

        function delete_row(row) {

            row.closest('tr').remove();
            let table = document.getElementById("vtable");
            let rowCount = table.rows.length;

            if (rowCount < 2) {
                $("#isGuest").val(2);
            }

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
