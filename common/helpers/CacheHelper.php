<?php
namespace common\helpers;


use yii\caching\TagDependency;

class CacheHelper {
    
    public static function CacheAll($query,$tag=null){
        $db = \Yii::$app->db;
        return $db->cache(function ($db) use ($query) {
            return $query->all();
        },5,new  TagDependency(['tags'=>$tag]));
    }
    
    public static function CacheOne($query,$tag=null){
        $db = \Yii::$app->db;
        return $db->cache(function ($db) use ($query) {
            return $query->one();
        },5,new  TagDependency(['tags'=>$tag]));
    }
    
}