@extends('layouts.backend.app')

@section('title','Admin | Visitors')

@push('css')
    <!-- JQuery DataTable Css -->
    <link href="{{ asset('backend/plugins/jquery-datatable/skin/bootstrap/css/dataTables.bootstrap.css') }}" rel="stylesheet">
    <link href="{{ asset('backend/js/pages/tables/buttons.dataTables.min.css') }}" rel="stylesheet">
    <style>
        .table td{
            vertical-align: middle !important;
        }
        .filter-card .form-group { margin-bottom: 0; }
    </style>
@endpush
@section('content')
<div class="container-fluid">
    <div class="block-header">
        <a href="{{ route('home') }}" target="_blank" class="btn btn-primary waves-effect pull-right" style="margin-bottom:10px;" >
            <i class="material-icons">add</i>
            <span>Add New Visitor</span>
        </a>

    </div>
    <!-- Filter Card -->
    <div class="row clearfix">
        <div class="col-lg-12">
            <div class="card filter-card">
                <div class="body">
                    <form method="GET" action="{{ route('visitors.index') }}">
                        <div class="row">
                            <div class="col-md-2 col-sm-6">
                                <div class="form-group">
                                    <label>Date From</label>
                                    <input type="date" name="date_from" class="form-control"
                                        value="{{ request('date_from') }}">
                                </div>
                            </div>
                            <div class="col-md-2 col-sm-6">
                                <div class="form-group">
                                    <label>Date To</label>
                                    <input type="date" name="date_to" class="form-control"
                                        value="{{ request('date_to') }}">
                                </div>
                            </div>
                            <div class="col-md-2 col-sm-6">
                                <div class="form-group">
                                    <label>Status</label>
                                    <select name="status" class="form-control">
                                        <option value="">All Status</option>
                                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Pending Checkout</option>
                                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Checked Out</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2 col-sm-6">
                                <div class="form-group">
                                    <label>Visitor Type</label>
                                    <select name="visitor_type" class="form-control">
                                        <option value="">All Types</option>
                                        @foreach($visitorTypes as $type)
                                            <option value="{{ $type }}" {{ request('visitor_type') == $type ? 'selected' : '' }}>
                                                {{ $type }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2 col-sm-6">
                                <div class="form-group">
                                    <label>Department</label>
                                    <select name="department_id" class="form-control">
                                        <option value="">All Departments</option>
                                        @foreach($departments as $dept)
                                            <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                                                {{ $dept->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2 col-sm-6">
                                <div class="form-group">
                                    <label>Employee (Host)</label>
                                    <select name="employee_id" class="form-control">
                                        <option value="">All Employees</option>
                                        @foreach($employees as $emp)
                                            <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>
                                                {{ $emp->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                        </div>
                        <div class="row" style="margin-top:10px;">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary waves-effect">
                                    <i class="material-icons">filter_list</i> Filter
                                </button>
                                <a href="{{ route('visitors.index') }}" class="btn btn-default waves-effect">
                                    <i class="material-icons">clear</i> Reset
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Exportable Table -->
    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>
                        All Visitors
                        <span class="badge ">{{ $visitors->count() }}</span>

                    </h2>
                </div>
                <div class="body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                            <thead>
                                <tr>
                                    <th>SL</th>
                                    <th>Image</th>
                                    <th>Visitor Card</th>
                                    <th>Name</th>
                                    <th>Organization</th>
                                    <th>Phone</th>
                                    <th>In Date</th>
                                    <th>In Time </th>
                                    <th>Out</th>
                                    <th>To Whom</th>
                                    <th>Reson</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th>SL</th>
                                    <th>Image</th>
                                    <th>Visitor Card</th>
                                    <th>Name</th>
                                    <th>Organization</th>
                                    <th>Phone</th>
                                    <th>In</th>
                                    <th>Out</th>
                                    <th>To Whom</th>
                                    <th>Reson</th>
                                    <th>Action</th>
                                </tr>
                            </tfoot>
                            <tbody>
                                @foreach( $visitors as $key => $data)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td class="text-center"> <img src="{{ asset( $data->image) }}" style="height:100px;" alt=""> </td>
                                    <td>{{ $data->visitor_card_id }}</td>
                                    <td>{{ $data->name }}</td>
                                    <td>{{ $data->organization }}</td>
                                    <td>{{ $data->phone }} </td>
                                    <td>{{ date('d-m-Y', strtotime($data->in_time) )}} </td>
                                    <td>{{ date('h:i a', strtotime($data->in_time) )}} </td>
                                    <td> {!! $data->out_time ? date('d-m-Y h:i a', strtotime($data->out_time)) : "<span class='text-danger'>Pending Checkout</span>" !!} </td>
                                    <td>{{ $data->employee->name }} </td>
                                    <td>{{ $data->reason }}</td>

                                    <td>
                                        <a href="{{ route('visitors.show', $data->id) }}" class="btn btn-info waves-effect" style="width: 100px;" title="View Visitor" >
                                            <i class="material-icons">visibility</i>
                                            View
                                        </a>


                                        @if($data->checkout != 1)
                                        <button type="button" class="btn btn-danger waves-effect delete" data-delete-id="{{$data->id}}" style="width: 100px;"  title="Checkedout Visitor" >
                                            <i class="material-icons">exit_to_app</i>
                                            Checkout
                                        </button>
                                        @endif


                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $visitors->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- #END# Exportable Table -->
</div>



@endsection

@push('js')
    <!-- Jquery DataTable Plugin Js -->
    <script src="{{ asset('backend/plugins/jquery-datatable/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('backend/plugins/jquery-datatable/skin/bootstrap/js/dataTables.bootstrap.js') }}"></script>
    <script src="{{ asset('backend/plugins/jquery-datatable/extensions/export/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('backend/plugins/jquery-datatable/extensions/export/buttons.flash.min.js') }}"></script>
    <script src="{{ asset('backend/plugins/jquery-datatable/extensions/export/jszip.min.js') }}"></script>
    <script src="{{ asset('backend/plugins/jquery-datatable/extensions/export/pdfmake.min.js') }}"></script>
    <script src="{{ asset('backend/plugins/jquery-datatable/extensions/export/vfs_fonts.js') }}"></script>
    <script src="{{ asset('backend/plugins/jquery-datatable/extensions/export/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('backend/plugins/jquery-datatable/extensions/export/buttons.print.min.js') }}"></script>




    <script src="{{ asset('backend/js/pages/tables/jquery-datatable.js') }}"></script>

<script>

$( ".delete" ).click(function() {
   let result = confirm("Press OK to Checkout");

    if (result === true) {
        var data_id=$(this).data('delete-id');
        var url=location.origin+'/visitors/checkout/'+data_id;
        jQuery.ajax({
            url: url,
            type: "post",
            success: function(result){

                if(result.status === 201){

                    $('.delete[data-delete-id="' + data_id + '"]').addClass('hidden');
                    toastr.success('Succesfully Checked Out ', 'Success')
                } else {

                    toastr.error('Server not response', 'Error');
                }
            },
        });

    } else {
    }
});

</script>


@endpush
