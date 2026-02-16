<?php
class Products extends Controller {
    public function __construct(){
        $this->productModel = $this->model('Product');
        $this->categoryModel = $this->model('Category');
    }

    // Public Catalog
    public function index(){
        $products = $this->productModel->getProducts();

        $data = [
            'products' => $products
        ];

        $this->view('products/index', $data);
    }
    
    // Show single product
    public function show($id){
        $product = $this->productModel->getProductById($id);
        $category = $this->categoryModel->getCategoryById($product->category_id);
        
        $data = [
            'product' => $product,
            'category_name' => $category->name
        ];
        
        $this->view('products/show', $data);
    }
    
    // Admin List
    public function manage(){
        if(!isLoggedIn() || !isAdmin()){
            redirect('users/login');
        }
        
        $products = $this->productModel->getProducts();
        
        $data = [
            'products' => $products
        ];
        
        $this->view('products/manage', $data);
    }

    public function add(){
        if(!isLoggedIn() || !isAdmin()){
            redirect('users/login');
        }

        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $data = [
                'name' => trim($_POST['name']),
                'category_id' => trim($_POST['category_id']),
                'description' => trim($_POST['description']),
                'price' => trim($_POST['price']),
                'stock' => trim($_POST['stock']),
                'status' => trim($_POST['status']),
                'image' => '',
                'name_err' => '',
                'price_err' => '',
                'image_err' => ''
            ];

            // Image Upload
            if(!empty($_FILES['image']['name'])){
                $target_dir = "assets/img/products/";
                // Create dir if not exists (handled by sys but good to check)
                if (!file_exists($target_dir)) {
                    mkdir($target_dir, 0777, true);
                }
                
                $target_file = $target_dir . basename($_FILES["image"]["name"]);
                $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
                
                // Check if image file is a actual image or fake image
                $check = getimagesize($_FILES["image"]["tmp_name"]);
                if($check !== false) {
                    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                        $data['image'] = $_FILES['image']['name'];
                    } else {
                        $data['image_err'] = "Sorry, there was an error uploading your file.";
                    }
                } else {
                    $data['image_err'] = "File is not an image.";
                }
            }

            if(empty($data['name'])){
                $data['name_err'] = 'Please enter name';
            }
            if(empty($data['price'])){
                $data['price_err'] = 'Please enter price';
            }

            if(empty($data['name_err']) && empty($data['price_err']) && empty($data['image_err'])){
                if($this->productModel->addProduct($data)){
                    flash('product_message', 'Product Added');
                    redirect('products/manage');
                } else {
                    die('Something went wrong');
                }
            } else {
                $categories = $this->categoryModel->getCategories();
                $data['categories'] = $categories;
                $this->view('products/add', $data);
            }

        } else {
            $categories = $this->categoryModel->getCategories();
            $data = [
                'name' => '',
                'category_id' => '',
                'description' => '',
                'price' => '',
                'stock' => '',
                'status' => 'active',
                'image' => '',
                'categories' => $categories,
                'name_err' => '',
                'price_err' => '',
                'image_err' => ''
            ];

            $this->view('products/add', $data);
        }
    }
    
    public function edit($id){
        if(!isLoggedIn() || !isAdmin()){
            redirect('users/login');
        }

        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $data = [
                'id' => $id,
                'name' => trim($_POST['name']),
                'category_id' => trim($_POST['category_id']),
                'description' => trim($_POST['description']),
                'price' => trim($_POST['price']),
                'stock' => trim($_POST['stock']),
                'status' => trim($_POST['status']),
                'image' => '',
                'name_err' => '',
                'price_err' => '',
                'image_err' => ''
            ];

            // Image Upload
            if(!empty($_FILES['image']['name'])){
                $target_dir = "assets/img/products/";
                // Create dir if not exists (handled by sys but good to check)
                if (!file_exists($target_dir)) {
                    mkdir($target_dir, 0777, true);
                }
                
                $target_file = $target_dir . basename($_FILES["image"]["name"]);
                $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
                
                // Check if image file is a actual image or fake image
                $check = getimagesize($_FILES["image"]["tmp_name"]);
                if($check !== false) {
                    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                        $data['image'] = $_FILES['image']['name'];
                    } else {
                        $data['image_err'] = "Sorry, there was an error uploading your file.";
                    }
                } else {
                    $data['image_err'] = "File is not an image.";
                }
            } else {
                // Keep old image if no new one
                $product = $this->productModel->getProductById($id);
                $data['image'] = $product->image;
            }

            if(empty($data['name'])){
                $data['name_err'] = 'Please enter name';
            }
            if(empty($data['price'])){
                $data['price_err'] = 'Please enter price';
            }

            if(empty($data['name_err']) && empty($data['price_err']) && empty($data['image_err'])){
                if($this->productModel->updateProduct($data)){
                    flash('product_message', 'Product Updated');
                    redirect('products/manage');
                } else {
                    die('Something went wrong');
                }
            } else {
                $categories = $this->categoryModel->getCategories();
                $data['categories'] = $categories;
                $this->view('products/edit', $data);
            }

        } else {
            $product = $this->productModel->getProductById($id);
            $categories = $this->categoryModel->getCategories();
            
            // Check for owner (if we had specific owners, but here assumed admin is owner of all)
            
            $data = [
                'id' => $id,
                'name' => $product->name,
                'category_id' => $product->category_id,
                'description' => $product->description,
                'price' => $product->price,
                'stock' => $product->stock,
                'status' => $product->status,
                'image' => $product->image,
                'categories' => $categories,
                'name_err' => '',
                'price_err' => '',
                'image_err' => ''
            ];

            $this->view('products/edit', $data);
        }
    }

    public function delete($id){
        if(!isLoggedIn() || !isAdmin()){
             redirect('users/login');
        }
        
        $product = $this->productModel->getProductById($id);
        
        // Possibly delete image file here too?
        
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            if($this->productModel->deleteProduct($id)){
                flash('product_message', 'Product Removed');
                redirect('products/manage');
            } else {
                die('Something went wrong');
            }
        } else {
            redirect('products/manage');
        }
    }
}
