<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #343a40;
            color: #ffffff;
        }
        .card {
            background-color: #495057;
            border: none;
        }
        .form-control {
            background-color: #6c757d;
            border: 1px solid #ced4da;
            color: #ffffff;
        }
        .form-control:focus {
            background-color: #6c757d;
            color: #ffffff;
        }
        .btn-primary {
            background-color: #007bff;
            border: none;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        .form-check-label {
            color: #ffffff;
        }
        a {
            color: #007bff;
        }
        a:hover {
            color: #0056b3;
        }

        .form {
            --bg-light: #efefef;
            --bg-dark: #707070;
            --clr: #58bc82;
            --clr-alpha: #9c9c9c60;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1rem;
            width: 100%;
            max-width: 300px;
            margin: auto;
        }

        .form .input-span {
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .form input[type="email"],
        .form input[type="password"] {
            border-radius: 0.5rem;
            padding: 1rem 0.75rem;
            width: 100%;
            border: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background-color: var(--clr-alpha);
            outline: 2px solid var(--bg-dark);
            color: #ffffff;
        }

        .form input[type="email"]:focus,
        .form input[type="password"]:focus {
            outline: 2px solid var(--clr);
        }

        .label {
            align-self: flex-start;
            color: var(--clr);
            font-weight: 600;
        }

        .form .submit {
            padding: 1rem 0.75rem;
            width: 100%;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            border-radius: 3rem;
            background-color: var(--bg-dark);
            color: var(--bg-light);
            border: none;
            cursor: pointer;
            transition: all 300ms;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .form .submit:hover {
            background-color: var(--clr);
            color: var(--bg-dark);
        }

        .span {
            text-decoration: none;
            color: var(--bg-dark);
        }

        .span a {
            color: var(--clr);
        }

    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header text-center">
                        <h4>Login</h4>
                    </div>
                    <div class="card-body">
                        <form class="form pb-5" method="POST" action="{{route('login')}}">
                            @csrf
                            @if(session('error'))
                                <div class="alert alert-danger">
                                    {{ session('error') }}
                                </div>
                            @endif

                            <span class="input-span">
                                <label for="email" class="label">Email</label>
                                <input type="email" name="email" id="email" value="{{old('email')}}" autofocus />
                            </span>
                            @error('email')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror

                            <span class="input-span">
                                <label for="password" class="label">Password</label>
                                <input type="password" name="password" id="password" />
                            </span>
                            @error('password')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror

                            <span class="span"><a href="#">Forgot password?</a></span>
                            <input class="submit" type="submit" value="Log in" />
                        </form>
                    </div>
             
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
