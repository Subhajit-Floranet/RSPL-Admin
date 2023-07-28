@extends('layouts.admin.app')

@section('content')

<link rel="stylesheet" type="text/css" href="{{ asset('admin/assets/extra-libs/multicheck/multicheck.css') }}">
<link href="{{ asset('admin/assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.css') }}" rel="stylesheet">

<div class="page-breadcrumb">
    <div class="row">
        <div class="col-12 d-flex no-block align-items-center">
            <h4 class="page-title">Contact Management</h4>
            
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
                    <h5 class="card-title">Contact Management</h5>

                    <form action="{{url('/admin/contactmanagement/allcontact')}}" class="form-horizontal" id="formorder" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group m-t-20">
                                <label>Choose Contact Management</label>
                                <select name="contact" id="contact">
                                    <option value="1">ALL Sites</option>
                                    <option value="2">GBG</option>
                                    <option value="3">GBS</option>
                                </select>
                                <input class="btn btn-primary" type="submit" value="Submit">
                        </div>
                    </form>
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
        "order": [[7, "desc"]] 
    });

    

    
</script>




@endsection