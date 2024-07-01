@extends('layouts.backend.app')

@section('title', 'Admin | Visitors | Add')

@push('css')
    <link rel="stylesheet"
        href="{{ asset('backend/plugins/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.css') }}">
    <link href="{{ asset('backend/select2/select2.min.css') }}" rel="stylesheet" />

    <style>
        .snap {
            text-align: center;
            padding: 20px 10px;
        }
    </style>
@endpush
@section('content')
    <div class="container-fluid">
        <div class="block-header">
            <a href="{{ route('visitors.index') }}" class="btn btn-primary waves-effect pull-right"
                style="margin-bottom:10px;">
                <i class="material-icons">keyboard_return</i>
                <span>Return</span>
            </a>
        </div>
        <!-- Exportable Table -->
        <div class="row clearfix">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="card">
                    <div class="header">
                        <h2>
                            Add New Visitor
                        </h2>
                    </div>
                    <div class="body">
                        <form action="{{ route('visitors.store') }}" method="post" id="visitorForm"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div id="my_camera" class="snap"></div>
                                    <input type="hidden" name="image" class="image-tag">
                                </div>
                                <div class="col-md-6">
                                    <div id="results" style="text-align: center; padding-top: 30px;"></div>
                                </div>
                            </div>
                            <div class="row text-center">
                                <div class="col-md-12">
                                    <input type="button" class="btn btn-success btn-lg custom-btn" value="Take Snapshot"
                                        onClick="take_snapshot()">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group form-float">
                                        <label class="form-label">Visitor Card ID. <span
                                                class="text-danger font-bold">*</span></label>
                                        <div class="form-line">
                                            <input type="text" name="visitor_card" class="form-control"
                                                value="{{ old('visitor_card') }}" required>
                                        </div>
                                    </div>

                                    <div class="form-group form-float">
                                        <label class="form-label">Visitor Name <span
                                                class="text-danger font-bold">*</span></label>
                                        <div class="form-line">
                                            <input type="text" name="name" class="form-control"
                                                value="{{ old('name') }}" required>
                                        </div>
                                    </div>


                                    <div class="form-group form-float">
                                        <label class="form-label">Organization Name <span
                                                class="text-danger font-bold">*</span></label>
                                        <div class="form-line">
                                            <input type="text" name="factory_name" class="form-control"
                                                value="{{ old('factory_name') }}" required>
                                        </div>
                                    </div>


                                    <div class="form-group">
                                        <label class="form-label"> Reason <span class="text-danger font-bold">*</span>
                                        </label>
                                        <div class="form-line">
                                            <textarea class="form-control" name="reason" rows="3" placeholder="" required></textarea>
                                        </div>
                                    </div>

                                    <br>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group form-float">
                                        <label class="form-label"> Whom to Meet <span
                                                class="text-danger font-bold">*</span></label>
                                        <select name="employee" id="employee" class="form-control show-tick" required>
                                            <option value="">Select Employee</option>
                                            @foreach ($employees as $data)
                                                <option value="{{ $data->id }}"
                                                    {{ $data->id == old('employee') ? 'selected' : '' }}>
                                                    {{ $data->name . ' - ' . $data->department->short_name }} </option>
                                            @endforeach
                                        </select>
                                        <label id="employee-error" class="error" for="employee"></label>
                                    </div>
                                    <div class="form-group form-float">
                                        <label class="form-label">Visitor Email</label>
                                        <div class="form-line">
                                            <input type="text" name="email" class="form-control"
                                                value="{{ old('email') }}">
                                        </div>
                                    </div>

                                    <div class="form-group form-float">
                                        <label class="form-label">Visitor Phone <span
                                                class="text-danger font-bold">*</span></label>
                                        <div class="form-line">
                                            <input type="text" name="phone" class="form-control"
                                                value="{{ old('phone') }}" required>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label"> Address <span
                                                class="text-danger font-bold">*</span></label>
                                        <div class="form-line">
                                            <textarea class="form-control" name="address" rows="3" placeholder="" required></textarea>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 text-right">
                                    <button type="button" id="addVisitor" class="btn btn-lg btn-info">Add Guest</button>
                                </div>
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped" id="vtable">
                                            <thead>
                                                <tr>
                                                    <th>SL</th>
                                                    <th>Guest Name </th>
                                                    <th>Organization Name </th>
                                                    <th>Phone</th>
                                                    <th>Email</th>
                                                    <th>Address </th>

                                                </tr>
                                            </thead>

                                        </table>
                                        
                                    </div>
                                </div>
                            </div>
                            <div class="row text-center">
                                <input type="submit" class="btn btn-success btn-lg custom-btn" value="Save">
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>

    </div>


@endsection

@push('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.25/webcam.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.20.0/dist/jquery.validate.min.js"></script>
    <!-- Moment Plugin Js -->
    <script src="{{ asset('backend/plugins/momentjs/moment.js') }}"></script>
    <script src="{{ asset('backend/plugins/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.js') }}">
    </script>
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

        function take_snapshot() {
            Webcam.snap(function(data_uri) {
                    $(".image-tag").val(data_uri);
                    document.getElementById('results').innerHTML = '<img height="180px" width="220px" src="' +
                        data_uri + '"/>';
                }

            );

        }

        $('.datepicker').bootstrapMaterialDatePicker({
            format: 'DD-MM-YYYY',
            clearButton: true,
            weekStart: 1,
            time: false
        });
        $(document).ready(function() {
            $('#employee').select2();
        });
        $("#visitorForm").validate();


        $('#addVisitor').click(function() {

            let table = document.getElementById("vtable");
            let rowCount = table.rows.length;
            var row = table.insertRow(rowCount);
            for (j = 0; j <= rowCount; j++) {
                row.innerHTML = '<td id=' + j + 1 + '>' + j + `</td>
                
                <td><input type='text' class="form-control" /></td>
                <td><input type='text' class="form-control" /></td>
                <td><input type='text' class="form-control" /></td>
                <td><input type='text' class="form-control" /></td>
                <td><input type='text' class="form-control" /></td>
                <td style="width: 5%">
                    <button type="button" class="btn btn-danger btn-xs delete" onclick ="delete_row($(this))">Remove</button>
                </td>
                
                
                `;
            }
        });

        function delete_row(row) {
            row.closest('tr').remove();
            calculate_sub_total();
        }
    </script>
@endpush
