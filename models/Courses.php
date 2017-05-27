<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "courses".
 *
 * @property integer $id
 * @property string $code
 * @property string $name
 * @property integer $sks
 * @property string $category
 * @property integer $year
 * @property integer $term
 * @property integer $is_required
 * @property string $prerequisite
 * @property integer $min_sks
 * @property integer $matematika_dan_komputasi_ilmiah
 * @property integer $pemrograman_dan_rekayasa_perangkat_lunak
 * @property integer $pengolahan_informasi_cerdas
 * @property integer $komputasi_dan_algoritma
 * @property integer $arsitektur_dan_infrastruktur
 * @property integer $sistem_enterprise
 * @property integer $teknologi_informasi
 * @property integer $sistem_informasi_dan_aplikasi
 * @property integer $kepribadian_dan_ketrampilan_berkarya
 *
 * @property CoursePrediction[] $coursePredictions
 * @property CourseUser[] $courseUsers
 */
class Courses extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'courses';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['code', 'name', 'sks', 'category', 'year', 'term'], 'required'],
            [['sks', 'year', 'term', 'is_required', 'min_sks', 'matematika_dan_komputasi_ilmiah', 'pemrograman_dan_rekayasa_perangkat_lunak', 'pengolahan_informasi_cerdas', 'komputasi_dan_algoritma', 'arsitektur_dan_infrastruktur', 'sistem_enterprise', 'teknologi_informasi', 'sistem_informasi_dan_aplikasi', 'kepribadian_dan_ketrampilan_berkarya'], 'integer'],
            [['prerequisite'], 'string'],
            [['code', 'category'], 'string', 'max' => 15],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'code' => 'Code',
            'name' => 'Name',
            'sks' => 'Sks',
            'category' => 'Category',
            'year' => 'Year',
            'term' => 'Term',
            'is_required' => 'Is Required',
            'prerequisite' => 'Prerequisite',
            'min_sks' => 'Min Sks',
            'matematika_dan_komputasi_ilmiah' => 'Matematika Dan Komputasi Ilmiah',
            'pemrograman_dan_rekayasa_perangkat_lunak' => 'Pemrograman Dan Rekayasa Perangkat Lunak',
            'pengolahan_informasi_cerdas' => 'Pengolahan Informasi Cerdas',
            'komputasi_dan_algoritma' => 'Komputasi Dan Algoritma',
            'arsitektur_dan_infrastruktur' => 'Arsitektur Dan Infrastruktur',
            'sistem_enterprise' => 'Sistem Enterprise',
            'teknologi_informasi' => 'Teknologi Informasi',
            'sistem_informasi_dan_aplikasi' => 'Sistem Informasi Dan Aplikasi',
            'kepribadian_dan_ketrampilan_berkarya' => 'Kepribadian Dan Ketrampilan Berkarya',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCoursePredictions()
    {
        return $this->hasMany(CoursePrediction::className(), ['course_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCourseUsers()
    {
        return $this->hasMany(CourseUser::className(), ['course_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCourseDescription()
    {
        return $this->hasOne(CourseDescription::className(), ['code' => 'code']);
    }

    public function getPrerequisiteCourses()
    {
        $results = [];
        if ($this->prerequisite) {
            foreach (explode(', ', $this->prerequisite) as $code) {
                $results[] = Courses::findOne(['code' => $code]);
            }
        }
        return $results;
    }

    public function getSimplePrerequisiteCourses()
    {
        $results = [];
        if ($this->prerequisite) {
            foreach (explode(', ', $this->prerequisite) as $code) {
                $results[] = Courses::findOne(['code' => $code]) ? Courses::findOne(['code' => $code])->name :'';
            }
        }
        return $results;
    }
}
