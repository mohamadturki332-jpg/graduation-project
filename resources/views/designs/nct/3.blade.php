<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>NCT · الميناء — Heritage</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Amiri:ital,wght@0,400;0,700;1,400&family=Cairo:wght@300;400;600;800;900&family=Marcellus&display=swap" rel="stylesheet">
<style>
  :root { --sand:#e8dcc4; --sand-d:#d4c39a; --najdi:#9c2820; --teal:#137a73; --gold:#c08a3e; --palm:#3a5a2a; --ink:#2b1d10; }
  * { margin:0; padding:0; box-sizing:border-box; }
  body {
    background: var(--sand); color: var(--ink);
    font-family: 'Cairo', sans-serif; font-weight:400;
    min-height:100vh; padding:0; direction: rtl;
    background-image:
      url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='80' height='80' viewBox='0 0 80 80'><g fill='none' stroke='%239c2820' stroke-width='1' opacity='0.10'><path d='M40 0 L80 40 L40 80 L0 40 Z'/><path d='M40 20 L60 40 L40 60 L20 40 Z'/><circle cx='40' cy='40' r='6'/></g></svg>"),
      url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='240' height='240'><filter id='n'><feTurbulence type='fractalNoise' baseFrequency='0.85' numOctaves='2'/></filter><rect width='100%' height='100%' filter='url(%23n)' opacity='0.05'/></svg>");
    background-size: 80px 80px, 240px 240px;
  }
  .lat { font-family:'Marcellus',serif; direction:ltr; }
  .lat-cairo { font-family:'Cairo',sans-serif; direction:ltr; }
  .container { max-width:1240px; margin:0 auto; padding:36px 48px 60px; }

  .top {
    display:flex; justify-content:space-between; align-items:center; padding-bottom:18px;
    border-bottom:3px double var(--najdi);
  }
  .brand { display:flex; align-items:center; gap:14px; }
  .brand .seal {
    width:62px; height:62px; background: var(--najdi); color: var(--sand);
    clip-path: polygon(50% 0, 100% 25%, 100% 75%, 50% 100%, 0 75%, 0 25%);
    display:flex; align-items:center; justify-content:center;
    font-family:'Amiri',serif; font-weight:700; font-size:32px;
  }
  .brand h2 { font-family:'Cairo',sans-serif; font-weight:800; font-size:22px; color:var(--najdi); line-height:1; }
  .brand .en { font-family:'Marcellus',serif; font-size:13px; letter-spacing:0.16em; color:var(--ink); margin-top:6px; direction:ltr; text-align:right; }
  .meta { font-family:'Cairo'; font-size:13px; color: var(--ink); text-align:left; line-height:1.6; }
  .meta .lat-cairo { display:block; color: var(--najdi); font-weight:600; }

  .hero { padding:60px 0 30px; text-align:center; position:relative; }
  .hero::before, .hero::after {
    content:''; position:absolute; top:60px; width:90px; height:1px; background: var(--najdi);
  }
  .hero::before { right:0; }
  .hero::after { left:0; transform: scaleX(-1); }
  .hero .kicker {
    display:inline-block; font-family:'Cairo'; font-weight:600; font-size:13px; padding:6px 16px;
    border:1.5px solid var(--najdi); color:var(--najdi); letter-spacing:0.06em; margin-bottom:20px;
  }
  .hero h1 {
    font-family:'Amiri',serif; font-weight:700;
    font-size:clamp(56px, 7.5vw, 110px); line-height:1.1; color:var(--najdi);
    animation: rise 1s ease both;
  }
  .hero .en {
    font-family:'Marcellus',serif; direction:ltr; display:inline-block; margin-top:18px;
    font-size:21px; color:var(--ink); letter-spacing:0.04em;
    border-top:1px solid var(--najdi); border-bottom:1px solid var(--najdi); padding:8px 26px;
    animation: rise 1s ease 0.15s both;
  }
  @keyframes rise { from { opacity:0; transform: translateY(14px); } to { opacity:1; transform:none; } }

  .pillars { display:grid; grid-template-columns: repeat(3, 1fr); gap:22px; margin-top:50px; }
  .pillar {
    background: var(--sand-d); border:2px solid var(--najdi); padding:30px 26px 28px; text-align:center; position:relative;
    animation: rise 1s ease both;
  }
  .pillar:nth-child(1){ animation-delay:0.2s; }
  .pillar:nth-child(2){ animation-delay:0.3s; }
  .pillar:nth-child(3){ animation-delay:0.4s; }
  .pillar::before {
    content:''; position:absolute; top:-1px; right:-1px; bottom:-1px; left:-1px;
    border:1px dashed var(--najdi); pointer-events:none;
  }
  .pillar .arch {
    width:50px; height:60px; margin:0 auto 12px; background: var(--najdi);
    clip-path: path('M 25 0 Q 50 0 50 25 L 50 60 L 0 60 L 0 25 Q 0 0 25 0 Z');
    border-radius: 25px 25px 0 0;
  }
  .pillar .num {
    font-family:'Cairo',sans-serif; font-weight:900; font-size:96px; line-height:1; color:var(--najdi);
  }
  .pillar.t .num { color: var(--teal); }
  .pillar.t .arch { background: var(--teal); }
  .pillar.g .num { color: var(--gold); }
  .pillar.g .arch { background: var(--gold); }
  .pillar .ar { font-family:'Cairo'; font-weight:600; font-size:18px; margin-top:8px; }
  .pillar .en { font-family:'Marcellus',serif; direction:ltr; font-size:13px; letter-spacing:0.18em; color:var(--ink); margin-top:6px; }

  .panels { display:grid; grid-template-columns: 1fr 1fr; gap:22px; margin-top:36px; }
  .panel {
    background: linear-gradient(180deg, var(--sand) 0%, var(--sand-d) 100%);
    border:2px solid var(--najdi); padding:24px 26px; position:relative;
  }
  .panel::after {
    content:''; position:absolute; top:14px; right:14px; bottom:14px; left:14px; border:1px solid var(--najdi); opacity:0.3; pointer-events:none;
  }
  .panel h3 {
    font-family:'Amiri',serif; font-weight:700; font-size:24px; color: var(--najdi);
    border-bottom:2px solid var(--najdi); padding-bottom:8px; margin-bottom:12px;
  }
  .panel h3 small { font-family:'Marcellus',serif; direction:ltr; display:inline-block; font-size:14px; color:var(--ink); margin-left:8px; letter-spacing:0.16em; }
  .row { display:flex; justify-content:space-between; align-items:baseline; padding:9px 0; border-bottom:1px dotted rgba(43,29,16,0.4); }
  .row:last-child { border-bottom:none; }
  .row .nm { font-family:'Cairo'; font-weight:600; font-size:18px; }
  .row .nm small { font-family:'Marcellus',serif; direction:ltr; font-size:13px; color:var(--ink); margin-right:6px; letter-spacing:0.06em; }
  .row .v { font-family:'Cairo',sans-serif; font-weight:800; font-size:24px; color:var(--najdi); }

  .actions {
    margin-top:40px; display:flex; gap:16px; align-items:center; justify-content:flex-end;
    padding-top:20px; border-top:3px double var(--najdi); flex-wrap:wrap;
  }
  .btn {
    font-family:'Cairo'; font-weight:700; font-size:15px;
    padding:14px 26px; text-decoration:none; border:2px solid var(--najdi);
    color:var(--najdi); background:var(--sand);
    transition: all 0.15s;
  }
  .btn.primary { background: var(--najdi); color: var(--sand); }
  .btn .lat-cairo { font-family:'Marcellus',serif; direction:ltr; display:block; font-size:11px; letter-spacing:0.2em; margin-top:2px; }
  .btn:hover { background: var(--gold); color: var(--ink); border-color: var(--gold); }
  .stamp { margin-right:auto; font-family:'Cairo'; font-size:13px; color:var(--ink); opacity:0.6; }
</style>
</head>
<body>
<div class="container">
  <header class="top">
    <div class="brand">
      <div class="seal">ن</div>
      <div>
        <h2>الشركة الوطنية للحاويات</h2>
        <span class="en">National Container Terminal · Dammam</span>
      </div>
    </div>
    <div class="meta">
      <span class="lat-cairo">07 MAY 2026 · 14:22 AST</span>
      ميناء الملك عبدالعزيز · الرصيف الرابع
    </div>
  </header>

  <section class="hero">
    <div class="kicker">لوحة الإدارة · Admin Dashboard</div>
    <h1>أربعٌ وأربعون تذكرة على الميناء.</h1>
    <div class="en">FORTY-FOUR TICKETS — TWELVE STILL AWAIT BERTHING</div>
  </section>

  <section class="pillars">
    <div class="pillar">
      <div class="arch"></div>
      <div class="num">١٢</div>
      <div class="ar">في الساحة</div>
      <div class="en">OPEN · 12</div>
    </div>
    <div class="pillar t">
      <div class="arch"></div>
      <div class="num">٧</div>
      <div class="ar">قيد المعالجة</div>
      <div class="en">IN PROGRESS · 07</div>
    </div>
    <div class="pillar g">
      <div class="arch"></div>
      <div class="num">٢٥</div>
      <div class="ar">تم الإنجاز</div>
      <div class="en">CLOSED · 25</div>
    </div>
  </section>

  <section class="panels">
    <div class="panel">
      <h3>الفئات <small>BY CATEGORY</small></h3>
      <div class="row"><span class="nm">برمجيات <small>SOFTWARE</small></span><span class="v">١٨</span></div>
      <div class="row"><span class="nm">أجهزة <small>HARDWARE</small></span><span class="v">١١</span></div>
      <div class="row"><span class="nm">شبكة <small>NETWORK</small></span><span class="v">٨</span></div>
      <div class="row"><span class="nm">طلب وصول <small>ACCESS</small></span><span class="v">٧</span></div>
    </div>
    <div class="panel">
      <h3>الأولوية <small>BY PRIORITY</small></h3>
      <div class="row"><span class="nm">عاجل <small>HIGH</small></span><span class="v">٩</span></div>
      <div class="row"><span class="nm">متوسط <small>MEDIUM</small></span><span class="v">٢٢</span></div>
      <div class="row"><span class="nm">منخفض <small>LOW</small></span><span class="v">١٣</span></div>
    </div>
  </section>

  <div class="actions">
    <span class="stamp">NCT/03 · Amiri + Cairo + Marcellus · Heritage</span>
    <a class="btn primary" href="/admin/tickets">جميع التذاكر <span class="lat-cairo">ALL TICKETS →</span></a>
    <a class="btn" href="/admin/users">إدارة المستخدمين <span class="lat-cairo">USERS →</span></a>
  </div>
</div>
</body>
</html>
