<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>NCT · Calm Waters</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=Manrope:wght@200;300;400;500;600&family=Cairo:wght@300;500;700&display=swap" rel="stylesheet">
<style>
  :root { --deep:#021425; --mid:#093155; --shallow:#0d6494; --foam:#a8e1ff; --sand:#f6e9c5; --ink:#f3f9ff; --soft:#a6c3d8; --warm:#ffb78a; }
  * { margin:0; padding:0; box-sizing:border-box; }
  body {
    background: var(--deep); color: var(--ink);
    font-family: 'Manrope', sans-serif; font-weight:300;
    -webkit-font-smoothing: antialiased; min-height:100vh; overflow-x:hidden;
    background-image:
      radial-gradient(ellipse 70% 60% at 14% 10%, rgba(168,225,255,0.16), transparent 60%),
      radial-gradient(ellipse 50% 50% at 88% 18%, rgba(255,183,138,0.14), transparent 60%),
      radial-gradient(ellipse 90% 70% at 50% 100%, rgba(13,100,148,0.4), transparent 65%),
      linear-gradient(180deg, var(--deep) 0%, var(--mid) 60%, #062340 100%);
  }
  body::after {
    content:''; position:fixed; inset:0; pointer-events:none; z-index:1;
    background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='280' height='280'><filter id='n'><feTurbulence type='fractalNoise' baseFrequency='0.85' numOctaves='2'/></filter><rect width='100%' height='100%' filter='url(%23n)' opacity='0.20'/></svg>");
    mix-blend-mode: overlay; opacity:0.35;
  }
  .serif { font-family:'Instrument Serif',serif; font-weight:400; }
  .ar { font-family:'Cairo',sans-serif; }

  .container { max-width:1280px; margin:0 auto; padding:36px 56px 80px; position:relative; z-index:10; }

  .top {
    display:flex; justify-content:space-between; align-items:center;
    padding:14px 24px; backdrop-filter: blur(22px) saturate(160%);
    background: rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.12); border-radius:999px;
  }
  .top .nm { display:flex; align-items:center; gap:14px; font-size:14px; }
  .top .nm .orb {
    width:22px; height:22px; border-radius:50%;
    background: radial-gradient(circle at 30% 30%, var(--foam), var(--shallow) 60%, transparent 75%);
    box-shadow: 0 0 18px rgba(168,225,255,0.6);
  }
  .top .nm .ar { color: var(--soft); font-size:12px; }
  .top .right { display:flex; gap:24px; font-size:12px; color:var(--soft); letter-spacing:0.16em; text-transform:uppercase; }

  .hero { padding:110px 0 60px; max-width:980px; }
  .kicker { font-size:12px; color: var(--foam); letter-spacing:0.32em; text-transform:uppercase; margin-bottom:24px; animation: fade 1s ease both; }
  h1 {
    font-family:'Instrument Serif',serif; font-weight:400;
    font-size:clamp(60px,8vw,124px); line-height:1.0; letter-spacing:-0.025em;
    animation: fade 1.2s cubic-bezier(.2,.7,.2,1) 0.1s both;
  }
  h1 em { font-style:italic; color: var(--warm); }
  h1 .num { color: var(--foam); }
  .lede { margin-top:24px; max-width:600px; font-size:17px; line-height:1.6; color: var(--soft); animation: fade 1.2s cubic-bezier(.2,.7,.2,1) 0.25s both; }
  .lede .ar { color: var(--foam); font-style:normal; font-size:16px; display:block; margin-top:8px; }
  @keyframes fade { from { opacity:0; transform: translateY(14px); } to { opacity:1; transform:none; } }

  .containers { display:grid; grid-template-columns: repeat(3,1fr); gap:18px; margin: 56px 0 24px; }
  .glass {
    backdrop-filter: blur(28px) saturate(150%);
    background: linear-gradient(150deg, rgba(255,255,255,0.10), rgba(255,255,255,0.04));
    border:1px solid rgba(255,255,255,0.14); border-radius:28px;
    box-shadow: 0 1px 0 rgba(255,255,255,0.18) inset, 0 30px 60px -20px rgba(0,0,0,0.5);
    position:relative; overflow:hidden;
  }
  .glass::before {
    content:''; position:absolute; inset:0; pointer-events:none; opacity:0.5;
    background: radial-gradient(ellipse at top left, rgba(255,255,255,0.16), transparent 60%);
  }
  .stat { padding:30px 28px 32px; animation: fade 1s cubic-bezier(.2,.7,.2,1) both; position:relative; }
  .stat:nth-child(1){ animation-delay:0.4s; }
  .stat:nth-child(2){ animation-delay:0.5s; }
  .stat:nth-child(3){ animation-delay:0.6s; }
  .stat .lab { font-size:11px; color:var(--soft); letter-spacing:0.28em; text-transform:uppercase; }
  .stat .ar { font-family:'Cairo'; font-size:13px; color:var(--soft); margin-top:2px; }
  .stat .num { font-family:'Instrument Serif'; font-size:96px; line-height:1; margin-top:14px; font-weight:400; }
  .stat .meta { margin-top:14px; font-size:13px; color:var(--soft); font-style:italic; font-family:'Instrument Serif'; }
  .stat .pill {
    position:absolute; top:22px; right:22px;
    font-size:10px; padding:5px 12px; border-radius:999px; letter-spacing:0.18em; text-transform:uppercase;
  }
  .pill.foam { background: rgba(168,225,255,0.18); color: var(--foam); border:1px solid rgba(168,225,255,0.35); }
  .pill.warm { background: rgba(255,183,138,0.18); color: var(--warm); border:1px solid rgba(255,183,138,0.35); }
  .pill.sand { background: rgba(246,233,197,0.18); color: var(--sand); border:1px solid rgba(246,233,197,0.32); }

  .pair { display:grid; grid-template-columns: 1.4fr 1fr; gap:18px; margin-top:18px; }
  .panel { padding:30px 32px; }
  .panel h3 {
    font-size:11px; color:var(--soft); text-transform:uppercase; letter-spacing:0.28em; margin-bottom:24px;
    display:flex; justify-content:space-between; align-items:center;
  }
  .panel h3 .ar { font-family:'Cairo'; font-size:12px; color:var(--foam); letter-spacing:0; }
  .row { display:grid; grid-template-columns: 160px 1fr 60px; align-items:center; gap:18px; padding:14px 0; border-bottom:1px solid rgba(255,255,255,0.08); }
  .row:last-child { border-bottom:none; }
  .row .nm { font-family:'Instrument Serif'; font-size:22px; }
  .row .nm small { font-family:'Cairo'; display:block; font-size:13px; color:var(--soft); margin-top:2px; }
  .row .bar { height:6px; background: rgba(255,255,255,0.08); border-radius:99px; overflow:hidden; }
  .row .bar::after { content:''; display:block; height:100%; width: var(--w,50%); background: linear-gradient(90deg, var(--foam), var(--warm)); border-radius:99px; }
  .row .v { font-family:'Instrument Serif'; font-size:24px; text-align:right; color:var(--soft); }

  .actions { padding-top:36px; display:flex; align-items:center; gap:14px; flex-wrap:wrap; }
  .btn {
    font-size:14px; padding:14px 24px; border-radius:999px; text-decoration:none;
    background: rgba(255,255,255,0.10); border:1px solid rgba(255,255,255,0.18); color: var(--ink);
    backdrop-filter: blur(18px); transition: all 0.18s;
  }
  .btn.primary { background: var(--ink); color: var(--deep); border-color: var(--ink); }
  .btn:hover { transform: translateY(-1px); }
  .stamp { margin-left:auto; font-size:11px; color:var(--soft); letter-spacing:0.22em; text-transform:uppercase; opacity:0.7; }
</style>
</head>
<body>
<div class="container">
  <header class="top">
    <div class="nm"><span class="orb"></span> <span>National Container Terminal · <span class="ar">الشركة الوطنية للحاويات</span></span></div>
    <div class="right"><span>Berth 04</span><span>14:22 AST</span></div>
  </header>

  <section class="hero">
    <div class="kicker">— calm waters this afternoon —</div>
    <h1>You have <span class="num">44</span> shipments at the terminal — <em>twelve</em> still on the dock.</h1>
    <p class="lede">Software is the heaviest cargo this week, and high-priority lots remain comfortably below average. Nothing is on fire. Floating cards below show today's manifest.<span class="ar">٤٤ تذكرة في الميناء — ١٢ ما زالت تنتظر.</span></p>
  </section>

  <section class="containers">
    <div class="stat glass">
      <span class="pill foam">in yard</span>
      <div class="lab">Open · <span class="ar">في الساحة</span></div>
      <div class="num">12</div>
      <div class="meta">awaiting an agent</div>
    </div>
    <div class="stat glass">
      <span class="pill warm">in transit</span>
      <div class="lab">In progress · <span class="ar">قيد المعالجة</span></div>
      <div class="num">07</div>
      <div class="meta">currently in hand</div>
    </div>
    <div class="stat glass">
      <span class="pill sand">cleared</span>
      <div class="lab">Closed · <span class="ar">تم الإنجاز</span></div>
      <div class="num">25</div>
      <div class="meta">resolved this week</div>
    </div>
  </section>

  <section class="pair">
    <div class="panel glass">
      <h3>— Manifest by category <span class="ar">الفئات</span></h3>
      <div class="row"><span class="nm">Software <small>برمجيات</small></span><span class="bar" style="--w:100%"></span><span class="v">18</span></div>
      <div class="row"><span class="nm">Hardware <small>أجهزة</small></span><span class="bar" style="--w:61%"></span><span class="v">11</span></div>
      <div class="row"><span class="nm">Network <small>شبكة</small></span><span class="bar" style="--w:44%"></span><span class="v">08</span></div>
      <div class="row"><span class="nm">Access request <small>طلب وصول</small></span><span class="bar" style="--w:38%"></span><span class="v">07</span></div>
    </div>
    <div class="panel glass">
      <h3>— By priority <span class="ar">الأولوية</span></h3>
      <div class="row"><span class="nm">High <small>عاجل</small></span><span class="bar" style="--w:41%"></span><span class="v">09</span></div>
      <div class="row"><span class="nm">Medium <small>متوسط</small></span><span class="bar" style="--w:100%"></span><span class="v">22</span></div>
      <div class="row"><span class="nm">Low <small>منخفض</small></span><span class="bar" style="--w:59%"></span><span class="v">13</span></div>
    </div>
  </section>

  <div class="actions">
    <a class="btn primary" href="/admin/tickets">View all tickets →</a>
    <a class="btn" href="/admin/users">Manage users</a>
    <span class="stamp">NCT/07 · Instrument Serif + Manrope · Calm Waters</span>
  </div>
</div>
</body>
</html>
