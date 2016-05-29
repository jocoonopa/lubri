var initMsg;

$('tr').popover({
    "html": true,
    "triger": "hover"
});

$('form#import-task').find('input[type="submit"]').click(function () {
    var $this = $(this);

    bootbox.confirm({
        title: '任務預覽',
        message: genPreviewMessage($this), 
        buttons: {
            "confirm": {
                className: 'btn btn-raised btn-primary'
            }
        }, 
        callback: function(result) {
            return (true === result) ? submitTaskCreateForm() : this.modal('hide');
    }}); 

    return false;
});

function genPreviewMessage($e)
{
    var message = '';
    var $form = $e.closest('form');
    var tasks = {
        '任務名稱': $form.find('input[name="name"]').val(),
        '上傳檔案': $form.find('input[name="file"]').val().split('\\').pop(),
        '會員類別': $form.find('input[name="category"]').val(),
        '會員區別': $form.find('input[name="distinction"]').val(),
        '新客旗標': $form.find('input[name="insertFlagString"]').val(),
        '舊客旗標': $form.find('input[name="updateFlagString"]').val()
    };

    for (var key in tasks) {
        if (!tasks.hasOwnProperty(key)) {
            continue;
        }

        var val = tasks[key];
        
        message += getLiStyleListGroupItemString(key, val);
    }

    return '<ul class="list-group">' + message + '</ul>';
}

function getLiStyleListGroupItemString(columnName, colVal)
{
    return '<li class="list-group-item">' + columnName + ':&nbsp;' + '<b>' + colVal + '</b></li>';
}

function submitTaskCreateForm()
{
    $importBlockUI(); 
    initMsg = '(請勿關閉視窗) 檔案匯入中，請稍後...';

    $('form#import-task').submit();

    ImportTask_loadImportProgress_init();
}

$('.import-task-export').click(function () {
    var $this = $(this);

    $blockUI('檔案下載中，請稍後...');

    $.getJSON($this.data('href'), function (fileInfo) {        
        $.unblockUI();
        
        window.location.href = '/flap/pos_member/import_task/' + $this.data('task-id') + '/export?f=' + fileInfo.full;
     });      

    return false; 
});

$('.import-content-delete').click(function () {
    var $this = $(this);

    bootbox.confirm({
        size: 'small',
        message: '確定將&nbsp;<b>' + $this.data('content-name') + '</b>&nbsp;從任務移除嗎?', 
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
        message: '確定推送項目&nbsp;<b>' + $this.data('content-name') + '</b>&nbsp;嗎?', 
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
        message: '確定刪除任務&nbsp;<b>' + $this.data('task-name') + '</b>&nbsp;嗎?', 
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
        message: '確定執行任務&nbsp;<b>' + $this.data('task-name') + '</b>&nbsp;嗎?', 
        buttons: {
            "confirm": {
                className: 'btn btn-raised btn-primary'
            }
        }, 
        callback: function(result) {
            if (true === result) {
                $importBlockUI();
                window.location.href = $this.attr('href'); 

                var taskId = $this.data('task-id');
                
                ImportTask_loadPushProgress_init(taskId);            
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
        message: '確定更新任務&nbsp;<b>' + $this.data('task-name') + '</b>&nbsp;的狀態嗎?', 
        buttons: {
            "confirm": {
                className: 'btn btn-raised btn-primary'
            }
        }, 
        callback: function(result) {
            if (true === result) {
                $importBlockUI();
                window.location.href = $this.attr('href'); 
                ImportTask_loadPullProgress_init();            
            } else {
                this.modal('hide');
            }
        }}); 

    return false;
});

function ImportTask_loadPushProgress_init(taskId) {
    initMsg =  '推送中，請稍後...';
    $('.blockMsg').text(initMsg);
            
    return ImportTask_loadPushProgress(4000, taskId);
}

function ImportTask_loadPushProgress(timedistance, taskId) {
    return setTimeout(function () {
        var nextTimeDis = 1500;
        
        $.get('/flap/pos_member/import_task/' + taskId + '/push_progress', function(importedCount){
            if (0 === parseInt(importedCount)) {
                $('.blockMsg').text(initMsg);
                nextTimeDis = 5000;
            } else {
                $('.blockMsg').text('已推送' + importedCount + '筆');
                nextTimeDis = 1500;
            }
        })

        return ImportTask_loadPushProgress(nextTimeDis, taskId);
    }, timedistance);
}

function ImportTask_loadPullProgress_init() {
    initMsg =  '更新中，請稍後...';
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
                $('.blockMsg').text('已更新' + importedCount + '筆');
                nextTimeDis = 2500;
            }
        })

        return ImportTask_loadPullProgress(nextTimeDis, taskId);
    }, timedistance);
}

function ImportTask_loadImportProgress_init() {
    $('.blockMsg').find('p').text(initMsg);
            
    return ImportTask_loadImportProgress(2000);
}

function ImportTask_loadImportProgress(timedistance) {
    return setTimeout(function () {
        var nextTimeDis = 1500;
        
        $.get('/flap/pos_member/import_task/import_progesss', function(importedCount){
            if (0 === parseInt(importedCount)) {
                $('.blockMsg').find('p').text(initMsg);
                $('.blockMsg').find('.progress-bar').css('width', '0%');

                nextTimeDis = 5000;
            } else {
                $('.blockMsg').find('p').html('已匯入<b>' + importedCount + '</b>筆');
                $('.blockMsg').find('.progress-bar').css('width', Math.floor(Math.random() * 100)  + '%');
                nextTimeDis = 1500;
            }
        })

        return ImportTask_loadImportProgress(nextTimeDis);
    }, timedistance);
}

function trick_load_progress(id) {
    $importBlockUI(); ImportTask_loadPushProgress_init(id);
}

$('.check-component').find('.check-all').click(function () {
    $(this).closest('form').find('[type="checkbox"]').prop('checked', true);
});

$('.check-component').find('.cancel-all').click(function () {
    $(this).closest('form').find('[type="checkbox"]').prop('checked', false);
});