<?php

// Return author info. Deprecated
function authorinfo(string $name = null, string $about = null): string {
    if (config('author.info') == 'true') {
        return '<div class="author-info"><h4>by <strong>' . $name . '</strong></h4>' . $about . '</div>';
    }
        return '<div class="author-info"><h4>by <strong>author unknown</strong></h4>' . $about . '</div>';
}

