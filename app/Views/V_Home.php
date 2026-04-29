<!DOCTYPE html>
<html lang="en">
<head>
  <?= view('V_Head') ?>
  <script>document.documentElement.setAttribute('data-theme',localStorage.getItem('theme')||'dark');</script>
  <title>Featherlight</title>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&family=Space+Grotesk:wght@300..700&family=JetBrains+Mono:wght@400;700&display=swap');
    
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }
    html { overflow-x: hidden; }

    body {
        font-family: "Plus Jakarta Sans", sans-serif;
        color: white;
        background-color: #0B1426;
        overflow-x: hidden;
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
        background: rgba(11,20,38,0.35);
        backdrop-filter: blur(48px) saturate(1.8) brightness(0.9);
        -webkit-backdrop-filter: blur(48px) saturate(1.8) brightness(0.9);
        border: 1px solid rgba(255,255,255,0.1);
        border-radius: 14px;
        box-shadow: 0 8px 32px rgba(0,0,0,0.35), inset 0 1px 0 rgba(255,255,255,0.08);
        transition: transform .7s cubic-bezier(.4,0,.2,1), opacity .7s ease;
    }
    .nav--hidden {
        transform: translateX(-50%) translateY(calc(-100% - 28px));
        opacity: 0;
        pointer-events: none;
    }
    .nav--intro {
        transform: translateX(-50%) translateY(calc(-100% - 28px));
        pointer-events: none;
    }
    /* ticker-strip base merged below */
    .ticker-strip.ticker--intro {
        clip-path: inset(0 100% 0 0);
        pointer-events: none;
    }
    .ticker-strip::before {
        content: '';
        position: absolute;
        top: 0; left: 0;
        width: 2px;
        height: 100%;
        background: linear-gradient(to bottom, transparent, var(--accent), transparent);
        box-shadow: 0 0 8px var(--accent), 0 0 20px rgba(56,189,248,0.4);
        z-index: 10;
        opacity: 0;
        pointer-events: none;
    }
    .ticker-strip.ray-sweep::before {
        opacity: 1;
        animation: ticker-ray 1.6s cubic-bezier(0.4, 0, 0.2, 1) forwards;
    }
    @keyframes ticker-ray {
        from { left: 0;    opacity: 1; }
        90%  { left: 100%; opacity: 1; }
        to   { left: 100%; opacity: 0; }
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
        background: rgba(255,255,255,0.4);
        backdrop-filter: blur(48px) saturate(1.8) brightness(1.05);
        -webkit-backdrop-filter: blur(48px) saturate(1.8) brightness(1.05);
        border-color: rgba(15,23,42,0.1);
        box-shadow: 0 8px 32px rgba(0,0,0,0.08), inset 0 1px 0 rgba(255,255,255,0.8);
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

    /* ── Hero UI ───────────────────────────────────────────── */
    .hero {
        position: relative;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
        padding: 80px 0 0;
    }
    .hero::after {
        content: '';
        position: absolute;
        bottom: 0; left: 0; right: 0;
        height: 320px;
        background: linear-gradient(to top, var(--bg) 0%, rgba(11,20,38,0.5) 60%, transparent 100%);
        pointer-events: none;
        z-index: 2;
    }
    .hero-bg-wrap {
        position: absolute;
        inset: 0;
        overflow: hidden;
        pointer-events: none;
    }
    .hero-orb {
        position: absolute;
        border-radius: 50%;
        filter: blur(120px);
        pointer-events: none;
    }
    .hero-orb-1 {
        width: 700px; height: 700px;
        background: rgba(52,211,153,0.18);
        top: -150px; right: -100px;
        animation: orb-drift-1 12s ease-in-out infinite;
    }
    .hero-orb-2 {
        width: 600px; height: 600px;
        background: rgba(244,114,182,0.15);
        bottom: -80px; left: -120px;
        animation: orb-drift-2 15s ease-in-out infinite;
    }
    .hero-orb-3 {
        width: 480px; height: 480px;
        background: rgba(52,211,153,0.1);
        top: 35%; left: 35%;
        animation: orb-drift-3 10s ease-in-out infinite;
    }
    html[data-theme="light"] .hero-orb-1 { background: rgba(6,95,70,0.18); }
    html[data-theme="light"] .hero-orb-2 { background: rgba(244,114,182,0.15); }
    html[data-theme="light"] .hero-orb-3 { background: rgba(6,95,70,0.12); }
    html[data-theme="light"] .hero::after {
        background: linear-gradient(to top, var(--bg) 0%, rgba(241,245,249,0.5) 60%, transparent 100%);
    }
    @keyframes orb-drift-1 {
        0%, 100% { transform: translate(0, 0); }
        50%       { transform: translate(-60px, 40px); }
    }
    @keyframes orb-drift-2 {
        0%, 100% { transform: translate(0, 0); }
        50%       { transform: translate(50px, -50px); }
    }
    @keyframes orb-drift-3 {
        0%, 100% { transform: translate(0, 0); }
        50%       { transform: translate(-40px, -60px); }
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
    .hero .container { position: relative; z-index: 3; text-align: left; padding: 0 0 60px 0px; margin: 0 0 0 6%; max-width: 600px; }
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
        font-family: "Space Grotesk", sans-serif;
        font-size: clamp(2rem, 5vw, 4rem);
        font-weight: 700;
        line-height: 1.04;
        letter-spacing: -.03em;
        margin-bottom: 20px;
    }
    .hero-title .accent { color: var(--accent); font-family: "Plus Jakarta Sans", sans-serif; font-weight: 800; letter-spacing: -.04em; display: table; margin-left: 2rem; }
    .hero-sub {
        position: absolute;
        top: 20%;
        right: 6%;
        font-size: 0.875rem;
        color: var(--muted);
        max-width: 260px;
        margin: 0;
        text-align: right;
        line-height: 1.6;
    }

    .hero-actions { display: flex; flex-direction: column; gap: 14px; align-items: flex-start; margin-left: 2.5rem; }
    .hero-actions .btn-primary { font-size: 0.95rem; padding: 13px 48px; }
    .hero-secondary-link {
        font-size: 0.875rem;
        color: var(--muted);
        text-decoration: none;
        transition: color .15s;
    }
    .hero-secondary-link:hover { color: #fff; }
    html[data-theme="light"] .hero-secondary-link:hover { color: #0f172a; }

    /* Ticker strip */
    .ticker-strip {
        overflow: hidden;
        margin-top: 16px;
        border-top: 1px solid rgba(56,189,248,.15);
        border-bottom: 1px solid rgba(56,189,248,.15);
        background: rgba(56,189,248,.03);
        position: relative;
        z-index: 3;
        padding: 12px 0;
        font-family: "JetBrains Mono", monospace;
        clip-path: inset(0 0% 0 0);
        transition: clip-path 1.6s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .ticker-inner {
        display: flex;
        width: max-content;
        animation: ticker 45s linear infinite;
    }
    @keyframes ticker {
        from { transform: translateX(0); }
        to   { transform: translateX(-50%); }
    }
    .ticker-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 0 32px;
        border-right: 1px solid rgba(56,189,248,.12);
        white-space: nowrap;
        font-size: 0.8rem;
        letter-spacing: .02em;
    }
    .ticker-name { color: rgba(56,189,248,.6); font-weight: 400; }
    .ticker-price { color: #fff; font-weight: 700; }
    .ticker-change { font-weight: 700; font-size: 0.78rem; }
    .up   { color: #34D399; }
    .down { color: #F472B6; }
    html[data-theme="light"] .ticker-name  { color: rgba(15,23,42,.5); }
    html[data-theme="light"] .ticker-price { color: #0f172a; }
    html[data-theme="light"] .ticker-strip .down { color: #e11d48; }
    html[data-theme="light"] .ticker-strip .up   { color: #059669; }

    /* ── Achievements ───────────────────────────────────── */
    .achievements {
        padding: 64px 0 64px;
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
    .features { padding: 96px 0; }
    .features-grid {
        display: grid;
        grid-template-columns: 1.2fr 1fr 1fr 1fr;
        grid-template-rows: 200px 180px 180px;
        gap: 12px;
        grid-template-areas:
            "a b b c"
            "a d e c"
            "a d f f";
    }
    .feature-card:nth-child(1) { grid-area: a; }
    .feature-card:nth-child(2) { grid-area: b; }
    .feature-card:nth-child(3) { grid-area: c; }
    .feature-card:nth-child(4) { grid-area: d; }
    .feature-card:nth-child(5) { grid-area: e; justify-content: center; }
    .feature-card:nth-child(6) { grid-area: f; }
    .feature-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 28px;
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
        overflow: hidden;
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
        margin-bottom: 20px;
    }
    .feature-icon svg {
        width: 28px;
        height: 28px;
        color: var(--accent);
    }
    .feature-card h3 {
        font-size: 1rem;
        font-weight: 700;
        margin-bottom: 8px;
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
        max-width: 860px;
        margin: 0 auto;
        display: flex;
        flex-direction: column;
    }
    .faq-item { border-top: 1px solid var(--border); }
    .faq-item:last-child { border-bottom: 1px solid var(--border); }
    .faq-q {
        width: 100%;
        background: none;
        border: none;
        color: inherit;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 24px;
        padding: 28px 0;
        font-size: 1.05rem;
        font-weight: 500;
        font-family: "Plus Jakarta Sans", sans-serif;
        text-align: left;
        transition: opacity .2s;
    }
    .faq-q:hover { opacity: .65; }
    .faq-num {
        font-family: "Plus Jakarta Sans", sans-serif;
        font-size: 0.7rem;
        font-weight: 700;
        letter-spacing: .1em;
        color: var(--accent);
        min-width: 28px;
        flex-shrink: 0;
    }
    .faq-icon {
        margin-left: auto;
        flex-shrink: 0;
        color: var(--muted);
        transition: transform .35s cubic-bezier(.4,0,.2,1), color .2s;
    }
    .faq-icon svg { display: block; width: 16px; height: 16px; }
    .faq-item.open .faq-icon { transform: rotate(90deg); color: var(--accent); }
    .faq-body {
        display: grid;
        grid-template-rows: 0fr;
        transition: grid-template-rows 0.35s cubic-bezier(.4,0,.2,1);
    }
    .faq-item.open .faq-body { grid-template-rows: 1fr; }
    .faq-body > p {
        overflow: hidden;
        min-height: 0;
        font-size: 0.875rem;
        color: var(--muted);
        line-height: 1.75;
        padding: 0 0 0 52px;
        transition: padding 0.35s cubic-bezier(.4,0,.2,1);
    }
    .faq-item.open .faq-body > p { padding: 0 0 28px 52px; }


    /* ── Responsive ─────────────────────────────────────── */
    @media (max-width: 768px) {
        .stats-grid       { grid-template-columns: repeat(2, 1fr); }
        .features-grid    { grid-template-columns: 1fr; }
        .nav-links        { display: none; }
    }
    ::view-transition-old(root),
    ::view-transition-new(root) {
        animation: none;
        mix-blend-mode: normal;
    }

    /* ── Intro overlay ──────────────────────────────────── */
    #intro-overlay {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 9999;
        pointer-events: none;
        width: 220px;
        height: 4px;
        border-radius: 3px;
        box-shadow: 0 0 0 9999px #ffffff;
        transition: width  0.55s cubic-bezier(0.76, 0, 0.24, 1),
                    height 0.55s cubic-bezier(0.76, 0, 0.24, 1),
                    border-radius 0.55s;
    }
    #intro-overlay.phase2 { width: 400px; height: 240px; border-radius: 14px; }
    #intro-overlay.phase3 {
        width: 200vw; height: 200vh; border-radius: 0;
        transition: width  0.65s cubic-bezier(0.76, 0, 0.24, 1),
                    height 0.65s cubic-bezier(0.76, 0, 0.24, 1),
                    border-radius 0.4s;
    }
  </style>
</head>
<body>
<div id="intro-overlay" aria-hidden="true"></div>

        <!-- Nav -->
        <nav class="nav nav--intro">
            <div class="nav-inner">
                <a href="/" class="nav-logo">
                    <!-- Logo (Hero Icon's Euro symbol) -->
                    <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 640 640">
                        <path d="M416 64C457 64 496.3 80.3 525.2 109.2L530.7 114.7C559.7 143.7 576 183 576 223.9C576 248 570.3 271.5 559.8 292.7C557.9 296.4 554.5 299.2 550.5 300.4L438.5 334C434.6 335.2 432 338.7 432 342.8C432 347.9 436.1 352 441.2 352L473.4 352C487.7 352 494.8 369.2 484.7 379.3L462.3 401.7C460.4 403.6 458.1 404.9 455.6 405.7L374.6 430C370.7 431.2 368.1 434.7 368.1 438.8C368.1 443.9 372.2 448 377.3 448C390.5 448 396.2 463.7 385.1 470.9C344 497.5 295.8 512 246.1 512L160.1 512L112.1 560C103.3 568.8 88.9 568.8 80.1 560C71.3 551.2 71.3 536.8 80.1 528L320 288C328.8 279.2 328.8 264.8 320 256C311.2 247.2 296.8 247.2 288 256L143.5 400.5C137.8 406.2 128 402.2 128 394.1C128 326.2 155 261.1 203 213.1L306.8 109.2C335.7 80.3 375 64 416 64z"/>
                    </svg>
                    Featherlight
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
            <div class="hero-orb hero-orb-1"></div>
            <div class="hero-orb hero-orb-2"></div>
            <div class="hero-orb hero-orb-3"></div>
            <div class="hero-bg-wrap">
                <canvas id="hero-canvas"></canvas>
                <div class="hero-bg-glow"></div>
            </div>
            <p class="hero-sub">
                Lorem ipsum dolor sit amet consectetur adipiscing elit. Dolor sit amet consectetur adipiscing elit quisque faucibus.
            </p>
            <div class="container">
                <h1 class="hero-title">
                    Analyze Crypto.<br>
                    <span class="accent">Trade Smarter.</span>
                </h1>
            </div>
            <!-- Ticker strip -->
            <div class="ticker-strip ticker--intro">
                <div class="ticker-inner">
                    <span class="ticker-item"><span class="ticker-name">BTC/USDT</span><span class="ticker-price">$67,420</span><span class="ticker-change up">▲ +2.41%</span></span>
                    <span class="ticker-item"><span class="ticker-name">ETH/USDT</span><span class="ticker-price">$3,511</span><span class="ticker-change up">▲ +1.78%</span></span>
                    <span class="ticker-item"><span class="ticker-name">SOL/USDT</span><span class="ticker-price">$142.50</span><span class="ticker-change down">▼ -0.92%</span></span>
                    <span class="ticker-item"><span class="ticker-name">BNB/USDT</span><span class="ticker-price">$589.00</span><span class="ticker-change up">▲ +3.12%</span></span>
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
                        <button class="faq-q"><span class="faq-num">01</span>Where does the market data come from?<span class="faq-icon"><svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 8h10M9 4l4 4-4 4"/></svg></span></button>
                        <div class="faq-body"><p>All OHLCV data is sourced directly from the Binance API (api.binance.com/api/v3/klines) and stored in our MySQL database. Data is refreshed daily via an automated import pipeline.</p></div>
                    </div>
                    <div class="faq-item">
                        <button class="faq-q"><span class="faq-num">02</span>Which coins are supported?<span class="faq-icon"><svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 8h10M9 4l4 4-4 4"/></svg></span></button>
                        <div class="faq-body"><p>Currently we support BTCUSDT, ETHUSDT, SOLUSDT, and BNBUSDT. Additional trading pairs will be added as the platform grows.</p></div>
                    </div>
                    <div class="faq-item">
                        <button class="faq-q"><span class="faq-num">03</span>How are MA20 and MA50 calculated?<span class="faq-icon"><svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 8h10M9 4l4 4-4 4"/></svg></span></button>
                        <div class="faq-body"><p>Moving averages are computed server-side using a sliding window over close prices sorted oldest-first. They are batch-updated after each daily import run.</p></div>
                    </div>
                    <div class="faq-item">
                        <button class="faq-q"><span class="faq-num">04</span>Is there an automated trading feature?<span class="faq-icon"><svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 8h10M9 4l4 4-4 4"/></svg></span></button>
                        <div class="faq-body"><p>An automated trading engine is in active development. The current platform focuses on data ingestion, indicator calculation, and interactive visualization.</p></div>
                    </div>
                    <div class="faq-item">
                        <button class="faq-q"><span class="faq-num">05</span>How do I get access to the dashboard?<span class="faq-icon"><svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 8h10M9 4l4 4-4 4"/></svg></span></button>
                        <div class="faq-body"><p>Register for a free account and log in. The dashboard requires authentication. Data import features require an Admin role assigned by a site administrator.</p></div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <?= view('V_Footer') ?>



    <script src="https://cdn.jsdelivr.net/npm/lenis@1/dist/lenis.min.js"></script>
    <script>
    const lenis = typeof Lenis !== 'undefined' ? new Lenis() : null;
    function raf(time) {
            if (lenis) lenis.raf(time);
            requestAnimationFrame(raf);
        }
        requestAnimationFrame(raf);

        const heroGlow    = document.querySelector('.hero-bg-glow');
        const heroCanvas  = document.getElementById('hero-canvas');

        // ── Particle network ──────────────────────────────────────
        (function initContour() {
            const canvas = heroCanvas;
            const ctx = canvas.getContext('2d');
            let t = 0;
            const CELL = 18;
            let COLS, ROWS;

            function resize() {
                canvas.width  = canvas.offsetWidth;
                canvas.height = canvas.offsetHeight;
                COLS = Math.ceil(canvas.width  / CELL) + 1;
                ROWS = Math.ceil(canvas.height / CELL) + 1;
            }
            resize();
            window.addEventListener('resize', resize);

            function noise(x, y, t) {
                return Math.sin(x * 2.1 + t * 0.4) * Math.cos(y * 1.7 - t * 0.3)
                     + Math.sin(x * 0.9 - t * 0.2 + y * 1.3) * 0.6
                     + Math.cos(x * 3.2 + y * 0.8 + t * 0.5) * 0.35;
            }

            const LEVELS = 8;

            function tick() {
                ctx.clearRect(0, 0, canvas.width, canvas.height);

                const grid = [];
                for (let r = 0; r <= ROWS; r++) {
                    grid[r] = [];
                    for (let c = 0; c <= COLS; c++) {
                        grid[r][c] = noise(c / COLS * 4, r / ROWS * 3, t);
                    }
                }

                for (let l = 0; l < LEVELS; l++) {
                    const level = -1.3 + (l / (LEVELS - 1)) * 2.6;
                    const isLight = document.documentElement.getAttribute('data-theme') === 'light';
                    const alpha = l % 3 === 0 ? 0.22 : 0.12;
                    const lw    = isLight ? (l % 3 === 0 ? 1.8 : 1.2) : (l % 3 === 0 ? 1.2 : 0.8);

                    ctx.beginPath();
                    ctx.strokeStyle = isLight ? `rgba(29,78,216,${alpha})` : `rgba(56,189,248,${alpha})`;
                    ctx.lineWidth = lw;

                    for (let r = 0; r < ROWS; r++) {
                        for (let c = 0; c < COLS; c++) {
                            const x0 = c * CELL, x1 = x0 + CELL;
                            const y0 = r * CELL, y1 = y0 + CELL;
                            const v00 = grid[r][c],   v10 = grid[r][c+1];
                            const v01 = grid[r+1][c], v11 = grid[r+1][c+1];
                            const pts = [];

                            if ((v00 < level) !== (v10 < level))
                                pts.push([x0 + (level - v00) / (v10 - v00) * CELL, y0]);
                            if ((v10 < level) !== (v11 < level))
                                pts.push([x1, y0 + (level - v10) / (v11 - v10) * CELL]);
                            if ((v01 < level) !== (v11 < level))
                                pts.push([x0 + (level - v01) / (v11 - v01) * CELL, y1]);
                            if ((v00 < level) !== (v01 < level))
                                pts.push([x0, y0 + (level - v00) / (v01 - v00) * CELL]);

                            if (pts.length >= 2) {
                                ctx.moveTo(pts[0][0], pts[0][1]);
                                ctx.lineTo(pts[1][0], pts[1][1]);
                            }
                        }
                    }
                    ctx.stroke();
                }

                t += 0.003;
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

            const delta = scroll - lastScroll;
            if (scroll > 80) {
                if (delta > 40)       nav.classList.add('nav--hidden');
                else if (delta < -40) nav.classList.remove('nav--hidden');
            } else {
                nav.classList.remove('nav--hidden');
            }
            lastScroll = scroll;
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

        btn.addEventListener('click', (e) => {
            const next = html.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';

            if (!document.startViewTransition) {
                html.setAttribute('data-theme', next);
                localStorage.setItem('theme', next);
                return;
            }

            const x = e.clientX, y = e.clientY;
            const endRadius = Math.hypot(Math.max(x, innerWidth - x), Math.max(y, innerHeight - y));

            const transition = document.startViewTransition(() => {
                html.setAttribute('data-theme', next);
                localStorage.setItem('theme', next);
            });

            transition.ready.then(() => {
                document.documentElement.animate(
                    { clipPath: [`circle(0px at ${x}px ${y}px)`, `circle(${endRadius}px at ${x}px ${y}px)`] },
                    { duration: 500, easing: 'ease-in-out', pseudoElement: '::view-transition-new(root)' }
                );
            });
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
            window.addEventListener('load', () => setTimeout(() => a.show(), 1600));
        }
    </script>
    <script>
    (function initTicker() {
        const inner = document.querySelector('.ticker-inner');
        if (!inner) return;
        const originalHTML = inner.innerHTML;

        function fill() {
            inner.innerHTML = originalHTML;
            const setWidth = inner.scrollWidth;
            const needed = Math.ceil((window.innerWidth * 3) / setWidth);
            const even = needed % 2 === 0 ? needed : needed + 1;
            for (let i = 1; i < even; i++) inner.insertAdjacentHTML('beforeend', originalHTML);
        }

        document.fonts.ready.then(() => {
            fill();
            window.addEventListener('resize', fill);
        });
    })();

    (function () {
        const o = document.getElementById('intro-overlay');
        // bar → small rect
        setTimeout(() => o.classList.add('phase2'), 80);
        // small rect → full screen
        setTimeout(() => o.classList.add('phase3'), 500);
        // remove overlay + reveal nav + reveal ticker
        setTimeout(() => {
            o.remove();
            document.querySelector('.nav').classList.remove('nav--intro');
            const ticker = document.querySelector('.ticker-strip');
            ticker.classList.remove('ticker--intro');
            ticker.classList.add('ray-sweep');
            setTimeout(() => ticker.classList.remove('ray-sweep'), 1700);
        }, 1100);
    })();
    </script>
</body>
</html>
