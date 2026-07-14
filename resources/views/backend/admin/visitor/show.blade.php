@extends('layouts.backend.app')

@section('title', 'Admin | Visitors | Show')

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
            <a href="{{ route('visitors.index') }}" class="btn btn-primary waves-effect pull-right"
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
                            <span class=""> Information of <strong>{{ $visitor->name }}</strong></span>
                                @if($visitor->checkout != 1)
                                <button type="button" class="btn btn-danger waves-effect delete pull-right d-block" data-delete-id="{{$visitor->id}}" style="width: 100px;" title="Checkout Visitor" >
                                    <i class="material-icons">exit_to_app</i>
                                    Checkout
                                </button>
                                @endif
                        </h2>
                    </div>
                    <div class="body table-responsive">
                        <div class="show-image text-center">
                            <img src="{{ asset('images/visitors/' . $visitor->image) }}" alt="">
                        </div>
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <th>Visitor Name</th>
                                    <td colspan="3">{{ $visitor->name }}</td>
                                    <th>Factory Name</th>
                                    <td colspan="3">{{ $visitor->factory_name }}</td>
                                </tr>
                                <tr>
                                    <th>Phone</th>
                                    <td colspan="3">{{ $visitor->phone }}</td>
                                    <th>Email</th>
                                    <td colspan="3">{{ $visitor->email }}</td>
                                </tr>
                                <tr>
                                    <th>Whom to Meet</th>
                                    <td colspan="3">{{ $visitor->employee->name }}</td>
                                    <th>Department</th>
                                    <td colspan="3">{{ $visitor->employee->department->name }}</td>
                                </tr>
                                <tr>
                                    <th>Address</th>
                                    <td colspan="3">{{ $visitor->address }}</td>
                                    <th>Reason</th>
                                    <td colspan="3">{{ $visitor->reason }}</td>
                                </tr>
                                <tr>
                                    <th>Vistor Entring Time</th>
                                    <td colspan="3">{{ $visitor->in_time }}</td>
                                    <th>Out Time</th>
                                    <td colspan="3">{{ $visitor->out_time }}</td>
                                </tr>
                                <tr>
                                    <th>Outing Remark</th>
                                    <td>{{ $visitor->checkout == 1 ? 'Out' : 'Not' }}</td>
                            </tbody>
                        </table>

                        <h3 class="">Guest List</h3>
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>SL</th>
                                    <th>Card No</th>
                                    <th>Guest Name</th>
                                    <th>Organization</th>
                                    <th>Phone</th>
                                    <th>Email</th>
                                    <th>Address</th>
                                    <th>Checkout Status</th>
                                    <th>Out Time</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($visitor->guests as $key => $guest)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $guest->visitor_card_id }}</td>
                                    <td>{{ $guest->name }}</td>
                                    <td>{{ $guest->organization }}</td>
                                    <td>{{ $guest->phone }}</td>
                                    <td>{{ $guest->email }}</td>
                                    <td>{{ $guest->address }}</td>
                                    <td>
                                        @if($guest->is_checkout)
                                            <span class="label label-success">Checked Out</span>
                                        @else
                                            <span class="label label-danger">Pending</span>
                                        @endif
                                    </td>
                                    <td>{{ $guest->out_time ?? '-' }}</td>
                                    <td>
                                        @if(!$guest->is_checkout)
                                            <button type="button"
                                                class="btn btn-warning btn-xs waves-effect guest-checkout-btn"
                                                data-guest-id="{{ $guest->id }}"
                                                data-guest-name="{{ $guest->name }}">
                                                <i class="material-icons" style="font-size:14px;vertical-align:middle;">exit_to_app</i>
                                                Checkout
                                            </button>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="10" class="text-center">No guests</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Checkout Modal --}}
<div class="modal fade" id="delete-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Checkout Visitor</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to checkout <strong>{{ $visitor->name }}</strong>?</p>
                @if($visitor->guests->count() > 0)
                <div style="margin-top:10px;">
                    <div class="checkbox">
                        <input type="checkbox" id="checkoutAllGuests" class="filled-in" checked>
                        <label for="checkoutAllGuests">Also checkout all <strong>{{ $visitor->guests->count() }}</strong> guest(s)</label>
                    </div>
                </div>
                @endif
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" id="confirmCheckoutBtn">Checkout</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="guest-checkout-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Checkout Guest</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to checkout guest <strong id="guestCheckoutName"></strong>?</p>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-warning" id="confirmGuestCheckoutBtn">Checkout Guest</button>
            </div>
        </div>
    </div>
</div>

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

        $(".delete").on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            $('#delete-modal').modal('show');
        });

        $('#confirmCheckoutBtn').on('click', function () {
            var visitorId  = {{ $visitor->id }};
            var withGuests = $('#checkoutAllGuests').is(':checked') ? 1 : 0;
            var url = location.origin + '/visitors/checkout/' + visitorId + '?with_guests=' + withGuests;

            $('#confirmCheckoutBtn').prop('disabled', true).text('Processing...');

            $.ajax({
                url: url,
                type: 'post',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function (result) {
                    if (result.status === 201) {
                        $('#delete-modal').modal('hide');
                        toastr.success('Successfully Checked Out', 'Success');
                        setTimeout(function() { location.reload(); }, 1000);
                    } else {
                        toastr.error('Server not responding', 'Error');
                        $('#confirmCheckoutBtn').prop('disabled', false).text('Checkout');
                    }
                },
                error: function () {
                    toastr.error('An error occurred. Please try again.', 'Error');
                    $('#confirmCheckoutBtn').prop('disabled', false).text('Checkout');
                }
            });
        });

        var selectedGuestId = null;

        $('.guest-checkout-btn').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            selectedGuestId = $(this).data('guest-id');
            var guestName = $(this).data('guest-name') || '';
            $('#guestCheckoutName').text(guestName);
            $('#guest-checkout-modal').modal('show');
        });

        $('#confirmGuestCheckoutBtn').on('click', function () {
            if (!selectedGuestId) return;

            var url = location.origin + '/visitors/guest-checkout/' + selectedGuestId;
            $('#confirmGuestCheckoutBtn').prop('disabled', true).text('Processing...');

            $.ajax({
                url: url,
                type: 'post',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function (result) {
                    if (result.status === 201 || result.status === 200) {
                        $('#guest-checkout-modal').modal('hide');
                        toastr.success(result.message || 'Guest checked out', 'Success');
                        setTimeout(function() { location.reload(); }, 800);
                    } else {
                        toastr.error('Server not responding', 'Error');
                    }
                },
                error: function () {
                    toastr.error('An error occurred. Please try again.', 'Error');
                },
                complete: function () {
                    selectedGuestId = null;
                    $('#confirmGuestCheckoutBtn').prop('disabled', false).text('Checkout Guest');
                }
            });
        });
    </script>
@endpush
