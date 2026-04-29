<!DOCTYPE html>
<html lang="en">
<head>
    <?= view('V_Head') ?>
    <title>Page Moved</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg:      #0B1426;
            --surface: rgba(255,255,255,0.04);
            --border:  rgba(255,255,255,0.09);
            --accent:  #38BDF8;
            --green:   #34D399;
            --red:     #F87171;
            --muted:   rgba(255,255,255,0.45);
            --radius:  12px;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        html, body { height: 100%; }
        body {
            font-family: "Plus Jakarta Sans", sans-serif;
            background: var(--bg);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 1rem;
        }
        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: 0 4px 32px rgba(0,0,0,0.4);
            padding: 2.5rem 2rem;
            max-width: 480px;
            width: 100%;
            text-align: center;
        }
        .icon { margin-bottom: 1.25rem; color: var(--accent); }
        h1 {
            font-size: 1.5rem;
            font-weight: 800;
            letter-spacing: -.02em;
            margin-bottom: .5rem;
        }
        p { color: var(--muted); line-height: 1.6; margin-bottom: .75rem; font-size: .9rem; }
        .url {
            display: inline-block;
            border-radius: 6px;
            padding: .3rem .7rem;
            font-family: monospace;
            font-size: .9rem;
            word-break: break-all;
        }
        .url.old {
            background: rgba(248,113,113,.1);
            border: 1px solid rgba(248,113,113,.25);
            color: var(--red);
            text-decoration: line-through;
        }
        .url.new {
            background: rgba(52,211,153,.1);
            border: 1px solid rgba(52,211,153,.25);
            color: var(--green);
        }
        .arrow { color: var(--muted); margin: .75rem 0; font-size: 1.2rem; }
        .btn {
            display: inline-block;
            margin-top: 1.75rem;
            padding: .75rem 2.25rem;
            background: var(--accent);
            color: #0B1426;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 700;
            font-size: .95rem;
            transition: opacity .15s, transform .05s;
        }
        .btn:hover  { opacity: .85; }
        .btn:active { transform: translateY(1px); }
        .note { margin-top: 1rem; font-size: .8rem; color: var(--muted); }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:48px;height:48px">
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

        <p class="note">Please update your bookmark.</p>
    </div>
</body>
</html>
