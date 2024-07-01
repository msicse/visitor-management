@extends('layouts.backend.app')

@section('title', 'Admin | Employees | Show')

@push('css')
    <style>
        .show-image {
            margin-bottom: 20px;
        }

        .show-image img {
            height: 200px;
        }
    </style>
@endpush
@section('content')
    <div class="container-fluid">
        <div class="block-header">
            <a href="{{ route('employees.index') }}" class="btn btn-primary waves-effect pull-right"
                style="margin-bottom:10px;">
                <i class="material-icons">keyboard_return</i>
                <span>Return</span>
            </a>

        </div>
        <div class="row clearfix">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="card">
                    <div class="header">
                        <h2>
                            Information of <strong>{{ $employee->name }}</strong>

                        </h2>
                    </div>
                    <div class="body table-responsive">
                        <div class="show-image text-center">
                            <img src="{{ asset('images/employee/' . $employee->image) }}" alt="">
                        </div>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <td colspan="3">{{ $employee->name }}</td>
                                </tr>
                                <tr>
                                    <th>Employee ID</th>
                                    <td>{{ sprintf('%03d', $employee->emply_id) }}</td>
                                    <th>Designation</th>
                                    <td>{{ $employee->designation }}</td>
                                </tr>
                                <tr>
                                    <th>Department</th>
                                    <td>{{ $employee->department->name }}</td>
                                    <th>Employee Status</th>
                                    <td>{!! $employee->status == 1
                                        ? '<span class=text-success>Active</span>'
                                        : '<span class=text-danger>Inactive</span>' !!} </td>
                                </tr>
                                <tr>
                                    <th>Phone</th>
                                    <td>{{ $employee->phone }}</td>
                                    <th>Email</th>
                                    <td>{{ $employee->email }} </td>
                                </tr>

                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="row clearfix">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="card">
                    <div class="header">
                        <h2>
                            Visitors come to <strong>{{ $employee->name }}</strong>

                        </h2>
                    </div>
                    <div class="body table-responsive">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                                <thead>
                                    <tr>
                                        <th>SL</th>
                                        <th>Image</th>
                                        <th>Name</th>
                                        <th>Factory</th>
                                        <th>Phone</th>
                                        <th>In</th>
                                        <th>Out</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>SL</th>
                                        <th>Image</th>
                                        <th>Name</th>
                                        <th>Factory</th>
                                        <th>Phone</th>
                                        <th>In</th>
                                        <th>Out</th>
                                        <th>Action</th>
                                    </tr>
                                </tfoot>
                                <tbody>
                                    @foreach( $employee->visitors as $key => $data)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td class="text-center"> <img src="{{ asset('images/visitors/'. $data->image) }}" style="height:100px;" alt=""> </td>
                                        <td>{{ $data->name }}</td>
                                        <td>{{ $data->factory_name }}</td>
                                        <td>{{ $data->phone }} </td>
                                        <td>{{ $data->in_time }} </td>
                                        <td>{{ $data->out_time }} </td>
                                        <td>
                                            <a href="{{ route('visitors.show', $data->id) }}" target="_blank" class="btn btn-info waves-effect " title="View Visitor" >
                                                <i class="material-icons">visibility</i>
                                                View
                                            </a>
    
                                       
                                            @if($data->checkout != 1)
                                            <button type="button" class="btn btn-danger waves-effect delete" data-delete-id="{{$data->id}}" data-toggle="modal" title="Disable Visitor" data-target="#delete-modal" >
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
                    </div>
                </div>
            </div>
        </div>
    </div>



    {{-- Delete Modal --}}
<div class="modal fade" id="delete-modal">
    <div class="modal-dialog">
        <form class="delete_form" method="post">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Checkout Visitor </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <strong>Are you sure to Checkout ?</strong>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">Checkout</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </form>
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

@endsection

@push('js')
    <!-- Moment Plugin Js -->
    <script src="{{ asset('backend/plugins/momentjs/moment.js') }}"></script>
    <script src="{{ asset('backend/plugins/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.js') }}">
    </script>


    <script>
        $('.datepicker').bootstrapMaterialDatePicker({
            format: 'dddd DD MMMM YYYY',
            clearButton: true,
            weekStart: 1,
            time: false
        });
    </script>
@endpush
