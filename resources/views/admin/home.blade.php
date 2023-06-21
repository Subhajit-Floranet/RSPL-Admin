<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <div class="container">
        <div class="row">
            <p>{{Auth::guard('admin')->user()->name}}</p>
            <p>
                <a href="{{route('admin.logout')}}" onclick="event.preventDefault();document.getElementById('logout-form').submit();">LOGOUT</a>
                <form id="logout-form" action="{{route('admin.logout')}}" method="POST">
                    @csrf
                </form>
            </p>
        </div>
    </div>
</body>
</html>