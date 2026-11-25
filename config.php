<?php
/* =========================================================================
   CONFIGURAÇÃO DA PRESSEL
   Este arquivo NUNCA deve ser acessível pelo navegador.
   O .htaccess já bloqueia, mas o ideal é movê-lo para fora da pasta pública.
   ========================================================================= */

/* -------------------------------------------------------------------------
   1. META / CONVERSIONS API
   ------------------------------------------------------------------------- */

// ID do conjunto de dados (é o mesmo número do Pixel)
define('DATASET_ID', '2002972087101209');

// >>> ATENÇÃO <<<
// O token abaixo foi exposto em texto aberto. Gere um NOVO em
// Events Manager > Conjunto de dados > Conversions API > Gerar token de acesso
// e revogue o antigo. Depois cole o novo aqui.
// Melhor ainda: defina a variável de ambiente META_CAPI_TOKEN no seu servidor
// e deixe a constante abaixo vazia.
define('CAPI_TOKEN', getenv('META_CAPI_TOKEN') ?: 'EAAUkoZAms8YQBSFTD26UBktNJ8sF5EaKyZAvGrMn3jCJMdwvAT1giUVizTIhjkvaXRYAdJArXKRHjKv85ZBNqqPAc5AZBGtmmOqOUDDcDxBRpMTYSMZAP9UKtTurR8rW6x6dQ2ZB9exHZCoXAoh1ge6dOZAgZCZANKZBFOpYcBuRqOni8OZCuYIFrWp4hbYrXTAklAZDZD');

// Versão do Graph API (atual: v25.0, lançada em fev/2026)
define('GRAPH_VERSION', 'v25.0');

// Código de teste — pegue em Events Manager > Testar eventos.
// Com ele preenchido, os eventos aparecem SÓ na aba de teste (não contam
// para otimização). Deixe VAZIO quando for para o ar.
define('TEST_EVENT_CODE', '');


/* -------------------------------------------------------------------------
   2. EVENTO DISPARADO AO FIM DA CONTAGEM
   ------------------------------------------------------------------------- */

// Nome do evento. 'Purchase' é o que você pediu.
// Leia a observação no final da conversa antes de subir isso em escala.
define('EVENTO_NOME', 'Purchase');

// Purchase exige value + currency. Coloque o valor real do produto.
define('EVENTO_VALOR',  '19.90');
define('EVENTO_MOEDA',  'BRL');
define('EVENTO_CONTEUDO', 'Acesso Canal VIP');
define('EVENTO_CONTENT_ID', 'canal-vip-001');


/* -------------------------------------------------------------------------
   3. ENTREGA
   ------------------------------------------------------------------------- */

// Segundos que o visitante precisa ficar na página
define('SEGUNDOS', 5);

// Link do entregável
define('LINK_ENTREGA', 'https://t.me/+Q8fdaDRuKVxmZGUx');

// Redirecionar sozinho depois de liberado? (segundos; 0 = só no clique)
// Recomendo 0: o clique garante que o app do Telegram abra no celular.
define('AUTO_REDIRECT', 0);


/* -------------------------------------------------------------------------
   4. TEXTOS DA PÁGINA
   ------------------------------------------------------------------------- */

define('TXT_EYEBROW',   'Pagamento confirmado');
define('TXT_TITULO',    'Estamos liberando seu acesso');
define('TXT_ESPERA',    'Não feche esta página.');
define('TXT_LIBERADO',  'Acesso liberado');
define('TXT_BOTAO',     'Entrar no canal');
define('TXT_RODAPE',    'O link abre direto no Telegram.');


/* -------------------------------------------------------------------------
   5. DEBUG
   ------------------------------------------------------------------------- */

// true = grava a resposta da Meta em capi.log (útil nos primeiros testes).
// Desligue depois: o log pode crescer rápido.
define('LOG_ATIVO', true);
