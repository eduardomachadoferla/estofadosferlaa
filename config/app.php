<?php
declare(strict_types=1);

// Endereços relativos permitem instalar na raiz do domínio ou em uma subpasta.
function base_url(): string {
    $script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '/index.php');
    $dir = str_replace('\\', '/', dirname($script));
    if (str_ends_with($dir, '/admin')) $dir = substr($dir, 0, -6);
    return $dir === '/' || $dir === '.' ? '' : rtrim($dir, '/');
}
function site_url(string $path = ''): string {
    return base_url() . '/' . ltrim($path, '/');
}
function e(?string $value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
function image_url(?string $path): string {
    $path = (string)$path;
    if (!$path) return site_url('assets/sem-imagem.svg');
    // Compatibilidade com imagens antigas salvas com prefixo /estofadosferla/.
    $pos = strpos($path, '/assets/uploads/');
    if ($pos !== false) return site_url(ltrim(substr($path, $pos), '/'));
    if (str_starts_with($path, 'assets/uploads/')) return site_url($path);
    return site_url('assets/sem-imagem.svg');
}
function money_value(string $input): ?float {
    $s = preg_replace('/[^\d,.]/u', '', trim($input));
    if ($s === '') return null;
    $comma = strrpos($s, ','); $dot = strrpos($s, '.');
    if ($comma !== false && $dot !== false) {
        $decimal = $comma > $dot ? ',' : '.';
        $s = $decimal === ',' ? str_replace(',', '.', str_replace('.', '', $s)) : str_replace(',', '', $s);
    } elseif ($comma !== false) {
        $s = strlen($s) - $comma - 1 === 3 ? str_replace(',', '', $s) : str_replace(',', '.', $s);
    } elseif ($dot !== false) {
        $s = strlen($s) - $dot - 1 === 3 ? str_replace('.', '', $s) : $s;
    }
    return is_numeric($s) ? (float)$s : null;
}
function money_display(?string $valor): string {
    if ($valor === null || trim($valor) === '') return 'Sob consulta';
    if (stripos($valor, 'R$') !== false) return $valor;
    $n = money_value($valor);
    return $n === null ? $valor : 'R$ ' . number_format($n, 2, ',', '.');
}
function categoria_nome(string $slug): string {
    static $map = ['sofas'=>'Sofás', 'poltronas'=>'Poltronas', 'cadeiras'=>'Cadeiras', 'cabeceiras'=>'Cabeceiras', 'puffs'=>'Puffs'];
    return $map[$slug] ?? ucfirst($slug);
}
