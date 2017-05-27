<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\Users;
use app\models\Courses;
use app\models\CourseUser;
use app\models\CoursesUser;
use yii\db\Query;

class CourseController extends BaseController
{
    public $prerequisites = array();

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                ],
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex($id)
    {
        $user = $this->findUser($id);
        $model = new CoursesUser;
        $model->user_id = $user->id;

        // view form for term for user input
        if ($term  = Yii::$app->request->get('term')) {
            $model->term = $term;
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                return $this->redirect(['index', 'id' => $id]);
            }
            return $this->render('form', [
                'model' => $model
            ]);
        }

        // view courses recommendation after calling buildRecommendation method
        if ($term = Yii::$app->request->get('run')) {
            $courses = $this->buildRecommendation($id, $term);
            $coursesSort = [];
            foreach ($courses as $key => $row)
            {
                $coursesSort[$key] = $row['accuracy'];
            }
            array_multisort($coursesSort, SORT_DESC, $courses);
            return $this->render('view', [
                'models' => $courses
            ]);
        }

        // view all courses from user input
        return $this->render('index', [
            'models' => CourseUser::findTermCategories($user->id),
        ]);
    }

    /**
     * Delete all courses for specific user id and term
     *
     * @return string
     */
    public function actionDelete($id, $term)
    {
        $user = $this->findUser($id);
        foreach (CourseUser::find()->where(['user_id' => $user->id, 'term' => $term])->all() as $model) {
            $model->delete();
        }
        return $this->redirect(['index', 'id' => $id]);
    }

    /**
     * Change course status (pass or not)
     *
     * @return string
     */
    public function actionTogglePass($user, $id)
    {
        $user = $this->findUser($user);
        $model = $this->findCourseUser($id);
        if ($model->is_pass) {
            $model->is_pass = 0;
        } else {
            $model->is_pass = 1;
        }
        $model->save();
        return $this->redirect(['index', 'id' => $user->id]);
    }

    /**
     * API for get list of course with request string q and return json as reponse 
     *
     * @return string
     */
    public function actionCourseList($q = null, $id = null)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];
        if (!is_null($q)) {
            $query = new Query;
            $query->select('id, name AS text')
                ->from('courses')
                ->where(['ilike', 'name', $q])
                ->limit(5);
            $command = $query->createCommand();
            $data = $command->queryAll();
            $out['results'] = array_values($data);
        }
        elseif ($id > 0) {
            $out['results'] = ['id' => $id, 'text' => Profile::find($id)->name];
        }
        return $out;
    }

    /**
     * With decision tree representation 
     *
     * @return [] array
     */
    private function buildRecommendation($id, $term)
    {
        $user = $this->findUser($id);
        $results = [];
        $userCourses = CourseUser::find()->where(['user_id' => $user->id, 'is_pass' => 1])->all();
        $allCourses = Courses::find()->all();

        $totalWeight = 0;
        foreach ($allCourses as $model) {
            $weight = 0;

            // convert year and term in course to real term (1 - 8)
            if ($model->term == 1) {
                $realTerm = $model->year + ($model->year - 1);
            } elseif ($model->term == 2) {
                $realTerm = $model->term * $model->year;
            } else {
                if (fmod($term, 2) == 0) {
                    $realTerm = 2 * $model->year;
                } else {
                    $realTerm = $model->year + ($model->year - 1);
                }
            }

            // ignore all course is already taken
            if (! $this->isAlreadyTaken($userCourses, $model)) {

                // ignore course if need prerequisite from course haven't taken by user
                if (! $this->isPrerequisite($userCourses, $model)) {
                    $weight += 10;
                    $weight += $this->valueKnowledge($userCourses, $model);

                    // check "program studi" with prediction
                    if ($this->checkFacultyCourse($userCourses, $model)) {
                        $weight += 10;

                        // realTerm == $term    // -_- \\
                        if ($realTerm == $term) {
                            $weight += 30;
                            if ($model->is_required) {
                                $weight += 30;
                                if ($model->category == 'UUI') {
                                    $weight += 30;
                                } elseif ($model->category == 'IKI') {
                                    $weight += 20;
                                }
                            } else {
                                if ($model->category == 'UUI') {
                                    $weight += 20;
                                } elseif ($model->category == 'IKI') {
                                    $weight += 10;
                                }
                            }

                        // $realTerm < $term    // -_- \\
                        } elseif ($realTerm < $term) {
                            if ((fmod($realTerm, 2) == 0 && fmod($term, 2) == 0) || (fmod($realTerm, 2) == 1 && fmod($term, 2) == 1)) {
                                $weight += 40;
                                if ($model->is_required) {
                                    $weight += 40;
                                    if ($model->category == 'UUI') {
                                        $weight += 30;
                                    } elseif ($model->category == 'IKI') {
                                        $weight += 20;
                                    }
                                } else {
                                    if ($model->category == 'UUI') {
                                        $weight += 20;
                                    } elseif ($model->category == 'IKI') {
                                        $weight += 10;
                                    }
                                }
                            } else {
                                $weight += 30;
                                if ($model->is_required) {
                                    $weight += 50;
                                    if ($model->category == 'UUI') {
                                        $weight += 30;
                                    } elseif ($model->category == 'IKI') {
                                        $weight += 20;
                                    }
                                } else {
                                    if ($model->category == 'UUI') {
                                        $weight += 20;
                                    } elseif ($model->category == 'IKI') {
                                        $weight += 10;
                                    }
                                }
                            }

                        // $realTerm < $term    // -_- \\
                        } else {
                            if ((fmod($realTerm, 2) == 0 && fmod($term, 2) == 0) || (fmod($realTerm, 2) == 1 && fmod($term, 2) == 1)) {
                                $weight += 20;
                                if ($model->is_required) {
                                    $weight += 20;
                                    if ($model->category == 'UUI') {
                                        $weight += 20;
                                    } elseif ($model->category == 'IKI') {
                                        $weight += 10;
                                    }
                                } else {
                                    if ($model->category == 'UUI') {
                                        $weight += 10;
                                    } elseif ($model->category == 'IKI') {
                                        $weight += 5;
                                    }
                                }
                            } else {
                                $weight += 10;
                                if ($model->is_required) {
                                    $weight += 30;
                                    if ($model->category == 'UUI') {
                                        $weight += 20;
                                    } elseif ($model->category == 'IKI') {
                                        $weight += 10;
                                    }
                                } else {
                                    if ($model->category == 'UUI') {
                                        $weight += 10;
                                    } elseif ($model->category == 'IKI') {
                                        $weight += 5;
                                    }
                                }
                            }
                        }
                    }
                }
            }

            // build results
            $results[] = [
                'model' => $model,
                'accuracy' => $weight
            ];
            $totalWeight += $weight;
        }
        $realResults = [];

        // make real results with real accuracy
        foreach ($results as $model) {
            $realResults[] = [
                'model' => $model['model'],
                'accuracy' => $model['accuracy'] / $totalWeight
            ];
        }
        return $realResults;
    }

    /**
     * Check if $course corresponding with "prodi" user
     *
     * @return boolean
     */
    private function checkFacultyCourse($userCourses, $course)
    {
        return ($course->category != 'MAT' && $course->category != 'FSK' && $course->category != 'IKO' && $course->category != 'IKS') 
            || ($this->isPredictIlkom($userCourses) && ($course->category == 'IKO' || $course->category == 'FSK' || $course->category == 'MAT')) 
            || (! $this->isPredictIlkom($userCourses) && $course->category == 'IKS');
    }

    /**
     * Check if $course have prerequisite with other course that haven't taken by user or minimum SKS
     *
     * @return boolean
     */
    private function isPrerequisite($userCourses, $course)
    {
        $sks = 0;
        if ($course->prerequisite || $course->min_sks) {
            foreach ($userCourses as $model) {
                $sks += $model->course->sks;
                if (strpos($course->prerequisite, $model->course->code) !== false) {
                    return false;
                }
            }
            if ($course->min_sks > $sks) {
                return true;
            }
            if ($course->prerequisite) {
                foreach (explode(', ', $course->prerequisite) as $code) {
                    if (array_key_exists($code, $this->prerequisites)) {
                        $this->prerequisites[$code] += 1;
                    } else {
                        $this->prerequisites[$code] = 1;
                    }
                }
                return true;
            }
        }
        return false;
    }

    /**
     * Check if $course is already taken or not
     *
     * @return boolean
     */
    private function isAlreadyTaken($userCourses, $course)
    {
        foreach ($userCourses as $model) {
            if ($model->course->code == $course->code) {
                return true;
            }
        }
        return false;
    }

    /**
     * Special method for predicting user's "prodi"
     *
     * @return boolean
     */
    private function isPredictIlkom($userCourses)
    {
        $results = [
            'FSK' => 0,
            'MAT' => 0,
            'IKO' => 0,
            'IKI' => 0,
            'IKS' => 0,
            'UUI' => 0,
        ];
        foreach ($userCourses as $model) {
            $results[$model->course->category] += 1;
        }
        return $results['IKS'] < ($results['IKO'] + ($results['MAT']-1) + ($results['FSK']-1));
    }

    /**
     * Make value for $course if corresponding with user's previous courses
     *
     * @return integer
     */
    private function valueKnowledge($userCourses, $course)
    {
        $results = [
            'matematika_dan_komputasi_ilmiah' => 0,
            'pemrograman_dan_rekayasa_perangkat_lunak' => 0,
            'pengolahan_informasi_cerdas' => 0,
            'komputasi_dan_algoritma' => 0,
            'arsitektur_dan_infrastruktur' => 0,
            'sistem_enterprise' => 0,
            'teknologi_informasi' => 0,
            'sistem_informasi_dan_aplikasi' => 0,
            'kepribadian_dan_ketrampilan_berkarya' => 0,
        ];
        foreach ($userCourses as $model) {
            if ($model->course->matematika_dan_komputasi_ilmiah) {
                $results['matematika_dan_komputasi_ilmiah'] += 1;
            } elseif ($model->course->pemrograman_dan_rekayasa_perangkat_lunak) {
                $results['pemrograman_dan_rekayasa_perangkat_lunak'] += 1;
            } elseif ($model->course->pengolahan_informasi_cerdas) {
                $results['pengolahan_informasi_cerdas'] += 1;
            } elseif ($model->course->komputasi_dan_algoritma) {
                $results['komputasi_dan_algoritma'] += 1;
            } elseif ($model->course->arsitektur_dan_infrastruktur) {
                $results['arsitektur_dan_infrastruktur'] += 1;
            } elseif ($model->course->sistem_enterprise) {
                $results['sistem_enterprise'] += 1;
            } elseif ($model->course->teknologi_informasi) {
                $results['teknologi_informasi'] += 1;
            } elseif ($model->course->sistem_informasi_dan_aplikasi) {
                $results['sistem_informasi_dan_aplikasi'] += 1;
            } elseif ($model->course->kepribadian_dan_ketrampilan_berkarya) {
                $results['kepribadian_dan_ketrampilan_berkarya'] += 1;
            } 
        }
        return $this->getKnowledgeType($course) ? $results[$this->getKnowledgeType($course)] * 10 : 0;
    }

    /**
     * Just helper method for get knowledge type of course
     *
     * @return stirng
     */
    private function getKnowledgeType($course)
    {
        if ($course->matematika_dan_komputasi_ilmiah) {
            return 'matematika_dan_komputasi_ilmiah';
        } elseif ($course->pemrograman_dan_rekayasa_perangkat_lunak) {
            return 'pemrograman_dan_rekayasa_perangkat_lunak';
        } elseif ($course->pengolahan_informasi_cerdas) {
            return 'pengolahan_informasi_cerdas';
        } elseif ($course->komputasi_dan_algoritma) {
            return 'komputasi_dan_algoritma';
        } elseif ($course->arsitektur_dan_infrastruktur) {
            return 'arsitektur_dan_infrastruktur';
        } elseif ($course->sistem_enterprise) {
            return 'sistem_enterprise';
        } elseif ($course->teknologi_informasi) {
            return 'teknologi_informasi';
        } elseif ($course->sistem_informasi_dan_aplikasi) {
            return 'sistem_informasi_dan_aplikasi';
        } elseif ($course->kepribadian_dan_ketrampilan_berkarya) {
            return 'kepribadian_dan_ketrampilan_berkarya';
        } else {
            return null;
        }
    }

    /**
     * Find user in database
     *
     * @return string
     */
    private function findUser($id)
    {
        if ($model = Users::findOne($id)) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * Find course_user in database
     *
     * @return string
     */
    private function findCourseUser($id)
    {
        if ($model = CourseUser::findOne($id)) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
