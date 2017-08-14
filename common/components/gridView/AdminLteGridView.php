<?php
namespace common\components\gridView;
use \yiister\adminlte\widgets\grid\GridView;

class AdminLteGridView extends GridView
{
    //register css file manually
    public $layout = '
        <div class="box">
            <div class="box-header">
            {summary}
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-sm-12">
                        {items}
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12"><div class="dataTables_paginate">{pager}</div></div>
                </div>
            </div>
        </div>
    ';
}