<!-- Product Management Section -->
<section id="product-management" class="section space-y-6 hidden">
            <h2 class="text-2xl font-semibold mb-6">Product Management</h2>
            
            <!-- Product Upload Form -->
            <div class="space-y-6 mb-8">
                <h3 class="text-xl font-semibold mb-4">Upload New Product</h3>
                <form action="admin.php" method="POST" enctype="multipart/form-data" class="space-y-6">
                    <div>
                        <label for="product_name" class="block text-sm font-medium text-gray-700">Product Name</label>
                        <input type="text" id="product_name" name="product_name" required class="w-full px-4 py-2 border border-gray-300 rounded-md">
                    </div>
                    <div>
                        <label for="product_description" class="block text-sm font-medium text-gray-700">Product Description</label>
                        <textarea id="product_description" name="product_description" required class="w-full px-4 py-2 border border-gray-300 rounded-md"></textarea>
                    </div>
                    <div>
                        <label for="product_image" class="block text-sm font-medium text-gray-700">Product Image</label>
                        <input type="file" id="product_image" name="product_image" required class="w-full px-4 py-2 border border-gray-300 rounded-md">
                    </div>
                    <button type="submit" name="upload_product" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700">Upload Product</button>
                </form>
            </div>

            <!-- Product List Section -->
            <div class="mt-8">
                <h3 class="text-xl font-semibold mb-4">Uploaded Products</h3>
                <table class="min-w-full bg-white border border-gray-200 rounded-lg shadow-md">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">ID</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Name</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Description</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Image</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-800"><?= $product['id'] ?></td>
                                <td class="px-6 py-4 text-sm text-gray-800"><?= $product['name'] ?></td>
                                <td class="px-6 py-4 text-sm text-gray-800"><?= $product['description'] ?></td>
                                <td class="px-6 py-4 text-sm text-gray-800">
                                    <img src="uploads/<?= $product['image'] ?>" alt="Product Image" class="h-20 w-20 object-cover">
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-800">
                                    <!-- Update Form -->
                                    <form action="admin.php" method="POST" class="inline-block space-x-2">
                                        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                        <input type="text" name="product_name" value="<?= $product['name'] ?>" required class="px-4 py-2 border border-gray-300 rounded-md">
                                        <textarea name="product_description" required class="px-4 py-2 border border-gray-300 rounded-md"><?= $product['description'] ?></textarea>
                                        <button type="submit" name="update_product" class="bg-yellow-500 text-white px-4 py-2 rounded-md hover:bg-yellow-600">Update</button>
                                    </form>
                                    
                                    <!-- Delete Form -->
                                    <form action="admin.php" method="POST" class="inline-block space-x-2">
                                        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                        <button type="submit" name="delete_product" class="bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>