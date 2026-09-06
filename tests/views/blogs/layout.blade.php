<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Blog Integration - {{ $title ?? 'Test' }}</title>
        @if(($theme ?? 'bootstrap-5') === 'bootstrap-5')
            <link
                href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
                rel="stylesheet"
                crossorigin="anonymous"
            >
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
            <script>window.bootstrap = bootstrap;</script>
        @endif
        <style>
            html { 
                scroll-behavior: auto !important; 
            }
            body { 
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; 
                margin: 0;
                line-height: 1.5;
                color: #333;
                background-color: #f4f7f6;
            }
            .mle-test-wrapper { 
                max-width: 1000px; 
                margin: 0 auto; 
                padding: 2rem; 
                background: #fff;
                box-shadow: 0 0 20px rgba(0,0,0,0.05);
            }
            .mle-test-container {
                padding-bottom: 10rem;
            }
            
            h1, h2, h3 { color: #222; margin-top: 0; }
            
            .mle-test-nav { margin-bottom: 2rem; display: flex; gap: 1rem; align-items: center; }
            
            .mle-test-table { width: 100%; border-collapse: collapse; margin-bottom: 2rem; }
            .mle-test-table th, .mle-test-table td { padding: 0.75rem 1rem; border-bottom: 1px solid #eee; text-align: left; }
            .mle-test-table th { background-color: #fafafa; font-weight: 600; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.05em; color: #666; }
            
            .mle-test-btn { 
                display: inline-block; 
                padding: 0.6rem 1.2rem; 
                border-radius: 6px; 
                text-decoration: none; 
                border: 1px solid #d1d5db; 
                background: #fff; 
                color: #374151; 
                cursor: pointer;
                font-size: 0.875rem;
                font-weight: 500;
                transition: all 0.2s;
            }
            .mle-test-btn:hover { background: #f9fafb; border-color: #9ca3af; }
            
            .mle-test-btn-primary { background: #2563eb; color: #fff; border-color: #2563eb; }
            .mle-test-btn-primary:hover { background: #1d4ed8; border-color: #1d4ed8; }
            
            .mle-test-btn-danger { background: #dc2626; color: #fff; border-color: #dc2626; }
            .mle-test-btn-danger:hover { background: #b91c1c; border-color: #b91c1c; }
            
            .mle-test-btn-info { background: #0891b2; color: #fff; border-color: #0891b2; }
            .mle-test-btn-info:hover { background: #0e7490; border-color: #0e7490; }
            
            .mle-test-btn-warning { background: #d97706; color: #fff; border-color: #d97706; }
            .mle-test-btn-warning:hover { background: #b45309; border-color: #b45309; }
            
            .mle-test-btn-sm { padding: 0.3rem 0.6rem; font-size: 0.75rem; }
            
            .mle-test-form-group { margin-bottom: 1.5rem; }
            .mle-test-label { display: block; margin-bottom: 0.5rem; font-weight: 600; font-size: 0.9rem; color: #4b5563; }
            .mle-test-input, .mle-test-textarea { 
                width: 100%; 
                padding: 0.6rem 0.8rem; 
                border: 1px solid #d1d5db; 
                border-radius: 6px; 
                box-sizing: border-box; 
                font-size: 1rem;
                font-family: inherit;
            }
            .mle-test-input:focus, .mle-test-textarea:focus {
                outline: none;
                border-color: #2563eb;
                box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
            }
            
            .mle-test-alert { padding: 1rem; border-radius: 6px; margin-bottom: 1.5rem; border: 1px solid transparent; }
            .mle-test-alert-success { background-color: #ecfdf5; color: #065f46; border-color: #a7f3d0; }
            
            .mle-test-section { margin-bottom: 3rem; border: 1px solid #e5e7eb; padding: 2rem; border-radius: 8px; background: #fafafa; }
            .mle-test-section h3 { margin-bottom: 1.5rem; border-bottom: 1px solid #e5e7eb; padding-bottom: 0.5rem; }
            
            .mle-test-grid { display: grid; grid-template-columns: 1fr; gap: 2rem; }
            @media (min-width: 1200px) { .mle-test-grid { grid-template-columns: 1fr 1fr; } }
            
            .mle-test-card { border: 1px solid #e5e7eb; padding: 2rem; border-radius: 12px; background: #fff; margin-bottom: 2rem; }
            
            .mle-test-display-content { border: 1px solid #eee; padding: 1.5rem; background: #fff; border-radius: 4px; line-height: 1.8; }
            
            .my-5 { margin-top: 3rem; margin-bottom: 3rem; }
            .mb-5 { margin-bottom: 3rem; }
            .mb-3 { margin-bottom: 1rem; }
            .d-inline { display: inline; }
        </style>
        <script class="mlbrgn-form-components-config" type="application/json">
            {
              "assetBasePath": "/vendor/mlbrgn/laravel-form-components"
            }
        </script>
    </head>
    <body class="bg-light">
        <div class="mle-test-wrapper mle-test-container">
            @if(session('success'))
                <div class="mle-test-alert mle-test-alert-success" id="flash-success">
                    {{ session('success') }}
                </div>
            @endif
        
            {{ $slot }}
        </div>
        @stack('scripts')
    </body>
</html>
