<?php

return [
    // --------------------------------------------------------------------------
    // Front View Metrics — Middle Third
    // --------------------------------------------------------------------------

    // Facial Width-to-Height Ratio (fWHR)
    'facial_width_height_ratio' => [
        'view'        => 'front',
        'zone'        => 'الثلث الأوسط',
        'title'       => 'نسبة عرض الوجه إلى ارتفاعه',
        'description' => 'تقيس العلاقة بين عرض الوجه وأقصى ارتفاع للثلث الأوسط.',
        'definition'  => 'عرض الوجه (من عظمة الوجنة إلى الأخرى) ÷ المسافة بين الحاجب وأعلى الشفة.',
        'unit'        => 'x',
        'scale_min'   => 1.07,
        'scale_max'   => 1.64,
        'ideal_min'   => 1.34,
        'ideal_max'   => 1.37,
        'overlay'     => [
            'horizontal' => ['start' => ['x' => 360, 'y' => 480], 'end' => ['x' => 640, 'y' => 480]],
            'vertical'   => ['start' => ['x' => 500, 'y' => 360], 'end' => ['x' => 500, 'y' => 620]],
            'badge'      => ['x' => 655, 'y' => 480],
        ],
    ],

    // --------------------------------------------------------------------------
    // Front View Metrics — Upper Third
    // --------------------------------------------------------------------------

    // Forehead Height Ratio compared to facial thirds
    'forehead_height_ratio' => [
        'view'        => 'front',
        'zone'        => 'الثلث العلوي',
        'title'       => 'نسبة ارتفاع الجبهة',
        'description' => 'تقيس نسبة ارتفاع الجبهة مقارنة بالثلث الأوسط والسفلي للوجه.',
        'definition'  => 'المسافة الرأسية من خط الشعر إلى الحواجب مقارنة بالأثلاث الأخرى.',
        'unit'        => 'x',
        'scale_min'   => 0.80,
        'scale_max'   => 1.35,
        'ideal_min'   => 1.00,
        'ideal_max'   => 1.08,
        'overlay'     => [
            'horizontal' => ['start' => ['x' => 370, 'y' => 260], 'end' => ['x' => 630, 'y' => 260]],
            'vertical'   => ['start' => ['x' => 500, 'y' => 180], 'end' => ['x' => 500, 'y' => 340]],
            'badge'      => ['x' => 520, 'y' => 240],
        ],
    ],

    // Forehead Width across temples
    'forehead_width' => [
        'view'        => 'front',
        'zone'        => 'الثلث العلوي',
        'title'       => 'عرض الجبهة',
        'description' => 'يقيس المسافة العرضية بين الصدغين وتناسبها مع إطار الوجه.',
        'definition'  => 'المسافة الأفقية بين أقصى نقطتين عند خط الصدغين.',
        'unit'        => 'mm',
        'scale_min'   => 110.0,
        'scale_max'   => 150.0,
        'ideal_min'   => 125.0,
        'ideal_max'   => 135.0,
        'overlay'     => [
            'horizontal' => ['start' => ['x' => 340, 'y' => 270], 'end' => ['x' => 660, 'y' => 270]],
            'vertical'   => ['start' => ['x' => 500, 'y' => 240], 'end' => ['x' => 500, 'y' => 300]],
            'badge'      => ['x' => 500, 'y' => 235],
        ],
    ],

    // Eyebrow Horizontal Alignment and Arch Symmetry
    'eyebrow_symmetry' => [
        'view'        => 'front',
        'zone'        => 'الثلث العلوي',
        'title'       => 'تماثل الحاجبين',
        'description' => 'يقيس مدى تطابق ارتفاع وانحناء الحاجبين على جانبي الوجه.',
        'definition'  => 'نسبة التطابق التناظري بين محوري الحاجبين الأيمن والأيسر.',
        'unit'        => '%',
        'scale_min'   => 70.0,
        'scale_max'   => 100.0,
        'ideal_min'   => 92.0,
        'ideal_max'   => 100.0,
        'overlay'     => [
            'horizontal' => ['start' => ['x' => 340, 'y' => 350], 'end' => ['x' => 660, 'y' => 350]],
            'vertical'   => ['start' => ['x' => 500, 'y' => 330], 'end' => ['x' => 500, 'y' => 370]],
            'badge'      => ['x' => 500, 'y' => 315],
        ],
    ],

    // Brow Thickness & Density
    'brow_thickness' => [
        'view'        => 'front',
        'zone'        => 'الثلث العلوي',
        'title'       => 'كثافة وسماكة الحواجب',
        'description' => 'يقيس متوسط سماكة شعر الحاجب ومظهره الذكوري المتوازن.',
        'definition'  => 'العرض الرأسي للحاجب عند نقطة التقوس الرئيسية.',
        'unit'        => 'mm',
        'scale_min'   => 4.0,
        'scale_max'   => 12.0,
        'ideal_min'   => 7.5,
        'ideal_max'   => 9.5,
        'overlay'     => [
            'horizontal' => ['start' => ['x' => 380, 'y' => 350], 'end' => ['x' => 460, 'y' => 350]],
            'vertical'   => ['start' => ['x' => 420, 'y' => 335], 'end' => ['x' => 420, 'y' => 365]],
            'badge'      => ['x' => 440, 'y' => 315],
        ],
    ],

    // Supraorbital Ridge Prominence
    'supraorbital_ridge' => [
        'view'        => 'front',
        'zone'        => 'الثلث العلوي',
        'title'       => 'بروز عظم الحاجب',
        'description' => 'يقيس مدى وضوح وبروز الحافة العظمية فوق محجر العين.',
        'definition'  => 'بروز الحافة العظمية فوق الحجاج ومدى تحديدها للجبهة.',
        'unit'        => 'mm',
        'scale_min'   => 2.0,
        'scale_max'   => 8.0,
        'ideal_min'   => 4.5,
        'ideal_max'   => 6.0,
        'overlay'     => [
            'horizontal' => ['start' => ['x' => 350, 'y' => 360], 'end' => ['x' => 650, 'y' => 360]],
            'vertical'   => ['start' => ['x' => 500, 'y' => 340], 'end' => ['x' => 500, 'y' => 380]],
            'badge'      => ['x' => 500, 'y' => 325],
        ],
    ],

    // --------------------------------------------------------------------------
    // Front View Metrics — Middle Third
    // --------------------------------------------------------------------------

    // Interocular Distance Ratio (Rule of Fifths)
    'interocular_ratio' => [
        'view'        => 'front',
        'zone'        => 'الثلث الأوسط',
        'title'       => 'المسافة بين العينين',
        'description' => 'يقيس نسبة المسافة بين الزاويتين الداخليتين للعينين مقارنة بعرض العين.',
        'definition'  => 'المسافة بين الزاويتين الإنسيتين للعينين ÷ متوسط عرض شق العين.',
        'unit'        => 'x',
        'scale_min'   => 0.85,
        'scale_max'   => 1.15,
        'ideal_min'   => 0.98,
        'ideal_max'   => 1.02,
        'overlay'     => [
            'horizontal' => ['start' => ['x' => 440, 'y' => 410], 'end' => ['x' => 560, 'y' => 410]],
            'vertical'   => ['start' => ['x' => 500, 'y' => 390], 'end' => ['x' => 500, 'y' => 430]],
            'badge'      => ['x' => 500, 'y' => 375],
        ],
    ],

    // Eye Fissure Width (Palpebral Fissure Length)
    'eye_fissure_width' => [
        'view'        => 'front',
        'zone'        => 'الثلث الأوسط',
        'title'       => 'اتساع فتحة العين',
        'description' => 'يقيس الطول الأفقي لفتحة العين من الزاوية الداخلية إلى الخارجية.',
        'definition'  => 'المسافة الأفقية بين الزاوية الإنسية والوحشية للعين.',
        'unit'        => 'mm',
        'scale_min'   => 26.0,
        'scale_max'   => 36.0,
        'ideal_min'   => 30.0,
        'ideal_max'   => 32.5,
        'overlay'     => [
            'horizontal' => ['start' => ['x' => 360, 'y' => 410], 'end' => ['x' => 440, 'y' => 410]],
            'vertical'   => ['start' => ['x' => 400, 'y' => 395], 'end' => ['x' => 400, 'y' => 425]],
            'badge'      => ['x' => 400, 'y' => 375],
        ],
    ],

    // Canthal Tilt (Inner vs Outer Corner Eye Angle)
    'canthal_tilt' => [
        'view'        => 'front',
        'zone'        => 'الثلث الأوسط',
        'title'       => 'ميلان زاوية العين (Canthal Tilt)',
        'description' => 'يقيس زاوية الميل الأفقي بين الزاوية الداخلية والزاوية الخارجية للعين.',
        'definition'  => 'الزاوية بين الخط الأفقي والخط الواصل بين زاويتي العين.',
        'unit'        => '°',
        'scale_min'   => -4.0,
        'scale_max'   => 10.0,
        'ideal_min'   => 2.0,
        'ideal_max'   => 5.5,
        'overlay'     => [
            'angle'      => 4.5,
            'horizontal' => ['start' => ['x' => 360, 'y' => 412], 'end' => ['x' => 440, 'y' => 412]],
            'vertical'   => ['start' => ['x' => 440, 'y' => 400], 'end' => ['x' => 440, 'y' => 424]],
            'badge'      => ['x' => 345, 'y' => 395],
        ],
    ],

    // Cheekbone Prominence (Bizygomatic Width)
    'cheekbone_prominence' => [
        'view'        => 'front',
        'zone'        => 'الثلث الأوسط',
        'title'       => 'بروز عظام الوجنتين',
        'description' => 'يقيس أقصى عرض للوجه عند عظام الوجنتين مقارنة بباقي أجزاء الوجه.',
        'definition'  => 'أقصى مسافة عرضية بين القوسين الوجنيين (Bizygomatic breadth).',
        'unit'        => 'mm',
        'scale_min'   => 120.0,
        'scale_max'   => 155.0,
        'ideal_min'   => 136.0,
        'ideal_max'   => 144.0,
        'overlay'     => [
            'horizontal' => ['start' => ['x' => 330, 'y' => 480], 'end' => ['x' => 670, 'y' => 480]],
            'vertical'   => ['start' => ['x' => 500, 'y' => 460], 'end' => ['x' => 500, 'y' => 500]],
            'badge'      => ['x' => 690, 'y' => 480],
        ],
    ],

    // Midface Ratio
    'midface_ratio' => [
        'view'        => 'front',
        'zone'        => 'الثلث الأوسط',
        'title'       => 'نسبة الثلث الأوسط',
        'description' => 'يقيس نسبة المسافة بين حدقتي العين وأعلى الشفة مقارنة بعرض الوجه.',
        'definition'  => 'المسافة بين منتصف الحدقتين وأعلى الشفة العليا ÷ عرض الوجه الوجني.',
        'unit'        => 'ratio',
        'scale_min'   => 0.80,
        'scale_max'   => 1.20,
        'ideal_min'   => 0.95,
        'ideal_max'   => 1.05,
        'overlay'     => [
            'horizontal' => ['start' => ['x' => 360, 'y' => 480], 'end' => ['x' => 640, 'y' => 480]],
            'vertical'   => ['start' => ['x' => 500, 'y' => 410], 'end' => ['x' => 500, 'y' => 620]],
            'badge'      => ['x' => 520, 'y' => 515],
        ],
    ],

    // Nasal Bridge Width
    'nasal_bridge_width' => [
        'view'        => 'front',
        'zone'        => 'الثلث الأوسط',
        'title'       => 'عرض جسر الأنف',
        'description' => 'يقيس عرض جسر الأنف العظمي في منتصف المسافة بين العينين والأرنبة.',
        'definition'  => 'المسافة العرضية لقصبة الأنف العظمية عند المنتصف.',
        'unit'        => 'mm',
        'scale_min'   => 10.0,
        'scale_max'   => 18.0,
        'ideal_min'   => 13.0,
        'ideal_max'   => 15.0,
        'overlay'     => [
            'horizontal' => ['start' => ['x' => 475, 'y' => 460], 'end' => ['x' => 525, 'y' => 460]],
            'vertical'   => ['start' => ['x' => 500, 'y' => 430], 'end' => ['x' => 500, 'y' => 490]],
            'badge'      => ['x' => 545, 'y' => 460],
        ],
    ],

    // Nasal Tip Width
    'nasal_tip_width' => [
        'view'        => 'front',
        'zone'        => 'الثلث الأوسط',
        'title'       => 'عرض أرنبة الأنف',
        'description' => 'يقيس العرض الدائري لأرنبة الأنف وتناسبها مع هيكل الوجه.',
        'definition'  => 'العرض الأفقي لأرنبة الأنف عند أقصى بروز لنقطة الذروة.',
        'unit'        => 'mm',
        'scale_min'   => 12.0,
        'scale_max'   => 22.0,
        'ideal_min'   => 15.5,
        'ideal_max'   => 18.0,
        'overlay'     => [
            'horizontal' => ['start' => ['x' => 465, 'y' => 535], 'end' => ['x' => 535, 'y' => 535]],
            'vertical'   => ['start' => ['x' => 500, 'y' => 515], 'end' => ['x' => 500, 'y' => 555]],
            'badge'      => ['x' => 555, 'y' => 535],
        ],
    ],

    // Alar Base Width (Nasal Base)
    'alar_base_width' => [
        'view'        => 'front',
        'zone'        => 'الثلث الأوسط',
        'title'       => 'عرض قاعدة جناحي الأنف',
        'description' => 'يقيس المسافة بين أقصى نقطتين لقاعدة جناحي الأنف ومقارنتها بالمسافة بين العينين.',
        'definition'  => 'المسافة بين الحافتين الخارجيتين لجناحي الأنف.',
        'unit'        => 'mm',
        'scale_min'   => 30.0,
        'scale_max'   => 42.0,
        'ideal_min'   => 34.0,
        'ideal_max'   => 37.0,
        'overlay'     => [
            'horizontal' => ['start' => ['x' => 445, 'y' => 545], 'end' => ['x' => 555, 'y' => 545]],
            'vertical'   => ['start' => ['x' => 500, 'y' => 525], 'end' => ['x' => 500, 'y' => 565]],
            'badge'      => ['x' => 575, 'y' => 545],
        ],
    ],

    // Nose Symmetry & Vertical Alignment
    'nose_symmetry' => [
        'view'        => 'front',
        'zone'        => 'الثلث الأوسط',
        'title'       => 'استقامة وتماثل الأنف',
        'description' => 'يقيس مدى استقامة المحور العمودي للأنف ومطابقته لخط منتصف الوجه.',
        'definition'  => 'نسبة انطباق محور الأنف من الجذع للأرنبة على الخط التناظري الأوسط.',
        'unit'        => '%',
        'scale_min'   => 75.0,
        'scale_max'   => 100.0,
        'ideal_min'   => 93.0,
        'ideal_max'   => 100.0,
        'overlay'     => [
            'horizontal' => ['start' => ['x' => 460, 'y' => 500], 'end' => ['x' => 540, 'y' => 500]],
            'vertical'   => ['start' => ['x' => 500, 'y' => 380], 'end' => ['x' => 500, 'y' => 560]],
            'badge'      => ['x' => 520, 'y' => 440],
        ],
    ],

    // --------------------------------------------------------------------------
    // Front View Metrics — Lower Third
    // --------------------------------------------------------------------------

    // Philtrum Length
    'philtrum_length' => [
        'view'        => 'front',
        'zone'        => 'الثلث السفلي',
        'title'       => 'طول النثرة (Philtrum)',
        'description' => 'يقيس المسافة الرأسية من قاعدة الأنف إلى منتصف قوس الشفة العليا.',
        'definition'  => 'المسافة الرأسية من نقطة أسفل الأنف (Subnasale) إلى قمة الشفة العليا (Labiale superius).',
        'unit'        => 'mm',
        'scale_min'   => 10.0,
        'scale_max'   => 18.0,
        'ideal_min'   => 12.0,
        'ideal_max'   => 14.5,
        'overlay'     => [
            'horizontal' => ['start' => ['x' => 470, 'y' => 585], 'end' => ['x' => 530, 'y' => 585]],
            'vertical'   => ['start' => ['x' => 500, 'y' => 560], 'end' => ['x' => 500, 'y' => 610]],
            'badge'      => ['x' => 525, 'y' => 585],
        ],
    ],

    // Total Lip Fullness
    'lip_fullness' => [
        'view'        => 'front',
        'zone'        => 'الثلث السفلي',
        'title'       => 'امتلاء الشفاه الكلي',
        'description' => 'يقيس الارتفاع العمودي المجمع لكل من الشفة العليا والسفلى عند نقطة المنتصف.',
        'definition'  => 'الارتفاع الرأسي الكلي للجزء الملون للشفاه من الحافة العلوية للسفلية.',
        'unit'        => 'mm',
        'scale_min'   => 12.0,
        'scale_max'   => 24.0,
        'ideal_min'   => 16.0,
        'ideal_max'   => 19.5,
        'overlay'     => [
            'horizontal' => ['start' => ['x' => 440, 'y' => 645], 'end' => ['x' => 560, 'y' => 645]],
            'vertical'   => ['start' => ['x' => 500, 'y' => 615], 'end' => ['x' => 500, 'y' => 675]],
            'badge'      => ['x' => 580, 'y' => 645],
        ],
    ],

    // Upper to Lower Lip Ratio
    'lip_ratio' => [
        'view'        => 'front',
        'zone'        => 'الثلث السفلي',
        'title'       => 'نسبة الشفة العليا إلى السفلى',
        'description' => 'يقيس النسبة الذهبية بين سمك الشفة العليا مقارنة بسمك الشفة السفلى (مثالياً 1:1.6).',
        'definition'  => 'ارتفاع الشفة العليا ÷ ارتفاع الشفة السفلى.',
        'unit'        => 'ratio',
        'scale_min'   => 0.50,
        'scale_max'   => 0.90,
        'ideal_min'   => 0.65,
        'ideal_max'   => 0.75,
        'overlay'     => [
            'horizontal' => ['start' => ['x' => 450, 'y' => 640], 'end' => ['x' => 550, 'y' => 640]],
            'vertical'   => ['start' => ['x' => 500, 'y' => 615], 'end' => ['x' => 500, 'y' => 675]],
            'badge'      => ['x' => 570, 'y' => 630],
        ],
    ],

    // Mouth Width
    'mouth_width' => [
        'view'        => 'front',
        'zone'        => 'الثلث السفلي',
        'title'       => 'عرض الفم',
        'description' => 'يقيس المسافة بين زاويتي الفم ومطابقتها لامتداد منتصف حدقتي العينين.',
        'definition'  => 'المسافة الأفقية بين زاويتي الفم (Chelion إلى Chelion).',
        'unit'        => 'mm',
        'scale_min'   => 45.0,
        'scale_max'   => 62.0,
        'ideal_min'   => 50.0,
        'ideal_max'   => 55.0,
        'overlay'     => [
            'horizontal' => ['start' => ['x' => 420, 'y' => 645], 'end' => ['x' => 580, 'y' => 645]],
            'vertical'   => ['start' => ['x' => 500, 'y' => 625], 'end' => ['x' => 500, 'y' => 665]],
            'badge'      => ['x' => 600, 'y' => 645],
        ],
    ],

    // Jaw Symmetry & Alignment
    'jaw_symmetry' => [
        'view'        => 'front',
        'zone'        => 'الثلث السفلي',
        'title'       => 'تماثل الفك السفلي',
        'description' => 'يقيس توازن زوايا الفك وامتداد خط الفك على جانبي الوجه الأيمن والأيسر.',
        'definition'  => 'درجة التطابق الهندسي بين الجانب الأيمن والأيسر لعظم الفك السفلي.',
        'unit'        => '%',
        'scale_min'   => 75.0,
        'scale_max'   => 100.0,
        'ideal_min'   => 92.0,
        'ideal_max'   => 100.0,
        'overlay'     => [
            'horizontal' => ['start' => ['x' => 300, 'y' => 710], 'end' => ['x' => 700, 'y' => 710]],
            'vertical'   => ['start' => ['x' => 500, 'y' => 680], 'end' => ['x' => 500, 'y' => 830]],
            'badge'      => ['x' => 500, 'y' => 770],
        ],
    ],

    // Frontal Gonial Angle (Jaw Angle)
    'gonial_angle_frontal' => [
        'view'        => 'front',
        'zone'        => 'الثلث السفلي',
        'title'       => 'زاوية الفك الأمامية',
        'description' => 'يقيس الزاوية العظمية البارزة لفك السفلي في الإطلالة الأمامية.',
        'definition'  => 'الزاوية المنفرجة المتشكلة عند زاوية الفك السفلي (Gonion).',
        'unit'        => '°',
        'scale_min'   => 110.0,
        'scale_max'   => 135.0,
        'ideal_min'   => 118.0,
        'ideal_max'   => 124.0,
        'overlay'     => [
            'angle'      => 122.0,
            'horizontal' => ['start' => ['x' => 310, 'y' => 710], 'end' => ['x' => 420, 'y' => 780]],
            'vertical'   => ['start' => ['x' => 310, 'y' => 610], 'end' => ['x' => 310, 'y' => 710]],
            'badge'      => ['x' => 280, 'y' => 720],
        ],
    ],

    // Chin Vertical Height
    'chin_height' => [
        'view'        => 'front',
        'zone'        => 'الثلث السفلي',
        'title'       => 'ارتفاع الذقن',
        'description' => 'يقيس الارتفاع الرأسي من الشفة السفلى حتى أسفل نقطة في الذقن (Menton).',
        'definition'  => 'المسافة الرأسية من الثلم الشفوي الذقني إلى أسفل حافة الذقن.',
        'unit'        => 'mm',
        'scale_min'   => 28.0,
        'scale_max'   => 44.0,
        'ideal_min'   => 34.0,
        'ideal_max'   => 38.0,
        'overlay'     => [
            'horizontal' => ['start' => ['x' => 440, 'y' => 760], 'end' => ['x' => 560, 'y' => 760]],
            'vertical'   => ['start' => ['x' => 500, 'y' => 680], 'end' => ['x' => 500, 'y' => 830]],
            'badge'      => ['x' => 520, 'y' => 755],
        ],
    ],

    // Chin Width
    'chin_width' => [
        'view'        => 'front',
        'zone'        => 'الثلث السفلي',
        'title'       => 'عرض الذقن',
        'description' => 'يقيس عرض قاعدة الذقن ومقارنتها بالمسافة بين زاويتي الفم.',
        'definition'  => 'المسافة العرضية عند قاعدة عظم الذقن.',
        'unit'        => 'mm',
        'scale_min'   => 35.0,
        'scale_max'   => 55.0,
        'ideal_min'   => 42.0,
        'ideal_max'   => 48.0,
        'overlay'     => [
            'horizontal' => ['start' => ['x' => 430, 'y' => 800], 'end' => ['x' => 570, 'y' => 800]],
            'vertical'   => ['start' => ['x' => 500, 'y' => 775], 'end' => ['x' => 500, 'y' => 825]],
            'badge'      => ['x' => 500, 'y' => 825],
        ],
    ],

    // Lower Facial Height (Subnasale to Menton)
    'lower_facial_height' => [
        'view'        => 'front',
        'zone'        => 'الثلث السفلي',
        'title'       => 'ارتفاع الثلث السفلي للوجه',
        'description' => 'يقيس الارتفاع الكلي من قاعدة الأنف إلى أسفل نقطة في الذقن.',
        'definition'  => 'المسافة الرأسية الإجمالية من نقطة تحت الأنف إلى أسفل نقطة في الذقن.',
        'unit'        => 'mm',
        'scale_min'   => 55.0,
        'scale_max'   => 80.0,
        'ideal_min'   => 64.0,
        'ideal_max'   => 72.0,
        'overlay'     => [
            'horizontal' => ['start' => ['x' => 420, 'y' => 700], 'end' => ['x' => 580, 'y' => 700]],
            'vertical'   => ['start' => ['x' => 500, 'y' => 560], 'end' => ['x' => 500, 'y' => 830]],
            'badge'      => ['x' => 525, 'y' => 700],
        ],
    ],

    // --------------------------------------------------------------------------
    // Side Profile View Metrics
    // --------------------------------------------------------------------------

    // Facial Convexity Angle (Glabella - Subnasale - Pogonion)
    'facial_convexity' => [
        'view'        => 'side',
        'zone'        => 'الملف الشخصي',
        'title'       => 'تحدب الوجه الجانبي',
        'description' => 'يقيس زاوية تحدب الوجه الجانبي من الجبين إلى قاعدة الأنف إلى الذقن.',
        'definition'  => 'الزاوية بين الخط الواصل من الجبين إلى قاعدة الأنف ومنها إلى أقصى بروز للذقن.',
        'unit'        => '°',
        'scale_min'   => 160.0,
        'scale_max'   => 180.0,
        'ideal_min'   => 168.0,
        'ideal_max'   => 172.0,
        'overlay'     => [
            'angle'      => 169.0,
            'horizontal' => ['start' => ['x' => 520, 'y' => 360], 'end' => ['x' => 580, 'y' => 540]],
            'vertical'   => ['start' => ['x' => 580, 'y' => 540], 'end' => ['x' => 560, 'y' => 780]],
            'badge'      => ['x' => 610, 'y' => 530],
        ],
    ],

    // Nasofrontal Angle (Forehead to Nasal Dorsum)
    'nasofrontal_angle' => [
        'view'        => 'side',
        'zone'        => 'الملف الشخصي',
        'title'       => 'الزاوية الأنفية الجبهية',
        'description' => 'تقيس الزاوية بين الجبهة وجسر الأنف.',
        'definition'  => 'الزاوية المتكونة عند نقطة التقاء الجبهة بجسر الأنف (Nasion).',
        'unit'        => '°',
        'scale_min'   => 115.0,
        'scale_max'   => 135.0,
        'ideal_min'   => 120.0,
        'ideal_max'   => 128.0,
        'overlay'     => [
            'angle'      => 124.0,
            'horizontal' => ['start' => ['x' => 480, 'y' => 260], 'end' => ['x' => 510, 'y' => 380]],
            'vertical'   => ['start' => ['x' => 510, 'y' => 380], 'end' => ['x' => 620, 'y' => 510]],
            'badge'      => ['x' => 540, 'y' => 360],
        ],
    ],

    // Nasolabial Angle (Columella to Upper Lip)
    'nasolabial_angle' => [
        'view'        => 'side',
        'zone'        => 'الملف الشخصي',
        'title'       => 'الزاوية الأنفية الشفوية',
        'description' => 'تقيس الزاوية بين قاعدة الأنف والشفة العليا.',
        'definition'  => 'الزاوية المتكونة بين خط عمود الأنف (Columella) وخط الشفة العليا.',
        'unit'        => '°',
        'scale_min'   => 85.0,
        'scale_max'   => 110.0,
        'ideal_min'   => 95.0,
        'ideal_max'   => 105.0,
        'overlay'     => [
            'angle'      => 100.0,
            'horizontal' => ['start' => ['x' => 530, 'y' => 520], 'end' => ['x' => 570, 'y' => 550]],
            'vertical'   => ['start' => ['x' => 570, 'y' => 550], 'end' => ['x' => 590, 'y' => 610]],
            'badge'      => ['x' => 600, 'y' => 540],
        ],
    ],

    // Chin Projection Profile
    'chin_projection' => [
        'view'        => 'side',
        'zone'        => 'الملف الشخصي',
        'title'       => 'بروز الذقن الجانبي',
        'description' => 'يقيس موضع الذقن مقارنة بخط السقوط العمودي من الشفة السفلى.',
        'definition'  => 'المسافة الأفقية بين نقطة بروز الذقن والخط المرجعي الرأسي المار بالشفة السفلى.',
        'unit'        => 'mm',
        'scale_min'   => -3.0,
        'scale_max'   => 5.0,
        'ideal_min'   => 0.0,
        'ideal_max'   => 2.5,
        'overlay'     => [
            'horizontal' => ['start' => ['x' => 560, 'y' => 780], 'end' => ['x' => 600, 'y' => 780]],
            'vertical'   => ['start' => ['x' => 590, 'y' => 670], 'end' => ['x' => 590, 'y' => 820]],
            'badge'      => ['x' => 610, 'y' => 770],
        ],
    ],

    // Forehead Slope Angle
    'forehead_slope' => [
        'view'        => 'side',
        'zone'        => 'الملف الشخصي',
        'title'       => 'انحدار الجبهة الجانبي',
        'description' => 'يقيس زاوية ميل الجبهة إلى الخلف عن الخط العمودي.',
        'definition'  => 'زاوية انحدار عظم الجبهة نسبة إلى الخط العمودي الحقيقي.',
        'unit'        => '°',
        'scale_min'   => 5.0,
        'scale_max'   => 20.0,
        'ideal_min'   => 9.0,
        'ideal_max'   => 14.0,
        'overlay'     => [
            'angle'      => 12.0,
            'horizontal' => ['start' => ['x' => 450, 'y' => 220], 'end' => ['x' => 510, 'y' => 360]],
            'vertical'   => ['start' => ['x' => 510, 'y' => 220], 'end' => ['x' => 510, 'y' => 360]],
            'badge'      => ['x' => 470, 'y' => 280],
        ],
    ],

    // Profile Gonial Angle
    'gonial_angle_profile' => [
        'view'        => 'side',
        'zone'        => 'الملف الشخصي',
        'title'       => 'زاوية الفك الجانبية',
        'description' => 'تقيس زاوية الفك الحقيقية من المنظور الجانبي.',
        'definition'  => 'الزاوية بين الفرع الصاعد للفك السفلي وقاعدة جسم الفك الأفقي.',
        'unit'        => '°',
        'scale_min'   => 110.0,
        'scale_max'   => 130.0,
        'ideal_min'   => 118.0,
        'ideal_max'   => 123.0,
        'overlay'     => [
            'angle'      => 120.0,
            'horizontal' => ['start' => ['x' => 360, 'y' => 690], 'end' => ['x' => 560, 'y' => 780]],
            'vertical'   => ['start' => ['x' => 380, 'y' => 520], 'end' => ['x' => 360, 'y' => 690]],
            'badge'      => ['x' => 330, 'y' => 700],
        ],
    ],

    // Submental Angle (Jaw to Neck Transition)
    'submental_angle' => [
        'view'        => 'side',
        'zone'        => 'الملف الشخصي',
        'title'       => 'الزاوية تحت الذقن (العنق)',
        'description' => 'تقيس زاوية انتقال خط الفك السفلي إلى الرقبة.',
        'definition'  => 'الزاوية بين المستوى تحت الذقني والجدار الأمامي للعنق.',
        'unit'        => '°',
        'scale_min'   => 95.0,
        'scale_max'   => 125.0,
        'ideal_min'   => 105.0,
        'ideal_max'   => 115.0,
        'overlay'     => [
            'angle'      => 110.0,
            'horizontal' => ['start' => ['x' => 560, 'y' => 780], 'end' => ['x' => 470, 'y' => 770]],
            'vertical'   => ['start' => ['x' => 470, 'y' => 770], 'end' => ['x' => 470, 'y' => 870]],
            'badge'      => ['x' => 450, 'y' => 790],
        ],
    ],

    // Brow Ridge Projection Profile
    'brow_ridge_projection' => [
        'view'        => 'side',
        'zone'        => 'الملف الشخصي',
        'title'       => 'بروز عظم الحاجب الجانبي',
        'description' => 'يقيس مدى بروز عظم الحاجب أمام قرنية العين.',
        'definition'  => 'المسافة الأفقية بين أقصى نقطة بروز لحافة الحاجب وسطح العين الأمامي.',
        'unit'        => 'mm',
        'scale_min'   => 5.0,
        'scale_max'   => 14.0,
        'ideal_min'   => 8.0,
        'ideal_max'   => 11.0,
        'overlay'     => [
            'horizontal' => ['start' => ['x' => 510, 'y' => 360], 'end' => ['x' => 550, 'y' => 360]],
            'vertical'   => ['start' => ['x' => 510, 'y' => 340], 'end' => ['x' => 510, 'y' => 380]],
            'badge'      => ['x' => 560, 'y' => 350],
        ],
    ],

    // Nasal Tip Projection (Goode Ratio)
    'nasal_tip_projection' => [
        'view'        => 'side',
        'zone'        => 'الملف الشخصي',
        'title'       => 'بروز أرنبة الأنف',
        'description' => 'يقيس نسبة بروز أرنبة الأنف مقارنة بطول الأنف الكلي (Goode Ratio).',
        'definition'  => 'المسافة الأفقية من ثلم الجناح إلى الأرنبة ÷ طول الأنف من الجذع للأرنبة.',
        'unit'        => 'ratio',
        'scale_min'   => 0.50,
        'scale_max'   => 0.70,
        'ideal_min'   => 0.55,
        'ideal_max'   => 0.62,
        'overlay'     => [
            'horizontal' => ['start' => ['x' => 530, 'y' => 520], 'end' => ['x' => 630, 'y' => 520]],
            'vertical'   => ['start' => ['x' => 530, 'y' => 480], 'end' => ['x' => 530, 'y' => 560]],
            'badge'      => ['x' => 640, 'y' => 510],
        ],
    ],

    // Mandibular Ramus Height
    'ramus_height' => [
        'view'        => 'side',
        'zone'        => 'الملف الشخصي',
        'title'       => 'ارتفاع فرع الفك الصاعد',
        'description' => 'يقيس الارتفاع الرأسي لفرع الفك من المفصل إلى زاوية الفك.',
        'definition'  => 'المسافة الرأسية بين لقمة المفصل الصدغي الفكي وزاوية الفك (Gonion).',
        'unit'        => 'mm',
        'scale_min'   => 45.0,
        'scale_max'   => 65.0,
        'ideal_min'   => 52.0,
        'ideal_max'   => 58.0,
        'overlay'     => [
            'horizontal' => ['start' => ['x' => 350, 'y' => 600], 'end' => ['x' => 390, 'y' => 600]],
            'vertical'   => ['start' => ['x' => 375, 'y' => 510], 'end' => ['x' => 365, 'y' => 690]],
            'badge'      => ['x' => 395, 'y' => 600],
        ],
    ],
];
