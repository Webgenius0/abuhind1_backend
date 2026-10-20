<?php

return [
    'facial_width_height_ratio' => [
         'view'        => 'front',
        'zone'        => 'الثلث الأوسط',
        'title'       => 'نسبة عرض الوجه إلى ارتفاعه',
         'description' => 'تقيس العلاقة بين عرض الوجه وارتفاعه.',
    'definition'  => 'عرض الوجه (من عظمة الوجنة إلى الأخرى) ÷ المسافة بين الحاجب وأعلى الشفة.',

        'unit'        => 'x',
        'scale_min'   => 1.07,
        'scale_max'   => 1.64,
        'ideal_min'   => 1.34,
        'ideal_max'   => 1.37,

    ],
    // one entry per metric, ~33 total
];
