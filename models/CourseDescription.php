<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "course_description".
 *
 * @property string $code
 * @property string $description
 */
class CourseDescription extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'course_description';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['code', 'description'], 'required'],
            [['description'], 'string'],
            [['code'], 'string', 'max' => 15],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'code' => 'Code',
            'description' => 'Description',
        ];
    }
}
