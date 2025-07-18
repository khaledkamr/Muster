<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Forbidden</title>
    <style>
        :root {
            --primary: #002361;
            --secondary: #0A9442;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background-color: #1a1a1a;
            color: #ffffff;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            line-height: 1.6;
        }

        .container {
            text-align: center;
            padding: 2rem;
            max-width: 600px;
            margin: 0 auto;
        }

        h1 {
            font-size: 6rem;
            color: var(--primary);
            margin-bottom: 1rem;
        }

        h2 {
            font-size: 2rem;
            color: #e0e0e0;
            margin-bottom: 1rem;
        }

        p {
            font-size: 1.2rem;
            color: #b0b0b0;
            margin-bottom: 2rem;
        }

        a {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            background-color: var(--secondary);
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
            font-weight: 500;
            transition: background-color 0.3s ease;
        }

        a:hover {
            background-color: #087a34;
        }

        @media (max-width: 600px) {
            h1 {
                font-size: 4rem;
            }

            h2 {
                font-size: 1.5rem;
            }

            p {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>403</h1>
        <h2>Access Forbidden</h2>
        <p>Sorry, you are not authorized to access this resource. Please check your permissions or contact the administrator.</p>
        <a href="{{ url('/') }}">Return to Home</a>
    </div>
</body>
</html>