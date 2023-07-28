@extends('layouts.admin.app')

@section('content')

{{-- <script type = "text/javascript" src = "https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.4.0/tinymce.min.js"></script>   --}}
{{-- <script src="{{ asset('admin/misc.js') }}"></script>
<script src="{{ asset('admin/tinymce.min.js') }}"></script> --}}

<script src="https://www.giftbasketworldwide.com/js/admin/misc.js"></script>
<script src="https://www.giftbasketworldwide.com/js/admin/tinymce.min.js"></script>

<div class="page-breadcrumb">
    <div class="row">
        <div class="col-12 d-flex no-block align-items-center">
            <h4 class="page-title">{{ strtoupper($websiteShortCode) }} Addon</h4>
            <div class="ml-auto text-right">
                <a href="{{ route('admin.'.$websiteShortCode.'.addon.list') }}" class="btn btn-outline-info">Back</a>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <!-- ============================================================== -->
    <!-- Start Page Content -->
    <!-- ============================================================== -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                @foreach (['danger', 'warning', 'success', 'info'] as $msg)
                    @if(Session::has('alert-' . $msg))
                        <h4 class="font-weight-light alert alert-{{ $msg }}">{{ Session::get('alert-' . $msg) }}</h4>
                    @endif
                @endforeach
                <form action="{{route('admin.'.$websiteShortCode.'.addon.add')}}" method="POST" class="form-horizontal" id="formadd" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <h4 class="card-title">Addon Add</h4>
                        
                        
                        <div class="form-group m-t-20">
                            <label>Image<span style="color:red;">*</span></label>
                            <input type="file" class="form-control" id="image" name="image" required>
                            <p>Please upload image exactly or larger than 100px X 100px for best view</p>
                        </div>

                        <div class="form-group m-t-20">
                            <label>Product Extra Title<span style="color:red;">*</span></label>
                            <input type="text" class="form-control" id="product_extra_title" name="product_extra_title" placeholder="Enter Product Extra Title" required>
                        </div>
                        
                        <div class="form-group m-t-20">
                            <label>Product Extra Price<span style="color:red;">*</span></label>
                            <input type="number" class="form-control" id="product_extra_price" name="product_extra_price" placeholder="Enter price" required>
                        </div>
                        
                    </div>
                    <div class="border-top">
                        <div class="card-body">
                            <input class="btn btn-primary" type="submit" value="CREATE">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

    

    $.validator.setDefaults({
            submitHandler: function(form) {
                form.submit();
            }
    });
    $(function() {
        // validate the comment form when it is submitted
        $("#formadd").validate({
            ignore: [],
            errorPlacement: function(label, element) {
                label.addClass('mt-2 text-danger');
                label.insertAfter(element);
            },
            highlight: function(element, errorClass) {
                $(element).parents('.form-group').addClass('has-danger')
                $(element).addClass('form-control-danger')
            }
          });
    });

    /*Tinymce editor*/
    if ($("#postcontent").length) {
        tinymce.init({
            selector: '#postcontent',
            height: 200,
            theme: 'modern',
            plugins: [
                'advlist autolink lists link image charmap print preview hr anchor pagebreak',
                'searchreplace wordcount visualblocks visualchars code fullscreen',
                'insertdatetime media nonbreaking save table contextmenu directionality',
                'emoticons template paste textcolor colorpicker textpattern imagetools codesample toc help'
            ],
            toolbar1: 'undo redo | insert | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image',
            toolbar2: 'print preview media | forecolor backcolor emoticons | codesample help',
            image_advtab: true,
            /*templates: [{
                    title: 'Test template 1',
                    content: 'Test 1'
                },
                {
                    title: 'Test template 2',
                    content: 'Test 2'
                }
            ],*/
            content_css: [],
            init_instance_callback: function (editor) {
			    editor.on('keydown', function (e) {
			      $('#content-error').hide();
			    });
			}
        });
    }


    $('#title').on('blur', function(){
        var title = $.trim($(this).val());
        if(title != ''){
            //if($.trim($('#slug').val()) == ''){
                $('#slug').val(title.replace(/ /g,"-").toLowerCase());
                $('#slug-error').hide();
            //}
        }
    });


</script>    

@endsection