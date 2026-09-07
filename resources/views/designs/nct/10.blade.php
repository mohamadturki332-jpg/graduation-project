<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>NCT · Annual Report Style</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300;0,9..144,400;0,9..144,600;0,9..144,700;1,9..144,400&family=Inter+Tight:wght@300;400;500;600&family=Cairo:wght@300;500;700&display=swap" rel="stylesheet">
<style>
  :root { --paper:#f6f1e8; --ink:#0e2924; --teal:#13433d; --teal-d:#0a2e2a; --gold:#b88a3c; --gold-d:#8c6826; --soft:#5a6b67; --line:#d3c9b6; }
  * { margin:0; padding:0; box-sizing:border-box; }
  body {
    background: var(--paper); color: var(--ink);
    font-family: 'Inter Tight', sans-serif; font-weight:400;
    -webkit-font-smoothing: antialiased; min-height:100vh;
    background-image:
      url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='280' height='280'><filter id='n'><feTurbulence type='fractalNoise' baseFrequency='0.85' numOctaves='2'/></filter><rect width='100%' height='100%' filter='url(%23n)' opacity='0.04'/></svg>");
  }
  .serif { font-family:'Fraunces',serif; }
  .ar { font-family:'Cairo',sans-serif; }

  .container { max-width:1280px; margin:0 auto; padding:30px 56px 80px; }

  .top {
    display:flex; justify-content:space-between; align-items:center;
    padding-bottom:18px; border-bottom:1px solid var(--line);
  }
  .brand { display:flex; align-items:center; gap:14px; }
  .brand .seal {
    width:54px; height:54px; background: var(--teal); color: var(--gold);
    display:flex; align-items:center; justify-content:center;
    font-family:'Fraunces',serif; font-weight:700; font-size:30px; font-style:italic;
    border-radius:4px; position:relative;
  }
  .brand .seal::after {
    content:''; position:absolute; inset:4px; border:1px solid var(--gold); border-radius:2px;
  }
  .brand h2 { font-family:'Fraunces'; font-weight:600; font-size:18px; line-height:1; letter-spacing:-0.005em; }
  .brand .ar { font-size:13px; color: var(--soft); margin-top:4px; display:block; }
  .meta { font-size:12px; line-height:1.7; color: var(--soft); text-align:right; letter-spacing:0.04em; }
  .meta b { color: var(--teal); font-family:'Fraunces',serif; font-weight:600; font-style:italic; }

  .titleblock {
    padding:80px 0 60px; display:grid; grid-template-columns: 1.6fr 1fr; gap:60px; align-items:end;
    border-bottom:1px solid var(--line);
  }
  .titleblock .kicker { font-size:11px; letter-spacing:0.32em; text-transform:uppercase; color: var(--gold-d); margin-bottom:20px; animation: rise 1s ease both; }
  .titleblock h1 {
    font-family:'Fraunces',serif; font-weight:300; font-style:italic;
    font-size:clamp(56px, 7.4vw, 116px); line-height:1.0; letter-spacing:-0.025em; color: var(--teal);
    animation: rise 1s cubic-bezier(.2,.7,.2,1) 0.1s both;
  }
  .titleblock h1 strong { font-family:'Fraunces',serif; font-weight:600; font-style:normal; color: var(--gold-d); }
  .titleblock .deck {
    font-family:'Fraunces',serif; font-weight:400; font-size:18px; line-height:1.55; color: var(--soft);
    border-left:2px solid var(--gold); padding-left:22px;
    animation: rise 1s cubic-bezier(.2,.7,.2,1) 0.25s both;
  }
  .titleblock .deck .ar { display:block; color: var(--teal); font-style:normal; font-family:'Cairo'; font-size:15px; margin-top:10px; }
  @keyframes rise { from { opacity:0; transform: translateY(14px); } to { opacity:1; transform:none; } }

  .stats {
    display:grid; grid-template-columns: repeat(3,1fr); gap:0;
    border-bottom:1px solid var(--line);
  }
  .stat {
    padding:48px 36px 50px; border-right:1px solid var(--line); position:relative;
    animation: rise 0.9s cubic-bezier(.2,.7,.2,1) both;
  }
  .stat:nth-child(1){ animation-delay:0.35s; }
  .stat:nth-child(2){ animation-delay:0.45s; }
  .stat:nth-child(3){ animation-delay:0.55s; }
  .stat:last-child { border-right:none; }
  .stat .label { font-size:10px; letter-spacing:0.32em; text-transform:uppercase; color: var(--gold-d); }
  .stat .ar { font-family:'Cairo'; font-size:13px; color: var(--soft); margin-top:2px; }
  .stat .num {
    font-family:'Fraunces',serif; font-weight:300; font-size:140px; line-height:1;
    color: var(--teal); margin-top:18px; letter-spacing:-0.04em; font-feature-settings: 'lnum';
  }
  .stat .delta { margin-top:16px; font-size:13px; color:var(--soft); display:flex; align-items:center; gap:8px; }
  .stat .delta b { color: var(--teal); font-weight:600; }
  .stat .arrow { color: var(--gold-d); font-family:'Fraunces'; font-style:italic; }

  .pair { display:grid; grid-template-columns: 1fr 1fr; gap:60px; padding:60px 0 40px; border-bottom:1px solid var(--line); }
  .panel h3 {
    font-family:'Fraunces'; font-weight:600; font-style:italic; font-size:26px; color: var(--teal);
    border-bottom:1px solid var(--teal); padding-bottom:10px; margin-bottom:20px;
    display:flex; justify-content:space-between; align-items:baseline;
  }
  .panel h3 small { font-family:'Cairo'; font-size:14px; color: var(--gold-d); font-style:normal; font-weight:500; }
  .row { display:grid; grid-template-columns: 180px 1fr 60px; gap:20px; align-items:center; padding:14px 0; border-bottom:1px solid var(--line); }
  .row:last-child { border-bottom:none; }
  .row .nm { font-family:'Fraunces'; font-weight:400; font-size:19px; color: var(--ink); }
  .row .nm small { font-family:'Cairo'; font-weight:500; font-size:13px; color: var(--soft); display:block; margin-top:2px; }
  .row .bar { height:3px; background: var(--line); position:relative; }
  .row .bar::after {
    content:''; position:absolute; left:0; top:0; bottom:0; width: var(--w,50%);
    background: linear-gradient(90deg, var(--teal), var(--gold));
  }
  .row .v { font-family:'Fraunces',serif; font-weight:400; font-size:24px; text-align:right; color: var(--teal); }

  .actions {
    padding:36px 0 0; display:flex; gap:18px; align-items:center; flex-wrap:wrap;
  }
  .btn {
    font-family:'Inter Tight',sans-serif; font-weight:500; font-size:14px;
    padding:14px 26px; text-decoration:none; letter-spacing:0.04em;
    border:1px solid var(--teal); color: var(--teal); background: transparent;
    transition: all 0.18s;
  }
  .btn.primary { background: var(--teal); color: var(--paper); }
  .btn.primary:hover { background: var(--teal-d); }
  .btn:hover { background: var(--teal); color: var(--paper); }
  .stamp { margin-left:auto; font-family:'Fraunces'; font-style:italic; font-size:13px; color: var(--soft); }
</style>
</head>
<body>
<div class="container">
  <header class="top">
    <div class="brand">
      <div class="seal">N</div>
      <div>
        <h2>National Container Terminal</h2>
        <span class="ar">الشركة الوطنية للحاويات · King Abdulaziz Port, Dammam</span>
      </div>
    </div>
    <div class="meta">
      <b>Q2 · 2026 · ADMIN OVERVIEW</b><br>
      Issued 07 May 2026 · 14:22 AST<br>
      Berth 04 · Reference NCT-HD-142
    </div>
  </header>

  <section class="titleblock">
    <div>
      <div class="kicker">— Helpdesk performance · second quarter</div>
      <h1>A quiet quarter at the quay; <strong>forty-four</strong> matters under our care.</h1>
    </div>
    <p class="deck">Twelve tickets remain unassigned, seven are presently in hand, and twenty-five have been resolved over the trailing seven days &mdash; a twelve per cent improvement on the prior period. Software continues to drive the heaviest demand.<span class="ar">٤٤ تذكرة قيد المتابعة · ١٢ بانتظار التعيين</span></p>
  </section>

  <section class="stats">
    <div class="stat">
      <div class="label">— Open</div>
      <div class="ar">في الساحة</div>
      <div class="num">12</div>
      <div class="delta"><span class="arrow">↘</span><b>−2</b> versus last week</div>
    </div>
    <div class="stat">
      <div class="label">— In progress</div>
      <div class="ar">قيد المعالجة</div>
      <div class="num">07</div>
      <div class="delta"><span class="arrow">→</span><b>flat</b> versus last week</div>
    </div>
    <div class="stat">
      <div class="label">— Closed · 7d</div>
      <div class="ar">تم الإنجاز</div>
      <div class="num">25</div>
      <div class="delta"><span class="arrow">↗</span><b>+12%</b> versus last week</div>
    </div>
  </section>

  <section class="pair">
    <div class="panel">
      <h3>Tickets by category <small>الفئات</small></h3>
      <div class="row"><span class="nm">Software <small>برمجيات</small></span><span class="bar" style="--w:100%"></span><span class="v">18</span></div>
      <div class="row"><span class="nm">Hardware <small>أجهزة</small></span><span class="bar" style="--w:61%"></span><span class="v">11</span></div>
      <div class="row"><span class="nm">Network <small>شبكة</small></span><span class="bar" style="--w:44%"></span><span class="v">08</span></div>
      <div class="row"><span class="nm">Access request <small>طلب وصول</small></span><span class="bar" style="--w:38%"></span><span class="v">07</span></div>
    </div>
    <div class="panel">
      <h3>By priority <small>الأولوية</small></h3>
      <div class="row"><span class="nm">High <small>عاجل</small></span><span class="bar" style="--w:41%"></span><span class="v">09</span></div>
      <div class="row"><span class="nm">Medium <small>متوسط</small></span><span class="bar" style="--w:100%"></span><span class="v">22</span></div>
      <div class="row"><span class="nm">Low <small>منخفض</small></span><span class="bar" style="--w:59%"></span><span class="v">13</span></div>
    </div>
  </section>

  <div class="actions">
    <a class="btn primary" href="/admin/tickets">View ticket index →</a>
    <a class="btn" href="/admin/users">Manage users</a>
    <span class="stamp">NCT/10 · Fraunces + Inter Tight + Cairo · Annual Report</span>
  </div>
</div>
</body>
</html>
