<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Import Data</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container d-flex flex-column align-items-center justify-content-center min-vh-100 py-4">

    @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>✔</strong> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>✖</strong> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

        <!-- Import Excel Data Card -->
        <div class="col-md-6">
            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h4 class="mb-0">Import Excel Data</h4>
                </div>
                <div class="card-body">
                    <form action="{{ url('voters/import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="input-group">
                            <input type="file" name="import_file" class="form-control" required>
                            <button type="submit" class="btn btn-primary">Import</button>
                        </div>
                    </form>
                    <small class="form-text text-muted mt-2 d-block">
                        Please upload an Excel file (.xlsx, .xls, .csv).
                    </small>
                </div>
            </div>
        </div>

        <!-- Manually Add Voter Card -->
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h4 class="mb-0">Manually Add a Voter</h4>
                </div>
                <div class="card-body">

                @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                    <form action="{{ url('voters/manual-add') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="voter_id" class="form-label">Voter ID</label>
                            <input type="text" name="voter_id" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="gender" class="form-label">Gender</label>
                            <select name="gender" class="form-select" required>
                                <option value="">Select gender</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="role" class="form-label">Role</label>
                            <select name="role" class="form-select" required>
                                <option value="">Select role</option>
                                <option value="Male">Staff</option>
                                <option value="Female">Student</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="programme" class="form-label">Programme</label>
                            <input type="text" name="programme" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="class" class="form-label">Class</label>
                            <input type="text" name="class" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-success">Add Voter</button>
                    </form>
                </div>
            </div>
        </div>

        
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>