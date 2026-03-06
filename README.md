<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="MarkURL - URL Shortener Profesional dengan Analytics. Gratis, Aman, dan Mudah Diinstall.">
    <title>MarkURL - URL Shortener Profesional dengan Analytics</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --secondary: #8b5cf6;
            --accent: #ec4899;
            --dark: #1e293b;
            --light: #f8fafc;
            --gray: #64748b;
            --success: #10b981;
            --gradient: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #ec4899 100%);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            line-height: 1.6;
            color: var(--dark);
            overflow-x: hidden;
        }

        /* Header/Navigation */
        nav {
            position: fixed;
            top: 0;
            width: 100%;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 20px rgba(0,0,0,0.1);
            z-index: 1000;
            padding: 1rem 0;
        }

        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 1.8rem;
            font-weight: 800;
            background: var(--gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .nav-links {
            display: flex;
            gap: 2rem;
            align-items: center;
        }

        .nav-links a {
            text-decoration: none;
            color: var(--dark);
            font-weight: 500;
            transition: color 0.3s;
        }

        .nav-links a:hover {
            color: var(--primary);
        }

        .btn-nav {
            background: var(--gradient);
            color: white !important;
            padding: 0.6rem 1.5rem;
            border-radius: 50px;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .btn-nav:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(99, 102, 241, 0.4);
        }

        /* Hero Section */
        .hero {
            margin-top: 80px;
            padding: 4rem 2rem;
            background: linear-gradient(135deg, #f0f4ff 0%, #e0e7ff 100%);
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 800px;
            height: 800px;
            background: radial-gradient(circle, rgba(99,102,241,0.1) 0%, transparent 70%);
            border-radius: 50%;
        }

        .hero-container {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: center;
            position: relative;
            z-index: 1;
        }

        .hero-content h1 {
            font-size: 3.5rem;
            font-weight: 800;
            line-height: 1.2;
            margin-bottom: 1.5rem;
            background: var(--gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-content p {
            font-size: 1.2rem;
            color: var(--gray);
            margin-bottom: 2rem;
        }

        .hero-buttons {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .btn-primary {
            background: var(--gradient);
            color: white;
            padding: 1rem 2.5rem;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1.1rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
            box-shadow: 0 10px 30px rgba(99, 102, 241, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(99, 102, 241, 0.4);
        }

        .btn-secondary {
            background: white;
            color: var(--primary);
            padding: 1rem 2.5rem;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1.1rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s;
            border: 2px solid var(--primary);
        }

        .btn-secondary:hover {
            background: var(--primary);
            color: white;
            transform: translateY(-3px);
        }

        .hero-image {
            position: relative;
        }

        .hero-image img {
            width: 100%;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.2);
        }

        .floating-card {
            position: absolute;
            background: white;
            padding: 1rem 1.5rem;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            animation: float 3s ease-in-out infinite;
        }

        .floating-card.card-1 {
            top: 10%;
            left: -10%;
            animation-delay: 0s;
        }

        .floating-card.card-2 {
            bottom: 10%;
            right: -5%;
            animation-delay: 1.5s;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        /* Stats Section */
        .stats {
            padding: 3rem 2rem;
            background: white;
        }

        .stats-container {
            max-width: 1000px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 2rem;
            text-align: center;
        }

        .stat-item h3 {
            font-size: 2.5rem;
            color: var(--primary);
            font-weight: 700;
        }

        .stat-item p {
            color: var(--gray);
            font-weight: 500;
        }

        /* Features Section */
        .features {
            padding: 6rem 2rem;
            background: var(--light);
        }

        .section-header {
            text-align: center;
            max-width: 700px;
            margin: 0 auto 4rem;
        }

        .section-header h2 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: var(--dark);
        }

        .section-header p {
            font-size: 1.1rem;
            color: var(--gray);
        }

        .features-grid {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 2rem;
        }

        .feature-card {
            background: white;
            padding: 2rem;
            border-radius: 20px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            transition: all 0.3s;
            border: 2px solid transparent;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.1);
            border-color: var(--primary);
        }

        .feature-icon {
            width: 60px;
            height: 60px;
            background: var(--gradient);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .feature-card h3 {
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 0.75rem;
            color: var(--dark);
        }

        .feature-card p {
            color: var(--gray);
            line-height: 1.6;
        }

        /* How It Works */
        .how-it-works {
            padding: 6rem 2rem;
            background: white;
        }

        .steps {
            max-width: 900px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }

        .step {
            display: flex;
            gap: 2rem;
            align-items: center;
            padding: 2rem;
            background: var(--light);
            border-radius: 20px;
            transition: all 0.3s;
        }

        .step:hover {
            transform: translateX(10px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .step-number {
            width: 60px;
            height: 60px;
            background: var(--gradient);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            font-weight: 700;
            flex-shrink: 0;
        }

        .step-content h3 {
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .step-content p {
            color: var(--gray);
        }

        /* Download Section */
        .download {
            padding: 6rem 2rem;
            background: var(--gradient);
            color: white;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .download::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }

        .download-container {
            max-width: 800px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
        }

        .download h2 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .download p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            opacity: 0.95;
        }

        .download-box {
            background: rgba(255,255,255,0.15);
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255,255,255,0.2);
            border-radius: 20px;
            padding: 2.5rem;
            margin-top: 2rem;
        }

        .download-btn {
            background: white;
            color: var(--primary);
            padding: 1.2rem 3rem;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 700;
            font-size: 1.2rem;
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            transition: all 0.3s;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }

        .download-btn:hover {
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 15px 40px rgba(0,0,0,0.3);
        }

        .download-info {
            margin-top: 1.5rem;
            font-size: 0.95rem;
            opacity: 0.9;
        }

        .download-info i {
            margin-right: 0.5rem;
        }

        /* Footer */
        footer {
            background: var(--dark);
            color: white;
            padding: 3rem 2rem 1.5rem;
            text-align: center;
        }

        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
        }

        .footer-links {
            display: flex;
            justify-content: center;
            gap: 2rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }

        .footer-links a {
            color: white;
            text-decoration: none;
            opacity: 0.8;
            transition: opacity 0.3s;
        }

        .footer-links a:hover {
            opacity: 1;
        }

        .social-links {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .social-links a {
            width: 45px;
            height: 45px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
            transition: all 0.3s;
        }

        .social-links a:hover {
            background: var(--primary);
            transform: translateY(-3px);
        }

        .copyright {
            padding-top: 2rem;
            border-top: 1px solid rgba(255,255,255,0.1);
            opacity: 0.8;
            font-size: 0.9rem;
        }

        /* Responsive */
        @media (max-width: 968px) {
            .hero-container {
                grid-template-columns: 1fr;
                text-align: center;
            }

            .hero-content h1 {
                font-size: 2.5rem;
            }

            .hero-image {
                order: -1;
            }

            .floating-card {
                display: none;
            }

            .stats-container {
                grid-template-columns: repeat(2, 1fr);
            }

            .features-grid {
                grid-template-columns: 1fr;
            }

            .nav-links {
                display: none;
            }
        }

        @media (max-width: 640px) {
            .hero-content h1 {
                font-size: 2rem;
            }

            .section-header h2 {
                font-size: 2rem;
            }

            .stats-container {
                grid-template-columns: 1fr;
            }

            .step {
                flex-direction: column;
                text-align: center;
            }
        }
    </style>
</head>
<body>

    <!-- Navigation -->
    <nav>
        <div class="nav-container">
            <div class="logo">
                <i class="fas fa-link"></i>
                MarkURL
            </div>
            <div class="nav-links">
                <a href="#features">Fitur</a>
                <a href="#how-it-works">Cara Kerja</a>
                <a href="create-database.html">Cara Create DB</a>
                <a href="#download" class="btn-nav">Download</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-container">
            <div class="hero-content">
                <h1>URL Shortener Profesional dengan Analytics</h1>
                <p>MarkURL adalah solusi pemendek URL yang aman, cepat, dan dilengkapi dengan fitur analytics lengkap. Gratis, open-source, dan mudah diinstall!</p>
                <div class="hero-buttons">
                    <a href="#download" class="btn-primary">
                        <i class="fas fa-download"></i>
                        Download Gratis
                    </a>
                    <a href="#features" class="btn-secondary">
                        <i class="fas fa-play-circle"></i>
                        Lihat Fitur
                    </a>
                </div>
            </div>
            <div class="hero-image">
    <!-- Browser Window Frame -->
    <div style="background: #fff; border-radius: 20px; box-shadow: 0 20px 60px rgba(99,102,241,0.3); overflow: hidden; border: 1px solid rgba(99,102,241,0.2);">
        <!-- Browser Header -->
        <div style="background: #f1f5f9; padding: 12px 16px; display: flex; align-items: center; gap: 8px; border-bottom: 1px solid #e2e8f0;">
            <div style="width: 12px; height: 12px; border-radius: 50%; background: #ef4444;"></div>
            <div style="width: 12px; height: 12px; border-radius: 50%; background: #f59e0b;"></div>
            <div style="width: 12px; height: 12px; border-radius: 50%; background: #10b981;"></div>
            <div style="flex: 1; background: #fff; border-radius: 6px; padding: 6px 12px; margin-left: 12px; font-size: 0.75rem; color: #64748b; display: flex; align-items: center; gap: 6px;">
                <i class="fas fa-lock" style="font-size: 0.65rem; color: #10b981;"></i>
                example.com
            </div>
        </div>
        
        <!-- App Interface Mockup -->
        <div style="padding: 2rem; background: linear-gradient(135deg, #f0f4ff 0%, #e0e7ff 100%);">
            <!-- Logo -->
            <div style="text-align: center; margin-bottom: 2rem;">
                <div style="display: inline-flex; align-items: center; gap: 0.5rem; background: linear-gradient(135deg, #6366f1, #8b5cf6); padding: 0.75rem 1.5rem; border-radius: 50px; color: white; font-weight: 700; font-size: 1.1rem;">
                    <i class="fas fa-link"></i>
                    MarkURL
                </div>
            </div>
            
            <!-- Main Form Card -->
            <div style="background: white; border-radius: 16px; padding: 2rem; box-shadow: 0 10px 40px rgba(0,0,0,0.1); max-width: 500px; margin: 0 auto;">
                <h3 style="color: #1e293b; font-weight: 600; margin-bottom: 1.5rem; text-align: center;">
                    <i class="fas fa-magic" style="color: #6366f1; margin-right: 0.5rem;"></i>
                    Pendekkan URL Sekarang
                </h3>
                
                <!-- URL Input -->
                <div style="margin-bottom: 1.25rem;">
                    <label style="display: block; color: #64748b; font-size: 0.85rem; font-weight: 500; margin-bottom: 0.5rem;">
                        <i class="fas fa-link" style="margin-right: 0.5rem; color: #6366f1;"></i>
                        URL Asli *
                    </label>
                    <div style="position: relative;">
                        <input type="text" value="https://contoh.com/halaman-yang-sangat-panjang-sekali" 
                               style="width: 100%; padding: 12px 16px; border: 2px solid #e2e8f0; border-radius: 12px; font-size: 0.85rem; background: #f8fafc; color: #1e293b;" readonly>
                    </div>
                </div>
                
                <!-- Custom Code Input -->
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; color: #64748b; font-size: 0.85rem; font-weight: 500; margin-bottom: 0.5rem;">
                        <i class="fas fa-tag" style="margin-right: 0.5rem; color: #6366f1;"></i>
                        Kode Custom (opsional)
                    </label>
                    <input type="text" value="promo2024" 
                           style="width: 100%; padding: 12px 16px; border: 2px solid #e2e8f0; border-radius: 12px; font-size: 0.85rem; background: #f8fafc; color: #1e293b;" readonly>
                </div>
                
                <!-- Submit Button -->
                <button style="width: 100%; background: linear-gradient(135deg, #6366f1, #8b5cf6); color: white; border: none; padding: 14px; border-radius: 12px; font-weight: 600; font-size: 0.95rem; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                    <i class="fas fa-bolt"></i>
                    Pendekkan URL
                </button>
            </div>
            
            <!-- Result Card (Floating) -->
            <div style="background: linear-gradient(135deg, #f0f9ff, #e0f2fe); border: 2px solid #bae6fd; border-radius: 12px; padding: 1rem 1.5rem; max-width: 500px; margin: 1.5rem auto 0; display: flex; align-items: center; justify-content: space-between; gap: 1rem;">
                <div style="flex: 1; min-width: 0;">
                    <div style="font-size: 0.75rem; color: #64748b; margin-bottom: 0.25rem;">URL Pendek Anda:</div>
                    <div style="font-weight: 600; color: #6366f1; font-size: 0.85rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                        <i class="fas fa-link" style="margin-right: 0.5rem;"></i>
                        example.com/short
                    </div>
                </div>
                <button style="background: white; color: #6366f1; border: 2px solid #6366f1; padding: 8px 16px; border-radius: 8px; font-weight: 600; font-size: 0.75rem; white-space: nowrap; cursor: pointer; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-copy"></i>
                    Salin
                </button>
            </div>
        </div>
    </div>
    
    <!-- Floating Analytics Card -->
    <div class="floating-card card-1" style="top: 15%; left: -5%; animation-delay: 0s;">
        <div style="display: flex; align-items: center; gap: 0.75rem; padding: 0.5rem;">
            <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #10b981, #059669); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: white;">
                <i class="fas fa-chart-line" style="font-size: 1.2rem;"></i>
            </div>
            <div>
                <div style="font-size: 0.7rem; color: #64748b;">Total Klik</div>
                <div style="font-weight: 700; color: #1e293b; font-size: 1.1rem;">12,458</div>
            </div>
        </div>
    </div>
    
    <!-- Floating Security Card -->
    <div class="floating-card card-2" style="bottom: 15%; right: -5%; animation-delay: 1.5s; top: auto; left: auto;">
        <div style="display: flex; align-items: center; gap: 0.75rem; padding: 0.5rem;">
        </div>
    </div>
    
    <!-- Floating URL Card -->
    <div class="floating-card" style="top: 50%; left: -8%; animation-delay: 0.75s; transform: translateY(-50%);">
        <div style="display: flex; align-items: center; gap: 0.75rem; padding: 0.5rem;">
            <div style="width: 35px; height: 35px; background: linear-gradient(135deg, #f59e0b, #d97706); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white;">
                <i class="fas fa-link" style="font-size: 1rem;"></i>
            </div>
            <div>
                <div style="font-size: 0.65rem; color: #64748b;">URL Aktif</div>
                <div style="font-weight: 600; color: #1e293b; font-size: 0.8rem;">248 URL</div>
            </div>
        </div>
    </div>
</div>
                <div class="floating-card card-1">
                    <i class="fas fa-shield-alt" style="color: var(--success); font-size: 1.5rem;"></i>
                    <div style="font-weight: 600; margin-top: 0.5rem;">100% Aman</div>
                </div>
                <div class="floating-card card-2">
                    <i class="fas fa-bolt" style="color: var(--accent); font-size: 1.5rem;"></i>
                    <div style="font-weight: 600; margin-top: 0.5rem;">Super Cepat</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats">
        <div class="stats-container">
            <div class="stat-item">
                <h3>∞</h3>
                <p>URL Tanpa Batas</p>
            </div>
            <div class="stat-item">
                <h3>100%</h3>
                <p>Gratis & Open Source</p>
            </div>
            <div class="stat-item">
                <h3>5 Menit</h3>
                <p>Instalasi Mudah</p>
            </div>
            <div class="stat-item">
                <h3>24/7</h3>
                <p>Uptime Tinggi</p>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features" id="features">
        <div class="section-header">
            <h2>Fitur Lengkap untuk Kebutuhan Anda</h2>
            <p>MarkURL dilengkapi dengan berbagai fitur profesional untuk membantu Anda mengelola URL dengan efisien</p>
        </div>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-user-shield"></i>
                </div>
                <h3>Admin Only Access</h3>
                <p>Sistem login aman dengan password hashing. Hanya admin yang bisa membuat dan mengelola URL pendek.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-chart-bar"></i>
                </div>
                <h3>Analytics Lengkap</h3>
                <p>Track jumlah klik per URL, waktu terakhir diakses, dan statistik real-time untuk setiap link.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-edit"></i>
                </div>
                <h3>Edit & Hapus URL</h3>
                <p>Ubah URL tujuan kapan saja tanpa perlu membuat link baru. Hapus URL yang tidak diperlukan dengan mudah.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-tag"></i>
                </div>
                <h3>Custom Short Code</h3>
                <p>Buat URL pendek dengan kode custom sesuai keinginan atau gunakan kode acak otomatis.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-layer-group"></i>
                </div>
                <h3>Pagination</h3>
                <p>Tampilan rapi dengan 5 URL per halaman. Navigasi mudah untuk mengelola ratusan URL.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-mobile-alt"></i>
                </div>
                <h3>Responsive Design</h3>
                <p>Tampilan optimal di semua perangkat - desktop, tablet, maupun smartphone.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-rocket"></i>
                </div>
                <h3>Auto Installer</h3>
                <p>Instalasi otomatis dalam 1 klik. Database dan tabel dibuat secara otomatis.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-lock"></i>
                </div>
                <h3>Keamanan Tinggi</h3>
                <p>Prepared statements untuk mencegah SQL injection. Password di-hash dengan bcrypt.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-paint-brush"></i>
                </div>
                <h3>UI/UX Modern</h3>
                <p>Desain clean dan modern dengan gradient yang menarik. Pengalaman pengguna yang intuitif.</p>
            </div>
        </div>
    </section>

    <!-- How It Works -->
    <section class="how-it-works" id="how-it-works">
        <div class="section-header">
            <h2>Cara Kerja Instalasi</h2>
            <p>Install MarkURL dalam 3 langkah mudah</p>
        </div>
        <div class="steps">
            <div class="step">
                <div class="step-number">1</div>
                <div class="step-content">
                    <h3>Download File Instalasi</h3>
                    <p>Download file install.php dan upload ke hosting Anda (public_html)</p>
                </div>
            </div>
            <div class="step">
                <div class="step-number">2</div>
                <div class="step-content">
                    <h3>Akses Installer</h3>
                    <p>Buka browser dan akses https://domain-anda.com/install.php, lalu isi konfigurasi database dan admin</p>
                </div>
            </div>
            <div class="step">
                <div class="step-number">3</div>
                <div class="step-content">
                    <h3>Klik Install & Selesai!</h3>
                    <p>Klik tombol install, sistem akan otomatis membuat database, tabel, dan semua file yang diperlukan</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Download Section -->
    <section class="download" id="download">
        <div class="download-container">
            <h2>Siap untuk Memulai?</h2>
            <p>Download MarkURL sekarang dan mulai pendekkan URL Anda dengan cara yang lebih profesional!</p>
            
            <div class="download-box">
                <a href="installer.php" class="download-btn" download>
                    <i class="fas fa-download"></i>
                    Download MarkURL v1.0
                </a>
                <div class="download-info">
                    <p><i class="fas fa-check-circle"></i> Gratis 100% - Tidak Ada Biaya Tersembunyi</p>
                    <p><i class="fas fa-check-circle"></i> File Size: ~50 KB</p>
                    <p><i class="fas fa-check-circle"></i> PHP 7.4+ Required</p>
                    <p><i class="fas fa-check-circle"></i> MySQL/MariaDB Database</p>
                </div>
            </div>

            <div style="margin-top: 3rem; padding: 1.5rem; background: rgba(255,255,255,0.1); border-radius: 15px;">
                <h4 style="margin-bottom: 1rem;"><i class="fas fa-cube mr-2"></i>Paket Instalasi Termasuk:</h4>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; text-align: left;">
                    <div><i class="fas fa-check" style="color: #10b981; margin-right: 0.5rem;"></i>Auto Installer (install.php)</div>
                    <div><i class="fas fa-check" style="color: #10b981; margin-right: 0.5rem;"></i>Main Application (index.php)</div>
                    <div><i class="fas fa-check" style="color: #10b981; margin-right: 0.5rem;"></i>Redirect Handler (r.php)</div>
                    <div><i class="fas fa-check" style="color: #10b981; margin-right: 0.5rem;"></i>Admin Login (login.php)</div>
                    <div><i class="fas fa-check" style="color: #10b981; margin-right: 0.5rem;"></i>Config Generator</div>
                    <div><i class="fas fa-check" style="color: #10b981; margin-right: 0.5rem;"></i>Security (.htaccess)</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="footer-content">
            <div class="logo" style="justify-content: center; margin-bottom: 1.5rem; font-size: 2rem;">
                <i class="fas fa-link"></i>
                MarkURL
            </div>
            <div class="footer-links">
                <a href="#features">Fitur</a>
                <a href="#how-it-works">Cara Kerja</a>
                <a href="#download">Download</a>
                <a href="#">Dokumentasi</a>
                <a href="#">GitHub</a>
            </div>
            <div class="social-links">
                <a href="#" title="GitHub"><i class="fab fa-github"></i></a>
                <a href="#" title="Twitter"><i class="fab fa-twitter"></i></a>
                <a href="#" title="Instagram"><i class="fab fa-instagram"></i></a>
                <a href="#" title="Telegram"><i class="fab fa-telegram"></i></a>
            </div>
            <div class="copyright">
                <p>&copy; 2024 MarkURL. Open Source under MIT License.</p>
                <p style="margin-top: 0.5rem; font-size: 0.85rem;">Made with <i class="fas fa-heart" style="color: #ec4899;"></i> From Longmark</p>
            </div>
        </div>
    </footer>

    <!-- Smooth Scroll Script -->
    <script>
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Add scroll effect to navbar
        window.addEventListener('scroll', function() {
            const nav = document.querySelector('nav');
            if (window.scrollY > 100) {
                nav.style.boxShadow = '0 2px 30px rgba(0,0,0,0.15)';
            } else {
                nav.style.boxShadow = '0 2px 20px rgba(0,0,0,0.1)';
            }
        });
    </script>
</body>
</html>
