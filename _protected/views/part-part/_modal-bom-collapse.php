<div class="modal fade"  id="modal-bom-collapse">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        
        
        <p style="float: right;margin-bottom: 0px">
          <button type="button" class="btn btn-sm btn-info" title="<?=Yii::t('app','Download')?>" id="btnDownloadDetBom">
            <span class="glyphicon glyphicon-download-alt"></span>
          </button>
          <button type="button" class="btn btn-sm btn-info" data-dismiss="modal"  title="<?=Yii::t('app','Close')?>">
            <span class="glyphicon glyphicon-remove"></span>
          </button>
        </p>
        
        <p style="float: left;margin-bottom: 0px">
          <span id="selected_part_state">P</span>
        </p>
        <p style="float: left;margin-left: 10px;margin-bottom: 0px">
          <span class="modal-title" id="selected_part_info"></span> | <span id="selected_part_unit"></span>
          <br>
          <span id="selected_part_name"></span>
        </p>
        
        
      </div>
      <div class="modal-body">
<!--        <table style="display: none" id="tableDownload"><tbody></tbody></table>-->
        <form method="post" action="<?=\yii\helpers\Url::toRoute('part-part/download-det-bom')?>" id="formDownload" >
          <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->csrfToken; ?>" />
          <input type="hidden" name="parts" id="dataDownload">
        </form>
        <ul class="bom_treeview">
          
          
        </ul>
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-info pull-right btn-sm" data-dismiss="modal"> <span class="glyphicon glyphicon-remove"></span> <?=Yii::t('app', 'Close')?></button>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>






