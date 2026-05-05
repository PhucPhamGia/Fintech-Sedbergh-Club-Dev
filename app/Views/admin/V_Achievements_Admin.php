<!DOCTYPE html>
<html lang="en">
<head>
  <?= view('V_Head') ?>
  <script>document.documentElement.setAttribute('data-theme', localStorage.getItem('theme') || 'dark');</script>
  <title>Achievement Lab</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=JetBrains+Mono:wght@400;700&display=swap">
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
    body {
      background: var(--bg);
      color: #fff;
      font-family: "Plus Jakarta Sans", sans-serif;
      min-height: 100vh;
      padding: 48px 32px 80px;
    }

    .page-header {
      display: flex;
      align-items: center;
      gap: 16px;
      margin-bottom: 36px;
    }
    .back-btn {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      font-size: 0.82rem;
      font-weight: 600;
      color: var(--muted);
      text-decoration: none;
      padding: 6px 12px;
      border: 1px solid var(--border);
      border-radius: 8px;
      background: var(--surface);
      transition: color .15s, border-color .15s;
    }
    .back-btn:hover { color: #fff; border-color: rgba(255,255,255,.25); }
    .back-btn svg { width: 14px; height: 14px; }
    .page-title {
      font-size: 1.4rem;
      font-weight: 700;
    }
    .page-eyebrow {
      font-size: 0.65rem;
      font-weight: 700;
      letter-spacing: .12em;
      text-transform: uppercase;
      color: var(--accent);
      margin-bottom: 4px;
    }

    .flash {
      padding: 10px 16px;
      border-radius: 8px;
      font-size: 0.83rem;
      font-weight: 600;
      margin-bottom: 24px;
      border: 1px solid;
    }
    .flash-success { background: rgba(52,211,153,0.08); border-color: rgba(52,211,153,0.3); color: var(--green); }

    .achievement-list {
      display: flex;
      flex-direction: column;
      gap: 14px;
      max-width: 620px;
    }

    .achievement-card {
      display: flex;
      align-items: center;
      gap: 16px;
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: var(--radius);
      padding: 18px 20px;
      position: relative;
      overflow: hidden;
    }
    .card-canvas {
      position: absolute;
      inset: 0;
      width: 100%;
      height: 100%;
      z-index: 0;
      border-radius: var(--radius);
    }
    .achievement-icon,
    .achievement-info,
    .badge-earned,
    .action-btns { position: relative; z-index: 1; }

    .achievement-icon {
      width: 44px;
      height: 44px;
      border-radius: 10px;
      background: rgba(52,211,153,0.1);
      display: flex;
      align-items: center;
      justify-content: center;
      color: var(--green);
      flex-shrink: 0;
    }
    .achievement-icon svg { width: 22px; height: 22px; }
    .achievement-info { flex: 1; }
    .achievement-label {
      font-size: 0.95rem;
      font-weight: 700;
      margin-bottom: 3px;
    }
    .achievement-desc {
      font-size: 0.78rem;
      color: var(--muted);
    }
    .achievement-key {
      font-family: "JetBrains Mono", monospace;
      font-size: 0.65rem;
      color: var(--muted);
      margin-top: 4px;
    }

    .badge-earned {
      display: inline-flex;
      align-items: center;
      gap: 5px;
      padding: 3px 10px;
      border-radius: 20px;
      font-size: 0.68rem;
      font-weight: 700;
      letter-spacing: .06em;
      text-transform: uppercase;
      background: rgba(52,211,153,0.12);
      color: var(--green);
      flex-shrink: 0;
    }
    .badge-locked {
      background: rgba(255,255,255,0.05);
      color: var(--muted);
    }
    .badge-earned svg, .badge-locked svg { width: 11px; height: 11px; }

    .action-btns {
      display: flex;
      gap: 8px;
      flex-shrink: 0;
    }
    .btn-grant, .btn-revoke {
      padding: 6px 14px;
      border-radius: 7px;
      font-family: "Plus Jakarta Sans", sans-serif;
      font-size: 0.78rem;
      font-weight: 600;
      cursor: pointer;
      border: 1px solid;
      transition: background .15s, color .15s;
    }
    .btn-grant {
      background: rgba(52,211,153,0.1);
      border-color: rgba(52,211,153,0.3);
      color: var(--green);
    }
    .btn-grant:hover { background: rgba(52,211,153,0.2); }
    .btn-revoke {
      background: rgba(248,113,113,0.08);
      border-color: rgba(248,113,113,0.25);
      color: var(--red);
    }
    .btn-revoke:hover { background: rgba(248,113,113,0.18); }

    /* Toast */
    .achievement-toast {
      position: fixed;
      bottom: 28px;
      right: 28px;
      display: flex;
      align-items: center;
      gap: 14px;
      min-width: 240px;
      background: rgba(11,20,38,0.96);
      backdrop-filter: blur(16px);
      -webkit-backdrop-filter: blur(16px);
      border: 1px solid var(--t-border);
      border-radius: 14px;
      padding: 14px 20px 14px 14px;
      box-shadow: 0 12px 40px rgba(0,0,0,0.5);
      z-index: 9999;
      pointer-events: none;
      opacity: 0;
      transform: translateY(16px);
      transition: opacity .35s ease, transform .5s cubic-bezier(.34,1.56,.64,1);
    }
    .achievement-toast.show { opacity: 1; transform: translateY(0); }
    .achievement-toast-icon {
      width: 40px; height: 40px; border-radius: 10px;
      background: var(--t-bg);
      display: flex; align-items: center; justify-content: center;
      flex-shrink: 0; color: var(--t-color);
    }
    .achievement-toast-icon svg { width: 20px; height: 20px; }
    .achievement-toast-label {
      font-size: 0.62rem; font-weight: 700;
      letter-spacing: .1em; text-transform: uppercase;
      color: var(--t-color); margin-bottom: 3px;
    }
    .achievement-toast-title { font-size: 0.88rem; font-weight: 700; color: #fff; }
  </style>
</head>
<body>

  <div class="page-header">
    <a href="<?= site_url('dashboard') ?>" class="back-btn">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
      Dashboard
    </a>
    <div>
      <div class="page-eyebrow">Admin</div>
      <div class="page-title">Achievement Lab</div>
    </div>
  </div>

  <?php if (session()->getFlashdata('success')): ?>
    <div class="flash flash-success"><?= esc(session()->getFlashdata('success')) ?></div>
  <?php endif; ?>

  <div class="achievement-list">
    <?php foreach ($achievements as $a): ?>
    <div class="achievement-card">
      <canvas class="card-canvas" data-color="<?= esc($a['canvas_color']) ?>"></canvas>

      <div class="achievement-icon" style="background:<?= esc($a['toast_bg']) ?>;color:<?= esc($a['toast_color']) ?>">
        <?php if ($a['icon'] === 'grass'): ?>
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10z"/>
            <path stroke-linecap="round" stroke-linejoin="round" d="M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 12 13 13 12"/>
          </svg>
        <?php elseif ($a['icon'] === 'academic_cap'): ?>
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.627 48.627 0 0 1 12 20.904a48.627 48.627 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.57 50.57 0 0 0-2.658-.813A59.905 59.905 0 0 1 12 3.493a59.902 59.902 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5"/>
          </svg>
        <?php endif; ?>
      </div>

      <div class="achievement-info">
        <div class="achievement-label"><?= esc($a['label']) ?></div>
        <div class="achievement-desc"><?= esc($a['desc']) ?></div>
        <div class="achievement-key"><?= esc($a['key']) ?></div>
      </div>

      <?php if ($a['earned']): ?>
        <span class="badge-earned">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
          Earned
        </span>
      <?php else: ?>
        <span class="badge-earned badge-locked">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z"/></svg>
          Locked
        </span>
      <?php endif; ?>

      <div class="action-btns">
        <form method="POST" action="<?= site_url('admin/achievements/grant') ?>">
          <?= csrf_field() ?>
          <input type="hidden" name="achievement" value="<?= esc($a['key']) ?>">
          <button type="submit" class="btn-grant">Grant</button>
        </form>
        <form method="POST" action="<?= site_url('admin/achievements/revoke') ?>">
          <?= csrf_field() ?>
          <input type="hidden" name="achievement" value="<?= esc($a['key']) ?>">
          <button type="submit" class="btn-revoke">Revoke</button>
        </form>
      </div>

    </div>
    <?php endforeach; ?>
  </div>

  <script>
  // #Canvas backgrounds
  (function () {
    function hexToRgb(hex) {
      var r = parseInt(hex.slice(1,3),16), g = parseInt(hex.slice(3,5),16), b = parseInt(hex.slice(5,7),16);
      return [r, g, b];
    }

    function initCardCanvas(canvas, hexColor) {
      var ctx = canvas.getContext('2d');
      var rgb = hexToRgb(hexColor);
      var phase = Math.random() * 8;
      var LEVELS = 10;

      canvas.width  = canvas.offsetWidth;
      canvas.height = canvas.offsetHeight;
      var W = canvas.width, H = canvas.height;

      ctx.lineJoin = 'round';
      ctx.lineCap  = 'round';

      for (var l = 0; l < LEVELS; l++) {
        var yBase = (l / (LEVELS - 1)) * H;
        var alpha = l % 3 === 0 ? 0.22 : 0.10;
        ctx.beginPath();
        ctx.strokeStyle = 'rgba(' + rgb[0] + ',' + rgb[1] + ',' + rgb[2] + ',' + alpha + ')';
        ctx.lineWidth   = l % 3 === 0 ? 1.5 : 0.8;

        var step = 6;
        var pts  = [];
        for (var x = 0; x <= W; x += step) {
          var y = yBase
            + Math.sin(x * 0.013 + phase) * H * 0.13
            + Math.sin(x * 0.007 + phase * 0.6 + l) * H * 0.09
            + Math.cos(x * 0.021 + phase * 1.1) * H * 0.05;
          pts.push([x, y]);
        }

        ctx.moveTo(pts[0][0], pts[0][1]);
        for (var i = 1; i < pts.length - 1; i++) {
          var mx = (pts[i][0] + pts[i+1][0]) / 2;
          var my = (pts[i][1] + pts[i+1][1]) / 2;
          ctx.quadraticCurveTo(pts[i][0], pts[i][1], mx, my);
        }
        ctx.lineTo(pts[pts.length-1][0], pts[pts.length-1][1]);
        ctx.stroke();
      }
    }

    document.querySelectorAll('.card-canvas').forEach(function(canvas) {
      initCardCanvas(canvas, canvas.dataset.color);
    });
  })();
  </script>

  <?php
    $playKey = session()->getFlashdata('play_toast');
    $toastA  = $playKey ? ($achievements[$playKey] ?? null) : null;
  ?>
  <?php if ($toastA): ?>
  <div id="achievement-toast" class="achievement-toast"
       style="--t-color:<?= esc($toastA['toast_color']) ?>;--t-bg:<?= esc($toastA['toast_bg']) ?>;--t-border:<?= esc($toastA['toast_border']) ?>">
    <div class="achievement-toast-icon">
      <?php if ($toastA['icon'] === 'grass'): ?>
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10z"/>
          <path stroke-linecap="round" stroke-linejoin="round" d="M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 12 13 13 12"/>
        </svg>
      <?php elseif ($toastA['icon'] === 'academic_cap'): ?>
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.627 48.627 0 0 1 12 20.904a48.627 48.627 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.57 50.57 0 0 0-2.658-.813A59.905 59.905 0 0 1 12 3.493a59.902 59.902 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5"/>
        </svg>
      <?php endif; ?>
    </div>
    <div>
      <div class="achievement-toast-label">Achievement Unlocked</div>
      <div class="achievement-toast-title"><?= esc($toastA['label']) ?></div>
    </div>
  </div>
  <script>
  // #Achievement toast
  setTimeout(function () {
    var t = document.getElementById('achievement-toast');
    if (t) { t.classList.add('show'); setTimeout(function () { t.classList.remove('show'); }, 4000); }
  }, 400);
  </script>
  <?php endif; ?>

</body>
</html>
