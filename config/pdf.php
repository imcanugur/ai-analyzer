<?php

return [

    /*
    |--------------------------------------------------------------------------
    | PDF Parser Configuration
    |--------------------------------------------------------------------------
    |
    | Here you can configure how the smalot/pdfparser library extracts text.
    |
    | font_space_limit: Controls spacing sensitivity.
    |   - Default in library is -50 (which often glues words together).
    |   - Recommended value is between -15 and -10 to insert proper spaces.
    |
    */
    'font_space_limit' => (int) env('PDF_FONT_SPACE_LIMIT', -15),

];
