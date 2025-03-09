@extends('layouts.backend.app')

@section('title','Admin | Employees | Add')

@push('css')

<link rel="stylesheet" href="{{ asset('backend/plugins/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.css') }}">
<link href="{{ asset('backend/select2/select2.min.css') }}" rel="stylesheet" />
@endpush
@section('content')
<div class="container-fluid">
    <div class="block-header">
        <a href="{{ route('employees.index') }}" class="btn btn-primary waves-effect pull-right" style="margin-bottom:10px;" >
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
                        Add New Employee
                    </h2>
                </div>
                <div class="body">
                    <form action="{{ route('employees.store')}}" method="post" enctype="multipart/form-data">
                            @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group form-float">
                                    <label class="form-label">Employee Name</label>
                                    <div class="form-line">
                                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                                    </div>
                                </div>
                                <div class="form-group form-float">
                                    <label class="form-label">Designation</label>
                                    <div class="form-line">
                                        <input type="text" name="designation" class="form-control" value="{{ old('designation') }}" required>
                                    </div>
                                </div>
                                <div class="form-group form-float">
                                    <label class="form-label">Phone</label>
                                    <div class="form-line">
                                        <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" required>
                                    </div>
                                </div>
                                <div class="form-group form-float">
                                    <label class="form-label">Employee ID</label>
                                    <div class="form-line">
                                        <input type="text" name="employee_id" class="form-control" value="{{ old('employee_id') }}" required>
                                    </div>
                                </div>
                
                                <div class="form-group form-float">
                                    <label class="form-label">Photo</label>
                                    <div class="">
                                        <input type="file" name="image" class="form-control" >
                                    </div>
                                </div>
                                <br>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group form-float">
                                    <label class="form-label"> Department</label>
                                    <select name="department" id="department" class="form-control show-tick">
                                        <option value="">Select Department</option>
                                        @foreach( $departments as $data)
                                        <option value="{{ $data->id }}" {{ $data->id == old('department') ? 'selected' : '' }}>{{ $data->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group form-float">
                                    <label class="form-label">Office Email</label>
                                    <div class="form-line">
                                        <input type="text" name="email" class="form-control" value="{{ old('email') }}" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Date of Joining</label>
                                    <div class="form-line">
                                        <input type="text" name="date_of_join" value="{{ old('date_of_join') }}" class="datepicker form-control" placeholder="Please choose Joining date...">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">About</label>
                                    <div class="form-line">
                                        <textarea class="form-control" name="about" rows="5" placeholder=""></textarea>
                                    </div>
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
<!-- Moment Plugin Js -->
<script src="{{ asset('backend/plugins/momentjs/moment.js') }}"></script>
<script src="{{ asset('backend/plugins/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.js') }}"></script>
<script src="{{ asset('backend/select2/select2.min.js') }}"></script>

<script>
    $('.datepicker').bootstrapMaterialDatePicker({
        format: 'DD-MM-YYYY',
        clearButton: true,
        weekStart: 1,
        time: false
    });
    $(document).ready(function(){
        $('#department').select2();
    });
</script>

@endpush
