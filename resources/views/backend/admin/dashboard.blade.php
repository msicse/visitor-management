@extends('layouts.backend.app')

@section('title', 'Admin | Dashboard')

@push('css')

<!-- JQuery DataTable Css -->
<link href="{{ asset('backend/plugins/jquery-datatable/skin/bootstrap/css/dataTables.bootstrap.css') }}" rel="stylesheet">
<link href="{{ asset('backend/js/pages/tables/buttons.dataTables.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css"
        integrity="sha512-MV7K8+y+gLIBoVD59lQIYicR65iaqukzvf/nwasF0nqhPay5w/9lJmVM2hMDcnK1OnMGCdVK+iQrJ7lzPJQd1w=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        .orange {
            color: #FF9800 !important;
        }

        .tinfo {
            color: #00BCD4 !important;
        }

        .tdanger {
            color: #F44336 !important;
        }
    </style>
@endpush

@section('content')

    <div class="container-fluid">
        <div class="block-header">
            <h2>DASHBOARD</h2>
        </div>

        <div class="row clearfix">
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <div class="info-box bg-green hover-expand-effect">
                    <div class="icon">
                        <i class="material-icons">wc</i>
                    </div>
                    <div class="content">
                        <div class="text">Today's Visitors</div>
                        <div class="number count-to" data-from="0" data-to="{{ $todayCount }}" data-speed="15"
                            data-fresh-interval="20">643</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <div class="info-box bg-cyan hover-expand-effect">
                    <div class="icon">
                        <i class="material-icons">assessment</i>
                    </div>
                    <div class="content">
                        <div class="text">Yesterday Visitors</div>
                        <div class="number count-to" data-from="0" data-to="{{ $yesterday }}" data-speed="1000"
                            data-fresh-interval="20"></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <div class="info-box bg-brown hover-expand-effect">
                    <div class="icon">
                        <i class="material-icons">library_books</i>
                    </div>
                    <div class="content">
                        <div class="text">Last 7 Days Visitors</div>
                        <div class="number count-to" data-from="0" data-to="{{ $visitors7 }}" data-speed="1000"
                            data-fresh-interval="20"></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <div class="info-box bg-pink hover-expand-effect">
                    <div class="icon">
                        <i class="material-icons">assignment_late</i>
                    </div>
                    <div class="content">
                        <div class="text">Last 30 Days Visitors</div>
                        <div class="number count-to" data-from="0" data-to="{{ $visitors30 }}" data-speed="1000"
                            data-fresh-interval="20"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row clearfix">
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <div class="info-box bg-purple hover-expand-effect">
                    <div class="icon">
                        <i class="material-icons">add_shopping_cart</i>
                    </div>
                    <div class="content">
                        <div class="text">Total Visitors Till Date</div>
                        <div class="number count-to" data-from="0" data-to="{{ $total }}" data-speed="15"
                            data-fresh-interval="20"></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <div class="info-box bg-red hover-expand-effect">
                    <div class="icon">
                        <i class="material-icons">add_shopping_cart</i>
                    </div>
                    <div class="content">
                        <div class="text">Total Unchecked Visitors</div>
                        <div class="number count-to" data-from="0" data-to="{{ $uncheckout }}" data-speed="15"
                            data-fresh-interval="20"></div>
                    </div>
                </div>
            </div>

        </div>


        <div class="row clearfix">
            <!-- Task Info -->
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <div class="card">
                    <div class="header">
                        <h2>Today's Visitors List</h2>
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
            <!-- #END# Task Info -->
        </div>



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
                    <table class="table table-bordered table-striped" id="visitorsTable" >
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





    <!-- Jquery CountTo Plugin Js -->
    <script src="{{ asset('backend/plugins/jquery-countto/jquery.countTo.js') }}"></script>

    <!-- Morris Plugin Js -->
    <script src="{{ asset('backend/plugins/raphael/raphael.min.js') }}"></script>
    <script src="{{ asset('backend/plugins/morrisjs/morris.js') }}"></script>

    <!-- Sparkline Chart Plugin Js -->
    <script src="{{ asset('backend/plugins/jquery-sparkline/jquery.sparkline.js') }}"></script>
    <script src="{{ asset('backend/select2/select2.min.js') }}"></script>
    <script>
        $(document).ready(function () {

            // Select2 for filter dropdowns
            $('#filter_status, #filter_visitor_type, #filter_department, #filter_employee').select2({
                width: '100%',
                allowClear: true
            });


            let today = new Date().toISOString().split('T')[0];

            $('#date_from').val(today);
            $('#date_to').val(today);

            // Server-side DataTable
            var table = $('#visitorsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('visitors.data') }}',
                    type: 'GET',
                    data: function (d) {
                        d.date_from     = today; // $('#date_from').val();
                        d.date_to       = today; // $('#date_to').val();
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
