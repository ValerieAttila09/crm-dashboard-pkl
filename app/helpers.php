<?php

function highlight_match($text, $query) {
    if (empty($query)) return e($text);
    return preg_replace('/(' . preg_quote($query, '/') . ')/i', '<span class="bg-yellow-200 dark:bg-yellow-900/60 text-gray-900 dark:text-yellow-100 font-semibold px-0.5 rounded">$1</span>', e($text));
}

?>