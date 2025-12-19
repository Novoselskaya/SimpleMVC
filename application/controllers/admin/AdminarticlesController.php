<?php
namespace application\controllers\admin;
use ItForFree\SimpleMVC\Config;
use ItForFree\SimpleMVC\Router\WebRouter;
use \application\models\ArticleModel;
use \application\models\CategoryModel;
use \application\models\SubcategoryModel;
use \application\models\UserModel;

/**
 * Администрирование статей
 */
class AdminarticlesController extends \ItForFree\SimpleMVC\MVC\Controller
{
    
    public string $layoutPath = 'admin-main.php';
    
    protected array $rules = [
         ['allow' => true, 'roles' => ['@']], // разрешаем доступ всем авторизованным пользователям
         ['allow' => false, 'roles' => ['?']], // запрещаем только гостям
    ];
    
    /**
     * Список всех статей
     */
    public function indexAction()
    {
        $Article = new ArticleModel();
        $data = $Article->getListFiltered(1000000, null, null, "publicationDate DESC", false);
        $articles = $data['results'];
        
        // Получаем категории
        $Category = new CategoryModel();
        $categoryData = $Category->getList();
        $categories = array();
        foreach ($categoryData['results'] as $category) {
            $categories[$category->id] = $category;
        }
        
        $this->view->addVar('articles', $articles);
        $this->view->addVar('categories', $categories);
        $this->view->addVar('totalRows', $data['totalRows']);
        $this->view->render('article/index.php');
    }

    /**
     * Создание новой статьи
     */
    public function addAction()
    {
        $Url = Config::get('core.router.class');
        
        if (!empty($_POST)) {
            if (!empty($_POST['saveChanges'])) {
                $Article = new ArticleModel();
                $article = $Article->loadFromArray($_POST);
                
                // Обработка даты публикации
                if (isset($_POST['publicationDate']) && !empty($_POST['publicationDate'])) {
                    $article->publicationDate = strtotime($_POST['publicationDate']);
                } else {
                    $article->publicationDate = time();
                }
                
                // Обработка видимости
                $article->is_visible = isset($_POST['is_visible']) ? 1 : 0;
                
                // Обработка категории
                if (empty($_POST['categoryId']) || $_POST['categoryId'] == 0) {
                    $article->categoryId = null;
                }
                
                // Обработка подкатегории
                if (empty($_POST['subcategory_id']) || $_POST['subcategory_id'] == 0) {
                    $article->subcategory_id = null;
                }
                
                // Обработка благодарностей
                if (isset($_POST['thanksIds']) && is_array($_POST['thanksIds'])) {
                    $article->thanksIds = array_map('intval', $_POST['thanksIds']);
                } else {
                    $article->thanksIds = array();
                }
                
                $article->insert();
                $this->redirect($Url::link("admin/adminarticles/index"));
            } 
            elseif (!empty($_POST['cancel'])) {
                $this->redirect($Url::link("admin/adminarticles/index"));
            }
        } else {
            // Получаем категории для формы
            $Category = new CategoryModel();
            $categoryData = $Category->getList();
            $categories = $categoryData['results'];
            
            // Получаем подкатегории
            $Subcategory = new SubcategoryModel();
            $subcategoryData = $Subcategory->getList();
            $allSubcategories = $subcategoryData['results'];
            
            // Получаем пользователей для выбора авторов
            $User = new UserModel();
            $usersData = $User->getList();
            $users = $usersData['results'];
            
            // Создаем пустую статью для формы
            $article = new ArticleModel();
            $article->publicationDate = time();
            $article->is_visible = 1;
            
            $this->view->addVar('article', $article);
            $this->view->addVar('categories', $categories);
            $this->view->addVar('allSubcategories', $allSubcategories);
            $this->view->addVar('users', $users);
            $this->view->addVar('formAction', 'add');
            $this->view->addVar('pageTitle', 'Новая статья');
            $this->view->render('article/edit.php');
        }
    }
    
    /**
     * Редактирование статьи
     */
    public function editAction()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $Url = Config::get('core.router.class');
            $this->redirect($Url::link("admin/adminarticles/index"));
            return;
        }
        
        $Url = Config::get('core.router.class');
        $Article = new ArticleModel();
        
        if (!empty($_POST)) {
            if (!empty($_POST['saveChanges'])) {
                $article = $Article->loadFromArray($_POST);
                
                // Обработка даты публикации
                if (isset($_POST['publicationDate']) && !empty($_POST['publicationDate'])) {
                    $article->publicationDate = strtotime($_POST['publicationDate']);
                }
                
                // Обработка видимости
                $article->is_visible = isset($_POST['is_visible']) ? 1 : 0;
                
                // Обработка категории
                if (empty($_POST['categoryId']) || $_POST['categoryId'] == 0) {
                    $article->categoryId = null;
                }
                
                // Обработка подкатегории
                if (empty($_POST['subcategory_id']) || $_POST['subcategory_id'] == 0) {
                    $article->subcategory_id = null;
                }
                
                // Обработка благодарностей
                if (isset($_POST['thanksIds']) && is_array($_POST['thanksIds'])) {
                    $article->thanksIds = array_map('intval', $_POST['thanksIds']);
                } else {
                    $article->thanksIds = array();
                }
                
                $article->update();
                $this->redirect($Url::link("admin/adminarticles/index"));
            } 
            elseif (!empty($_POST['cancel'])) {
                $this->redirect($Url::link("admin/adminarticles/index"));
            }
        } else {
            $article = $Article->getById($id);
            
            if (!$article) {
                $this->redirect($Url::link("admin/adminarticles/index"));
                return;
            }
            
            // Получаем категории для формы
            $Category = new CategoryModel();
            $categoryData = $Category->getList();
            $categories = $categoryData['results'];
            
            // Получаем подкатегории
            $Subcategory = new SubcategoryModel();
            $subcategoryData = $Subcategory->getList();
            $allSubcategories = $subcategoryData['results'];
            
            // Получаем пользователей для выбора авторов
            $User = new UserModel();
            $usersData = $User->getList();
            $users = $usersData['results'];
            
            $this->view->addVar('article', $article);
            $this->view->addVar('categories', $categories);
            $this->view->addVar('allSubcategories', $allSubcategories);
            $this->view->addVar('users', $users);
            $this->view->addVar('formAction', 'edit');
            $this->view->addVar('pageTitle', 'Редактирование статьи');
            $this->view->render('article/edit.php');
        }
    }
    
    /**
     * Удаление статьи
     */
    public function deleteAction()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $Url = Config::get('core.router.class');
            $this->redirect($Url::link("admin/adminarticles/index"));
            return;
        }
        
        $Url = Config::get('core.router.class');
        $Article = new ArticleModel();
        
        if (!empty($_POST)) {
            if (!empty($_POST['deleteArticle'])) {
                $article = $Article->loadFromArray($_POST);
                $article->delete();
                $this->redirect($Url::link("admin/adminarticles/index"));
            }
            elseif (!empty($_POST['cancel'])) {
                $this->redirect($Url::link("admin/adminarticles/edit&id=$id"));
            }
        } else {
            $article = $Article->getById($id);
            
            if (!$article) {
                $this->redirect($Url::link("admin/adminarticles/index"));
                return;
            }
            
            $this->view->addVar('article', $article);
            $this->view->addVar('pageTitle', 'Удаление статьи');
            $this->view->render('article/delete.php');
        }
    }
}

