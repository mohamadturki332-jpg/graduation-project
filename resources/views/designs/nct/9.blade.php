<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>NCT · BRUT/04</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Anton&family=Space+Mono:wght@400;700&family=Cairo:wght@700;900&display=swap" rel="stylesheet">
<style>
  :root { --concrete:#cdc6bb; --concrete-d:#a39e93; --shadow:#5d574d; --ink:#161310; --rust:#c44a1d; --safety:#ff7a1f; --paper:#ece8df; }
  * { margin:0; padding:0; box-sizing:border-box; }
  body {
    background: var(--concrete); color: var(--ink);
    font-family: 'Space Mono', monospace; font-weight:400;
    min-height:100vh; padding:0;
    background-image:
      url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='320' height='320'><filter id='n'><feTurbulence type='fractalNoise' baseFrequency='0.7' numOctaves='3'/></filter><rect width='100%' height='100%' filter='url(%23n)' opacity='0.18'/></svg>"),
      linear-gradient(180deg, var(--concrete) 0%, var(--concrete-d) 100%);
  }
  .display { font-family:'Anton',sans-serif; }
  .ar { font-family:'Cairo',sans-serif; }
  .container { max-width:1340px; margin:0 auto; padding:0 0 60px; }

  .top {
    display:grid; grid-template-columns: 1fr 1fr 1fr;
    background: var(--ink); color: var(--concrete);
    padding:18px 36px; border-bottom:6px solid var(--safety);
  }
  .top .l { display:flex; align-items:center; gap:16px; }
  .top .l .blk {
    width:56px; height:56px; background: var(--safety); color: var(--ink);
    display:flex; align-items:center; justify-content:center;
    font-family:'Anton'; font-size:32px;
  }
  .top .l .nm { font-family:'Anton'; font-size:22px; line-height:1; letter-spacing:0.04em; }
  .top .l .nm .ar { display:block; font-family:'Cairo'; font-size:13px; color: var(--safety); margin-top:4px; }
  .top .c { font-family:'Space Mono'; font-size:11px; letter-spacing:0.22em; text-align:center; align-self:center; }
  .top .r { display:flex; gap:18px; justify-content:flex-end; align-items:center; font-family:'Space Mono'; font-size:11px; letter-spacing:0.18em; }
  .top .r .led { width:10px; height:10px; background: var(--safety); display:inline-block; margin-right:4px; box-shadow: 0 0 8px var(--safety); }

  .grid {
    display:grid; grid-template-columns: 200px 1fr 1fr 1fr 200px;
    grid-auto-rows: minmax(80px,auto); gap:0; padding:36px;
  }
  .blk-cell {
    border:3px solid var(--ink); padding:24px; background: var(--paper);
    margin-right:-3px; margin-bottom:-3px; position:relative;
    animation: punch 0.7s cubic-bezier(.3,1.4,.6,1) both;
  }
  .blk-cell.dark { background: var(--ink); color: var(--concrete); }
  .blk-cell.orange { background: var(--safety); color: var(--ink); }
  .blk-cell.rust { background: var(--rust); color: var(--paper); }
  @keyframes punch { from { opacity:0; transform: translate(-12px,-8px); } to { opacity:1; transform:none; } }

  .span-3 { grid-column: span 3; }
  .span-2 { grid-column: span 2; }
  .span-4 { grid-column: span 4; }
  .span-5 { grid-column: span 5; }
  .row-2 { grid-row: span 2; }

  .hero-block {
    padding:36px 30px;
  }
  .hero-block .lab { font-family:'Space Mono'; font-size:11px; letter-spacing:0.32em; }
  .hero-block h1 {
    font-family:'Anton',sans-serif;
    font-size:clamp(80px, 12vw, 180px); line-height:0.85; letter-spacing:-0.01em;
    margin-top:8px; text-transform:uppercase;
  }
  .hero-block .deck { font-family:'Space Mono'; font-size:14px; line-height:1.6; max-width:520px; margin-top:18px; }

  .num-block {
    display:flex; flex-direction:column; justify-content:space-between; padding:20px;
  }
  .num-block .lab { font-family:'Space Mono'; font-size:11px; letter-spacing:0.28em; }
  .num-block .num {
    font-family:'Anton'; font-size:140px; line-height:0.85; letter-spacing:-0.02em;
  }
  .num-block .ar { font-family:'Cairo'; font-weight:700; font-size:16px; }

  .small-block { padding:18px 20px; }
  .small-block .lab { font-family:'Space Mono'; font-size:10px; letter-spacing:0.28em; opacity:0.7; }
  .small-block .v { font-family:'Anton'; font-size:54px; line-height:1; }
  .small-block .v small { font-family:'Space Mono'; font-size:11px; letter-spacing:0.18em; opacity:0.7; vertical-align:middle; margin-left:8px; }
  .small-block .nm { font-family:'Space Mono'; font-size:13px; margin-top:4px; letter-spacing:0.06em; }
  .small-block .nm .ar { font-family:'Cairo'; margin-left:8px; opacity:0.85; }

  .quote-block { padding:24px 26px; }
  .quote-block p {
    font-family:'Anton'; font-size:36px; line-height:1.05; letter-spacing:0.01em;
    text-transform:uppercase;
  }
  .quote-block cite { font-family:'Space Mono'; font-style:normal; font-size:11px; letter-spacing:0.22em; opacity:0.7; display:block; margin-top:14px; }

  .actions-bar { padding:20px 26px; display:flex; gap:14px; align-items:center; flex-wrap:wrap; }
  .lever {
    font-family:'Anton'; font-size:24px; letter-spacing:0.04em; padding:14px 22px;
    background: var(--paper); color: var(--ink); border:3px solid var(--ink); text-decoration:none;
    box-shadow: 6px 6px 0 var(--ink); transition: all 0.1s; text-transform:uppercase;
  }
  .lever.alt { background: var(--ink); color: var(--safety); }
  .lever:hover { transform: translate(-2px,-2px); box-shadow: 8px 8px 0 var(--ink); }
  .stamp { margin-left:auto; font-family:'Space Mono'; font-size:11px; letter-spacing:0.18em; opacity:0.7; }

  .footer { padding:18px 36px; background: var(--ink); color: var(--concrete); display:flex; justify-content:space-between; font-family:'Space Mono'; font-size:11px; letter-spacing:0.22em; }
</style>
</head>
<body>
<div class="container">
  <header class="top">
    <div class="l">
      <div class="blk">N</div>
      <div class="nm">NCT / BRUT_04
        <span class="ar">الشركة الوطنية للحاويات</span>
      </div>
    </div>
    <div class="c">// ADMIN CONSOLE / CONCRETE BLOCK / LIVE</div>
    <div class="r">
      <span><span class="led"></span>ONLINE</span>
      <span>14:22 AST</span>
      <span>BERTH 04</span>
    </div>
  </header>

  <section class="grid">
    <div class="blk-cell dark hero-block span-4">
      <div class="lab">— TICKET YARD :: STATE</div>
      <h1>FORTY/<br>FOUR.</h1>
      <p class="deck">RAW COUNT OF EVERY CRATE LOGGED IN THE HELPDESK YARD AS OF 14:22 AST. TWELVE STILL ON THE DOCK. SEVEN UNDER WORK. TWENTY-FIVE DISCHARGED CLEAN IN THE LAST WEEK.</p>
    </div>
    <div class="blk-cell orange num-block">
      <div class="lab">— OPEN</div>
      <div>
        <div class="num">12</div>
        <div class="ar">في الساحة</div>
      </div>
    </div>

    <div class="blk-cell num-block">
      <div class="lab">— IN PROGRESS</div>
      <div>
        <div class="num" style="color:var(--rust);">07</div>
        <div class="ar">قيد المعالجة</div>
      </div>
    </div>
    <div class="blk-cell rust num-block">
      <div class="lab">— CLOSED</div>
      <div>
        <div class="num">25</div>
        <div class="ar">تم الإنجاز</div>
      </div>
    </div>
    <div class="blk-cell dark quote-block span-3">
      <p>"NO TICKET OLDER<br>THAN THREE DAYS<br>SHALL REMAIN<br>UNATTENDED."</p>
      <cite>— YARD STANDING ORDER 04 / NCT</cite>
    </div>

    <div class="blk-cell small-block">
      <div class="lab">— CAT · SOFTWARE</div>
      <div class="v">18</div>
      <div class="nm">// HEAVIEST <span class="ar">برمجيات</span></div>
    </div>
    <div class="blk-cell small-block">
      <div class="lab">— CAT · HARDWARE</div>
      <div class="v">11</div>
      <div class="nm">// <span class="ar">أجهزة</span></div>
    </div>
    <div class="blk-cell small-block">
      <div class="lab">— CAT · NETWORK</div>
      <div class="v">08</div>
      <div class="nm">// <span class="ar">شبكة</span></div>
    </div>
    <div class="blk-cell small-block">
      <div class="lab">— CAT · ACCESS</div>
      <div class="v">07</div>
      <div class="nm">// <span class="ar">طلب وصول</span></div>
    </div>
    <div class="blk-cell orange small-block">
      <div class="lab">— PRIORITY</div>
      <div class="v">9 <small>HI</small></div>
      <div class="nm">22 MED · 13 LOW</div>
    </div>

    <div class="blk-cell actions-bar span-5">
      <a class="lever" href="/admin/tickets">▰ ALL TICKETS →</a>
      <a class="lever alt" href="/admin/users">▰ MANAGE USERS →</a>
      <span class="stamp">NCT/09 · ANTON + SPACE MONO + CAIRO · BRUTALIST CONCRETE</span>
    </div>
  </section>

  <footer class="footer">
    <span>// NCT · BRUT_04 · BLOCK 09</span>
    <span>// END OF TRANSMISSION</span>
  </footer>
</div>
</body>
</html>
