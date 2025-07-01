<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Bumil App</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
            min-height: 100vh;
        }
        
        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .navbar h1 {
            font-size: 1.5rem;
        }
        
        .navbar-right {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .navbar-right span {
            font-weight: 500;
        }
        
        .logout-btn {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }
        
        .logout-btn:hover {
            background: rgba(255, 255, 255, 0.3);
        }
        
        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 2rem;
        }
        
        .welcome-card {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        
        .welcome-card h2 {
            color: #333;
            margin-bottom: 1rem;
        }
        
        .welcome-card p {
            color: #666;
            font-size: 1.1rem;
        }
        
        .success-message {
            background: #efe;
            color: #3c3;
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 2rem;
            border-left: 4px solid #3c3;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <h1>Bumil App</h1>
        <div class="navbar-right">
            <span>Halo, {{ auth()->user()->name }}!</span>
            <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                @csrf
                <button type="submit" class="logout-btn">Logout</button>
            </form>
        </div>
    </nav>

    <div class="container">
        @if (session('success'))
            <div class="success-message">
                {{ session('success') }}
            </div>
        @endif

        <div class="welcome-card">
            <h2>Selamat Datang di Dashboard</h2>
            <p>Anda berhasil masuk ke aplikasi Bumil. Ini adalah halaman dashboard yang dilindungi dan hanya bisa diakses oleh pengguna yang sudah login.</p>
        </div>
    </div>
</body>
</html>
