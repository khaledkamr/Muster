<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #343a40;
            color: #ffffff;
        }

        .card {
            background-color: #495057;
            border: 1px solid #58bc82;
        }

        .border-danger {
            border: 1px solid #df606c !important;
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

        .form input[type="email"] {
            border-radius: 0.5rem;
            padding: 1rem 0.75rem 1rem 2.5rem;
            width: 100%;
            border: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background-color: var(--clr-alpha);
            outline: 2px solid var(--bg-dark);
            color: #ffffff;
        }

        .form input[type="email"]:focus {
            outline: 2px solid var(--clr);
        }

        .form .group {
            position: relative;
        }

        .form .group i {
            position: absolute;
            left: 0.75rem;
            bottom: 35%;
            color: var(--clr);
        }

        .form input.input-error {
            outline: 2px solid #df606c !important;
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
            <div class="col-md-6 col-lg-4">
                <div class="card shadow {{ $errors->has('email') || session('error') ? 'border-danger' : '' }}">
                    <div class="text-center">
                        <img src="{{ asset('imgs/logo.png') }}" alt="Logo" class="img-fluid pt-5 pb-2"
                            style="max-width: 150px;">
                    </div>
                    <div class="card-body">
                        <form class="form pb-5" method="POST" action="{{ route('password.email') }}">
                            @csrf
                            @if (session('status'))
                                <div class="badge bg-success">
                                    {{ session('status') }}
                                </div>
                            @endif
                            @if (session('error'))
                                <div class="badge bg-danger">
                                    {{ session('error') }}
                                </div>
                            @endif

                            <span class="input-span">
                                <label for="email" class="label">Email</label>
                                <div class="group">
                                    <i class="fa-solid fa-envelope"></i>
                                    <input type="email" name="email" id="email" value="{{ old('email') }}"
                                        class="{{ $errors->has('email') ? 'input-error' : '' }}" autofocus />
                                </div>
                            </span>
                            @error('email')
                                <div class="badge bg-danger">{{ $message }}</div>
                            @enderror

                            <span class="span" style="text-decoration: none;">
                                <a href="{{ route('loginForm') }}" style="text-decoration: none;">Back to Login</a>
                            </span>
                            <input class="submit" type="submit" value="Send Reset Link" />
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
