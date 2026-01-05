<?php
// サニタイズ関数を定義
function sanitizeInput($input) {
    // 入力が配列の場合と文字列の場合を両方処理
    if (is_array($input)) {
        return array_map('sanitizeInput', $input);
    }
    
    // 文字列の場合
    if (is_string($input)) {
        // トリム処理
        $input = trim($input);
        // HTMLエスケープ
        $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
        return $input;
    }
    
    // 他の型はそのまま返す
    return $input;
}

// 安全な出力関数
function h($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}