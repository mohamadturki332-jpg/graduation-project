<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Helpdesk · Memphis Pop</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Bagel+Fat+One&family=Caprasimo&family=Quicksand:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
  :root {
    --cream:#fef6e4; --pink:#ff7eb6; --mint:#7eebd2; --sun:#ffd84d;
    --grape:#7a5cff; --coral:#ff5e6c; --ink:#181321;
  }
  * { margin:0; padding:0; box-sizing:border-box; }
  body {
    background: var(--cream); color: var(--ink);
    font-family: 'Quicksand', sans-serif; font-weight: 500;
    min-height: 100vh; padding: 0;
    background-image:
      radial-gradient(circle at 12% 18%, var(--mint) 0 14px, transparent 14px),
      radial-gradient(circle at 86% 22%, var(--coral) 0 10px, transparent 10px),
      radial-gradient(circle at 22% 92%, var(--sun) 0 18px, transparent 18px),
      radial-gradient(circle at 78% 88%, var(--pink) 0 12px, transparent 12px),
      url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='120' height='120'><g fill='none' stroke='%237a5cff' stroke-width='1.5' opacity='0.18'><path d='M10 60 Q30 30 50 60 T 90 60'/></g></svg>"),
      url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='180' height='180'><g fill='%23ff7eb6' opacity='0.25'><circle cx='30' cy='30' r='2.5'/><circle cx='150' cy='90' r='2.5'/><circle cx='80' cy='160' r='2.5'/></g></svg>");
    background-size: auto, auto, auto, auto, 120px 120px, 180px 180px;
  }
  .container { max-width: 1180px; margin: 0 auto; padding: 48px 56px 80px; position: relative; z-index: 1; }
  .top { display: flex; justify-content: space-between; align-items: center; }
  .logo { font-family: 'Bagel Fat One', cursive; font-size: 32px; color: var(--grape); display: flex; align-items: center; gap: 10px; }
  .logo .blob { width: 32px; height: 32px; background: var(--coral); border-radius: 50%; display: inline-block; box-shadow: 6px 6px 0 var(--ink); animation: bob 2.4s ease-in-out infinite; }
  @keyframes bob { 50% { transform: translateY(-6px) rotate(8deg); } }
  .pill {
    background: var(--mint); padding: 8px 16px; border: 3px solid var(--ink); border-radius: 99px;
    font-weight: 700; font-size: 13px; box-shadow: 4px 4px 0 var(--ink);
    transform: rotate(-2deg);
  }

  .hero { padding: 70px 0 30px; position: relative; }
  .hero h1 {
    font-family: 'Bagel Fat One', cursive; font-weight: 400;
    font-size: clamp(96px, 14vw, 200px); line-height: 0.9; letter-spacing: -0.02em;
    color: var(--ink); position: relative; z-index: 2;
    animation: pop 0.7s cubic-bezier(.4,1.6,.6,1) both;
  }
  .hero h1 .a { color: var(--coral); display: inline-block; transform: rotate(-4deg); }
  .hero h1 .b { color: var(--grape); display: inline-block; transform: rotate(3deg); }
  .hero .squiggle {
    position: absolute; bottom: -10px; left: 0; right: 30%; height: 18px;
    background: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 200 20'><path d='M0 10 Q 25 0 50 10 T 100 10 T 150 10 T 200 10' fill='none' stroke='%23ffd84d' stroke-width='6'/></svg>") repeat-x;
    background-size: 200px 20px;
    animation: pop 0.7s cubic-bezier(.4,1.6,.6,1) 0.2s both;
  }
  @keyframes pop { from { opacity: 0; transform: translateY(20px) scale(0.95); } to { opacity: 1; } }

  .stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 22px; margin: 60px 0 42px; }
  .stat {
    border: 4px solid var(--ink); padding: 28px 26px; position: relative;
    box-shadow: 8px 8px 0 var(--ink);
    animation: pop 0.7s cubic-bezier(.4,1.6,.6,1) both;
  }
  .stat:nth-child(1){ background: var(--pink); transform: rotate(-1.5deg); animation-delay: 0.05s; }
  .stat:nth-child(2){ background: var(--sun); transform: rotate(1.5deg); animation-delay: 0.15s; }
  .stat:nth-child(3){ background: var(--mint); transform: rotate(-1deg); animation-delay: 0.25s; }
  .stat .lab { font-weight: 700; font-size: 13px; text-transform: uppercase; letter-spacing: 0.16em; }
  .stat .num { font-family: 'Bagel Fat One', cursive; font-size: 96px; line-height: 1; color: var(--ink); margin-top: 8px; }
  .stat .deco {
    position: absolute; top: -18px; right: -18px;
    width: 40px; height: 40px; background: var(--grape); border: 3px solid var(--ink);
    border-radius: 50%; box-shadow: 4px 4px 0 var(--ink); animation: spin 8s linear infinite;
  }
  .stat:nth-child(2) .deco { background: var(--coral); border-radius: 6px; }
  .stat:nth-child(3) .deco { background: var(--ink); }
  @keyframes spin { to { transform: rotate(360deg); } }

  .lower { display: grid; grid-template-columns: 1.2fr 1fr; gap: 22px; }
  .panel {
    background: var(--cream); border: 4px solid var(--ink); padding: 24px 28px; box-shadow: 8px 8px 0 var(--grape);
    animation: pop 0.7s cubic-bezier(.4,1.6,.6,1) 0.35s both;
  }
  .panel.b { box-shadow: 8px 8px 0 var(--coral); animation-delay: 0.45s; }
  .panel h2 { font-family: 'Caprasimo', serif; font-size: 26px; margin-bottom: 16px; }
  .panel h2 em { color: var(--grape); font-style: italic; }
  .row { display: flex; align-items: center; gap: 14px; padding: 12px 0; border-bottom: 2px dotted var(--ink); }
  .row:last-child { border-bottom: none; }
  .row .nm { font-weight: 700; font-size: 18px; flex: 0 0 130px; }
  .row .bar { flex: 1; height: 18px; background: var(--cream); border: 2px solid var(--ink); position: relative; border-radius: 99px; overflow: hidden; }
  .row .bar::after { content: ''; position: absolute; left: 0; top: 0; bottom: 0; width: var(--w, 50%); background: var(--coral); }
  .row.mint .bar::after { background: var(--mint); }
  .row.sun .bar::after { background: var(--sun); }
  .row.grape .bar::after { background: var(--grape); }
  .row .v { font-family: 'Bagel Fat One', cursive; font-size: 28px; min-width: 36px; text-align: right; }

  .actions { margin-top: 56px; display: flex; gap: 18px; align-items: center; flex-wrap: wrap; }
  .btn {
    font-family: 'Caprasimo', serif; font-size: 22px; padding: 14px 26px; text-decoration: none;
    border: 4px solid var(--ink); color: var(--ink); box-shadow: 6px 6px 0 var(--ink);
    transition: all 0.12s;
  }
  .btn.pink { background: var(--pink); }
  .btn.mint { background: var(--mint); }
  .btn:hover { transform: translate(-3px,-3px); box-shadow: 9px 9px 0 var(--ink); }
  .footer-mark { font-weight: 700; font-size: 12px; letter-spacing: 0.2em; text-transform: uppercase; opacity: 0.6; margin-left: auto; }
</style>
</head>
<body>
<div class="container">
  <header class="top">
    <div class="logo"><span class="blob"></span> Helpdesk!</div>
    <span class="pill">✦ admin · live</span>
  </header>
  <section class="hero">
    <h1>Forty<span class="a">·</span><span class="b">four!</span></h1>
    <div class="squiggle"></div>
  </section>
  <section class="stats">
    <div class="stat"><span class="deco"></span><div class="lab">Open</div><div class="num">12</div></div>
    <div class="stat"><span class="deco"></span><div class="lab">In progress</div><div class="num">07</div></div>
    <div class="stat"><span class="deco"></span><div class="lab">Closed</div><div class="num">25</div></div>
  </section>
  <section class="lower">
    <div class="panel">
      <h2>By <em>category</em></h2>
      <div class="row"><span class="nm">Software</span><span class="bar" style="--w:100%"></span><span class="v">18</span></div>
      <div class="row mint"><span class="nm">Hardware</span><span class="bar" style="--w:61%"></span><span class="v">11</span></div>
      <div class="row sun"><span class="nm">Network</span><span class="bar" style="--w:44%"></span><span class="v">8</span></div>
      <div class="row grape"><span class="nm">Access</span><span class="bar" style="--w:38%"></span><span class="v">7</span></div>
    </div>
    <div class="panel b">
      <h2>By <em>priority</em></h2>
      <div class="row"><span class="nm">High</span><span class="bar" style="--w:41%"></span><span class="v">9</span></div>
      <div class="row mint"><span class="nm">Medium</span><span class="bar" style="--w:100%"></span><span class="v">22</span></div>
      <div class="row sun"><span class="nm">Low</span><span class="bar" style="--w:59%"></span><span class="v">13</span></div>
    </div>
  </section>
  <div class="actions">
    <a class="btn pink" href="/admin/tickets">→ All tickets</a>
    <a class="btn mint" href="/admin/users">→ Users</a>
    <span class="footer-mark">Design 08 · Bagel Fat One + Caprasimo + Quicksand</span>
  </div>
</div>
</body>
</html>
