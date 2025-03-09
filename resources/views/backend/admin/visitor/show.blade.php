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
                                <button type="button" class="btn btn-danger waves-effect delete pull-right d-block" data-delete-id="{{$visitor->id}}" style="width: 100px;" data-toggle="modal" title="Disable Visitor" data-target="#delete-modal" >
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
                                </tr>
                                @foreach($visitor->guests as $key => $guest)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $guest->visitor_card_id }}</td>
                                    <td>{{ $guest->name }}</td>
                                    <td>{{ $guest->organization }}</td>
                                    <td>{{ $guest->phone }}</td>
                                    <td>{{ $guest->email }}</td>
                                    <td>{{ $guest->address }}</td>
                                </tr>
                                @endforeach
                            </thead>
                        </table>
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

        $(".delete").click(function() {
            var data_id = $(this).data('delete-id');
            var url = location.origin + '/visitors/checkout/' + data_id;
            $('.delete_form').attr('action', url);

        });
    </script>
@endpush
