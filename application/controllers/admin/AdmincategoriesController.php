<?php
namespace application\controllers\admin;

use ItForFree\SimpleMVC\Config;
use ItForFree\SimpleMVC\Router\WebRouter;
use application\models\CategoryModel;
use application\models\ArticleModel;

/**
 * Администрирование категорий
 */
class AdmincategoriesController extends \ItForFree\SimpleMVC\MVC\Controller
{
    public string $layoutPath = 'admin-main.php';
    
    protected array $rules = [
        ['allow' => true, 'roles' => ['@']],
        ['allow' => false, 'roles' => ['?']],
    ];
    
    /**
     * Список всех категорий
     */
    public function indexAction()
    {
        $Category = new CategoryModel();
        $data = $Category->getList();
        $categories = $data['results'];
        
        $this->view->addVar('categories', $categories);
        $this->view->addVar('totalRows', $data['totalRows']);
        $this->view->addVar('pageTitle', 'Категории');
        $this->view->render('category/index.php');
    }
    
    /**
     * Создание новой категории
     */
    public function addAction()
    {
        $Url = Config::get('core.router.class');
        
        if (!empty($_POST)) {
            if (!empty($_POST['saveChanges'])) {
                $Category = new CategoryModel();
                $category = new CategoryModel($_POST);
                $category->insert();
                $this->redirect($Url::link("admin/admincategories/index"));
            } 
            elseif (!empty($_POST['cancel'])) {
                $this->redirect($Url::link("admin/admincategories/index"));
            }
        } else {
            $category = new CategoryModel();
            
            $this->view->addVar('category', $category);
            $this->view->addVar('formAction', 'add');
            $this->view->addVar('pageTitle', 'Новая категория');
            $this->view->render('category/edit.php');
        }
    }
    
    /**
     * Редактирование категории
     */
    public function editAction()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $Url = Config::get('core.router.class');
            $this->redirect($Url::link("admin/admincategories/index"));
            return;
        }
        
        $Url = Config::get('core.router.class');
        $Category = new CategoryModel();
        
        if (!empty($_POST)) {
            if (!empty($_POST['saveChanges'])) {
                $category = new CategoryModel($_POST);
                $category->update();
                $this->redirect($Url::link("admin/admincategories/index"));
            } 
            elseif (!empty($_POST['cancel'])) {
                $this->redirect($Url::link("admin/admincategories/index"));
            }
        } else {
            $category = $Category->getById($id);
            
            if (!$category) {
                $this->redirect($Url::link("admin/admincategories/index"));
                return;
            }
            
            $this->view->addVar('category', $category);
            $this->view->addVar('formAction', 'edit');
            $this->view->addVar('pageTitle', 'Редактирование категории');
            $this->view->render('category/edit.php');
        }
    }
    
    /**
     * Удаление категории
     */
    public function deleteAction()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $Url = Config::get('core.router.class');
            $this->redirect($Url::link("admin/admincategories/index"));
            return;
        }
        
        $Url = Config::get('core.router.class');
        $Category = new CategoryModel();
        
        if (!empty($_POST)) {
            if (!empty($_POST['deleteCategory'])) {
                // Проверяем, есть ли статьи в этой категории
                $Article = new ArticleModel();
                $articles = $Article->getListFiltered(1000000, $id, null, "publicationDate DESC", false);
                
                if ($articles['totalRows'] > 0) {
                    $this->view->addVar('errorMessage', 'Ошибка: В категории есть статьи. Удалите статьи или назначьте их другой категории перед удалением категории.');
                    $category = $Category->getById($id);
                    $this->view->addVar('category', $category);
                    $this->view->addVar('pageTitle', 'Удаление категории');
                    $this->view->render('category/delete.php');
                    return;
                }
                
                $category = new CategoryModel($_POST);
                $category->delete();
                $this->redirect($Url::link("admin/admincategories/index"));
            }
            elseif (!empty($_POST['cancel'])) {
                $this->redirect($Url::link("admin/admincategories/edit&id=$id"));
            }
        } else {
            $category = $Category->getById($id);
            
            if (!$category) {
                $this->redirect($Url::link("admin/admincategories/index"));
                return;
            }
            
            $this->view->addVar('category', $category);
            $this->view->addVar('pageTitle', 'Удаление категории');
            $this->view->render('category/delete.php');
        }
    }
}

