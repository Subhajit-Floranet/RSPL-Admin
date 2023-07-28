@extends('layouts.admin.app')

@section('content')

<link rel="stylesheet" type="text/css" href="{{ asset('admin/assets/extra-libs/multicheck/multicheck.css') }}">
<link href="{{ asset('admin/assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.css') }}" rel="stylesheet">

<div class="page-breadcrumb">
    <div class="row">
        <div class="col-12 d-flex no-block align-items-center">
            <h4 class="page-title">Order Management</h4>
            <div class="ml-auto text-right">
                <a href="{{url('admin/ordermanagement/')}}" class="btn btn-outline-info">Back</a>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    @foreach (['danger', 'warning', 'success', 'info'] as $msg)
                        @if(Session::has('alert-' . $msg))
                            <h4 class="font-weight-light alert alert-{{ $msg }}">{{ Session::get('alert-' . $msg) }}</h4>
                        @endif
                    @endforeach
                    <h5 class="card-title">OrderManagement</h5>
                    @if(count($result) > 0)
                    <div class="table-responsive">
                        <table id="zero_config" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th style="width: 5%"><b>No.</b></th>
                                    <th style="width: 25%"><b>Unique_Order_Id</b></th>
                                    <th style="width: 25%"><b>Payment method</b></th>
                                    <th style="width: 25%"><b>Payment Status</b></th>
                                    <th style="width: 25%"><b>Order Delivery Status</b></th>
                                    <th style="width: 25%"><b>Purchase Date</b></th>
                                    <th style="width: 20%"><b>Created Date</b></th>
                                    <th style="width: 9%"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $i=1; @endphp
                                @foreach($result as $key => $data)
                                <tr>
                                    <td>{{ $i + $key }}</td>
                                    <td>{{$data->unique_order_id}}</td>

                                    @if($data->payment_method == 1) <td>COD</td> 
                                    @elseif($data->payment_method == 2) <td>PayPal</td> 
                                    @elseif($data->payment_method == 3) <td>PayU</td>
                                    @else <td>Bank Transfer</td> 
                                    @endif
 
                                    @if($data->payment_status == 'C') <td>Completed</td>
                                    @endif

                                    @if($data->order_delivery_status == 'P') <td>Pending</td>
                                    @elseif($data->order_delivery_status == 'PC') <td>Processed</td>
                                    @elseif($data->order_delivery_status == 'CL') <td>Cancel</td>
                                    @elseif($data->order_delivery_status == 'H') <td>Hold</td>
                                    @elseif($data->order_delivery_status == 'D') <td>Delivered</td>
                                    @else <td>Shipped</td>
                                    @endif

                                    <td>{{date('d-m-Y', strtotime($data->purchase_date))}}</td>
                                    <td>{{date('d-m-Y', strtotime($data->created_at))}}</td>

                                    <td >
                                        <a href="">
                                            <i class="fa fa-eye" style="font-size:20px"></i>
                                        </a>
                                        <!-- <a href="">
                                            <i class="fas fa-trash-alt"></i>
                                        </a> -->
                                    </td>
                                    
                                </tr>
                                @endforeach
                            </tfoot>
                        </table>
                    </div>
                    @else
                    <div class="card">No Data Found...</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>



<script src="{{ asset('admin/assets/extra-libs/multicheck/datatable-checkbox-init.js') }}"></script>
<script src="{{ asset('admin/assets/extra-libs/multicheck/jquery.multicheck.js') }}"></script>
<script src="{{ asset('admin/assets/extra-libs/DataTables/datatables.min.js') }}"></script>
<script>
    /****************************************
     *       Basic Table                   *
     ****************************************/
    $('#zero_config').DataTable({
        "order": [[5, "desc"]] 
    });

    

    
</script>




@endsection