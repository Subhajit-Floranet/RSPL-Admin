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
            <h4 class="page-title">Contact Edit</h4>
            <div class="ml-auto text-right">
                <a href="" class="btn btn-outline-info">Back</a>
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
                <form action="" method="POST" class="form-horizontal" id="formadd" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <h4 class="card-title">Contact Edit</h4>
                        
                        <input type="text" name="formid" value="{{$result->id}}">

                        <div class="form-group m-t-20">
                            <label>Name</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{$result->name}}" required>
                        </div>

                        <div class="form-group m-t-20">
                            <label>Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{$result->email}}" required>
                        </div>

                        <div class="form-group m-t-20">
                            <label>Phone</label>
                            <input type="phone" class="form-control" id="phone" name="phone" value="{{$result->mobile}}" required>
                        </div>

                        <div class="form-group m-t-20">
                            <label>Contact Type</label>
                            <input type="text" class="form-control" id="contact_type" name="contact_type" value="{{$result->contact_type}}" required>
                        </div>

                        <div class="form-group m-t-20">
                            <label>Related Query</label>
                            <input type="text" class="form-control" id="related_query" name="related_query" value="{{$result->query_related}}" required>
                        </div>

                        @if($result->order_id)
                            <div class="form-group m-t-20">
                                <label>Order_ID</label>
                                <input type="text" class="form-control" id="order_id" name="order_id" value="{{$result->order_id}}" required>
                            </div>
                        @endif

                        @if($result->subject)
                            <div class="form-group m-t-20">
                                <label>Subject</label>
                                <input type="text" class="form-control" id="subject" name="subject" value="{{$result->subject}}" required>
                            </div>
                        @endif
                         
                        <div class="form-group m-t-20">
                            <label>Message</label><br>
                            <span><b>{{$result->name}}</b><br>{{$result2->message}}</span>
                        </div>

                        <div class="form-group m-t-20 admin">
                            <label>Admin Message:</label>
                            <textarea class="form-control" rows="5" id="comment" name="comment"></textarea>
                           <br><input type="submit" id="AdminComment" class="btn btn-primary">
                        </div>
                    <div class="border-top">
                        <div class="card-body">
                            <input class="btn btn-primary" type="submit" value="UPDATE">
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

    $(document).on('click', '#AdminComment', function(event){
        var cgnobj = this;
        var message = $("#comment").val();
        alert(message);
        die;
        

        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            async: true,
            type : "",
            url : "",
            data:  { catid : catid },
            success : function(response){
                response = JSON.parse(response);
                console.log(response);
                // if(response.status == 'success'){
                //     location.reload();
                // }

                $('#product_id').html(response.data);
                  
                $('.selectpicker').selectpicker('refresh');


            },
            error : function(){
            }
            
        });  
    })
</script>    

@endsection