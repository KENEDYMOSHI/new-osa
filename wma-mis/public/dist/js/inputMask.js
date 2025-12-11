$(":input").inputmask();
$('.phone').inputmask({"mask": "255999999999"});
// $('.postal').inputmask({"mask": "P.O Box "});
 $('.postal').inputmask({ mask: 'P.O Box *{0,} *{0,}', greedy: false });
$('.tin').inputmask({"mask": "999-999-999"});
$('.license').inputmask({"mask": "9999999999"});
$('.sblSticker').inputmask({"mask": "SB9999999"});
$('.vtcSticker').inputmask({"mask": "VT9999999"});
$('.control').inputmask({"mask": "999999999999"});
$('.tansad').inputmask({ mask: 'TZNG-*{0,}', greedy: false });




