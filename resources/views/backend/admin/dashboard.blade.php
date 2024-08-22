@extends('layouts.backend.app')

@section('title', 'Admin | Dashboard')

@push('css')
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
                        <div class="number count-to" data-from="0" data-to="{{ $today->count() }}" data-speed="15"
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
                            <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                                <thead>
                                    <tr>
                                        <th>SL</th>
                                        <th>Image</th>
                                        <th>Visitor Card</th>
                                        <th>Name</th>
                                        <th>Organization</th>
                                        <th>Phone</th>
                                        <th>In</th>
                                        <th>Out</th>
                                        <th>Whom</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($today->limit(10)->get() as $key => $data)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td class="text-center"> <img src="{{ asset( $data->image) }}" style="height:100px;" alt=""> </td>
                                            <td>{{ $data->visitor_card_id }}</td>
                                            <td>{{ $data->name }}</td>
                                            <td>{{ $data->factory_name }}</td>
                                            <td>{{ $data->phone }} </td>
                                            <td>{{ $data->in_time }} </td>
                                            <td>{{ $data->out_time }} </td>
                                            <td>{{ $data->employee->name }} </td>

                                            <td>
                                                <a href="{{ route('visitors.show', $data->id) }}"
                                                    class="btn btn-info waves-effect " title="View Visitor" style="width: 100px;">
                                                    <i class="material-icons">visibility</i>
                                                    View
                                                </a>


                                                @if ($data->checkout != 1)
                                                    <button type="button" class="btn btn-danger waves-effect delete"
                                                        data-delete-id="{{ $data->id }}" data-toggle="modal"
                                                        title="Checkout Visitor" style="width: 100px;" data-target="#delete-modal">
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
            <!-- #END# Task Info -->
        </div>



    </div>


    {{-- Checkout Modal --}}
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
    <!-- Jquery CountTo Plugin Js -->
    <script src="{{ asset('backend/plugins/jquery-countto/jquery.countTo.js') }}"></script>

    <!-- Morris Plugin Js -->
    <script src="{{ asset('backend/plugins/raphael/raphael.min.js') }}"></script>
    <script src="{{ asset('backend/plugins/morrisjs/morris.js') }}"></script>

    <!-- Sparkline Chart Plugin Js -->
    <script src="{{ asset('backend/plugins/jquery-sparkline/jquery.sparkline.js') }}"></script>

    <script src="{{ asset('backend/js/pages/index.js') }}"></script>

    <script>
        $( ".delete" ).click(function() {
            var data_id=$(this).data('delete-id');
            var url=location.origin+'/visitors/checkout/'+data_id;
            $('.delete_form').attr('action',url);

        });
    </script>
@endpush
