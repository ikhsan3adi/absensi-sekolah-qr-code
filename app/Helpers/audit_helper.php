<?php

/**
 * Render data lama dengan highlight merah pada nilai yang dihapus.
 * Render data baru dengan highlight hijau pada nilai yang ditambah.
 * Render data baru dengan highlight biru pada nilai yang berubah.
 *
 * Tampilan berupa key: value tanpa kurung kurawal JSON — lebih bersih.
 *
 * @param string|null $oldJson
 * @param string|null $newJson
 * @return array [$oldHtml, $newHtml]
 */
function render_json_diff_side_by_side($oldJson, $newJson)
{
    $old = $oldJson ? json_decode($oldJson, true) : null;
    $new = $newJson ? json_decode($newJson, true) : null;

    return [
        _render_kv($old, $new, 'old'),
        _render_kv($new, $old, 'new'),
    ];
}

/**
 * Internal: render key: value list dengan highlight.
 *
 * @param mixed $data       data untuk ditampilkan (sisi ini)
 * @param mixed $otherData  data sisi pembanding
 * @param string $side      'old' atau 'new'
 * @return string HTML
 */
function _render_kv($data, $otherData, $side)
{
    if ($data === null) {
        return '<span class="text-muted">-</span>';
    }

    if (!is_array($data)) {
        return '<span>' . htmlspecialchars((string) $data) . '</span>';
    }

    $otherData = is_array($otherData) ? $otherData : [];

    $allKeys = array_unique(array_merge(array_keys($data), array_keys($otherData)));
    sort($allKeys);

    $lines = [];

    foreach ($allKeys as $key) {
        $existsHere  = array_key_exists($key, $data);
        $existsOther = array_key_exists($key, $otherData);

        // Jika tidak ada di kedua sisi (tidak mungkin dari union), skip
        if (!$existsHere && !$existsOther) {
            continue;
        }

        // Jika ada di kedua sisi
        if ($existsHere && $existsOther) {
            if ($data[$key] === $otherData[$key]) {
                // Tidak berubah — tampil normal
                $lines[] = _kv_line($key, $data[$key], '');
            } else {
                // Berubah — merah atau hijau tergantung side
                $status = ($side === 'old') ? 'changed-old' : 'changed-new';
                $lines[] = _kv_line($key, $data[$key], $status);
            }
        } else if ($existsHere && !$existsOther) {
            // Hanya ada di sisi ini, tidak di sisi lain
            if ($side === 'old') {
                // Dihapus (ada di old, tidak di new)
                $lines[] = _kv_line($key, $data[$key], 'removed');
            } else {
                // Ditambah (ada di new, tidak di old)
                $lines[] = _kv_line($key, $data[$key], 'added');
            }
        }
        // Jika tidak ada di sini tapi ada di sisi lain: skip (tidak perlu ditampilkan)
    }

    return '<div style="font-size:11px; line-height:1.7;">' . implode('', $lines) . '</div>';
}

/**
 * Render satu baris "key: value".
 *
 * @param string $key
 * @param mixed  $value
 * @param string $status  '' | 'removed' | 'added' | 'changed-old' | 'changed-new'
 * @return string HTML
 */
function _kv_line($key, $value, $status)
{
    if (is_bool($value)) {
        $display = $value ? 'true' : 'false';
    } elseif ($value === null) {
        $display = '<em>null</em>';
    } elseif (is_array($value)) {
        $display = json_encode($value, JSON_UNESCAPED_UNICODE);
    } else {
        $display = (string) $value;
    }

    $display  = htmlspecialchars($display);
    $keyHtml  = htmlspecialchars($key);
    $leftCol  = '<span style="display:inline-block; min-width:64px;">' . $keyHtml . '</span>';

    switch ($status) {
        case 'removed':
            return '<div class="text-danger">' . $leftCol . ': ' . $display . '</div>';
        case 'added':
            return '<div class="text-success">' . $leftCol . ': ' . $display . '</div>';
        case 'changed-old':
            return '<div class="text-info">' . $leftCol . ': ' . $display . '</div>';
        case 'changed-new':
            return '<div class="text-info">' . $leftCol . ': ' . $display . '</div>';
        default:
            return '<div>' . $leftCol . ': ' . $display . '</div>';
    }
}
