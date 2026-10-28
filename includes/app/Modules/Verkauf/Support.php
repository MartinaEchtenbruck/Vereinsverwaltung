<?php
function salesMoney(int $cents): string { return number_format($cents/100,2,',','.').' €'; }
function salesH($v): string { return htmlspecialchars((string)$v,ENT_QUOTES,'UTF-8'); }
