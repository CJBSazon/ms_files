<!-- Order Management Section -->
<section id="order-management" class="section space-y-8 hidden">
    <h2 class="text-3xl font-bold text-gray-700">Order Management</h2>

    <!-- Upload New Item Form -->
    <div class="p-6 bg-white rounded-lg shadow-md">
        <h3 class="text-xl font-semibold text-gray-600 mb-6">Upload New Menu Item</h3>
        <form method="POST" enctype="multipart/form-data" class="space-y-4">
            <div>
                <label for="item_name" class="block text-sm font-medium text-gray-700 mb-2">Item Name</label>
                <input type="text" name="item_name" id="item_name" required class="block w-full border border-gray-300 rounded-md px-4 py-2 text-gray-700 focus:ring-orange-500 focus:border-orange-500">
            </div>
            <div>
                <label for="item_price" class="block text-sm font-medium text-gray-700 mb-2">Item Price</label>
                <input type="text" name="item_price" id="item_price" required class="block w-full border border-gray-300 rounded-md px-4 py-2 text-gray-700 focus:ring-orange-500 focus:border-orange-500">
            </div>
            <div>
                <label for="order_type" class="block text-sm font-medium text-gray-700 mb-2">Select Order Type</label>
                <select name="order_type" id="order_type" required class="block w-full border border-gray-300 rounded-md px-4 py-2 text-gray-700 focus:ring-orange-500 focus:border-orange-500">
                    <option value="special_menu">Special Menu</option>
                    <option value="combo_meal">Combo Meal</option>
                    <option value="budget_meal">Budget Meal</option>
                    <option value="ala_carte">À La Carte</option>
                    <option value="add_ons">Add-Ons</option>
                    <option value="drinks_dessert">Drinks & Dessert</option>
                </select>
            </div>
            <div>
                <label for="item_image" class="block text-sm font-medium text-gray-700 mb-2">Item Image</label>
                <input type="file" name="item_image" id="item_image" required class="block w-full border border-gray-300 rounded-md px-4 py-2 text-gray-700 focus:ring-orange-500 focus:border-orange-500">
            </div>
            <button type="submit" name="upload_item" class="bg-orange-500 text-white font-medium px-6 py-2 rounded-md hover:bg-orange-600">
                Upload Item
            </button>
        </form>
    </div>

    <!-- List Menu Items -->
    <div class="p-6 bg-white rounded-lg shadow-md">
        <h3 class="text-xl font-semibold text-gray-600 mb-6">Existing Menu Items</h3>
        <table class="min-w-full bg-white border-collapse border border-gray-200 rounded-lg">
            <thead class="bg-gray-100">
                <tr>
                    <th class="py-3 px-4 text-left text-sm font-medium text-gray-600 border-b">Item ID</th>
                    <th class="py-3 px-4 text-left text-sm font-medium text-gray-600 border-b">Item Name</th>
                    <th class="py-3 px-4 text-left text-sm font-medium text-gray-600 border-b">Price</th>
                    <th class="py-3 px-4 text-left text-sm font-medium text-gray-600 border-b">Type</th>
                    <th class="py-3 px-4 text-left text-sm font-medium text-gray-600 border-b">Image</th>
                    <th class="py-3 px-4 text-left text-sm font-medium text-gray-600 border-b">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($menu_items)): ?>
                    <?php foreach ($menu_items as $item): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="py-3 px-4 text-gray-700"><?= htmlspecialchars($item['id']) ?></td>
                            <td class="py-3 px-4 text-gray-700"><?= htmlspecialchars($item['name']) ?></td>
                            <td class="py-3 px-4 text-gray-700"><?= htmlspecialchars($item['price']) ?></td>
                            <td class="py-3 px-4 text-gray-700"><?= ucfirst(str_replace('_', ' ', htmlspecialchars($item['type']))) ?></td>
                            <td class="py-3 px-4">
                                <img src="uploads/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="h-12 w-12 rounded-md object-cover border border-gray-200">
                            </td>
                            <td class="py-3 px-4 text-gray-700 space-y-2">
                                <!-- Update Form -->
                                <form method="POST" class="space-y-2">
                                    <input type="hidden" name="item_id" value="<?= htmlspecialchars($item['id']) ?>">
                                    <div class="flex items-center gap-2">
                                        <input type="text" name="item_name" value="<?= htmlspecialchars($item['name']) ?>" class="w-32 border rounded-md px-2 py-1 text-gray-700 focus:ring-orange-500 focus:border-orange-500">
                                        <input type="text" name="item_price" value="<?= htmlspecialchars($item['price']) ?>" class="w-24 border rounded-md px-2 py-1 text-gray-700 focus:ring-orange-500 focus:border-orange-500">
                                    </div>
                                    <select name="order_type" class="block w-full border rounded-md px-2 py-1 text-gray-700 focus:ring-orange-500 focus:border-orange-500">
                                        <option value="special_menu" <?= $item['type'] === 'special_menu' ? 'selected' : '' ?>>Special Menu</option>
                                        <option value="combo_meal" <?= $item['type'] === 'combo_meal' ? 'selected' : '' ?>>Combo Meal</option>
                                        <option value="budget_meal" <?= $item['type'] === 'budget_meal' ? 'selected' : '' ?>>Budget Meal</option>
                                        <option value="ala_carte" <?= $item['type'] === 'ala_carte' ? 'selected' : '' ?>>À La Carte</option>
                                        <option value="add_ons" <?= $item['type'] === 'add_ons' ? 'selected' : '' ?>>Add-Ons</option>
                                        <option value="drinks_dessert" <?= $item['type'] === 'drinks_dessert' ? 'selected' : '' ?>>Drinks & Dessert</option>
                                    </select>
                                    <button type="submit" name="update_item" class="text-blue-500 hover:underline">
                                        Update
                                    </button>
                                </form>

                                <!-- Delete Form -->
                                <form method="POST">
                                    <input type="hidden" name="item_id" value="<?= htmlspecialchars($item['id']) ?>">
                                    <button type="submit" name="delete_item" class="text-red-500 hover:underline">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="py-4 px-4 text-center text-gray-500">No menu items found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
