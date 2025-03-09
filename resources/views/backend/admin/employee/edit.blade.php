@extends('layouts.backend.app')

@section('title','Admin | Employees | Edit')

@push('css')

<!-- JQuery Select Css -->
<link href="{{ asset('backend/plugins/bootstrap-select/css/bootstrap-select.css') }}" rel="stylesheet">

<link rel="stylesheet" href="{{ asset('backend/plugins/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.css') }}">

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
                    <form action="{{ route('employees.update', $employee->id)}}" method="post" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group form-float">
                                    <label class="form-label">Employee Name</label>
                                    <div class="form-line">
                                        <input type="text" name="name" class="form-control" value="{{ empty(old('name')) ? $employee->name : old('name')  }}" required>
                            
                                    </div>
                                </div>
                                <div class="form-group form-float">
                                    <label class="form-label">Designation</label>
                                    <div class="form-line">
                                        <input type="text" name="designation" class="form-control" value="{{ empty(old('designation')) ? $employee->designation : old('designation')  }}" required>
                            
                                    </div>
                                </div>
                                <div class="form-group form-float">
                                    <label class="form-label">Phone</label>
                                    <div class="form-line">
                                        <input type="text" name="phone" class="form-control" value="{{ empty(old('phone')) ? $employee->phone : old('phone')  }}" required>
                            
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Date of Resign</label>
                                    <div class="form-line">
                                        <input type="text" name="date_of_resign" value="{{ empty(old('date_of_resign')) ? $employee->resign_date : old('date_of_resign')  }}" class="datepicker form-control" placeholder="Please choose Joining date...">
                                    </div>
                                </div>
                                <br>
                                <div class="row form-group form-float">
                                    <div class="col-md-6">
                                        <label class="form-label">Photo</label>
                                        <div class="">
                                            <input type="file" name="image" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <img src="{{ asset('images/employee/'. $employee->image) }}" class="imag-responsive" style="height: 100px;" alt="">
                                    </div>
                                    
                                </div>
                                <br>
                            
                                
                            </div>
                            <div class="col-md-6">
                                <div class="form-group form-float">
                                    <label class="form-label">Select Department</label>
                                    <select name="department" id="department" class="form-control show-tick">
                                        <option value="">Select Department</option>
                                        @foreach( $departments as $data)
                                        <option value="{{ $data->id }}" {{ empty(old('department')) ? ($employee->department_id == $data->id  ? 'selected' : '') : (old('department') == $data->id  ? 'selected' : '') }}>{{ $data->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group form-float">
                                    <label class="form-label">Email</label>
                                    <div class="form-line">
                                        <input type="text" name="email" class="form-control" value="{{ empty(old('email')) ? $employee->email : old('email')  }}" required>
                            
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Date of Joining</label>
                                    <div class="form-line">
                                        <input type="text" name="date_of_join" value="{{ empty(old('date_of_join')) ? $employee->date_of_join : old('date_of_join')  }}" class="datepicker form-control" placeholder="Please choose Joining date...">
                                    </div>
                                </div>
                                

                                <div class="form-group">
                                    <label class="form-label">About</label>
                                    <div class="form-line">
                                        <textarea class="form-control" name="about" rows="5" placeholder="Please choose a date...">{{ empty(old('about')) ? $employee->about : old('about')  }}</textarea>
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


<script>
    $('.datepicker').bootstrapMaterialDatePicker({
        format: ' DD-MM-YYYY',
        clearButton: true,
        weekStart: 1,
        time: false,
    });
</script>

@endpush
