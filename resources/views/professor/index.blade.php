<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Professor Home Page</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #343a40;
            color: #ffffff;
        }
        .jumbotron {
            background-color: #495057;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="jumbotron text-center">
            <h1 class="display-4">Hello, Professor, {{$user->name}}!</h1>
            <p class="lead">Welcome to your home page.</p>
            <hr class="my-4">
            <p>Use the navigation bar to access different sections of the site.</p>
            <a class="btn btn-primary btn-lg" href="#" role="button">Learn more</a>
            <form action="{{route('logout')}}" method="POST">
                @csrf
                <button type="submit" class="btn btn-danger btn-lg mt-3">Logout</button>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>