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
                                    <th>In</th>
                                    <th>Out</th>
                                    <th>To Whom</th>
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
                                    <td>{{ $data->factory_name }}</td>
                                    <td>{{ $data->phone }} </td>
                                    <td>{{ date('d-m-Y h:i a', $data->in_titme) }} </td>
                                    <td>{{ $data->out_time }} </td>
                                    <td>{{ $data->employee->name }} </td>

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
