<?php
class Categories extends Controller {
    public function __construct(){
        if(!isLoggedIn() || !isAdmin()){
            redirect('users/login');
        }

        $this->categoryModel = $this->model('Category');
    }

    public function index(){
        $categories = $this->categoryModel->getCategories();

        $data = [
            'categories' => $categories
        ];

        $this->view('categories/index', $data);
    }

    public function add(){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $data = [
                'name' => trim($_POST['name']),
                'description' => trim($_POST['description']),
                'name_err' => '',
                'description_err' => ''
            ];

            if(empty($data['name'])){
                $data['name_err'] = 'Please enter name';
            }

            if(empty($data['name_err'])){
                if($this->categoryModel->addCategory($data)){
                    flash('category_message', 'Category Added');
                    redirect('categories/index');
                } else {
                    die('Something went wrong');
                }
            } else {
                $this->view('categories/add', $data);
            }
        } else {
            $data = [
                'name' => '',
                'description' => '',
                'name_err' => '',
                'description_err' => ''
            ];

            $this->view('categories/add', $data);
        }
    }

    public function edit($id){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $data = [
                'id' => $id,
                'name' => trim($_POST['name']),
                'description' => trim($_POST['description']),
                'name_err' => '',
                'description_err' => ''
            ];

            if(empty($data['name'])){
                $data['name_err'] = 'Please enter name';
            }

            if(empty($data['name_err'])){
                if($this->categoryModel->updateCategory($data)){
                    flash('category_message', 'Category Updated');
                    redirect('categories/index');
                } else {
                    die('Something went wrong');
                }
            } else {
                $this->view('categories/edit', $data);
            }
        } else {
            $category = $this->categoryModel->getCategoryById($id);

            if($category->id != $id){ // Basic check
                redirect('categories/index');
            }

            $data = [
                'id' => $id,
                'name' => $category->name,
                'description' => $category->description,
                'name_err' => '',
                'description_err' => ''
            ];

            $this->view('categories/edit', $data);
        }
    }

    public function delete($id){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            if($this->categoryModel->deleteCategory($id)){
                flash('category_message', 'Category Removed');
                redirect('categories/index');
            } else {
                die('Something went wrong');
            }
        } else {
            redirect('categories/index');
        }
    }
}
