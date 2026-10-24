<?php
// Migrações acontecem automaticamente em config/db.php.
// Bloqueia o antigo instalador aberto à internet.
http_response_code(404);
exit('Página indisponível.');
