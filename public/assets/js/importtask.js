var initMsg;

$('tr').popover({
    "html": true,
    "triger": "hover"
});

$('.import-content-delete').click(function () {
    var $this = $(this);

    bootbox.confirm({
        size: 'small',
        message: '確定將 <b>' + $this.data('content-name') + '</b> 從任務移除嗎?', 
        buttons: {
            "confirm": {
                className: 'btn btn-raised btn-primary'
            }
        }, 
        callback: function(result) {
            return (true === result) ? $this.closest('form').submit() : this.modal('hide');
        }}); 

    return false;
});

$('.import-content-push').click(function () {
    var $this = $(this);

    bootbox.confirm({
        size: 'small',
        message: '確定推送項目' + $this.data('content-name') + '嗎?', 
        buttons: {
            "confirm": {
                className: 'btn btn-raised btn-primary'
            }
        }, 
        callback: function(result) {
            return (true === result) ? window.location.href = $this.attr('href') : this.modal('hide');
        }}); 

    return false;
});

$('.import-task-delete').click(function () {
    var $this = $(this);

    bootbox.confirm({
        size: 'small',
        message: '確定刪除任務' + $this.data('task-id') + '嗎?', 
        buttons: {
            "confirm": {
                className: 'btn btn-raised btn-primary'
            }
        }, 
        callback: function(result) {
            return (true === result) ? $this.closest('form').submit() : this.modal('hide');
        }}); 

    return false;
});

$('.import-task-push').click(function () {
    var $this = $(this);

    bootbox.confirm({
        size: 'small',
        message: '確定執行任務' + $this.data('task-id') + '嗎?', 
        buttons: {
            "confirm": {
                className: 'btn btn-raised btn-primary'
            }
        }, 
        callback: function(result) {
            if (true === result) {
                $blockUI();
                window.location.href = $this.attr('href'); 
                ImportTask_loadPushProgress_init();            
            } else {
                this.modal('hide');
            }
        }}); 

    return false;
});

$('.import-task-pull').click(function () {
    var $this = $(this);

    bootbox.confirm({
        size: 'small',
        message: '確定更新任務' + $this.data('task-id') + ' 的狀態嗎?', 
        buttons: {
            "confirm": {
                className: 'btn btn-raised btn-primary'
            }
        }, 
        callback: function(result) {
            if (true === result) {
                $blockUI();
                window.location.href = $this.attr('href'); 
                ImportTask_loadPullProgress_init();            
            } else {
                this.modal('hide');
            }
        }}); 

    return false;
});

function ImportTask_loadPushProgress_init() {
    initMsg =  '(請勿關閉視窗) 推送中，請稍後...';
    $('.blockMsg').text(initMsg);
            
    return ImportTask_loadPushProgress(4000, $('.import-task-push').first().data('task-id'));
}

function ImportTask_loadPushProgress(timedistance, taskId) {
    return setTimeout(function () {
        var nextTimeDis = 1500;
        
        $.get('/flap/pos_member/import_task/' + taskId + '/push_progress', function(importedCount){
            if (0 === parseInt(importedCount)) {
                $('.blockMsg').text(initMsg);
                nextTimeDis = 5000;
            } else {
                $('.blockMsg').text('(請勿關閉視窗) 已推送' + importedCount + '筆');
                nextTimeDis = 1500;
            }
        })

        return ImportTask_loadPushProgress(nextTimeDis, taskId);
    }, timedistance);
}

function ImportTask_loadPullProgress_init() {
    initMsg =  '(請勿關閉視窗) 更新中，請稍後...';
    $('.blockMsg').text(initMsg);
            
    return ImportTask_loadPullProgress(4000, $('.import-task-pull').first().data('task-id'));
}

function ImportTask_loadPullProgress(timedistance, taskId) {
    return setTimeout(function () {
        var nextTimeDis = 2500;
        
        $.get('/flap/pos_member/import_task/' + taskId + '/pull_progress', function(importedCount){
            if (0 === parseInt(importedCount)) {
                $('.blockMsg').text(initMsg);
                nextTimeDis = 5000;
            } else {
                $('.blockMsg').text('(請勿關閉視窗) 已更新' + importedCount + '筆');
                nextTimeDis = 2500;
            }
        })

        return ImportTask_loadPullProgress(nextTimeDis, taskId);
    }, timedistance);
}

$('form#import-task').find('input[type="submit"]').click(function () {
    $blockUI(); 
    initMsg = '(請勿關閉視窗) 檔案上傳中，請稍後...';

    $('form#import-task').submit();

    ImportTask_loadImportProgress_init();

    return false;
});

function ImportTask_loadImportProgress_init() {
    $('.blockMsg').text(initMsg);
            
    return ImportTask_loadImportProgress(2000);
}

function ImportTask_loadImportProgress(timedistance) {
    return setTimeout(function () {
        var nextTimeDis = 1500;
        
        $.get('/flap/pos_member/import_task/import_progesss', function(importedCount){
            if (0 === parseInt(importedCount)) {
                $('.blockMsg').text(initMsg);
                nextTimeDis = 5000;
            } else {
                $('.blockMsg').text('(請勿關閉視窗) 已匯入' + importedCount + '筆');
                nextTimeDis = 1500;
            }
        })

        return ImportTask_loadImportProgress(nextTimeDis);
    }, timedistance);
}