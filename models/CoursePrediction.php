<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "course_prediction".
 *
 * @property integer $id
 * @property integer $term
 * @property integer $course_id
 * @property integer $user_id
 * @property integer $accuracy
 *
 * @property Users $user
 * @property Courses $course
 */
class CoursePrediction extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'course_prediction';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['term', 'course_id', 'user_id', 'accuracy'], 'required'],
            [['term', 'course_id', 'user_id', 'accuracy'], 'integer'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['course_id'], 'exist', 'skipOnError' => true, 'targetClass' => Courses::className(), 'targetAttribute' => ['course_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'term' => 'Term',
            'course_id' => 'Course ID',
            'user_id' => 'User ID',
            'accuracy' => 'Accuracy',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Users::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCourse()
    {
        return $this->hasOne(Courses::className(), ['id' => 'course_id']);
    }
}
