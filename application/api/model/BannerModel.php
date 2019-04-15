<?php
namespace app\api\model;

use think\Db;
use think\Model;
class BannerModel extends Model {
    //模型对应的数据表名
    protected $table = 'banner';
    //ORM查询时隐藏字段
    protected $hidden = ['update_time', 'delete_time'];
    public function items()
    {
        return $this->hasMany('BannerItemModel','banner_id','id');
    }

    public static function getBannerByID($id)
    {
        /***********************************************************
        +  version 0.1
        ************************************************************
        |    //TODO:根据banner id去获取对应的信息
        +    try{
        |        1 / 0;
        +    }catch(Exception $e){
        |        //TODO 可以记录日志
        +        throw $e;
        |    }
        +    //返回查询到的信息
        |    return 'this is banner info';
        +*************************************************************/

        /***************************************************************
        +原生SQL查询
        ****************************************************************/
        $result = Db::Query(
            'SELECT * FROM banner_item WHERE banner_id=?',[$id]
        );
        /***************************************************************
        +构造器查询
        ****************************************************************/
        $result = Db::table('banner_item')->where('banner_id','=',$id);
        /***************************************************************
        +构造器闭包查询
        ****************************************************************/
        $result = Db::table('banner_item')->where(
            function ($query) use ($id)
            {
                //$query:查询构造器对象
                $query->where('banner_id', '=', $id);
            }
        )->select();
        /***************************************************************
        +嵌套关联之后的ORM 查询
        ****************************************************************/
        $result = self::with(['items', 'items.image'])->find($id);
        //返回查询到的信息
        return $result;
    }
}
