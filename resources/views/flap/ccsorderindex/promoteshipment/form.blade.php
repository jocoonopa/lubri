<div class="form-group label-floating">
    <div class="row">
        <div class="col-md-3">
            <label class="control-label" for="promote-start-at">活動開始時間</label>
            <input class="form-control" name="promote-start-at" id="promote-start-at" type="text">
        </div>
        <div class="col-md-3">
            <label class="control-label" for="promote-end-at">活動結束時間</label>
            <input class="form-control" name="promote-end-at" id="promote-end-at" type="text">
        </div>
        <div class="col-md-3">
            <label class="control-label" for="promote-code">活動代號</label>
            <input class="form-control" name="promote-code" id="promote-code" type="text">
        </div>
        <div class="col-md-3">
            <button type="button" class="promote-add btn btn-raised btn-default" disabled>
                <i class="glyphicon glyphicon-plus"></i>
            </button>
        </div>    
    </div>
    
    <p class="help-block">請選擇時間區間，並且指定活動代號</p>
</div>

<div class="form-group">
    <input type="hidden" name="promote-q" value="" />
    <button type="submit" class="btn btn-raised btn-primary" disabled><i class="glyphicon glyphicon-save"></i> 匯出</button>
</div>



