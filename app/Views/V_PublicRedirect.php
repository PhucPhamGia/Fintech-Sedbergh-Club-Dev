<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Moved</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: system-ui, -apple-system, sans-serif;
            background: #f4f5f7;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 1rem;
        }
        .card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(0,0,0,.08);
            padding: 2.5rem 2rem;
            max-width: 480px;
            width: 100%;
            text-align: center;
        }
        .icon { margin-bottom: 1rem; }
        h1 { font-size: 1.4rem; color: #1a1a2e; margin-bottom: .5rem; }
        p { color: #555; line-height: 1.6; margin-bottom: .75rem; }
        .url {
            display: inline-block;
            background: #f0f0f0;
            border-radius: 6px;
            padding: .25rem .6rem;
            font-family: monospace;
            font-size: .95rem;
            word-break: break-all;
        }
        .url.old { color: #c0392b; text-decoration: line-through; }
        .url.new { color: #27ae60; }
        .arrow { color: #aaa; margin: .5rem 0; font-size: 1.2rem; }
        .btn {
            display: inline-block;
            margin-top: 1.5rem;
            padding: .75rem 2rem;
            background: #2563eb;
            color: #fff;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1rem;
            transition: background .15s;
        }
        .btn:hover { background: #1d4ed8; }
        .note { margin-top: 1rem; font-size: .82rem; color: #999; }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:48px;height:48px;color:#2563eb">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.181 8.68a4.503 4.503 0 0 1 1.903 6.405m-9.768-2.782L3.56 14.06a4.5 4.5 0 0 0 6.364 6.365l3.129-3.129m5.614-5.615 1.757-1.757a4.5 4.5 0 0 0-6.364-6.365l-4.5 4.5c-.258.26-.479.541-.661.84m1.903 6.405a4.495 4.495 0 0 1-1.242-.88 4.483 4.483 0 0 1-1.062-1.683m6.587 2.345 5.907 5.907m-5.907-5.907L8.898 8.898M2.991 2.99 8.898 8.9" />
            </svg>
        </div>
        <h1>This URL has moved</h1>
        <p>You've landed on an outdated URL:</p>
        <span class="url old">/public<?= esc($clean_path) ?></span>
        <div class="arrow">↓</div>
        <p>The correct URL is:</p>
        <span class="url new"><?= esc($clean_path) ?></span>

        <br>
        <a class="btn" href="<?= esc($clean_path) ?>">Take me there</a>

        <p class="note">You'll be taken to the correct page. Please update your bookmark.</p>
    </div>
</body>
</html>
