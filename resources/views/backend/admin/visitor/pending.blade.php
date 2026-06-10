@extends('layouts.backend.app')

@section('title','Admin | Pending Visitors')

@push('css')
    <!-- JQuery DataTable Css -->
    <link href="{{ asset('backend/plugins/jquery-datatable/skin/bootstrap/css/dataTables.bootstrap.css') }}" rel="stylesheet">
    <link href="{{ asset('backend/js/pages/tables/buttons.dataTables.min.css') }}" rel="stylesheet">
    <!-- Bootstrap Select -->
    <link href="{{ asset('backend/select2/select2.min.css') }}" rel="stylesheet">
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
        <a href="{{ route('visitors.index') }}" class="btn btn-default waves-effect pull-right" style="margin-bottom:10px;">
            <i class="material-icons">list</i>
            <span>All Visitors</span>
        </a>
        <a href="{{ route('home') }}" target="_blank" class="btn btn-primary waves-effect pull-right" style="margin-bottom:10px; margin-right:5px;">
            <i class="material-icons">add</i>
            <span>Add New Visitor</span>
        </a>
    </div>

    <!-- Filter Card -->
    <div class="row clearfix">
        <div class="col-lg-12">
            <div class="card filter-card">
                <div class="body">
                    <div class="row">
                        <div class="col-md-3 col-sm-6">
                            <div class="form-group">
                                <label>Date From</label>
                                <input type="date" id="date_from" name="date_from" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="form-group">
                                <label>Date To</label>
                                <input type="date" id="date_to" name="date_to" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="form-group">
                                <label>Department</label>
                                <select name="department_id" id="filter_department" class="form-control">
                                    <option value="">All Departments</option>
                                    @foreach($departments as $dept)
                                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="form-group">
                                <label>Employee (Host)</label>
                                <select name="employee_id" id="filter_employee" class="form-control">
                                    <option value="">All Employees</option>
                                    @foreach($employees as $emp)
                                        <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row" style="margin-top:10px;">
                        <div class="col-md-12">
                            <button type="button" id="applyFilter" class="btn btn-primary waves-effect">
                                <i class="material-icons">filter_list</i> Filter
                            </button>
                            <button type="button" id="resetFilter" class="btn btn-default waves-effect">
                                <i class="material-icons">clear</i> Reset
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Visitors Table -->
    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>
                        Pending Visitors <small>(Not Yet Checked Out)</small>
                    </h2>
                </div>
                <div class="body">
                    <div class="table-responsive">
                        <table id="pendingVisitorsTable" class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>SL</th>
                                    <th>Image</th>
                                    <th>Visitor Card</th>
                                    <th>Name</th>
                                    <th>Organization</th>
                                    <th>Phone</th>
                                    <th>In Date</th>
                                    <th>In Time</th>
                                    <th>To Whom</th>
                                    <th>Reason</th>
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
                                    <th>In Date</th>
                                    <th>In Time</th>
                                    <th>To Whom</th>
                                    <th>Reason</th>
                                    <th>Action</th>
                                </tr>
                            </tfoot>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
    <script src="{{ asset('backend/select2/select2.min.js') }}"></script>

    <script>
        $(document).ready(function () {

            $('#filter_department, #filter_employee').select2({
                width: '100%',
                allowClear: true
            });

            var table = $('#pendingVisitorsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('visitors.data') }}',
                    type: 'GET',
                    data: function (d) {
                        d.status        = '0'; // always pending (not checked out)
                        d.date_from     = $('#date_from').val();
                        d.date_to       = $('#date_to').val();
                        d.department_id = $('#filter_department').val();
                        d.employee_id   = $('#filter_employee').val();
                    }
                },
                columns: [
                    { title: 'SL',           orderable: false },
                    { title: 'Image',        orderable: false },
                    { title: 'Visitor Card' },
                    { title: 'Name' },
                    { title: 'Organization' },
                    { title: 'Phone',        orderable: false },
                    { title: 'In Date' },
                    { title: 'In Time' },
                    { title: 'Out',          visible: false },  // hidden — always pending
                    { title: 'To Whom',      orderable: false },
                    { title: 'Reason' },
                    { title: 'Action',       orderable: false, searchable: false },
                ],
                order: [[6, 'desc']],
                pageLength: 25,
                lengthMenu: [10, 25, 50, 100, 200],
            });

            $('#applyFilter').on('click', function () {
                table.ajax.reload();
            });

            $('#resetFilter').on('click', function () {
                $('#date_from, #date_to').val('');
                $('#filter_department, #filter_employee').val(null).trigger('change');
                table.ajax.reload();
            });

            // Checkout button
            $(document).on('click', '.delete', function () {
                if (!confirm('Press OK to Checkout')) return;
                var data_id = $(this).data('delete-id');
                var url = location.origin + '/visitors/checkout/' + data_id;
                var $btn = $(this);
                jQuery.ajax({
                    url: url,
                    type: 'post',
                    success: function (result) {
                        if (result.status === 201) {
                            toastr.success('Successfully Checked Out', 'Success');
                            table.ajax.reload(null, false);
                        } else {
                            toastr.error('Server not responding', 'Error');
                        }
                    },
                });
            });
        });
    </script>
@endpush
