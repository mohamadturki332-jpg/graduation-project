<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>NCT · Helpdesk Operations · ميناء الملك عبدالعزيز</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Saira+Condensed:wght@300;400;500;600;700;800&family=Public+Sans:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500;700&family=Cairo:wght@400;600;700;900&display=swap" rel="stylesheet">
<style>
  :root {
    --navy:#001a36;
    --navy-2:#062a4f;
    --navy-3:#0a3c70;
    --steel:#1a4980;
    --orange:#ff6b00;
    --amber:#ffb300;
    --green:#00875a;
    --pale:#e6edf5;
    --muted:#8aa2bd;
    --line:rgba(138,162,189,0.18);
  }
  * { margin:0; padding:0; box-sizing:border-box; }
  body {
    background: var(--navy); color: var(--pale);
    font-family: 'Public Sans', sans-serif; font-weight: 400;
    -webkit-font-smoothing: antialiased; min-height: 100vh; padding: 0;
    background-image:
      radial-gradient(ellipse 80% 50% at 50% -10%, rgba(255,107,0,0.08), transparent 60%),
      radial-gradient(ellipse 60% 40% at 0% 100%, rgba(0,135,90,0.10), transparent 60%),
      linear-gradient(0deg, var(--navy) 0%, var(--navy-2) 100%);
    overflow-x: hidden;
  }
  body::before {
    content: ''; position: fixed; inset: 0; pointer-events: none; z-index: 0;
    background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='280' height='280'><filter id='n'><feTurbulence type='fractalNoise' baseFrequency='0.85' numOctaves='2'/></filter><rect width='100%' height='100%' filter='url(%23n)' opacity='0.18'/></svg>");
    mix-blend-mode: overlay; opacity: 0.35;
  }

  /* ─────────── port grid background pattern ─────────── */
  .port-grid {
    position: fixed; inset: 0; z-index: 0; pointer-events: none; opacity: 0.16;
    background-image:
      linear-gradient(0deg, transparent 0 calc(100% - 1px), rgba(138,162,189,0.4) 100%),
      linear-gradient(90deg, transparent 0 calc(100% - 1px), rgba(138,162,189,0.25) 100%);
    background-size: 60px 60px, 60px 60px;
  }

  .display { font-family: 'Saira Condensed', sans-serif; font-weight: 700; letter-spacing: 0.005em; }
  .mono { font-family: 'JetBrains Mono', monospace; }
  .ar { font-family: 'Cairo', sans-serif; }

  .container { max-width: 1320px; margin: 0 auto; padding: 28px 48px 64px; position: relative; z-index: 5; }

  /* ─────────── top status bar ─────────── */
  .topbar {
    display: flex; align-items: center; justify-content: space-between;
    padding: 14px 22px; background: rgba(6,42,79,0.6); backdrop-filter: blur(14px);
    border: 1px solid var(--line); border-radius: 4px;
    box-shadow: 0 4px 18px rgba(0,0,0,0.25);
    animation: slidein 0.6s ease both;
  }
  .brand { display: flex; align-items: center; gap: 14px; }
  .brand .mark {
    width: 42px; height: 42px; position: relative;
    background: var(--orange);
    clip-path: polygon(0 0, 100% 0, 100% 70%, 80% 100%, 0 100%);
    display: grid; place-items: center;
  }
  .brand .mark::before {
    content: 'N'; font-family: 'Saira Condensed', sans-serif; font-weight: 800;
    font-size: 24px; color: var(--navy); line-height: 1;
  }
  .brand .name { line-height: 1.05; }
  .brand .name .en { font-family: 'Saira Condensed', sans-serif; font-weight: 700; font-size: 18px; letter-spacing: 0.06em; }
  .brand .name .sub { font-family: 'JetBrains Mono', monospace; font-size: 10px; color: var(--muted); letter-spacing: 0.18em; text-transform: uppercase; margin-top: 2px; }
  .brand .ar { font-size: 14px; color: var(--muted); border-left: 1px solid var(--line); padding-left: 14px; margin-left: 4px; }

  .live { display: flex; align-items: center; gap: 22px; font-family: 'JetBrains Mono', monospace; font-size: 11px; letter-spacing: 0.16em; text-transform: uppercase; color: var(--muted); }
  .live .pulse { display: inline-flex; align-items: center; gap: 8px; color: #6dffaf; }
  .live .pulse .dot { width: 8px; height: 8px; background: #6dffaf; border-radius: 50%; box-shadow: 0 0 12px #6dffaf; animation: blink 1.6s ease-in-out infinite; }
  @keyframes blink { 50% { opacity: 0.35; } }
  .live .clock { color: var(--pale); font-weight: 500; }
  .live .berth { color: var(--amber); }

  @keyframes slidein { from { opacity: 0; transform: translateY(-12px); } to { opacity: 1; transform: none; } }

  /* ─────────── hero ─────────── */
  .hero { padding: 64px 0 40px; display: grid; grid-template-columns: 1.6fr 1fr; gap: 40px; align-items: end; }
  .hero .eyebrow {
    font-family: 'JetBrains Mono', monospace;
    font-size: 11px; color: var(--orange); letter-spacing: 0.32em; text-transform: uppercase;
    display: flex; align-items: center; gap: 10px; margin-bottom: 20px;
    animation: fade 0.8s ease 0.1s both;
  }
  .hero .eyebrow::before { content: ''; display: inline-block; width: 28px; height: 2px; background: var(--orange); }
  .hero h1 {
    font-family: 'Saira Condensed', sans-serif; font-weight: 800;
    font-size: clamp(64px, 9vw, 132px); line-height: 0.92; letter-spacing: -0.005em;
    text-transform: uppercase;
    animation: fade 0.9s cubic-bezier(.2,.7,.2,1) 0.15s both;
  }
  .hero h1 .accent { color: var(--orange); }
  .hero h1 .ar-overlay { display: block; font-family: 'Cairo', sans-serif; font-size: 0.18em; color: var(--muted); letter-spacing: 0.04em; margin-top: 10px; font-weight: 600; }
  .hero .lede {
    font-size: 16px; line-height: 1.55; color: var(--muted);
    border-left: 2px solid var(--orange); padding: 6px 0 6px 20px;
    animation: fade 0.9s cubic-bezier(.2,.7,.2,1) 0.3s both;
  }
  .hero .lede strong { color: var(--pale); font-weight: 600; }
  @keyframes fade { from { opacity: 0; transform: translateY(14px); } to { opacity: 1; transform: none; } }

  /* ─────────── containers (status cards) ─────────── */
  .yard { display: grid; grid-template-columns: repeat(3, 1fr); gap: 18px; margin: 28px 0 56px; }
  .container-card {
    position: relative;
    border: 1px solid var(--line); border-top: 0; border-bottom: 0;
    background: var(--navy-2);
    overflow: hidden;
    animation: load 0.7s cubic-bezier(.2,.7,.2,1) both;
  }
  .container-card:nth-child(1) { animation-delay: 0.4s; }
  .container-card:nth-child(2) { animation-delay: 0.5s; }
  .container-card:nth-child(3) { animation-delay: 0.6s; }
  @keyframes load { from { opacity: 0; transform: translateX(-30px); } to { opacity: 1; transform: none; } }

  /* corrugated container side */
  .container-card::before, .container-card::after {
    content: ''; position: absolute; left: 0; right: 0; height: 14px;
    background-image: repeating-linear-gradient(90deg, rgba(0,0,0,0.35) 0 2px, transparent 2px 6px);
  }
  .container-card::before { top: 0; }
  .container-card::after { bottom: 0; }

  .cc-color { background: var(--steel); }
  .cc-orange { background: linear-gradient(180deg, #c14a00, #8a3500); }
  .cc-green { background: linear-gradient(180deg, #006d4a, #014830); }
  .cc-blue { background: linear-gradient(180deg, #114e8c, #0a3768); }

  .cc-head {
    display: flex; justify-content: space-between; align-items: center;
    padding: 24px 22px 14px;
    border-bottom: 1px dashed rgba(255,255,255,0.12);
    margin-top: 14px;
  }
  .cc-code { font-family: 'JetBrains Mono', monospace; font-size: 11px; letter-spacing: 0.12em; color: rgba(255,255,255,0.85); }
  .cc-iso { font-family: 'JetBrains Mono', monospace; font-size: 10px; padding: 2px 7px; background: rgba(0,0,0,0.4); border: 1px solid rgba(255,255,255,0.16); border-radius: 2px; color: rgba(255,255,255,0.7); letter-spacing: 0.1em; }
  .cc-body { padding: 26px 22px 30px; position: relative; }
  .cc-body .label {
    font-family: 'JetBrains Mono', monospace; font-size: 10px; color: rgba(255,255,255,0.65); letter-spacing: 0.28em; text-transform: uppercase; margin-bottom: 6px;
  }
  .cc-body .label .ar { font-family: 'Cairo', sans-serif; font-size: 11px; opacity: 0.7; margin-left: 8px; letter-spacing: 0; }
  .cc-body .num {
    font-family: 'Saira Condensed', sans-serif; font-weight: 800;
    font-size: 124px; line-height: 0.95; color: #fff;
    text-shadow: 0 4px 22px rgba(0,0,0,0.4);
    margin-top: 4px;
  }
  .cc-body .meta { margin-top: 10px; font-family: 'JetBrains Mono', monospace; font-size: 11px; color: rgba(255,255,255,0.65); letter-spacing: 0.06em; }
  .cc-body .stripe {
    position: absolute; right: 22px; bottom: 24px;
    width: 60px; height: 3px; background: rgba(255,255,255,0.5);
  }

  /* ─────────── manifest panels ─────────── */
  .manifest { display: grid; grid-template-columns: 1.45fr 1fr; gap: 22px; }
  .panel {
    background: rgba(6,42,79,0.55); backdrop-filter: blur(8px);
    border: 1px solid var(--line); border-radius: 4px;
    padding: 26px 28px;
    animation: fade 0.8s ease 0.7s both;
  }
  .panel:nth-of-type(2) { animation-delay: 0.8s; }
  .panel-head { display: flex; justify-content: space-between; align-items: center; padding-bottom: 16px; border-bottom: 1px solid var(--line); margin-bottom: 14px; }
  .panel-head .title {
    font-family: 'Saira Condensed', sans-serif; font-weight: 700;
    font-size: 18px; letter-spacing: 0.16em; text-transform: uppercase;
  }
  .panel-head .ar { font-family: 'Cairo', sans-serif; font-size: 14px; color: var(--muted); font-weight: 600; }
  .panel-head .tag {
    font-family: 'JetBrains Mono', monospace; font-size: 10px;
    padding: 4px 10px; border: 1px solid var(--orange); color: var(--orange);
    letter-spacing: 0.18em; text-transform: uppercase;
  }
  .row {
    display: grid; grid-template-columns: 28px 130px 1fr 60px; align-items: center; gap: 16px;
    padding: 13px 0; border-bottom: 1px dashed var(--line);
  }
  .row:last-child { border-bottom: none; }
  .glyph {
    width: 18px; height: 18px; background: var(--steel);
    clip-path: polygon(0 20%, 100% 20%, 100% 80%, 0 80%);
    box-shadow: 0 2px 4px rgba(0,0,0,0.4);
  }
  .row.orange .glyph { background: var(--orange); }
  .row.green .glyph { background: var(--green); }
  .row.amber .glyph { background: var(--amber); }
  .row .nm { font-family: 'Saira Condensed', sans-serif; font-weight: 600; font-size: 19px; letter-spacing: 0.06em; text-transform: uppercase; }
  .row .nm .ar { display: block; font-family: 'Cairo', sans-serif; font-size: 11px; color: var(--muted); font-weight: 400; letter-spacing: 0; text-transform: none; margin-top: 1px; }
  .row .bar { height: 8px; background: rgba(0,0,0,0.35); border-radius: 1px; position: relative; overflow: hidden; }
  .row .bar::after {
    content: ''; position: absolute; left: 0; top: 0; bottom: 0; width: var(--w, 50%);
    background: var(--steel);
    background-image: repeating-linear-gradient(135deg, transparent 0 6px, rgba(0,0,0,0.18) 6px 8px);
  }
  .row.orange .bar::after { background: var(--orange); background-image: repeating-linear-gradient(135deg, transparent 0 6px, rgba(0,0,0,0.18) 6px 8px); }
  .row.green .bar::after { background: var(--green); background-image: repeating-linear-gradient(135deg, transparent 0 6px, rgba(0,0,0,0.18) 6px 8px); }
  .row.amber .bar::after { background: var(--amber); background-image: repeating-linear-gradient(135deg, transparent 0 6px, rgba(0,0,0,0.18) 6px 8px); }
  .row .v { font-family: 'JetBrains Mono', monospace; font-size: 18px; font-weight: 500; color: var(--pale); text-align: right; }

  /* ─────────── action bar ─────────── */
  .actions {
    margin-top: 32px;
    display: flex; flex-wrap: wrap; gap: 14px; align-items: center;
    padding: 18px 22px; border-top: 1px solid var(--line);
    background: rgba(6,42,79,0.45);
  }
  .btn {
    font-family: 'Saira Condensed', sans-serif; font-weight: 700;
    font-size: 14px; letter-spacing: 0.18em; text-transform: uppercase;
    padding: 13px 24px; text-decoration: none;
    border: 1px solid var(--orange); color: var(--orange);
    background: transparent; transition: all 0.16s;
    display: inline-flex; align-items: center; gap: 10px;
  }
  .btn.primary { background: var(--orange); color: var(--navy); }
  .btn:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(255,107,0,0.3); }
  .btn .arrow { font-family: 'JetBrains Mono', monospace; }

  .footer-meta { margin-left: auto; font-family: 'JetBrains Mono', monospace; font-size: 10px; color: var(--muted); letter-spacing: 0.18em; text-transform: uppercase; text-align: right; line-height: 1.6; }
  .footer-meta .ar { font-family: 'Cairo', sans-serif; font-size: 11px; letter-spacing: 0; text-transform: none; color: var(--muted); }

  /* gantry crane silhouette */
  .gantry { position: fixed; bottom: 0; right: -40px; width: 320px; height: 320px; pointer-events: none; z-index: 1; opacity: 0.10; }
</style>
</head>
<body>
<div class="port-grid"></div>

<svg class="gantry" viewBox="0 0 320 320" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="#8aa2bd" stroke-width="2">
  <!-- gantry crane silhouette -->
  <line x1="40" y1="320" x2="40" y2="60"/>
  <line x1="280" y1="320" x2="280" y2="60"/>
  <line x1="20" y1="60" x2="300" y2="60"/>
  <line x1="40" y1="80" x2="280" y2="80"/>
  <line x1="160" y1="80" x2="160" y2="160"/>
  <rect x="140" y="160" width="40" height="22" />
  <line x1="40" y1="220" x2="280" y2="220" stroke-dasharray="4 4"/>
  <line x1="60" y1="60" x2="40" y2="40"/>
  <line x1="80" y1="60" x2="40" y2="20"/>
  <line x1="100" y1="60" x2="60" y2="20"/>
</svg>

<div class="container">
  <!-- TOP BAR -->
  <header class="topbar">
    <div class="brand">
      <div class="mark"></div>
      <div class="name">
        <div class="en">National Container Terminal</div>
        <div class="sub">KAP · Dammam · Helpdesk Operations</div>
      </div>
      <div class="ar">الشركة الوطنية للحاويات</div>
    </div>
    <div class="live">
      <span class="pulse"><span class="dot"></span> SYSTEM ONLINE</span>
      <span class="berth">▣ BERTH 04</span>
      <span class="clock">14:22:08 AST · 07.05.2026</span>
    </div>
  </header>

  <!-- HERO -->
  <section class="hero">
    <div>
      <div class="eyebrow">Helpdesk Operations Center</div>
      <h1>
        Terminal<br>
        Status — <span class="accent">44</span> active<br>
        tickets in yard.
        <span class="ar-overlay">حالة المحطة — 44 تذكرة نشطة في الساحة</span>
      </h1>
    </div>
    <p class="lede">
      <strong>12 containers</strong> awaiting an agent at the gate. <strong>7 in transit</strong> across the cranes. <strong>25 cleared</strong> the yard this week — closure rate up 12% on the previous shift cycle. No critical incidents.
    </p>
  </section>

  <!-- CONTAINERS -->
  <section class="yard">
    <div class="container-card cc-blue">
      <div class="cc-head">
        <span class="cc-code">NCTU 4421 097-3</span>
        <span class="cc-iso">22G1</span>
      </div>
      <div class="cc-body">
        <div class="label">In Yard <span class="ar">في الساحة</span></div>
        <div class="num">12</div>
        <div class="meta">awaiting agent · open</div>
        <div class="stripe"></div>
      </div>
    </div>
    <div class="container-card cc-orange">
      <div class="cc-head">
        <span class="cc-code">NCTU 7843 217-1</span>
        <span class="cc-iso">22G1</span>
      </div>
      <div class="cc-body">
        <div class="label">In Transit <span class="ar">قيد المعالجة</span></div>
        <div class="num">07</div>
        <div class="meta">live workload · in_progress</div>
        <div class="stripe"></div>
      </div>
    </div>
    <div class="container-card cc-green">
      <div class="cc-head">
        <span class="cc-code">NCTU 1239 884-6</span>
        <span class="cc-iso">22G1</span>
      </div>
      <div class="cc-body">
        <div class="label">Cleared <span class="ar">تم الإنجاز</span></div>
        <div class="num">25</div>
        <div class="meta">shipped this week · closed</div>
        <div class="stripe"></div>
      </div>
    </div>
  </section>

  <!-- MANIFEST -->
  <section class="manifest">
    <div class="panel">
      <div class="panel-head">
        <div>
          <div class="title">Cargo Manifest · By Category</div>
          <div class="ar">قائمة الشحن — حسب الفئة</div>
        </div>
        <div class="tag">/CAT</div>
      </div>
      <div class="row">
        <span class="glyph"></span>
        <div class="nm">Software<span class="ar">برمجيات</span></div>
        <div class="bar" style="--w:100%"></div>
        <div class="v">18</div>
      </div>
      <div class="row orange">
        <span class="glyph"></span>
        <div class="nm">Hardware<span class="ar">أجهزة</span></div>
        <div class="bar" style="--w:61%"></div>
        <div class="v">11</div>
      </div>
      <div class="row green">
        <span class="glyph"></span>
        <div class="nm">Network<span class="ar">شبكة</span></div>
        <div class="bar" style="--w:44%"></div>
        <div class="v">08</div>
      </div>
      <div class="row amber">
        <span class="glyph"></span>
        <div class="nm">Access Request<span class="ar">طلب وصول</span></div>
        <div class="bar" style="--w:38%"></div>
        <div class="v">07</div>
      </div>
    </div>

    <div class="panel">
      <div class="panel-head">
        <div>
          <div class="title">Vessel Priority Queue</div>
          <div class="ar">أولوية السفن</div>
        </div>
        <div class="tag">/PRI</div>
      </div>
      <div class="row orange">
        <span class="glyph"></span>
        <div class="nm">High<span class="ar">عاجل</span></div>
        <div class="bar" style="--w:41%"></div>
        <div class="v">09</div>
      </div>
      <div class="row">
        <span class="glyph"></span>
        <div class="nm">Medium<span class="ar">متوسط</span></div>
        <div class="bar" style="--w:100%"></div>
        <div class="v">22</div>
      </div>
      <div class="row green">
        <span class="glyph"></span>
        <div class="nm">Low<span class="ar">منخفض</span></div>
        <div class="bar" style="--w:59%"></div>
        <div class="v">13</div>
      </div>
    </div>
  </section>

  <!-- ACTIONS -->
  <div class="actions">
    <a href="/admin/tickets" class="btn primary">▶ Berth Queue <span class="arrow">→</span></a>
    <a href="/admin/users" class="btn">⚙ Crew Roster <span class="arrow">→</span></a>
    <div class="footer-meta">
      NCT · Helpdesk Ops v2.1.0 · Saira Cond. + Public Sans + JetBrains Mono<br>
      <span class="ar">الشركة الوطنية للحاويات — مركز عمليات الدعم الفني</span>
    </div>
  </div>
</div>
</body>
</html>
