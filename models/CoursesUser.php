<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;

class CoursesUser extends Model
{
    public $user_id;
    public $term;
    public $courses;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['user_id', 'term'], 'required'],
            [['courses'], 'safe']
        ];
    }

    public function save()
    {
        foreach (CourseUser::find()->where(['user_id' => $this->user_id, 'term' => $this->term])->all() as $model) {
            $model->delete();
        }
        foreach ($this->courses as $id) { 
            $model = new CourseUser([
                'user_id' => $this->user_id, 
                'term' => $this->term,
                'course_id' => $id
            ]);
            if (! $model->save()) {
                return false;
            }
        }
        return true;
    }

    public function getCourses()
    {
        $array = Courses::find()->asArray()->all();
        return ArrayHelper::map($array, 'id', 'name');
    }

    public function getCurrentCourses()
    {
        $array = CourseUser::find()->where(['term' => $this->term, 'user_id' => $this->user_id])->asArray()->all();
        return ArrayHelper::getColumn($array, 'course_id');
    }
}
