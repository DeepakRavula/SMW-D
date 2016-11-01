<?php
use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\ItemType;
?>
<table>
    <tr>
        <td width="20%">
            <?php
                if((int) $model->item_type_id === ItemType::TYPE_PRIVATE_LESSON){
                    echo 'LESSON';
                }else{
                    echo 'MISC';
                }
            ?>
        </td>
        <td width="50%">
            <?php
                echo $model->description;
            ?>
        </td>
        <td width="10%">
            <?php
                echo $model->unit;
            ?>
        </td>
        <td width="10%">
        <?php
            if($model->item_type_id === ItemType::TYPE_PRIVATE_LESSON){
                echo $model->lesson->enrolment->program->rate;
            }else{
                echo $model->amount;
            }
        ?>
        </td>
        <td width="10%">
        <?php
            echo $model->amount;
        ?>
        </td>
    </tr>
</table>