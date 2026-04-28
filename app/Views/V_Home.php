<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Homepage</title>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap');
    
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: "Plus Jakarta Sans", sans-serif;
        color: white;
        background-color: #0B1426;
    }
    
    #loading-screen {
        width: 100vw;
        height: 100vh;
        background: #0B1426;
        display: flex;
        justify-content: center;
        align-items: center;
    }
    #icon-wrapper {
        position: relative;
        width: 10vmin;
        height: 10vmin;
        overflow: visible;
    }
    #loading-icon, #trail-layer {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
    }

    /* ── Design tokens ─────────────────────────────────── */
    :root {
        --bg:       #0B1426;
        --surface:  rgba(255,255,255,0.04);
        --border:   rgba(255,255,255,0.09);
        --accent:   #38BDF8;
        --green:    #34D399;
        --red:      #F87171;
        --muted:    rgba(255,255,255,0.45);
        --radius:   12px;
    }

    /* ── Shared Classes ───────────────────────────────── */
    .container {
        max-width: 1160px;
        margin: 0 auto;
        padding: 0 24px;
    }
    .section-eyebrow {
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.18em;
        text-transform: uppercase;
        color: var(--accent);
        margin-bottom: 12px;
    }
    .section-title {
        font-size: clamp(1.6rem, 3vw, 2.4rem);
        font-weight: 700;
        line-height: 1.2;
        margin-bottom: 48px;
    }
    .section-header { text-align: center; }

    .btn-primary {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: var(--accent);
        color: #0B1426;
        font-weight: 700;
        font-size: 0.9rem;
        padding: 12px 24px;
        border-radius: 8px;
        text-decoration: none;
        transition: opacity .15s;
    }
    .btn-primary:hover { opacity: .85; }
    .btn-ghost {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: transparent;
        color: rgba(255,255,255,.75);
        font-weight: 600;
        font-size: 0.9rem;
        padding: 12px 24px;
        border-radius: 8px;
        border: 1px solid var(--border);
        text-decoration: none;
        transition: border-color .15s, color .15s;
    }
    .btn-ghost:hover { border-color: rgba(255,255,255,.35); color: #fff; }

    .up   { color: var(--green); }
    .down { color: var(--red);   }

    /* ── Navigation ────────────────────────────────────────────── */
    .nav {
        position: fixed;
        top: 20px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 100;
        width: calc(100% - 48px);
        max-width: 1140px;
        background: rgba(11,20,38,.88);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid var(--border);
        border-radius: 14px;
        box-shadow: 0 4px 32px rgba(0,0,0,0.4);
        transition: transform .35s cubic-bezier(.4,0,.2,1), opacity .35s ease;
    }
    .nav--hidden {
        transform: translateX(-50%) translateY(calc(-100% - 28px));
        opacity: 0;
        pointer-events: none;
    }
    .nav-inner {
        padding: 0 24px;
        height: 58px;
        display: flex;
        align-items: center;
        gap: 32px;
    }
    .nav-logo {
        display: flex;
        align-items: center;
        gap: 8px;
        font-weight: 800;
        font-size: 1.1rem;
        letter-spacing: -.02em;
        text-decoration: none;
        color: #fff;
        flex-shrink: 0;
    }
    .nav-logo svg { width: 22px; height: 22px; color: var(--accent); }
    .nav-links {
        display: flex;
        gap: 28px;
        margin-left: 8px;
    }
    .nav-links a {
        font-size: 0.875rem;
        font-weight: 500;
        color: var(--muted);
        text-decoration: none;
        transition: color .15s;
    }
    .nav-links a:hover { color: #fff; }
    .nav-cta { margin-left: auto; display: flex; gap: 10px; align-items: center; }
    .nav-cta .btn-ghost { padding: 8px 16px; }
    .nav-cta .btn-primary { padding: 8px 16px; }
    .btn-theme {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 34px;
        height: 34px;
        border-radius: 8px;
        border: 1px solid var(--border);
        background: transparent;
        color: var(--muted);
        cursor: pointer;
        flex-shrink: 0;
        transition: border-color .15s, color .15s;
    }
    .btn-theme:hover { border-color: rgba(255,255,255,.35); color: #fff; }
    .btn-theme svg { width: 16px; height: 16px; }
    .btn-theme .icon-sun { display: none; }
    html[data-theme="light"] .btn-theme .icon-moon { display: none; }
    html[data-theme="light"] .btn-theme .icon-sun  { display: block; }

    /* ── Light mode ────────────────────────────────────────────── */
    html[data-theme="light"] {
        --bg:      #f1f5f9;
        --surface: rgba(15,23,42,0.05);
        --border:  rgba(15,23,42,0.12);
        --muted:   rgba(15,23,42,0.5);
    }
    html[data-theme="light"] body {
        background-color: #f1f5f9;
        color: #0f172a;
    }
    html[data-theme="light"] .nav {
        background: rgba(241,245,249,0.92);
        box-shadow: 0 4px 24px rgba(0,0,0,0.08);
    }
    html[data-theme="light"] .btn-ghost {
        color: rgba(15,23,42,.65);
    }
    html[data-theme="light"] .btn-ghost:hover {
        border-color: rgba(0,0,0,0.3);
        color: #0f172a;
    }
    html[data-theme="light"] .btn-theme:hover {
        border-color: rgba(0,0,0,0.25);
        color: #0f172a;
    }
    html[data-theme="light"] .market-table thead {
        background: rgba(15,23,42,0.04);
    }
    html[data-theme="light"] .ticker-strip {
        background: rgba(15,23,42,0.03);
    }
    html[data-theme="light"] .nav-logo {
        color: #0f172a;
    }
    html[data-theme="light"] .nav-links a:hover {
        color: #0f172a;
    }
    html[data-theme="light"] .footer-logo {
        color: #0f172a;
    }
    html[data-theme="light"] .footer-col a {
        color: rgba(15,23,42,.55);
    }
    html[data-theme="light"] .footer-col a:hover {
        color: #0f172a;
    }

    /* ── Hero UI ───────────────────────────────────────────── */
    .hero {
        position: relative;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        justify-content: center;
        padding: 80px 0 0;
    }
    .hero-bg-wrap {
        position: absolute;
        inset: 0;
        overflow: hidden;
        pointer-events: none;
    }
    #hero-canvas {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        mask-image: radial-gradient(ellipse 80% 70% at 50% 40%, black, transparent);
    }
    .hero-bg-glow {
        position: absolute;
        top: -120px; left: 50%;
        transform: translateX(-50%);
        width: 700px; height: 500px;
        background: radial-gradient(ellipse, rgba(56,189,248,.12) 0%, transparent 70%);
        pointer-events: none;
    }
    .hero .container { position: relative; z-index: 1; text-align: center; }
    .hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 0.78rem;
        font-weight: 600;
        color: var(--muted);
        border: 1px solid var(--border);
        border-radius: 99px;
        padding: 5px 14px;
        margin-bottom: 28px;
        letter-spacing: .04em;
    }
    .hero-badge .dot {
        width: 6px; height: 6px;
        border-radius: 50%;
        background: var(--green);
        box-shadow: 0 0 6px var(--green);
        animation: pulse 2s ease-in-out infinite;
    }
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50%       { opacity: .4; }
    }
    .hero-title {
        font-size: clamp(2.4rem, 6vw, 4.5rem);
        font-weight: 800;
        line-height: 1.08;
        letter-spacing: -.03em;
        margin-bottom: 20px;
    }
    .hero-title .accent { color: var(--accent); }
    .hero-sub {
        font-size: clamp(1rem, 2vw, 1.15rem);
        color: var(--muted);
        max-width: 520px;
        margin: 0 auto 36px;
        line-height: 1.6;
    }
    .hero-actions { display: flex; gap: 12px; justify-content: center; }
    .hero-actions .btn-primary,
    .hero-actions .btn-ghost { font-size: 0.95rem; padding: 13px 28px; }

    /* Ticker strip */
    .ticker-strip {
        margin-top: 80px;
        border-top: 1px solid var(--border);
        border-bottom: 1px solid var(--border);
        background: rgba(255,255,255,.02);
        overflow: hidden;
        padding: 14px 0;
    }
    .ticker-inner {
        display: flex;
        gap: 0;
        width: max-content;
        animation: ticker 28s linear infinite;
    }
    @keyframes ticker {
        from { transform: translateX(0); }
        to   { transform: translateX(-50%); }
    }
    .ticker-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 0 36px;
        border-right: 1px solid var(--border);
        white-space: nowrap;
        font-size: 0.82rem;
    }
    .ticker-name { color: var(--muted); font-weight: 600; }
    .ticker-price { font-weight: 700; }
    .ticker-change { font-weight: 600; font-size: 0.78rem; }

    /* ── Achievements ───────────────────────────────────── */
    .achievements {
        padding: 64px 0;
        border-bottom: 1px solid var(--border);
    }
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1px;
        background: var(--border);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        overflow: hidden;
        position: relative;
    }
    .stat-card {
        background: var(--bg);
        padding: 36px 32px;
        text-align: center;
        transition: background .15s;
    }
    .stat-card:hover { background: var(--surface); }
    .stat-value {
        font-size: clamp(1.8rem, 3vw, 2.6rem);
        font-weight: 800;
        letter-spacing: -.03em;
        color: var(--accent);
        margin-bottom: 6px;
    }
    .stat-label {
        font-size: 0.82rem;
        color: var(--muted);
        font-weight: 500;
    }

    /* ── Features ───────────────────────────────────────── */
    .features {
        padding: 96px 0;
    }
    .features-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
    }
    .feature-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 32px;
        transition: border-color .2s, background .2s;
    }
    .feature-card:hover {
        border-color: rgba(56,189,248,.3);
        background: rgba(56,189,248,.04);
    }
    .feature-card--accent {
        border-color: rgba(56,189,248,.25);
        background: rgba(56,189,248,.06);
    }
    .feature-icon {
        margin-bottom: 16px;
    }
    .feature-icon svg {
        width: 28px;
        height: 28px;
        color: var(--accent);
        animation: float-icon 3s ease-in-out infinite;
    }
    .feature-card:nth-child(2) .feature-icon svg { animation-delay: -0.5s; }
    .feature-card:nth-child(3) .feature-icon svg { animation-delay: -1s;   }
    .feature-card:nth-child(4) .feature-icon svg { animation-delay: -1.5s; }
    .feature-card:nth-child(5) .feature-icon svg { animation-delay: -0.3s; }
    .feature-card:nth-child(6) .feature-icon svg { animation-delay: -0.8s; }
    @keyframes float-icon {
        0%, 100% { transform: translateY(0px);  }
        50%       { transform: translateY(-5px); }
    }
    .feature-card h3 {
        font-size: 1rem;
        font-weight: 700;
        margin-bottom: 10px;
        line-height: 1.3;
    }
    .feature-card p {
        font-size: 0.875rem;
        color: var(--muted);
        line-height: 1.65;
    }

    /* ── Market Data ────────────────────────────────────── */
    .market-data {
        padding: 96px 0;
        background: linear-gradient(180deg, transparent, rgba(56,189,248,.03), transparent);
    }
    .market-table-wrap {
        border: 1px solid var(--border);
        border-radius: var(--radius);
        overflow: hidden;
        margin-bottom: 32px;
    }
    .market-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.875rem;
    }
    .market-table thead {
        background: rgba(255,255,255,.03);
        border-bottom: 1px solid var(--border);
    }
    .market-table th {
        padding: 14px 20px;
        text-align: left;
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: .08em;
        text-transform: uppercase;
        color: var(--muted);
    }
    .market-table td {
        padding: 16px 20px;
        border-bottom: 1px solid var(--border);
        font-weight: 500;
    }
    .market-table tbody tr:last-child td { border-bottom: none; }
    .market-table tbody tr:hover td { background: var(--surface); }
    .coin-name {
        font-weight: 700;
        margin-right: 8px;
    }
    .coin-full {
        font-size: 0.78rem;
        color: var(--muted);
    }
    .market-cta { text-align: center; }

    /* ── FAQ ────────────────────────────────────────────── */
    .faq {
        padding: 96px 0;
        border-top: 1px solid var(--border);
    }
    .faq-list {
        max-width: 720px;
        margin: 0 auto;
        display: flex;
        flex-direction: column;
        gap: 2px;
    }
    .faq-item {
        border: 1px solid var(--border);
        border-radius: 8px;
        overflow: hidden;
        transition: border-color .15s;
    }
    .faq-item.open { border-color: rgba(56,189,248,.3); }
    .faq-q {
        width: 100%;
        background: none;
        border: none;
        color: inherit;
        font-family: inherit;
        padding: 20px 24px;
        font-weight: 600;
        font-size: 0.95rem;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
        user-select: none;
        transition: background .15s;
        text-align: left;
    }
    .faq-q:hover { background: var(--surface); }
    .faq-icon {
        font-size: 1.3rem;
        font-weight: 300;
        color: var(--muted);
        transition: transform .25s ease, color .25s ease;
        flex-shrink: 0;
        margin-left: 16px;
    }
    .faq-item.open .faq-icon {
        transform: rotate(45deg);
        color: var(--accent);
    }
    .faq-body {
        display: grid;
        grid-template-rows: 0fr;
        transition: grid-template-rows 0.3s ease;
    }
    .faq-item.open .faq-body {
        grid-template-rows: 1fr;
    }
    .faq-body > p {
        overflow: hidden;
        min-height: 0;
        padding: 0 24px;
        font-size: 0.875rem;
        color: var(--muted);
        line-height: 1.7;
        transition: padding 0.3s ease;
    }
    .faq-item.open .faq-body > p {
        padding: 0 24px 20px;
    }

    /* ── Footer ─────────────────────────────────────────── */
    .footer {
        border-top: 1px solid var(--border);
        padding: 64px 0 32px;
    }
    .footer-top {
        display: flex;
        gap: 64px;
        margin-bottom: 48px;
    }
    .footer-brand { max-width: 260px; }
    .footer-logo {
        display: block;
        font-size: 1.2rem;
        font-weight: 800;
        letter-spacing: -.02em;
        margin-bottom: 12px;
        color: #fff;
    }
    .footer-brand p {
        font-size: 0.82rem;
        color: var(--muted);
        line-height: 1.6;
    }
    .footer-links {
        display: flex;
        gap: 48px;
        margin-left: auto;
    }
    .footer-col { display: flex; flex-direction: column; gap: 10px; }
    .footer-col h4 {
        font-size: 0.78rem;
        font-weight: 700;
        letter-spacing: .1em;
        text-transform: uppercase;
        color: var(--muted);
        margin-bottom: 4px;
    }
    .footer-col a {
        font-size: 0.875rem;
        color: rgba(255,255,255,.6);
        text-decoration: none;
        transition: color .15s;
    }
    .footer-col a:hover { color: #fff; }
    .footer-bottom {
        border-top: 1px solid var(--border);
        padding-top: 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.78rem;
        color: var(--muted);
    }
    .footer-bottom a { color: var(--accent); text-decoration: none; }
    .footer-bottom a:hover { text-decoration: underline; }

    /* ── Responsive ─────────────────────────────────────── */
    @media (max-width: 768px) {
        .stats-grid       { grid-template-columns: repeat(2, 1fr); }
        .features-grid    { grid-template-columns: 1fr; }
        .nav-links        { display: none; }
        .footer-top       { flex-direction: column; gap: 32px; }
        .footer-links     { margin-left: 0; }
        .footer-bottom    { flex-direction: column; gap: 8px; text-align: center; }
    }
  </style>
</head>
<body>
    <div id="loading-screen">
        <div id="icon-wrapper">
            <svg id="trail-layer" viewBox="0 0 100 100" overflow="visible"></svg>
            <svg id="loading-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M14.121 7.629A3 3 0 0 0 9.017 9.43c-.023.212-.002.425.028.636l.506 3.541a4.5 4.5 0 0 1-.43 2.65L9 16.5l1.539-.513a2.25 2.25 0 0 1 1.422 0l.655.218a2.25 2.25 0 0 0 1.718-.122L15 15.75M8.25 12H12m9 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
        </div>
    </div>
    <div id="content" style="display: none;">

        <!-- Nav -->
        <nav class="nav">
            <div class="nav-inner">
                <a href="/" class="nav-logo">
                    <!-- Logo (Hero Icon's Euro symbol) -->
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.121 7.629A3 3 0 0 0 9.017 9.43c-.023.212-.002.425.028.636l.506 3.541a4.5 4.5 0 0 1-.43 2.65L9 16.5l1.539-.513a2.25 2.25 0 0 1 1.422 0l.655.218a2.25 2.25 0 0 0 1.718-.122L15 15.75" />
                    </svg>
                    gambling and gambling
                </a>
                <div class="nav-links">
                    <a href="#features">Features</a>
                    <a href="#market">Market</a>
                    <a href="#faq">FAQ</a>
                </div>
                <div class="nav-cta">
                    <a href="/login" class="btn-ghost">Log in</a>
                    <a href="/register" class="btn-primary">Get Started</a>
                    <button id="theme-toggle" class="btn-theme" aria-label="Toggle theme">
                        <svg class="icon-moon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z" />
                        </svg>
                        <svg class="icon-sun" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z" />
                        </svg>
                    </button>
                </div>
            </div>
        </nav>

        <!-- Hero / CTA -->
        <section class="hero" id="hero">
            <div class="hero-bg-wrap">
                <canvas id="hero-canvas"></canvas>
                <div class="hero-bg-glow"></div>
            </div>
            <div class="container">
                <div class="hero-badge">
                    <span class="dot"></span>
                    Live market data &bull; BTC &bull; ETH &bull; SOL &bull; BNB
                </div>
                <h1 class="hero-title">
                    Analyze Crypto.<br>
                    <span class="accent">Trade Smarter.</span>
                </h1>
                <p class="hero-sub">
                    If you like gambling, you better join.
                </p>
                <div class="hero-actions">
                    <a href="/register" class="btn-primary">Start for Free &rarr;</a>
                    <a href="/dashboard" class="btn-ghost">View Dashboard</a>
                </div>
            </div>
            <!-- Ticker strip -->
            <div class="ticker-strip">
                <div class="ticker-inner">
                    <span class="ticker-item"><span class="ticker-name">BTC/USDT</span><span class="ticker-price">$67,420</span><span class="ticker-change up">+2.41%</span></span>
                    <span class="ticker-item"><span class="ticker-name">ETH/USDT</span><span class="ticker-price">$3,511</span><span class="ticker-change up">+1.78%</span></span>
                    <span class="ticker-item"><span class="ticker-name">SOL/USDT</span><span class="ticker-price">$142.50</span><span class="ticker-change down">-0.92%</span></span>
                    <span class="ticker-item"><span class="ticker-name">BNB/USDT</span><span class="ticker-price">$589.00</span><span class="ticker-change up">+3.12%</span></span>
                    <!-- Duplicate set for seamless loop -->
                    <span class="ticker-item"><span class="ticker-name">BTC/USDT</span><span class="ticker-price">$67,420</span><span class="ticker-change up">+2.41%</span></span>
                    <span class="ticker-item"><span class="ticker-name">ETH/USDT</span><span class="ticker-price">$3,511</span><span class="ticker-change up">+1.78%</span></span>
                    <span class="ticker-item"><span class="ticker-name">SOL/USDT</span><span class="ticker-price">$142.50</span><span class="ticker-change down">-0.92%</span></span>
                    <span class="ticker-item"><span class="ticker-name">BNB/USDT</span><span class="ticker-price">$589.00</span><span class="ticker-change up">+3.12%</span></span>
                </div>
            </div>
        </section>

        <!-- Achievements / Awards -->
        <section class="achievements" id="achievements">
            <div class="container">
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-value">$10Qn+</div>
                        <div class="stat-label">Total Volume Tracked</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value">500T+</div>
                        <div class="stat-label">Registered Users</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value">99.9%</div>
                        <div class="stat-label">Platform Uptime</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value">6.7 ★</div>
                        <div class="stat-label">Average Rating</div>
                    </div>
                </div>
                </div>
            </div>
        </section>

        <!-- Key Features -->
        <section class="features" id="features">
            <div class="container">
                <div class="section-header">
                    <p class="section-eyebrow">Platform Features</p>
                    <h2 class="section-title">Everything You Need to Trade with Confidence</h2>
                </div>
                <div class="features-grid">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" />
                            </svg>
                        </div>
                        <h3>Real-Time Candlestick Charts</h3>
                        <p>interactive chart blah blah</p>
                    </div>
                    <div class="feature-card feature-card--accent">
                        <div class="feature-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18 9 11.25l4.306 4.306a11.95 11.95 0 0 1 5.814-5.518l2.74-1.22m0 0-5.94-2.281m5.94 2.28-2.28 5.941" />
                            </svg>
                        </div>
                        <h3>Moving Average Indicators</h3>
                        <p>automated ma20 &amp; ma50 calculation</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m3.75 13.5 10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75Z" />
                            </svg>
                        </div>
                        <h3>Binance Data Pipeline</h3>
                        <p>Direct integration with the Binance kline API. Import historical and daily OHLCV data with deduplication built in.</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                            </svg>
                        </div>
                        <h3>Secure Authentication</h3>
                        <p>Role-based access control with remember-me sessions, email verification, and login throttling.</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" />
                            </svg>
                        </div>
                        <h3>Multi-Asset Support</h3>
                        <p>Analyze BTC, ETH, SOL, and BNB side by side from a single unified dashboard.</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 3v1.5M4.5 8.25H3m18 0h-1.5M4.5 12H3m18 0h-1.5m-15 3.75H3m18 0h-1.5M8.25 19.5V21M12 3v1.5m0 15V21m3.75-18v1.5m0 15V21m-9-1.5h10.5a2.25 2.25 0 0 0 2.25-2.25V6.75a2.25 2.25 0 0 0-2.25-2.25H6.75A2.25 2.25 0 0 0 4.5 6.75v10.5a2.25 2.25 0 0 0 2.25 2.25Zm.75-12h9v9h-9v-9Z" />
                            </svg>
                        </div>
                        <h3>Automated Engine <span style="font-size:.7rem;color:var(--muted)">Coming Soon</span></h3>
                        <p>A fully automated trading engine that executes on technical signals — actively in development.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Market Data (small section) -->
        <section class="market-data" id="market">
            <div class="container">
                <div class="section-header">
                    <p class="section-eyebrow">Live Snapshot</p>
                    <h2 class="section-title">Market Overview</h2>
                </div>
                <div class="market-table-wrap">
                    <table class="market-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Asset</th>
                                <th>Price (USDT)</th>
                                <th>24h Change</th>
                                <th>MA20</th>
                                <th>MA50</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td><span class="coin-name">BTC</span><span class="coin-full">Bitcoin</span></td>
                                <td>$67,420.00</td>
                                <td class="up">+2.41%</td>
                                <td>$65,210</td>
                                <td>$61,430</td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td><span class="coin-name">ETH</span><span class="coin-full">Ethereum</span></td>
                                <td>$3,511.20</td>
                                <td class="up">+1.78%</td>
                                <td>$3,380</td>
                                <td>$3,120</td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td><span class="coin-name">SOL</span><span class="coin-full">Solana</span></td>
                                <td>$142.50</td>
                                <td class="down">-0.92%</td>
                                <td>$148</td>
                                <td>$135</td>
                            </tr>
                            <tr>
                                <td>4</td>
                                <td><span class="coin-name">BNB</span><span class="coin-full">BNB</span></td>
                                <td>$589.00</td>
                                <td class="up">+3.12%</td>
                                <td>$571</td>
                                <td>$545</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="market-cta">
                    <a href="/dashboard" class="btn-primary">View Full Dashboard &rarr;</a>
                </div>
            </div>
        </section>

        <!-- FAQ -->
        <section class="faq" id="faq">
            <div class="container">
                <div class="section-header">
                    <p class="section-eyebrow">FAQ</p>
                    <h2 class="section-title">Common Questions</h2>
                </div>
                <div class="faq-list">
                    <div class="faq-item">
                        <button class="faq-q">Where does the market data come from?<span class="faq-icon">+</span></button>
                        <div class="faq-body"><p>All OHLCV data is sourced directly from the Binance API (api.binance.com/api/v3/klines) and stored in our MySQL database. Data is refreshed daily via an automated import pipeline.</p></div>
                    </div>
                    <div class="faq-item">
                        <button class="faq-q">Which coins are supported?<span class="faq-icon">+</span></button>
                        <div class="faq-body"><p>Currently we support BTCUSDT, ETHUSDT, SOLUSDT, and BNBUSDT. Additional trading pairs will be added as the platform grows.</p></div>
                    </div>
                    <div class="faq-item">
                        <button class="faq-q">How are MA20 and MA50 calculated?<span class="faq-icon">+</span></button>
                        <div class="faq-body"><p>Moving averages are computed server-side using a sliding window over close prices sorted oldest-first. They are batch-updated after each daily import run.</p></div>
                    </div>
                    <div class="faq-item">
                        <button class="faq-q">Is there an automated trading feature?<span class="faq-icon">+</span></button>
                        <div class="faq-body"><p>An automated trading engine is in active development. The current platform focuses on data ingestion, indicator calculation, and interactive visualization.</p></div>
                    </div>
                    <div class="faq-item">
                        <button class="faq-q">How do I get access to the dashboard?<span class="faq-icon">+</span></button>
                        <div class="faq-body"><p>Register for a free account and log in. The dashboard requires authentication. Data import features require an Admin role assigned by a site administrator.</p></div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="footer">
            <div class="container">
                <div class="footer-top">
                    <div class="footer-brand">
                        <span class="footer-logo">GiaPhuc</span>
                        <p>A cryptocurrency data analysis platform powered by Binance API. Built for traders who think in data.</p>
                    </div>
                    <div class="footer-links">
                        <div class="footer-col">
                            <h4>Platform</h4>
                            <a href="/dashboard">Dashboard</a>
                            <a href="/database">Database</a>
                            <a href="/importbinance">Import Data</a>
                        </div>
                        <div class="footer-col">
                            <h4>Account</h4>
                            <a href="/login">Log In</a>
                            <a href="/register">Register</a>
                        </div>
                    </div>
                </div>
                <div class="footer-bottom">
                    <p>&copy; 2026 GiaPhuc. All rights reserved.</p>
                    <p>Live: <a href="https://giaphuc.thuytrieu.vn/" target="_blank">giaphuc.thuytrieu.vn</a></p>
                </div>
            </div>
        </footer>

    </div>


    <script src="https://cdn.jsdelivr.net/gh/studio-freight/lenis/bundled/lenis.min.js"></script>
    <script>
    let angle = 0;
    let lastSpawnAngle = 0;
    const el = document.getElementById("loading-icon");
    const trailLayer = document.getElementById("trail-layer");
    const trails = []; // active trail rects being faded out

    setInterval(() => {
        angle += 1;
        el.style.transform = `rotate(${angle}deg)`;

        // spawn a new trail rect every 15 degrees
        if (angle - lastSpawnAngle >= 15) {
            lastSpawnAngle = angle;
            const rad = (angle - 90) * Math.PI / 180; // offset -90 so 0deg points up
            const cos = Math.cos(rad), sin = Math.sin(rad);
            const rect = document.createElementNS('http://www.w3.org/2000/svg', 'rect');
            const w = 6, h = 14;
            // position rect at radius 57 from center (50,50), rotated to face outward
            rect.setAttribute('x', 50 + cos * 57 - w / 2);
            rect.setAttribute('y', 50 + sin * 57 - h / 2);
            rect.setAttribute('width', w);
            rect.setAttribute('height', h);
            rect.setAttribute('rx', 3);
            rect.setAttribute('fill', 'white');
            rect.setAttribute('transform', `rotate(${angle}, ${50 + cos * 57}, ${50 + sin * 57})`);
            trailLayer.appendChild(rect);
            trails.push({ el: rect, opacity: 1 });
        }

        // fade out all active trails, remove when fully transparent
        for (let i = trails.length - 1; i >= 0; i--) {
            trails[i].opacity -= 0.02;
            if (trails[i].opacity <= 0) {
                trails[i].el.remove();
                trails.splice(i, 1);
            } else {
                trails[i].el.setAttribute('opacity', trails[i].opacity);
            }
        }
    }, 16);
    
    window.addEventListener('load', () => {
        document.getElementById('loading-screen').style.display = 'none';
        document.getElementById('content').style.display = 'block';

        const lenis = new Lenis();
        function raf(time) {
            lenis.raf(time);
            requestAnimationFrame(raf);
        }
        requestAnimationFrame(raf);

        const heroGlow    = document.querySelector('.hero-bg-glow');
        const heroCanvas  = document.getElementById('hero-canvas');

        // ── Particle network ──────────────────────────────────────
        (function initNetwork() {
            const canvas = heroCanvas;
            const ctx = canvas.getContext('2d');
            const COUNT = 75;
            const MAX_DIST = 150;
            const SPEED = 0.4;
            let nodes = [];

            function resize() {
                canvas.width  = canvas.offsetWidth;
                canvas.height = canvas.offsetHeight;
            }

            function makeNode() {
                return {
                    x:  Math.random() * canvas.width,
                    y:  Math.random() * canvas.height,
                    vx: (Math.random() - 0.5) * SPEED * 2,
                    vy: (Math.random() - 0.5) * SPEED * 2,
                };
            }

            resize();
            window.addEventListener('resize', () => {
                resize();
                nodes.forEach(n => {
                    if (n.x > canvas.width)  n.x = Math.random() * canvas.width;
                    if (n.y > canvas.height) n.y = Math.random() * canvas.height;
                });
            });

            for (let i = 0; i < COUNT; i++) nodes.push(makeNode());

            function tick() {
                ctx.clearRect(0, 0, canvas.width, canvas.height);

                for (let i = 0; i < nodes.length; i++) {
                    const a = nodes[i];
                    for (let j = i + 1; j < nodes.length; j++) {
                        const b = nodes[j];
                        const dx = a.x - b.x, dy = a.y - b.y;
                        const dist = Math.sqrt(dx * dx + dy * dy);
                        if (dist < MAX_DIST) {
                            const alpha = (1 - dist / MAX_DIST) * 0.35;
                            ctx.beginPath();
                            ctx.moveTo(a.x, a.y);
                            ctx.lineTo(b.x, b.y);
                            ctx.strokeStyle = `rgba(56,189,248,${alpha})`;
                            ctx.lineWidth = 1;
                            ctx.stroke();
                        }
                    }
                }

                nodes.forEach(n => {
                    n.x += n.vx;
                    n.y += n.vy;
                    if (n.x < 0 || n.x > canvas.width)  n.vx *= -1;
                    if (n.y < 0 || n.y > canvas.height)  n.vy *= -1;
                    ctx.beginPath();
                    ctx.arc(n.x, n.y, 1.8, 0, Math.PI * 2);
                    ctx.fillStyle = 'rgba(56,189,248,0.7)';
                    ctx.fill();
                });

                requestAnimationFrame(tick);
            }
            tick();
        })();
        // ─────────────────────────────────────────────────────────

        const nav = document.querySelector('.nav');
        let lastScroll = 0;
        lenis.on('scroll', ({ scroll }) => {
            if (heroGlow)   heroGlow.style.transform   = `translateX(-50%) translateY(${scroll * 0.4}px)`;
            if (heroCanvas) heroCanvas.style.transform = `translateY(${scroll * 0.15}px)`;

            if (scroll > 80) {
                nav.classList.toggle('nav--hidden', scroll > lastScroll);
            } else {
                nav.classList.remove('nav--hidden');
            }
            lastScroll = scroll;
        });
    });

    document.querySelectorAll('.faq-q').forEach(btn => {
        btn.addEventListener('click', () => {
            btn.closest('.faq-item').classList.toggle('open');
        });
    });

    (function initTheme() {
        const html = document.documentElement;
        const btn  = document.getElementById('theme-toggle');
        html.setAttribute('data-theme', localStorage.getItem('theme') || 'dark');
        btn.addEventListener('click', () => {
            const next = html.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
            html.setAttribute('data-theme', next);
            localStorage.setItem('theme', next);
        });
    })();
    </script>

    <script type="module">
        import { annotate } from 'https://unpkg.com/rough-notation?module';
        const accentEl = document.querySelector('.hero-title .accent');
        if (accentEl) {
            const a = annotate(accentEl, {
                type: 'underline',
                color: '#38BDF8',
                strokeWidth: 5,
                padding: 6,
                animate: true,
                animationDuration: 600,
            });
            window.addEventListener('load', () => a.show());
        }
    </script>
</body>
</html>
