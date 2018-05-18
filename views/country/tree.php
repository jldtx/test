<?php
use yii\helpers\Html;
use app\assets\AppAsset;  
/* @var $this yii\web\View */
/* @var $searchModel app\models\CountrySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */



AppAsset::register($this); 
$this->registerCssFile('@web/js/jstree/default/style.min.css',['depends'=>['app\assets\AppAsset']]); 

$this->title = 'Countries';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="country-index">
	<h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Country', ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('查看树状结构', ['tree'], ['class' => 'btn btn-success']) ?>
    </p>
    <p>
    	<div id="ajax"></div>
    </p>
</div>
<?php 
$this->registerJsFile('@web/js/jquery.min.js',['depends'=>['app\assets\AppAsset']]);
$this->registerJsFile('@web/js/jstree/jstree.min.js',['depends'=>['app\assets\AppAsset']]);
?>
<?php $this->registerJs('
$("#ajax").jstree({
    "core" : {
    	"data" : {
    		"url" : "http://127.0.0.1/yii2/web/index.php?r=country/treedata",
			"dataType" : "json" 
		},
		"check_callback" : true
	},
	"plugins" : ["contextmenu"],
	"contextmenu": {
		"items": {
			"remove": null,
			"ccp": null,
			"create":{    
                "label":"新建组织",    
                "action":function(data){  
					var inst = jQuery.jstree.reference(data.reference),  
					obj = inst.get_node(data.reference);  
					inst.create_node(obj, {}, "last", function (new_node) {  
						try {  
							new_node.text="请写入组织名称";  
							inst.edit(new_node);  
						} catch (ex) {
							setTimeout(function () { inst.edit(new_node); },0);  
						}
					});  
                }    
            }, 
			"rename":{
				"label": "修改组织",
				"action": function (obj) {
					var inst = jQuery.jstree.reference(obj.reference);
					var clickedNode = inst.get_node(obj.reference);
					inst.edit(obj.reference,clickedNode.val)
				}
			},
			"delete": {
			"label": "删除组织",
				"action": function (obj) {
					var inst = jQuery.jstree.reference(obj.reference);
					var clickedNode = inst.get_node(obj.reference);
					inst.delete_node(obj.reference);
				}
			}
		}
	}
});
	//节点重命名事件
    $("#ajax").on("rename_node.jstree", function (e, data) {
		let upInfo = {};
		upInfo.id = data.node.id;
		upInfo.text = data.text;
		upInfo.old = data.old;
		$.ajax({
			url:"http://127.0.0.1/yii2/web/index.php?r=country/treedata",
			type:"POST",
			data:upInfo
		}).then((response)=>{
			if(response == 1){
				//alert("重命名成功");
			}else{
				alert(response);
			}
		})
    });
	//节点删除事件
    $("#ajax").on("delete_node.jstree", function (e, data) {
		let upInfo = {};
		upInfo.id = data.node.id;
		$.ajax({
			url:"?m=organize&c=index&a=delete_node",
			type:"POST",
			data:upInfo
		}).then((response)=>{
			if(response == 1){

			}else{
				console.error(response);
			}
		})
    });
	//节点添加事件
    $("#ajax").on("create_node.jstree", function (e, data) {
		let upInfo = {};
		upInfo.id = data.node.id;
		upInfo.pid = data.parent;
		$.ajax({
			url:"?m=organize&c=index&a=create_node",
			type:"POST",
			data:upInfo
		}).then((response)=>{
			if(response.status == 1){
				var re = jQuery.jstree.reference("#ajax").set_id(data.node,response.id);
			}else{
				alert(response);
			}
		})
	});
')?>
