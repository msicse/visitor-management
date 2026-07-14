@extends('layouts.backend.app')

@section('title','Admin | Visitors')

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
                    <div class="row">
                            <div class="col-md-2 col-sm-6">
                                <div class="form-group">
                                    <label>Date From</label>
                                    <input type="date" id="date_from" name="date_from" class="form-control"
                                        value="{{ request('date_from') }}">
                                </div>
                            </div>
                            <div class="col-md-2 col-sm-6">
                                <div class="form-group">
                                    <label>Date To</label>
                                    <input type="date" id="date_to" name="date_to" class="form-control"
                                        value="{{ request('date_to') }}">
                                </div>
                            </div>
                            <div class="col-md-2 col-sm-6">
                                <div class="form-group">
                                    <label>Status</label>
                                    <select name="status" id="filter_status" class="form-control">
                                        <option value="">All Status</option>
                                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Pending Checkout</option>
                                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Checked Out</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2 col-sm-6">
                                <div class="form-group">
                                    <label>Visitor Type</label>
                                    <select name="visitor_type" id="filter_visitor_type" class="form-control">
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
                                    <select name="department_id" id="filter_department" class="form-control">
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
                                    <select name="employee_id" id="filter_employee" class="form-control">
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
    <!-- Exportable Table -->
    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>
                        All Visitors
                    </h2>
                </div>
                <div class="body">
                    <div class="table-responsive">
                        <table id="visitorsTable" class="table table-bordered table-striped table-hover">
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
                                    <th>Guests</th>
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
                                    <th>Out</th>
                                    <th>Guests</th>
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
    <!-- #END# Exportable Table -->
</div>

{{-- Checkout Modal --}}
<div class="modal fade" id="checkoutModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Checkout Visitor</h4>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to checkout <strong id="checkoutVisitorName"></strong>?</p>
                <div id="checkoutGuestOption" style="display:none;margin-top:10px;">
                    <div class="checkbox">
                        <input type="checkbox" id="checkoutAllGuests" class="filled-in" checked>
                        <label for="checkoutAllGuests">Also checkout all <strong id="checkoutGuestCount"></strong> guest(s)</label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmCheckoutBtn">Checkout</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="guestListModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Guest List: <span id="guestListVisitorName"></span></h4>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>SL</th>
                                <th>Card No</th>
                                <th>Name</th>
                                <th>Organization</th>
                                <th>Phone</th>
                                <th>Status</th>
                                <th>Out Time</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="guestListTableBody">
                            <tr><td colspan="8" class="text-center">Loading...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
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




    <script src="{{ asset('backend/js/pages/tables/jquery-datatable.js') }}"></script>
    <script src="{{ asset('backend/select2/select2.min.js') }}"></script>
    <script>
        $(document).ready(function () {

            // Select2 for filter dropdowns
            $('#filter_status, #filter_visitor_type, #filter_department, #filter_employee').select2({
                width: '100%',
                allowClear: true
            });

            // Server-side DataTable
            var table = $('#visitorsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('visitors.data') }}',
                    type: 'GET',
                    data: function (d) {
                        d.date_from     = $('#date_from').val();
                        d.date_to       = $('#date_to').val();
                        d.status        = $('#filter_status').val();
                        d.visitor_type  = $('#filter_visitor_type').val();
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
                    { title: 'Out' },
                    { title: 'Guests',       orderable: false },
                    { title: 'To Whom',      orderable: false },
                    { title: 'Reason' },
                    { title: 'Action',       orderable: false, searchable: false },
                ],
                order: [[6, 'desc']],
                pageLength: 25,
                lengthMenu: [10, 25, 50, 100, 200],
            });

            // Filter button
            $('#applyFilter').on('click', function () {
                table.ajax.reload();
            });

            // Reset button
            $('#resetFilter').on('click', function () {
                $('#date_from, #date_to').val('');
                $('#filter_status, #filter_visitor_type, #filter_department, #filter_employee').val(null).trigger('change');
                table.ajax.reload();
            });

            // Checkout modal trigger
            var checkoutVisitorId  = null;
            var selectedVisitorIdForGuests = null;

            $(document).on('click', '.checkout-btn', function () {
                var $btn       = $(this);
                checkoutVisitorId  = $btn.data('delete-id');
                var guestCount = parseInt($btn.data('guest-count')) || 0;
                var visitorName = $btn.data('visitor-name') || '';

                $('#checkoutVisitorName').text(visitorName);
                $('#checkoutGuestCount').text(guestCount);
                $('#checkoutAllGuests').prop('checked', true);

                if (guestCount > 0) {
                    $('#checkoutGuestOption').show();
                } else {
                    $('#checkoutGuestOption').hide();
                }

                $('#checkoutModal').modal('show');
            });

            $('#confirmCheckoutBtn').on('click', function () {
                if (!checkoutVisitorId) return;
                var withGuests = $('#checkoutAllGuests').is(':checked') ? 1 : 0;
                var url = location.origin + '/visitors/checkout/' + checkoutVisitorId + '?with_guests=' + withGuests;

                $('#confirmCheckoutBtn').prop('disabled', true).text('Processing...');

                $.ajax({
                    url: url,
                    type: 'post',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: function (result) {
                        if (result.status === 201) {
                            $('#checkoutModal').modal('hide');
                            toastr.success('Successfully Checked Out', 'Success');
                            table.ajax.reload(null, false);
                        } else {
                            toastr.error('Server not responding', 'Error');
                        }
                    },
                    error: function () {
                        toastr.error('An error occurred. Please try again.', 'Error');
                    },
                    complete: function () {
                        checkoutVisitorId = null;
                        $('#confirmCheckoutBtn').prop('disabled', false).text('Checkout');
                    }
                });
            });

            function buildGuestRows(guests) {
                if (!guests || guests.length === 0) {
                    return '<tr><td colspan="8" class="text-center">No guests found</td></tr>';
                }

                var rows = '';
                $.each(guests, function (index, guest) {
                    var statusHtml = guest.is_checkout
                        ? '<span class="label label-success">Checked Out</span>'
                        : '<span class="label label-danger">Pending</span>';

                    var actionHtml = guest.is_checkout
                        ? '<span class="text-muted">-</span>'
                        : '<button type="button" class="btn btn-warning btn-xs waves-effect guest-checkout-btn" data-guest-id="' + guest.id + '"><i class="material-icons" style="font-size:14px;vertical-align:middle;">exit_to_app</i> Checkout</button>';

                    rows += '<tr>' +
                        '<td>' + (index + 1) + '</td>' +
                        '<td>' + (guest.visitor_card_id || '-') + '</td>' +
                        '<td>' + (guest.name || '-') + '</td>' +
                        '<td>' + (guest.organization || '-') + '</td>' +
                        '<td>' + (guest.phone || '-') + '</td>' +
                        '<td>' + statusHtml + '</td>' +
                        '<td>' + (guest.out_time || '-') + '</td>' +
                        '<td>' + actionHtml + '</td>' +
                        '</tr>';
                });

                return rows;
            }

            function loadGuestList(visitorId) {
                $('#guestListTableBody').html('<tr><td colspan="8" class="text-center">Loading...</td></tr>');
                $.ajax({
                    url: location.origin + '/visitors/guests/' + visitorId,
                    type: 'GET',
                    success: function (result) {
                        if (result.status === 200) {
                            $('#guestListTableBody').html(buildGuestRows(result.guests));
                        } else {
                            $('#guestListTableBody').html('<tr><td colspan="8" class="text-center text-danger">Failed to load guests</td></tr>');
                        }
                    },
                    error: function () {
                        $('#guestListTableBody').html('<tr><td colspan="8" class="text-center text-danger">Failed to load guests</td></tr>');
                    }
                });
            }

            $(document).on('click', '.guest-list-btn', function () {
                selectedVisitorIdForGuests = $(this).data('visitor-id');
                var visitorName = $(this).data('visitor-name') || '';
                $('#guestListVisitorName').text(visitorName);
                $('#guestListModal').modal('show');
                loadGuestList(selectedVisitorIdForGuests);
            });

            $(document).on('click', '#guestListTableBody .guest-checkout-btn', function () {
                var guestId = $(this).data('guest-id');
                var $btn = $(this);
                if (!guestId) return;

                $btn.prop('disabled', true).text('Processing...');
                $.ajax({
                    url: location.origin + '/visitors/guest-checkout/' + guestId,
                    type: 'POST',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: function (result) {
                        if (result.status === 201 || result.status === 200) {
                            toastr.success(result.message || 'Guest checked out', 'Success');
                            if (selectedVisitorIdForGuests) {
                                loadGuestList(selectedVisitorIdForGuests);
                            }
                            table.ajax.reload(null, false);
                        } else {
                            toastr.error('Server not responding', 'Error');
                        }
                    },
                    error: function () {
                        toastr.error('Failed to checkout guest', 'Error');
                    },
                    complete: function () {
                        $btn.prop('disabled', false).html('<i class="material-icons" style="font-size:14px;vertical-align:middle;">exit_to_app</i> Checkout');
                    }
                });
            });
        });
    </script>


@endpush
