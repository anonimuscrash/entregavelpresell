<?php
require __DIR__ . '/config.php';

/* Captura o fbclid da URL e grava no cookie _fbc no formato que a Meta espera.
   Isso melhora muito o casamento de eventos quando o Pixel do navegador falha. */
if (!empty($_GET['fbclid']) && empty($_COOKIE['_fbc'])) {
    $fbclid = preg_replace('/[^a-zA-Z0-9\.\-_]/', '', $_GET['fbclid']);
    $fbc = 'fb.1.' . (time() * 1000) . '.' . $fbclid;
    setcookie('_fbc', $fbc, time() + 7776000, '/', '', !empty($_SERVER['HTTPS']), false);
    $_COOKIE['_fbc'] = $fbc;
}

/* Um único event_id para o navegador e para o servidor.
   É isso que faz a Meta contar 1 conversão em vez de 2. */
$eventId = bin2hex(random_bytes(16));
?>
<!doctype html>
<html lang="pt-BR">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<meta name="robots" content="noindex, nofollow">
<title><?= htmlspecialchars(TXT_TITULO) ?></title>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;700&family=Inter:wght@400;500&display=swap" rel="stylesheet">

<style>
:root{
  --fundo:#061119;
  --fundo-2:#0A1E2C;
  --carta:#0D2432;
  --borda:rgba(126,176,210,.16);
  --tinta:#E9F3F9;
  --mudo:#7F9BB0;
  --tg:#2AABEE;
  --tg-esc:#1B8FCC;
  --espera:#F5A623;
  --anel-vazio:rgba(126,176,210,.13);
}

*{box-sizing:border-box;margin:0;padding:0}

body{
  min-height:100svh;
  display:flex;
  align-items:center;
  justify-content:center;
  padding:24px 20px calc(24px + env(safe-area-inset-bottom));
  background:
    radial-gradient(120% 80% at 50% 0%, var(--fundo-2) 0%, var(--fundo) 62%),
    var(--fundo);
  font-family:'Inter',-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;
  color:var(--tinta);
  -webkit-font-smoothing:antialiased;
}

.carta{
  width:100%;
  max-width:400px;
  background:var(--carta);
  border:1px solid var(--borda);
  border-radius:22px;
  padding:38px 26px 30px;
  text-align:center;
  box-shadow:0 30px 70px -30px rgba(0,0,0,.85);
}

/* ---- cabeçalho ---- */
.eyebrow{
  font-family:'Space Grotesk',sans-serif;
  font-size:11px;
  font-weight:700;
  letter-spacing:.16em;
  text-transform:uppercase;
  color:var(--tg);
  display:inline-flex;
  align-items:center;
  gap:8px;
}
.eyebrow::before{
  content:"";
  width:6px;height:6px;border-radius:50%;
  background:var(--tg);
  box-shadow:0 0 0 4px rgba(42,171,238,.18);
}

h1{
  margin-top:14px;
  font-family:'Space Grotesk',sans-serif;
  font-size:24px;
  font-weight:700;
  line-height:1.22;
  letter-spacing:-.015em;
}

/* ---- o anel de sinal (elemento assinatura) ---- */
.palco{
  position:relative;
  width:190px;height:190px;
  margin:30px auto 26px;
  display:grid;
  place-items:center;
}

.pulso{
  position:absolute;
  inset:26px;
  border-radius:50%;
  border:1px solid var(--espera);
  opacity:0;
  animation:emitir 2.4s cubic-bezier(.2,.6,.3,1) infinite;
}
.pulso:nth-child(2){animation-delay:.8s}
.pulso:nth-child(3){animation-delay:1.6s}
@keyframes emitir{
  0%   {transform:scale(.72);opacity:.55}
  100% {transform:scale(1.32);opacity:0}
}

.anel{
  position:absolute;inset:0;
  transform:rotate(-90deg);
}
.anel circle{
  fill:none;
  stroke-width:6;
  stroke-linecap:round;
}
.anel .trilho{stroke:var(--anel-vazio)}
.anel .marca{
  stroke:var(--espera);
  stroke-dasharray:439.8;
  stroke-dashoffset:439.8;
  transition:stroke .45s ease;
}

.numero{
  font-family:'Space Grotesk',sans-serif;
  font-size:62px;
  font-weight:500;
  font-variant-numeric:tabular-nums;
  line-height:1;
  color:var(--tinta);
}

.check{
  position:absolute;
  width:64px;height:64px;
  stroke:var(--tg);
  stroke-width:7;
  stroke-linecap:round;
  stroke-linejoin:round;
  fill:none;
  stroke-dasharray:60;
  stroke-dashoffset:60;
  opacity:0;
}

/* ---- textos de estado ---- */
.aviso{
  font-size:14px;
  color:var(--mudo);
  min-height:20px;
}

/* ---- botão ---- */
.acao{
  margin-top:24px;
  opacity:0;
  transform:translateY(10px);
  pointer-events:none;
  transition:opacity .45s ease, transform .45s cubic-bezier(.2,.8,.3,1);
}
.botao{
  display:flex;
  align-items:center;
  justify-content:center;
  gap:10px;
  width:100%;
  padding:17px 20px;
  border-radius:14px;
  border:0;
  background:linear-gradient(180deg,var(--tg),var(--tg-esc));
  color:#fff;
  font-family:'Space Grotesk',sans-serif;
  font-size:17px;
  font-weight:700;
  letter-spacing:-.01em;
  text-decoration:none;
  cursor:pointer;
  box-shadow:0 12px 28px -12px rgba(42,171,238,.75);
  transition:transform .15s ease, box-shadow .15s ease;
}
.botao:hover{transform:translateY(-1px);box-shadow:0 16px 34px -12px rgba(42,171,238,.85)}
.botao:active{transform:translateY(0)}
.botao:focus-visible{outline:3px solid var(--tg);outline-offset:3px}
.botao svg{width:20px;height:20px;fill:currentColor}

.rodape{
  margin-top:16px;
  font-size:12.5px;
  color:var(--mudo);
}

/* ---- estado liberado ---- */
.carta[data-estado="liberado"] .pulso{display:none}
.carta[data-estado="liberado"] .anel .marca{stroke:var(--tg)}
.carta[data-estado="liberado"] .numero{opacity:0;transform:scale(.6);transition:.3s ease}
.carta[data-estado="liberado"] .check{
  opacity:1;
  animation:desenhar .5s .12s cubic-bezier(.6,0,.3,1) forwards;
}
@keyframes desenhar{to{stroke-dashoffset:0}}
.carta[data-estado="liberado"] .acao{
  opacity:1;
  transform:translateY(0);
  pointer-events:auto;
}

@media (prefers-reduced-motion:reduce){
  .pulso{display:none}
  *{animation-duration:.01ms !important;transition-duration:.01ms !important}
  .check{stroke-dashoffset:0}
}
</style>

<!-- Meta Pixel -->
<script>
!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
document,'script','https://connect.facebook.net/en_US/fbevents.js');
fbq('init','<?= DATASET_ID ?>');
fbq('track','PageView');
</script>
<noscript><img height="1" width="1" style="display:none"
src="https://www.facebook.com/tr?id=<?= DATASET_ID ?>&ev=PageView&noscript=1"></noscript>
</head>

<body>
<main class="carta" data-estado="contando">

  <span class="eyebrow"><?= htmlspecialchars(TXT_EYEBROW) ?></span>
  <h1><?= htmlspecialchars(TXT_TITULO) ?></h1>

  <div class="palco">
    <span class="pulso"></span>
    <span class="pulso"></span>
    <span class="pulso"></span>

    <svg class="anel" viewBox="0 0 190 190" aria-hidden="true">
      <circle class="trilho" cx="95" cy="95" r="70"></circle>
      <circle class="marca"  cx="95" cy="95" r="70"></circle>
    </svg>

    <span class="numero" id="numero" role="timer" aria-live="off"><?= (int) SEGUNDOS ?></span>

    <svg class="check" viewBox="0 0 100 100" aria-hidden="true">
      <path d="M24 52 L43 71 L77 33"></path>
    </svg>
  </div>

  <p class="aviso" id="aviso"><?= htmlspecialchars(TXT_ESPERA) ?></p>

  <div class="acao" id="acao">
    <a class="botao" id="botao" href="<?= htmlspecialchars(LINK_ENTREGA) ?>" rel="noopener" tabindex="-1">
      <svg viewBox="0 0 24 24"><path d="M9.8 16.3l-.4 5.1c.6 0 .8-.2 1.1-.5l2.6-2.5 5.4 3.9c1 .5 1.7.3 2-.9l3.5-16.6c.3-1.4-.5-2-1.5-1.6L1.1 9.9c-1.4.5-1.4 1.3-.2 1.7l5.5 1.7L19.1 5.4c.6-.4 1.1-.2.7.2z"/></svg>
      <span><?= htmlspecialchars(TXT_BOTAO) ?></span>
    </a>
    <p class="rodape"><?= htmlspecialchars(TXT_RODAPE) ?></p>
  </div>

</main>

<script>
(function(){
  "use strict";

  var SEGUNDOS      = <?= (int) SEGUNDOS ?>;
  var EVENT_ID      = <?= json_encode($eventId) ?>;
  var EVENTO        = <?= json_encode(EVENTO_NOME) ?>;
  var VALOR         = <?= json_encode((float) EVENTO_VALOR) ?>;
  var MOEDA         = <?= json_encode(EVENTO_MOEDA) ?>;
  var CONTEUDO      = <?= json_encode(EVENTO_CONTEUDO) ?>;
  var CONTENT_ID    = <?= json_encode(EVENTO_CONTENT_ID) ?>;
  var AUTO          = <?= (int) AUTO_REDIRECT ?>;
  var LINK          = <?= json_encode(LINK_ENTREGA) ?>;

  var carta  = document.querySelector('.carta');
  var numero = document.getElementById('numero');
  var aviso  = document.getElementById('aviso');
  var botao  = document.getElementById('botao');
  var marca  = document.querySelector('.anel .marca');

  var VOLTA = 439.8;             // 2 * PI * 70
  var TOTAL = SEGUNDOS * 1000;
  var inicio = performance.now();
  var disparado = false;

  function cookie(nome){
    var m = document.cookie.match('(^|;)\\s*' + nome + '\\s*=\\s*([^;]+)');
    return m ? m.pop() : '';
  }

  function tick(agora){
    var passou = Math.min(agora - inicio, TOTAL);
    var frac   = passou / TOTAL;

    marca.style.strokeDashoffset = VOLTA * (1 - frac);

    var restante = Math.ceil((TOTAL - passou) / 1000);
    if (String(restante) !== numero.textContent && restante >= 0){
      numero.textContent = restante;
    }

    if (passou < TOTAL){ requestAnimationFrame(tick); }
    else { liberar(); }
  }

  function liberar(){
    if (disparado) return;
    disparado = true;

    marca.style.strokeDashoffset = 0;
    carta.setAttribute('data-estado','liberado');
    aviso.textContent = <?= json_encode(TXT_LIBERADO) ?>;
    botao.setAttribute('tabindex','0');
    botao.focus({preventScroll:true});

    enviarEvento();

    if (AUTO > 0){
      setTimeout(function(){ window.location.href = LINK; }, AUTO * 1000);
    }
  }

  function enviarEvento(){
    // 1) Navegador (Pixel)
    if (window.fbq){
      fbq('track', EVENTO, {
        value: VALOR,
        currency: MOEDA,
        content_name: CONTEUDO,
        content_ids: [CONTENT_ID],
        content_type: 'product'
      }, { eventID: EVENT_ID });
    }

    // 2) Servidor (Conversions API) — mesmo event_id = sem duplicidade
    try {
      fetch('api/evento.php', {
        method: 'POST',
        headers: {'Content-Type':'application/json'},
        keepalive: true,
        body: JSON.stringify({
          event_id: EVENT_ID,
          url: window.location.href,
          fbp: cookie('_fbp'),
          fbc: cookie('_fbc')
        })
      }).catch(function(){});
    } catch(e){}
  }

  requestAnimationFrame(tick);
})();
</script>
</body>
</html>
