<!-- Order List Section -->
<div class="section" id="order-list">
    <h2 class="text-2xl font-bold mb-6">Order List</h2>
    <div class="overflow-x-auto bg-white p-6 rounded-lg shadow-md">
        <table class="min-w-full table-auto">
            <thead>
                <tr class="bg-gray-800 text-white">
                    <th class="px-4 py-2">Order #</th>
                    <th class="px-4 py-2">Email</th>
                    <th class="px-4 py-2">Service Type</th>
                    <th class="px-4 py-2">Address</th>
                    <th class="px-4 py-2">Contact No.</th>
                    <th class="px-4 py-2">Landmark</th>
                    <th class="px-4 py-2">Order Date</th>
                    <th class="px-4 py-2">Status</th>
                    <th class="px-4 py-2">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($orders)) : ?>
                    <?php foreach ($orders as $order) : ?>
                        <?php if ($order['status'] != 'completed') : ?>
                            <tr>
                                <td class="px-4 py-2"><?= $order['id']; ?></td>
                                <td class="px-4 py-2"><?= $order['email']; ?></td>
                                <td class="px-4 py-2"><?= $order['service_type']; ?></td>
                                <td class="px-4 py-2"><?= $order['address']; ?></td>
                                <td class="px-4 py-2"><?= $order['contact_number']; ?></td>
                                <td class="px-4 py-2"><?= $order['landmark']; ?></td>
                                <td class="px-4 py-2"><?= date('F j, Y, g:i a', strtotime($order['order_date'])); ?></td>
                                <td class="px-4 py-2"><?= ucfirst($order['status']); ?></td>
                                <td class="px-4 py-2">
                                    <form action="admin.php?page=orders" method="POST">
                                        <input type="hidden" name="order_id" value="<?= $order['id']; ?>">
                                        <select name="status" class="px-4 py-2 border border-gray-300 rounded-md" required>
                                            <option value="pending" <?= $order['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                            <option value="in_progress" <?= $order['status'] == 'in_progress' ? 'selected' : ''; ?>>In Progress</option>
                                            <option value="completed" <?= $order['status'] == 'completed' ? 'selected' : ''; ?>>Completed</option>
                                            <option value="canceled" <?= $order['status'] == 'canceled' ? 'selected' : ''; ?>>Canceled</option>
                                        </select>
                                        <button type="submit" name="update_status" class="bg-blue-500 text-white px-4 py-2 rounded ml-2">Update</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="9" class="text-center text-red-500">No orders found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Completed Orders Section -->
<div class="section" id="completed-orders">
    <h2 class="text-2xl font-bold mt-12 mb-6">Completed Orders</h2>
    <div class="overflow-x-auto bg-white p-6 rounded-lg shadow-md">
        <table class="min-w-full table-auto">
            <thead>
                <tr class="bg-green-800 text-white">
                    <th class="px-4 py-2">Order #</th>
                    <th class="px-4 py-2">Email</th>
                    <th class="px-4 py-2">Service Type</th>
                    <th class="px-4 py-2">Address</th>
                    <th class="px-4 py-2">Contact No.</th>
                    <th class="px-4 py-2">Landmark</th>
                    <th class="px-4 py-2">Order Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($orders)) : ?>
                    <?php foreach ($orders as $order) : ?>
                        <?php if ($order['status'] == 'completed') : ?>
                            <tr>
                                <td class="px-4 py-2"><?= $order['id']; ?></td>
                                <td class="px-4 py-2"><?= $order['email']; ?></td>
                                <td class="px-4 py-2"><?= $order['service_type']; ?></td>
                                <td class="px-4 py-2"><?= $order['address']; ?></td>
                                <td class="px-4 py-2"><?= $order['contact_number']; ?></td>
                                <td class="px-4 py-2"><?= $order['landmark']; ?></td>
                                <td class="px-4 py-2"><?= date('F j, Y, g:i a', strtotime($order['order_date'])); ?></td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="7" class="text-center text-red-500">No completed orders found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
