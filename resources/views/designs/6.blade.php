<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>幫助系統 // HELPDESK</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Bungee&family=Bungee+Inline&family=Noto+Serif+TC:wght@400;700;900&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
<style>
  :root { --bg:#08060d; --neon:#ff2e63; --teal:#00ffd1; --gold:#ffd23f; --plum:#1a0e26; }
  * { margin:0; padding:0; box-sizing:border-box; }
  body {
    background: var(--bg); color: #e8e8f0;
    font-family: 'Space Mono', monospace;
    min-height: 100vh; overflow-x: hidden;
    background-image:
      radial-gradient(ellipse 60% 40% at 80% 0%, rgba(255,46,99,0.30), transparent 60%),
      radial-gradient(ellipse 50% 40% at 0% 100%, rgba(0,255,209,0.18), transparent 60%),
      url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='240' height='240'><filter id='n'><feTurbulence type='fractalNoise' baseFrequency='1.0' numOctaves='2'/></filter><rect width='100%' height='100%' filter='url(%23n)' opacity='0.06'/></svg>");
  }
  .scan { position: fixed; inset: 0; pointer-events: none; z-index: 50; background: repeating-linear-gradient(0deg, transparent, transparent 3px, rgba(255,255,255,0.012) 3px, rgba(255,255,255,0.012) 4px); }

  .grid { display: grid; grid-template-columns: 220px 1fr 220px; min-height: 100vh; }
  .left, .right { padding: 24px 18px; border-right: 1px solid rgba(255,46,99,0.25); position: relative; }
  .right { border-right: none; border-left: 1px solid rgba(0,255,209,0.25); }

  .vert {
    writing-mode: vertical-rl; transform: rotate(180deg);
    font-family: 'Noto Serif TC', serif; font-weight: 900;
    font-size: 56px; line-height: 1; letter-spacing: 0.1em;
    color: var(--neon); text-shadow: 0 0 8px var(--neon), 0 0 24px rgba(255,46,99,0.5);
    margin: 60px auto 0;
    animation: flicker 4s infinite;
  }
  @keyframes flicker {
    0%, 100% { opacity: 1; }
    91% { opacity: 1; }
    92% { opacity: 0.4; }
    93% { opacity: 1; }
    94% { opacity: 0.6; }
    96% { opacity: 1; }
  }
  .right .vert { color: var(--teal); text-shadow: 0 0 8px var(--teal), 0 0 24px rgba(0,255,209,0.5); }
  .stamp {
    position: absolute; bottom: 24px; left: 18px;
    font-family: 'Bungee', sans-serif; font-size: 10px; letter-spacing: 0.18em;
    color: rgba(232,232,240,0.6);
  }

  .center { padding: 48px 56px; }
  .top {
    display: flex; justify-content: space-between; align-items: center;
    border-bottom: 1px dashed rgba(255,46,99,0.4); padding-bottom: 18px;
  }
  .blip { display: inline-flex; align-items: center; gap: 10px; font-size: 11px; letter-spacing: 0.2em; }
  .blip .dot { width: 8px; height: 8px; background: var(--teal); border-radius: 50%; box-shadow: 0 0 10px var(--teal); animation: ping 1.4s ease-out infinite; }
  @keyframes ping { 0% { transform: scale(1); opacity: 1; } 70% { transform: scale(1.6); opacity: 0; } 100% { opacity: 0; } }
  .clock { font-family: 'Space Mono', monospace; font-size: 11px; color: rgba(232,232,240,0.6); letter-spacing: 0.1em; }

  .hero { padding: 56px 0 36px; }
  .hero h1 {
    font-family: 'Bungee Inline', sans-serif; font-weight: 400;
    font-size: clamp(72px, 11vw, 168px); line-height: 0.92; letter-spacing: -0.005em;
    color: var(--gold); text-shadow: 0 0 6px rgba(255,210,63,0.5), 4px 4px 0 var(--neon);
    animation: glitch 0.6s steps(2) 1 both;
  }
  .hero .sub {
    margin-top: 18px; font-size: 13px; color: var(--teal); letter-spacing: 0.3em; text-transform: uppercase;
  }
  @keyframes glitch {
    0% { transform: translateX(-12px); opacity: 0; clip-path: inset(40% 0 50% 0); }
    50% { clip-path: inset(20% 0 60% 0); }
    100% { transform: none; opacity: 1; clip-path: inset(0 0 0 0); }
  }

  .tiles { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; margin: 28px 0; }
  .tile {
    border: 1px solid rgba(255,46,99,0.4); padding: 22px 20px; position: relative; background: rgba(26,14,38,0.4); backdrop-filter: blur(4px);
    animation: rise 0.7s cubic-bezier(.2,.7,.2,1) both;
  }
  .tile:nth-child(2){ animation-delay: 0.12s; border-color: rgba(0,255,209,0.45); }
  .tile:nth-child(3){ animation-delay: 0.22s; border-color: rgba(255,210,63,0.5); }
  @keyframes rise { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: none; } }
  .tile .lab { font-size: 10px; letter-spacing: 0.28em; color: rgba(232,232,240,0.65); text-transform: uppercase; }
  .tile .num { font-family: 'Bungee', sans-serif; font-size: 78px; line-height: 1; margin-top: 8px; color: var(--neon); text-shadow: 0 0 10px var(--neon); }
  .tile:nth-child(2) .num { color: var(--teal); text-shadow: 0 0 10px var(--teal); }
  .tile:nth-child(3) .num { color: var(--gold); text-shadow: 0 0 10px var(--gold); }
  .tile .hk { position: absolute; top: 12px; right: 14px; font-family: 'Noto Serif TC', serif; font-size: 22px; opacity: 0.55; }

  .panels { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-top: 22px; }
  .panel { border: 1px dashed rgba(0,255,209,0.4); padding: 22px; background: rgba(8,6,13,0.5); }
  .panel h3 { font-family: 'Bungee', sans-serif; font-size: 12px; letter-spacing: 0.2em; color: var(--teal); margin-bottom: 16px; }
  .panel .row { display: flex; justify-content: space-between; align-items: center; padding: 8px 0; font-size: 12px; border-bottom: 1px dotted rgba(232,232,240,0.18); }
  .panel .row:last-child { border-bottom: none; }
  .panel .nm { letter-spacing: 0.06em; }
  .panel .nm .hk { font-family: 'Noto Serif TC', serif; opacity: 0.5; margin-right: 6px; }
  .panel .v { font-family: 'Bungee', sans-serif; font-size: 16px; color: var(--neon); }

  .nav { display: flex; gap: 10px; margin-top: 30px; }
  .nav a { font-family: 'Bungee', sans-serif; font-size: 12px; letter-spacing: 0.16em; padding: 12px 18px; text-decoration: none; border: 1px solid; }
  .nav a.pink { color: var(--neon); border-color: var(--neon); box-shadow: 0 0 14px rgba(255,46,99,0.3); }
  .nav a.teal { color: var(--teal); border-color: var(--teal); box-shadow: 0 0 14px rgba(0,255,209,0.3); }
  .nav a:hover { background: currentColor; color: var(--bg); }
  .nav a.pink:hover { color: var(--bg); background: var(--neon); }
  .nav a.teal:hover { color: var(--bg); background: var(--teal); }

  @media (max-width: 900px) { .grid { grid-template-columns: 1fr; } .left, .right { display: none; } }
</style>
</head>
<body>
<div class="scan"></div>
<div class="grid">
  <aside class="left">
    <div class="vert">支援中心</div>
    <div class="stamp">DESIGN 06 / 10</div>
  </aside>
  <main class="center">
    <header class="top">
      <div class="blip"><span class="dot"></span> SYSTEM ONLINE · OPS-04</div>
      <div class="clock">14:22:08 HKT · 07.05.2026</div>
    </header>
    <section class="hero">
      <h1>QUEUE/<br>FORTY-FOUR</h1>
      <div class="sub">// 12 open · 7 active · 25 cleared //</div>
    </section>
    <section class="tiles">
      <div class="tile"><div class="lab">// open</div><div class="num">12</div><div class="hk">開</div></div>
      <div class="tile"><div class="lab">// active</div><div class="num">07</div><div class="hk">行</div></div>
      <div class="tile"><div class="lab">// closed</div><div class="num">25</div><div class="hk">完</div></div>
    </section>
    <section class="panels">
      <div class="panel">
        <h3>分類 / CATEGORY</h3>
        <div class="row"><span class="nm"><span class="hk">軟</span>SOFTWARE</span><span class="v">18</span></div>
        <div class="row"><span class="nm"><span class="hk">硬</span>HARDWARE</span><span class="v">11</span></div>
        <div class="row"><span class="nm"><span class="hk">網</span>NETWORK</span><span class="v">08</span></div>
        <div class="row"><span class="nm"><span class="hk">入</span>ACCESS</span><span class="v">07</span></div>
      </div>
      <div class="panel">
        <h3>優先 / PRIORITY</h3>
        <div class="row"><span class="nm"><span class="hk">高</span>HIGH</span><span class="v">09</span></div>
        <div class="row"><span class="nm"><span class="hk">中</span>MEDIUM</span><span class="v">22</span></div>
        <div class="row"><span class="nm"><span class="hk">低</span>LOW</span><span class="v">13</span></div>
      </div>
    </section>
    <nav class="nav">
      <a href="/admin/tickets" class="pink">// TICKETS →</a>
      <a href="/admin/users" class="teal">// USERS →</a>
    </nav>
  </main>
  <aside class="right">
    <div class="vert">霓虹之夜</div>
    <div class="stamp" style="left:auto; right:18px;">BUNGEE × NOTO TC</div>
  </aside>
</div>
</body>
</html>
