<?php
/**
 * Created by PhpStorm.
 * User: lijian
 * Date: 16/5/13
 * Time: 下午3:33
 */

namespace Admin\Controller;


/**
 * 阶段性共修
 * Class StageGXController
 * @package Admin\Controller
 */
class StageGXController extends CommonController{

    public function addStageGX(){
        $this->display("editStageGX");
    }

    public function editStageGX(){
        $id = I("id");
        $stageGX = M("stage_gx")->where("id=$id")->find();
        $this->assign("stageGX", $stageGX);
        $this->display("editStageGX");
    }

    public function stageGXList(){
        $list = M("stage_gx")->select();
        $this->assign("list", $list);
        $this->display();
    }

    public function editStageGXHandler(){
        $id = I("id");
        $stageGX['title'] = I("title");
        $stageGX['num'] = I("num");
        $stageGX['beg_date'] = I("begDate");
        $stageGX['end_date'] = I("endDate");
        if( $id ){
            $stageGX['id'] = $id;
            M("stage_gx")->save($stageGX);
        }else{
            if( !M("stage_gx")->add($stageGX) ){
                $this->error("数据库中无法添加阶段性共修!");
            }
        }
        $this->success("成功!");
    }

}