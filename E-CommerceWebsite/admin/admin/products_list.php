<?php
session_start();
include("../../db.php");
error_reporting(E_ALL); // Enable error reporting for debugging purposes

if (isset($_GET['action']) && $_GET['action'] != "" && $_GET['action'] == 'delete') {
    $product_id = $_GET['product_id'];

    // Debugging: Before delete query
    echo "Product ID: " . $product_id . "<br>";
    echo "Action: " . $_GET['action'] . "<br>";

    // Delete image file
    $result = mysqli_query($con, "SELECT product_image FROM products WHERE product_id='$product_id'") or die("Query to get image failed");
    list($picture) = mysqli_fetch_array($result);
    $path = "../product_images/$picture";

    if (file_exists($path) == true) {
        unlink($path);
    }

    // Delete product from database
    mysqli_query($con, "DELETE FROM products WHERE product_id='$product_id'") or die("Delete query failed: " . mysqli_error($con));

    // Debugging: After delete query
    echo "Delete query executed successfully!<br>";
}



// Handle product update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['product_id'])) {
    $product_id = $_GET['product_id'];

    // Check if the form fields are set before using them
    $product_name = isset($_POST['product_name']) ? mysqli_real_escape_string($con, $_POST['product_name']) : '';
    $product_price = isset($_POST['product_price']) ? mysqli_real_escape_string($con, $_POST['product_price']) : '';

    // Validate and update the database if the required fields are not empty
    if (!empty($product_name) && !empty($product_price)) {
        $update_query = "UPDATE products SET 
                         product_title='$product_name', 
                         product_price='$product_price' 
                         WHERE product_id='$product_id'";
        $result = mysqli_query($con, $update_query);

        if ($result) {
            // Display update message
            echo "Product updated successfully!<br>";
        } else {
            echo "Error updating product: " . mysqli_error($con);
        }
    } else {
        echo "Product name and price are required.";
    }
}

// Pagination
$page = isset($_GET['page']) ? $_GET['page'] : 1;

if ($page == "" || $page == "1") {
    $page1 = 0;
} else {
    $page1 = ($page * 12) - 12;
}

include "sidenav.php";
include "topheader.php";
?>
<!-- Rest of your existing code ... -->


<!-- End Navbar -->
<div class="content">
    <div class="container-fluid">
        <div class="col-md-14">
            <div class="card ">
                <div class="card-header card-header-primary">
                    <h4 class="card-title"> Products List</h4>
                </div>
                <div class="card-body">
                    <!-- Display update message here -->
                    <div class="update-message">
                        <?php
                        // Display update message
                        if (isset($updateMessage)) {
                            echo $updateMessage;
                        }
                        ?>
                    </div>
                    <div class="table-responsive ps">
                        <table class="table tablesorter" id="page1">
                            <thead class="text-primary">
                                <tr>
                                    <th>Image</th>
                                    <th>Name</th>
                                    <th>Price</th>
                                    <th>Action</th> <!-- Add a new column for actions -->
                                </tr>
                            </thead>

                            <tbody>
                                <?php
                                $result = mysqli_query($con, "SELECT product_id, product_image, product_title, product_price FROM products WHERE product_cat=2 OR product_cat=3 OR product_cat=4 LIMIT $page1,12") or die("Query to get products failed.");

                                while (list($product_id, $image, $product_name, $price) = mysqli_fetch_array($result)) {
                                    echo "<tr>
                                        <td><img src='../../product_images/$image' style='width:50px; height:50px; border:groove #000'></td>
                                        <td>$product_name</td>
                                        <td>$price</td>
                                        <td>
                                            <button type='button' class='btn btn-success' data-toggle='modal' data-target='#updateModal$product_id'>Edit</button>
                                            <a class='btn btn-danger' href='products_list.php?product_id=$product_id&action=delete'>Delete</a>
                                        </td>
                                    </tr>";

                                    // Modal for updating product
                                    echo "<div class='modal fade' id='updateModal$product_id' tabindex='-1' role='dialog' aria-labelledby='updateModalLabel$product_id' aria-hidden='true'>
                                        <div class='modal-dialog' role='document'>
                                            <div class='modal-content'>
                                                <div class='modal-header'>
                                                    <h5 class='modal-title' id='updateModalLabel$product_id'>Update Product</h5>
                                                    <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
                                                        <span aria-hidden='true'>&times;</span>
                                                    </button>
                                                </div>
                                                <div class='modal-body'>
                                                    <!-- The form inside the modal -->
                                                    <form method='post' action='update_product.php'>
                                                     <div class='text-center'>
                                                        <input type='hidden' name='product_id' value='$product_id'>
                                                        
                                                        <label for='product_name'>Product Name:</label>
                                                        <input type='text' name='product_name' value='$product_name' required>
                                                     </div>
                                                    <br>
                                                     <div class='text-center'>    
                                                        <label for='product_price'>Product Price:</label>
                                                        <input type='text' name='product_price' value='$price' required>
                                                     </div>   
                                                        <div class='text-center'>
                                                            <input type='submit' value='Update' class='btn btn-primary'>
                                                        </div>
                                                    </form>
                                                </div>

                                            </div>
                                        </div>
                                    </div>";
                                }
                                ?>
                            </tbody>
                        </table>
                        <div class="ps__rail-x" style="left: 0px; bottom: 0px;">
                            <div class="ps__thumb-x" tabindex="0" style="left: 0px; width: 0px;"></div>
                        </div>
                        <div class="ps__rail-y" style="top: 0px; right: 0px;">
                            <div class="ps__thumb-y" tabindex="0" style="top: 0px; height: 0px;"></div>
                        </div>
                    </div>
                </div>
            </div>
            <nav aria-label="Page navigation example">
                <ul class="pagination">
                    <li class="page-item">
                        <a class="page-link" href="#" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                            <span class="sr-only">Previous</span>
                        </a>
                    </li>
                    <?php
                    // Counting pages
                    $count = mysqli_num_rows(mysqli_query($con, "SELECT product_id FROM products WHERE product_cat=2 OR product_cat=3 OR product_cat=4"));
                    $totalPages = ceil($count / 12);

                    for ($b = 1; $b <= $totalPages; $b++) {
                        echo "<li class='page-item'><a class='page-link' href='productlist.php?page=$b'>$b</a></li>";
                    }
                    ?>
                    <li class="page-item">
                        <a class="page-link" href="#" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                            <span class="sr-only">Next</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</div>
<?php include "footer.php"; ?>