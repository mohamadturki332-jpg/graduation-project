<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>HELPDESK//TERMINAL</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@200;300;400;500;700;800&family=VT323&display=swap" rel="stylesheet">
<style>
  :root { --bg:#0c0a04; --amber:#ffb000; --amber-dim:#a87400; --amber-faint:#5c3f00; --warn:#ff5252; --green:#27c93f; }
  * { margin:0; padding:0; box-sizing:border-box; }
  body {
    background: var(--bg); color: var(--amber);
    font-family: 'JetBrains Mono', monospace; font-weight: 400;
    min-height: 100vh; padding: 32px;
    text-shadow: 0 0 1px rgba(255,176,0,0.6), 0 0 8px rgba(255,176,0,0.18);
    background-image:
      repeating-linear-gradient(0deg, transparent, transparent 2px, rgba(0,0,0,0.35) 2px, rgba(0,0,0,0.35) 3px),
      radial-gradient(ellipse at center, rgba(255,176,0,0.08), transparent 70%);
  }
  body::after {
    content:''; position:fixed; inset:0; pointer-events:none;
    background: radial-gradient(circle at center, transparent 60%, rgba(0,0,0,0.5) 100%);
  }
  .crt { max-width: 1280px; margin: 0 auto; border: 1px solid var(--amber-dim); padding: 18px 24px 28px; position: relative; }
  .crt::before { content:''; position:absolute; top:-1px; left:24px; right:24px; height:2px; background: var(--amber); }
  .bar { display:flex; justify-content:space-between; font-size: 11px; padding-bottom: 14px; border-bottom: 1px dashed var(--amber-faint); }
  .blink { animation: blink 1.1s steps(2) infinite; }
  @keyframes blink { 50% { opacity: 0; } }
  .greeting { font-family: 'VT323', monospace; font-size: 28px; line-height: 1; margin: 28px 0 6px; letter-spacing: 0.04em; }
  .echo { font-size: 12px; color: var(--amber-dim); margin-bottom: 28px; }
  .echo .cursor::after { content:'_'; animation: blink 1s steps(2) infinite; margin-left: 2px; }

  .ascii-box { border: 1px solid var(--amber-dim); padding: 18px 20px; }
  .ascii-head { font-size: 10px; color: var(--amber-dim); text-transform: uppercase; letter-spacing: 0.3em; margin-bottom: 14px; }
  .grid-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; margin: 14px 0; }
  .num-tile { padding: 18px 18px; border: 1px dashed var(--amber-faint); position: relative; animation: boot 0.8s steps(20) both; }
  .num-tile:nth-child(1){ animation-delay: 0.1s; }
  .num-tile:nth-child(2){ animation-delay: 0.2s; }
  .num-tile:nth-child(3){ animation-delay: 0.3s; }
  @keyframes boot { from { opacity: 0; clip-path: inset(0 100% 0 0); } to { opacity: 1; clip-path: inset(0 0 0 0); } }
  .num-tile .lab { font-size: 10px; color: var(--amber-dim); text-transform: uppercase; letter-spacing: 0.25em; }
  .num-tile .num { font-family: 'VT323', monospace; font-size: 92px; line-height: 0.9; margin-top: 6px; }
  .num-tile.warn .num { color: var(--warn); text-shadow: 0 0 8px var(--warn); }
  .num-tile.ok .num { color: var(--green); text-shadow: 0 0 8px var(--green); }

  .row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-top: 14px; }
  table { width: 100%; border-collapse: collapse; font-size: 13px; }
  th, td { text-align: left; padding: 8px 4px; border-bottom: 1px dashed var(--amber-faint); }
  th { font-size: 10px; text-transform: uppercase; letter-spacing: 0.25em; color: var(--amber-dim); font-weight: 400; }
  .bar-mini { display: inline-block; width: 80px; height: 8px; background: var(--amber-faint); position: relative; margin-left: 8px; }
  .bar-mini::after { content:''; position: absolute; left:0; top:0; bottom:0; width: var(--w, 50%); background: var(--amber); box-shadow: 0 0 6px var(--amber); }

  .ticker { margin-top: 18px; padding: 10px 0; border-top: 1px dashed var(--amber-faint); font-size: 12px; color: var(--amber-dim); overflow: hidden; white-space: nowrap; }
  .ticker span { display: inline-block; padding-left: 100%; animation: scroll 30s linear infinite; }
  @keyframes scroll { from { transform: translateX(0); } to { transform: translateX(-100%); } }
  .footer { margin-top: 14px; display: flex; justify-content: space-between; font-size: 10px; color: var(--amber-dim); text-transform: uppercase; letter-spacing: 0.25em; }
  .footer a { color: var(--amber); text-decoration: none; border: 1px solid var(--amber-dim); padding: 6px 10px; margin-left: 6px; }
  .footer a:hover { background: var(--amber); color: var(--bg); text-shadow: none; }
</style>
</head>
<body>
<div class="crt">
  <div class="bar">
    <span>HELPDESK//TERMINAL <span class="blink">●</span> v2.1.0</span>
    <span>SESSION: ADMIN · 0x7F3A · TUE 07 MAY 2026 14:22:08 GMT</span>
  </div>
  <div class="greeting">$ ./status --all</div>
  <div class="echo">> 44 records found across 3 partitions <span class="cursor"></span></div>

  <div class="ascii-box">
    <div class="ascii-head">┌─ system telemetry ────────────────────────────┐</div>
    <div class="grid-3">
      <div class="num-tile"><div class="lab">queue.open</div><div class="num">12</div></div>
      <div class="num-tile warn"><div class="lab">queue.in_progress</div><div class="num">07</div></div>
      <div class="num-tile ok"><div class="lab">queue.closed</div><div class="num">25</div></div>
    </div>
    <div class="row">
      <div>
        <table>
          <thead><tr><th>category</th><th style="text-align:right">count</th><th></th></tr></thead>
          <tr><td>software</td><td style="text-align:right">18</td><td><span class="bar-mini" style="--w:100%"></span></td></tr>
          <tr><td>hardware</td><td style="text-align:right">11</td><td><span class="bar-mini" style="--w:61%"></span></td></tr>
          <tr><td>network</td><td style="text-align:right">08</td><td><span class="bar-mini" style="--w:44%"></span></td></tr>
          <tr><td>access</td><td style="text-align:right">07</td><td><span class="bar-mini" style="--w:38%"></span></td></tr>
        </table>
      </div>
      <div>
        <table>
          <thead><tr><th>priority</th><th style="text-align:right">count</th><th></th></tr></thead>
          <tr><td>HIGH</td><td style="text-align:right">09</td><td><span class="bar-mini" style="--w:41%"></span></td></tr>
          <tr><td>MEDIUM</td><td style="text-align:right">22</td><td><span class="bar-mini" style="--w:100%"></span></td></tr>
          <tr><td>LOW</td><td style="text-align:right">13</td><td><span class="bar-mini" style="--w:59%"></span></td></tr>
        </table>
      </div>
    </div>
  </div>

  <div class="ticker"><span>// LIVE FEED // 14:22:01 ticket #48 opened by m.turki · 14:21:47 ticket #47 closed by agent.k · 14:20:33 ticket #46 priority raised to high · 14:19:12 ticket #45 assigned to agent.s · 14:18:55 ticket #44 reopened · cycle //</span></div>
  <div class="footer">
    <span>Design 02 / 10 · JetBrains Mono + VT323</span>
    <span><a href="/admin/tickets">[ tickets ]</a><a href="/admin/users">[ users ]</a></span>
  </div>
</div>
</body>
</html>
