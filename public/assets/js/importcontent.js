moment.locale('zh-tw');
var bmdObg = {
    'time': false, 
    'clearButton': true,
    'cancelText': '取消',
    'okText': '確認',
    'clearText': '清除',
    'format': 'YYYYMMDD'
};

$('#period_at').add($('#birthday')).bootstrapMaterialDatePicker(bmdObg);
$('#twzipcode').twzipcode();