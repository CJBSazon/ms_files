<!-- Order Management Section -->
<section id="order-management" class="section space-y-6 hidden">
            <h2 class="text-2xl font-bold">Order Management</h2>

            <!-- Upload New Item Form -->
            <div class="p-6 bg-white rounded shadow">
                <h3 class="text-xl font-semibold mb-4">Upload New Menu Item</h3>
                <form method="POST" enctype="multipart/form-data" class="space-y-4">
                    <div>
                        <label for="item_name" class="block font-medium">Item Name</label>
                        <input type="text" name="item_name" id="item_name" required class="w-full border rounded px-4 py-2">
                    </div>
                    <div>
                        <label for="item_price" class="block font-medium">Item Price</label>
                        <input type="text" name="item_price" id="item_price" required class="w-full border rounded px-4 py-2">
                    </div>
                    <div>
                        <label for="order_type" class="block font-medium">Select Order Type</label>
                        <select name="order_type" id="order_type" required class="w-full border rounded px-4 py-2">
                            <option value="special_menu">Special Menu</option>
                            <option value="combo_meal">Combo Meal</option>
                            <option value="budget_meal">Budget Meal</option>
                            <option value="ala_carte">À La Carte</option>
                            <option value="add_ons">Add-Ons</option>
                            <option value="drinks_dessert">Drinks & Dessert</option>
                        </select>
                    </div>
                    <div>
                        <label for="item_image" class="block font-medium">Item Image</label>
                        <input type="file" name="item_image" id="item_image" required class="w-full border rounded px-4 py-2">
                    </div>
                    <button type="submit" name="upload_item" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Upload Item</button>
                </form>
            </div>

            <!-- List Menu Items -->
            <div class="mt-6 p-6 bg-white rounded shadow">
                <h3 class="text-xl font-semibold mb-4">Existing Menu Items</h3>
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="border-b">
                            <th class="py-2 px-4">Item ID</th>
                            <th class="py-2 px-4">Item Name</th>
                            <th class="py-2 px-4">Price</th>
                            <th class="py-2 px-4">Type</th>
                            <th class="py-2 px-4">Image</th>
                            <th class="py-2 px-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($menu_items)): ?>
                            <?php foreach ($menu_items as $item): ?>
                                <tr class="border-b">
                                    <td class="py-2 px-4"><?= $item['id'] ?></td>
                                    <td class="py-2 px-4"><?= $item['name'] ?></td>
                                    <td class="py-2 px-4"><?= $item['price'] ?></td>
                                    <td class="py-2 px-4"><?= ucfirst(str_replace('_', ' ', $item['type'])) ?></td>
                                    <td class="py-2 px-4"><img src="uploads/<?= $item['image'] ?>" alt="<?= $item['name'] ?>" class="h-12 w-12 object-cover"></td>
                                    <td class="py-2 px-4">
                                        <!-- Update Form -->
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                                            <input type="text" name="item_name" value="<?= $item['name'] ?>" class="border rounded px-2 py-1">
                                            <input type="text" name="item_price" value="<?= $item['price'] ?>" class="border rounded px-2 py-1">
                                            <select name="order_type" class="border rounded px-2 py-1">
                                                <option value="special_menu" <?= $item['type'] === 'special_menu' ? 'selected' : '' ?>>Special Menu</option>
                                                <option value="combo_meal" <?= $item['type'] === 'combo_meal' ? 'selected' : '' ?>>Combo Meal</option>
                                                <option value="budget_meal" <?= $item['type'] === 'budget_meal' ? 'selected' : '' ?>>Budget Meal</option>
                                                <option value="ala_carte" <?= $item['type'] === 'ala_carte' ? 'selected' : '' ?>>À La Carte</option>
                                                <option value="add_ons" <?= $item['type'] === 'add_ons' ? 'selected' : '' ?>>Add-Ons</option>
                                                <option value="drinks_dessert" <?= $item['type'] === 'drinks_dessert' ? 'selected' : '' ?>>Drinks & Dessert</option>
                                            </select>
                                            <button type="submit" name="update_item" class="text-blue-600 hover:text-blue-800 ml-2">Update</button>
                                        </form>
                                        <!-- Delete Form -->
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                                            <button type="submit" name="delete_item" class="text-red-600 hover:text-red-800 ml-2">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="6" class="py-2 px-4 text-center">No menu items found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>