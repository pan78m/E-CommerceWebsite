<?php
session_start();
include("../../db.php");
error_reporting(E_ALL);

if (isset($_GET['product_id'])) {
    $product_id = $_GET['product_id'];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Process form submission and update the product in the database
        $product_name = mysqli_real_escape_string($con, $_POST['product_name']);
        $product_price = mysqli_real_escape_string($con, $_POST['product_price']);

        // Add more fields as needed

        // Validate and update the database
        $update_query = "UPDATE products SET 
                         product_title='$product_name', 
                         product_price='$product_price' 
                         WHERE product_id='$product_id'";
        $result = mysqli_query($con, $update_query);

        if ($result) {
            echo "Product updated successfully!";
        } else {
            echo "Error updating product: " . mysqli_error($con);
        }
    }

    // Fetch product details from the database based on $product_id
    $result = mysqli_query($con, "SELECT * FROM products WHERE product_id='$product_id'");
    $row = mysqli_fetch_array($result);

    // Display a button to trigger the modal
    echo '<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#updateModal">
            Update Product
          </button>';

    // Display the modal
?>
    <div class="modal" id="updateModal" tabindex="-1" role="dialog" aria-labelledby="updateModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateModalLabel">Update Product</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- The form inside the modal -->
                    <form method="post">
                        <label for="product_name">Product Name:</label>
                        <input type="text" name="product_name" value="<?php echo $row['product_title']; ?>" required>

                        <label for="product_price">Product Price:</label>
                        <input type="text" name="product_price" value="<?php echo $row['product_price']; ?>" required>

                        <!-- Add more fields as needed -->

                        <input type="submit" value="Update" class="btn btn-primary">
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php
} else {
    // Redirect to the product list page if product_id is not provided
    header("Location: products_list.php");
    exit();
}
?>