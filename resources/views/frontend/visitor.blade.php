@extends('layouts.frontend.app')
@section('title', 'Visitor Register')
@push('css')
<link href="{{ asset('backend/select2/select2.min.css') }}" rel="stylesheet" />
    <style>
        html,
        body {
            position: absolute;
            height: 100%;
        }

        .form-group label.error {
            font-size: 12px;
            display: block;
            margin-top: 5px;
            font-weight: normal;
            color: #F44336;
        }
        .snap-h {
            height: 240px;
            text-align: center;
            align-items: center;
            padding-right: 10px;
        }
    </style>
    {{-- display: flex; --}}
    {{-- align-items: center; --}}
@endpush

@section('content')
    <div class="min-vw-100">

        <div class="container-fluid m-0 p-0 ">
            <div class="row g-0 py-3 align-items-center">
                <div class="col-lg-3 col-md-3 align-middle ps-5 ">
                    <img src="{{ asset('images/rsc.png') }}" class="img-fluid" height="100px" width="300px" alt="">
                </div>
                <div class="col-lg-7 col-md-7 text-center">
                    <h1 class="display-3">Visitor Registration</h1>
                </div>
                <div class="col-lg-2 col-md-2 text-end pe-5">
                    <a href="{{ route('login') }}" class="btn btn-primary">Admin Login</a>
                </div>
            </div>
            <section class="text-lg-start">


                <div class="card">


                    <div class="row g-0">
                        <div class="col-lg-3 col-md-2 align-middle">
                            <div class="p-5 text-center">
                                <div class="row snap-h d-flex align-center" >
                                    <div class="col">
                                        <span id="my_camera" class="snap"></span>
                                    </div>

                                </div>
                                <div class="row">
                                    <div class="col">
                                        <span id="results"></span>
                                    </div>

                                    </div>
                                <div class="row text-center ">
                                    <div class="col">
                                        <input type="button" class="btn btn-success btn-lg custom-btn"
                                            value="Take Photo" onClick="take_snapshot()">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-9 col-md-10">
                            <div class="card-body py-5 px-md-5">

                                <form action="{{ route('visitor.store') }}" method="POST" id="addVisitorForm">
                                    @csrf
                                    <input type="hidden" name="image" class="image-tag">
                                    <input type="hidden" id="isGuest" name="is_guest" value="2" class="">
                                    <div class="row">
                                        <div class="col">
                                            <div class="row">
                                                <div class="col">
                                                    <div class="form-group mb-1">
                                                        <label class="form-label"> Visitor From <span class="text-danger font-bold">*</span></label>
                                                        <select name="visitor_type" class="form-select form-select-sm"
                                                            id="organization-type" required>
                                                            <option value="">Select </option>
                                                            <option value="brand">Brand </option>
                                                            <option value="factory">Factory </option>
                                                            <option value="trade-union">Trade Union </option>
                                                            <option value="official">Others </option>
                                                        </select>
                                                        <label id="organization-type-error" class="error"
                                                            for="organization-type"></label>
                                                    </div>
                                                </div>
                                                <div class="col">
                                                    <div class="form-group mb-3">
                                                        <label for="visitor_card_id" class="form-label">Visitor Card ID <span
                                                                class="text-danger font-bold">*</span></label>
                                                        <input type="text" class="form-control form-control-sm" name="visitor_card_id"
                                                            id="visitor_card_id" placeholder="Enter Card ID" required>
                                                    </div>
                                                </div>
                                            </div>



                                            <div class="form-group mb-3">
                                                <label for="name" class="form-label">Visitor Name <span
                                                        class="text-danger font-bold">*</span></label>
                                                <input type="text" class="form-control form-control-sm" name="name"
                                                    id="name" placeholder="Enter Name" required>
                                            </div>
                                            <div class="form-group mb-3">
                                                <label for="phone" class="form-label">Phone <span
                                                        class="text-danger font-bold">*</span></label>
                                                <input type="text" name="phone" class="form-control form-control-sm"
                                                    id="phone" placeholder="Enter phone" required>
                                            </div>

                                            <div class="form-group mb-3">
                                                <label for="address" class="form-label">Address <span
                                                        class="text-danger font-bold">*</span> </label>
                                                <textarea class="form-control" name="address" placeholder="Address" id="address" style="height: 60px" required></textarea>
                                            </div>



                                        </div>
                                        <div class="col">
                                            <div class="form-group mb-3">
                                                <label class="form-label"> Whom to Meet <span
                                                        class="text-danger font-bold">*</span></label>
                                                <select name="employee" id="employee"
                                                    class="form-control form-select form-control-sm" required>
                                                    <option value="">Select RSC Employee</option>
                                                    @foreach ($employees as $data)
                                                        <option value="{{ $data->id }}"
                                                            {{ $data->id == old('employee') ? 'selected' : '' }}>
                                                            {{ $data->name . ' - ' . $data->department->short_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <label id="employee-error" class="error" for="employee"></label>
                                            </div>

                                            <div class="form-group mb-3">
                                                <label for="organization" class="form-label">Organization <span
                                                        class="text-danger font-bold">*</span></label>
                                                <input type="text" class="form-control form-control-sm"
                                                    name="organization" id="organization"
                                                    placeholder="Enter Organization Name" required>

                                            </div>


                                            <div class="form-group mb-3">
                                                <label for="email" class="form-label">Email <span
                                                        class="text-danger font-bold" id="emailReq">*</span></label>
                                                <input type="email" name="email" class="form-control form-control-sm"
                                                    id="email" placeholder="Enter email">
                                            </div>

                                            <div class="form-group mb-3">
                                                <label for="reason" class="form-label">Reason <span
                                                        class="text-danger font-bold">*</span></label>
                                                <textarea class="form-control" placeholder="Reason" id="reason" style="height: 60px" required></textarea>
                                            </div>



                                        </div>
                                    </div>

                                    <div class="row mt-3">
                                        <div class="col-12 text-end mb-2">
                                            <button type="button" id="addVisitor" class="btn btn-sm btn-primary ">Accopanied By</button>
                                        </div>
                                        <div class="col-12">
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-striped" id="vtable">
                                                    <thead>
                                                        <tr>
                                                            <th>SL</th>
                                                            <th>Name </th>
                                                            <th>Organization Name </th>
                                                            <th>Phone</th>
                                                            <th>Email</th>
                                                            <th>Address </th>
                                                            <th>Action </th>

                                                        </tr>
                                                    </thead>

                                                </table>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="col text-center">
                                        <!-- Submit button -->
                                        <input type="submit" class="btn btn-success btn-block btn-lg mb-4 px-10" />
                                    </div>


                                </form>

                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

    </div>
@endsection

@push('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.25/webcam.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.20.0/dist/jquery.validate.min.js"></script>
    <script src="{{ asset('backend/select2/select2.min.js') }}"></script>
    <script>
        Webcam.set({
                width: 220,
                height: 220,
                image_format: 'jpeg',
                jpeg_quality: 90
            }

        );

        Webcam.attach('#my_camera');

        $("#organization-type").change(function() {
            let id = $(this).val();

            if (id === 'official') {
                $("#email").removeAttr('required');
                $("#emailReq").addClass('d-none');
            } else {
                console.log(id);
                $('#email').attr('required', 'required');
                $("#emailReq").removeClass('d-none');
            }

            // alert(id);


        })

        function take_snapshot() {
            Webcam.snap(function(data_uri) {
                    $(".image-tag").val(data_uri);
                    document.getElementById('results').innerHTML = '<img height="180px" width="220px" src="' +
                        data_uri + '"/>';
                }

            );
        }

        $(document).ready(function() {
            $('#employee').select2();
        });
        $("#addVisitorForm").validate();


        $('#addVisitor').click(function(e) {
            e.preventDefault();

            $("#isGuest").val(1);
            let table = document.getElementById("vtable");
            let rowCount = table.rows.length;
            console.log(rowCount);
            var row = table.insertRow(rowCount);
            for (j = 0; j <= rowCount; j++) {
                row.innerHTML = `<td class='align-top' id=` + j + 1 + '>' + j + `</td>

                <td class='align-top'><input type='text' name="guest_name[]" class="form-control" required /></td>
                <td class='align-top'><input type='text' name="guest_organization[]" class="form-control"  required/></td>
                <td class='align-top'><input type='text' name="guest_phone[]" class="form-control"  required/></td>
                <td class='align-top'><input type='text' name="guest_email[]" class="form-control"  /></td>
                <td class='align-top'><input type='text' name="guest_address[]" class="form-control" required/></td>
                <td class='align-top' style="width: 5%">
                    <button type="button" class="btn btn-danger btn-xs delete" onclick ="delete_row($(this))">X</button>
                </td>


                `;
            }
        });

        function delete_row(row) {

            row.closest('tr').remove();
            let table = document.getElementById("vtable");
            let rowCount = table.rows.length;

            if( rowCount < 2 ){
                $("#isGuest").val(2);
            }

            console.log('row'+ rowCount);
            console.log('guest'+ $("#isGuest").val());

        }
    </script>
@endpush
