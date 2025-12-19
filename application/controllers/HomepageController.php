<?php

namespace application\controllers;

use application\models\ArticleModel;
use application\models\CategoryModel;

/**
 * Контроллер для домашней страницы
 */
class HomepageController extends \ItForFree\SimpleMVC\MVC\Controller
{
    /**
     * @var string Название страницы
     */
    public $homepageTitle = "Домашняя страница";
    
    /**
     * @var string Пусть к файлу макета 
     */
    public string $layoutPath = 'cms-main.php';
    
    protected array $rules = [
        ['allow' => true, 'roles' => ['?', '@'], 'actions' => ['index']],
    ];
      
    /**
     * Выводит на экран страницу "Домашняя страница" со списком статей
     */
    public function indexAction()
    {
        $results = array();
        
        // Получаем статьи (первые 5 для главной страницы, как в оригинале)
        $HOMEPAGE_NUM_ARTICLES = 5;
        $Article = new ArticleModel();
        $data = $Article->getListFiltered($HOMEPAGE_NUM_ARTICLES, null, null, "publicationDate DESC", true);
        $results['articles'] = $data['results'] ?? [];
        $results['totalRows'] = $data['totalRows'] ?? 0;
        
        // Отладка: проверяем что получили
        // error_log("Articles count: " . count($results['articles']));
        // if (!empty($results['articles'])) {
        //     error_log("First article title: " . ($results['articles'][0]->title ?? 'NULL'));
        // }
        
        // Получаем категории
        $Category = new CategoryModel();
        $categoryData = $Category->getList();
        $results['categories'] = array();
        if (isset($categoryData['results']) && is_array($categoryData['results'])) {
            foreach ($categoryData['results'] as $category) {
                $results['categories'][$category->id] = $category;
            }
        }
        
        $results['pageHeading'] = "Последние статьи";
        $results['pageTitle'] = $results['pageHeading'] . " | Простой CMS блог";
        
        $this->view->addVar('results', $results);
        $this->view->addVar('homepageTitle', $this->homepageTitle);
        $this->view->render('homepage/index.php');
    }
}

