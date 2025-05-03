<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MUSTER</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        :root {
            --primary-color: #002147; /* Bootstrap primary color */
            --secondary-color: #00a651; /* Bootstrap secondary color */
        }
        .bg-primary {
            background-color: var(--primary-color) !important;
        }
        .text-primary {
            color: var(--primary-color) !important;
        }
        .text-success {
            color: var(--secondary-color) !important;
        }
        .btn-success {
            background-color: var(--secondary-color) !important;
            border-color: var(--secondary-color) !important;
        }
        body {
            font-family: 'Arial', sans-serif;
        }
        .hero {
            position: relative;
            color: white;
        }
        .hero h1 {
            font-size: 3rem;
            font-weight: bold;
        }
        .hero p {
            font-size: 1.5rem;
        }
        footer {
            background-color: #343a40;
        }
        footer a {
            color: #adb5bd;
        }
    </style>
</head>
<body>
    <!-- Top Bar -->
    <div class="bg-primary text-white py-1">
        <div class="container">
            <div class="row">
                <div class="col-md-6 d-flex align-items-center">
                    <span class="text-secondary"><i class="fas fa-envelope me-2"></i>info@muster.edu.eg</span>
                    <span class="ms-3 text-secondary"><i class="fas fa-phone me-2"></i>+202-38247455 – 38247456 – 38247457 – 16878</span>
                </div>
                <div class="col-md-6 text-end">
                    <a href="{{ route('loginForm') }}" class="btn btn-success btn-sm fw-bold ms-2">LOGIN</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Hero Section -->
    <section class="hero position-relative text-white" style="background: url('{{ asset('imgs/must-cover.png') }}') center/cover; height: 80vh;">
        <div class="position-absolute top-0 start-0 w-100 h-100 bg-dark opacity-25"></div>
        <div class="container h-100">
            <div class="row h-100 align-items-end">
                <div class="col-md-6 mb-5">
                    <div class="position-relative z-1">
                        <!-- Hero Content -->
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-primary text-white pt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-3 mb-4">
                    <h5>MUSTER</h5>
                    <p>The complete university experience app for Misr University for Science & Technology students.</p>
                    <div>
                        <a href="#" class="text-white me-2"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-white me-2"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-white me-2"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-white"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <h5>Features</h5>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-white-50 text-decoration-none">Academic Services</a></li>
                        <li><a href="#" class="text-white-50 text-decoration-none">Community</a></li>
                        <li><a href="#" class="text-white-50 text-decoration-none">University Services</a></li>
                        <li><a href="#" class="text-white-50 text-decoration-none">Documents & Payments</a></li>
                    </ul>
                </div>
                <div class="col-md-3 mb-4">
                    <h5>Resources</h5>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-white-50 text-decoration-none">Help & Support</a></li>
                        <li><a href="#" class="text-white-50 text-decoration-none">FAQ</a></li>
                        <li><a href="#" class="text-white-50 text-decoration-none">Privacy Policy</a></li>
                        <li><a href="#" class="text-white-50 text-decoration-none">Terms of Service</a></li>
                    </ul>
                </div>
                <div class="col-md-3 mb-4">
                    <h5>Contact</h5>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-envelope me-2 text-success"></i> muster@must.edu.eg</li>
                        <li><i class="fas fa-phone me-2 text-success"></i> +202-38247455</li>
                        <li><i class="fas fa-map-marker-alt me-2 text-success"></i> Misr University for Science & Technology, 6th of October City, Giza</li>
                    </ul>
                </div>
            </div>
            <hr class="border-white-50">
            <div class="row">
                <div class="col-md-6">
                    <p>&copy; 2023 MUSTER App. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-end">
                    <a href="#" class="text-white-50">Privacy</a>
                    <a href="#" class="text-white-50 ms-3">Terms</a>
                    <a href="#" class="text-white-50 ms-3">Cookies</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="script.js"></script>
</body>
</html>
