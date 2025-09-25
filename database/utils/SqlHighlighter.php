<?php

declare(strict_types=1);

function highlightSQLClean($sql)
{
    // Escape HTML first
    $escaped = htmlspecialchars($sql);

    // Use a single-pass approach with unique placeholders
    $tokens = [];
    $tokenIndex = 0;

    // Keywords - do these first
    $keywords = ['SELECT', 'FROM', 'WHERE', 'JOIN', 'INNER', 'LEFT', 'RIGHT', 'FULL', 'ON', 'AND', 'OR', 'AS', 'GROUP BY', 'ORDER BY', 'LIMIT'];
    foreach ($keywords as $keyword) {
        $pattern = '/\b' . preg_quote($keyword, '/') . '\b/i';
        $escaped = preg_replace_callback($pattern, function ($matches) use (&$tokens, &$tokenIndex) {
            $token = "###TOKEN_KEYWORD_{$tokenIndex}###";
            $tokens[$token] = '<span style="color: #FF6B9D; font-weight: bold;">' . strtoupper($matches[0]) . '</span>';
            $tokenIndex++;
            return $token;
        }, $escaped);
    }

    // Strings (quoted text)
    $escaped = preg_replace_callback("/'([^'\\\\]|\\\\.)*'/", function ($matches) use (&$tokens, &$tokenIndex) {
        $token = "###TOKEN_STRING_{$tokenIndex}###";
        $tokens[$token] = '<span style="color: #A8E6CF;">' . $matches[0] . '</span>';
        $tokenIndex++;
        return $token;
    }, $escaped);

    // Backticked identifiers
    $escaped = preg_replace_callback('/`[^`]+`/', function ($matches) use (&$tokens, &$tokenIndex) {
        $token = "###TOKEN_IDENTIFIER_{$tokenIndex}###";
        $tokens[$token] = '<span style="color: #87CEEB;">' . $matches[0] . '</span>';
        $tokenIndex++;
        return $token;
    }, $escaped);

    // Numbers
    $escaped = preg_replace_callback('/\b\d+(\.\d+)?\b/', function ($matches) use (&$tokens, &$tokenIndex) {
        $token = "###TOKEN_NUMBER_{$tokenIndex}###";
        $tokens[$token] = '<span style="color: #FFD93D;">' . $matches[0] . '</span>';
        $tokenIndex++;
        return $token;
    }, $escaped);

    // Replace all tokens with their styled versions
    foreach ($tokens as $token => $replacement) {
        $escaped = str_replace($token, $replacement, $escaped);
    }

    // Wrap in pre tag with dark theme styling
    return '<pre style="font-size: 11px; line-height: 1.4; background: #1e1e1e; color: #d4d4d4; padding: 12px; border-radius: 4px; border-left: 3px solid #FF6B35; font-family: \'Monaco\', \'Menlo\', \'Ubuntu Mono\', monospace; overflow-x: auto;">' . $escaped . '</pre>';
}