<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donor Profile</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('caller/js/caller.js') }}"></script>
    <style>
        .profile-header {
            background: linear-gradient(135deg, #007bff, #6610f2);
            color: white;
            padding: 2rem 1rem;
            text-align: center;
        }

        .profile-img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            border: 5px solid white;
            object-fit: cover;
        }

        .section-title {
            margin-top: 2rem;
            margin-bottom: 1rem;
            font-size: 1.5rem;
            font-weight: bold;
            color: #495057;
        }

        .donation-history table {
            margin-top: 1rem;
        }

        .btn-call {
            background-color: #28a745;
            color: #fff;
        }

        .btn-call:hover {
            background-color: #218838;
        }
    </style>
</head>

<body>
    <header class="profile-header">
        <img src="https://placehold.co/150" alt="Donor Image" class="profile-img mb-3">
        <h2>Suleman Sarfaraz</h2>
        <p>Dedicated Blood Donor</p>
    </header>

    <div class="container mt-4">
        <!-- Personal Information -->
        <section class="personal-info">
            <h3 class="section-title">Personal Information</h3>
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Phone:</strong> <a class="caller" data-number="03072781033">0333-1203725</a>
                    </p>
                    <p><strong>Email:</strong> <a href="mailto:suleman@example.com"
                            class="text-decoration-none">suleman@example.com</a></p>
                </div>
                <div class="col-md-6">
                    <p><strong>City:</strong> Karachi, Pakistan</p>
                    <p><strong>Blood Group:</strong> B+</p>
                </div>
            </div>
        </section>

        <!-- Donation History -->
        <section class="donation-history">
            <h3 class="section-title">Donation History</h3>
            <table class="table table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Date</th>
                        <th>Location</th>
                        <th>Units Donated</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>2024-10-01</td>
                        <td>City Hospital</td>
                        <td>2</td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>2024-08-15</td>
                        <td>Red Cross Center</td>
                        <td>1</td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td>2024-05-20</td>
                        <td>Blood Bank Karachi</td>
                        <td>1</td>
                    </tr>
                </tbody>
            </table>
        </section>

        <!-- Call to Action -->
        <section class="mt-4 text-center">
            <a class="caller" data-number="03331203725"><i class="bi bi-telephone"></i> Call Now</a>
        </section>
    </div>

    <footer class="text-center py-4 mt-4 bg-light">
        <p>&copy; 2025 Donor Management System</p>
    </footer>

    <!-- Bootstrap Icons -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.js"></script>
</body>

</html>
