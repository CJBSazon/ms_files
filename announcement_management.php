<!-- Announcement Management Section -->
<section id="announcement-management" class="section space-y-6 hidden">
            <h2 class="text-2xl font-semibold mb-6">Announcement Management</h2>

            <!-- Announcement Upload Form -->
            <div id="announcement-management-form" class="space-y-6 mb-8">
                <h3 class="text-xl font-semibold mb-4">Upload New Announcement</h3>
                <form action="admin.php" method="POST" enctype="multipart/form-data" class="space-y-6">
                    <div>
                        <label for="announcement_title" class="block text-sm font-medium text-gray-700">Announcement Title</label>
                        <input type="text" id="announcement_title" name="announcement_title" required class="w-full px-4 py-2 border border-gray-300 rounded-md">
                    </div>
                    <div>
                        <label for="announcement_description" class="block text-sm font-medium text-gray-700">Announcement Description</label>
                        <textarea id="announcement_description" name="announcement_description" required class="w-full px-4 py-2 border border-gray-300 rounded-md"></textarea>
                    </div>
                    <div>
                        <label for="announcement_image" class="block text-sm font-medium text-gray-700">Announcement Image</label>
                        <input type="file" id="announcement_image" name="announcement_image" required class="w-full px-4 py-2 border border-gray-300 rounded-md">
                    </div>
                    <button type="submit" name="upload_announcement" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700">Upload Announcement</button>
                </form>
            </div>

            <!-- Announcement List Section -->
            <div class="mt-8">
                <h3 class="text-xl font-semibold mb-4">Uploaded Announcements</h3>
                <table class="min-w-full bg-white border border-gray-200 rounded-lg shadow-md">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">ID</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Title</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Description</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Image</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($announcements as $announcement): ?>
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-800"><?= $announcement['id'] ?></td>
                                <td class="px-6 py-4 text-sm text-gray-800"><?= $announcement['title'] ?></td>
                                <td class="px-6 py-4 text-sm text-gray-800"><?= $announcement['description'] ?></td>
                                <td class="px-6 py-4 text-sm text-gray-800">
                                    <img src="uploads/<?= $announcement['image'] ?>" alt="Announcement Image" class="h-20 w-20 object-cover">
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-800">
                                    <!-- Update Form -->
                                    <form action="admin.php" method="POST" class="inline-block space-x-2">
                                        <input type="hidden" name="announcement_id" value="<?= $announcement['id'] ?>">
                                        <input type="text" name="announcement_title" value="<?= $announcement['title'] ?>" required class="px-4 py-2 border border-gray-300 rounded-md">
                                        <textarea name="announcement_description" required class="px-4 py-2 border border-gray-300 rounded-md"><?= $announcement['description'] ?></textarea>
                                        <button type="submit" name="update_announcement" class="bg-yellow-500 text-white px-4 py-2 rounded-md hover:bg-yellow-600">Update</button>
                                    </form>
                                    
                                    <!-- Delete Form -->
                                    <form action="admin.php" method="POST" class="inline-block space-x-2">
                                        <input type="hidden" name="announcement_id" value="<?= $announcement['id'] ?>">
                                        <button type="submit" name="delete_announcement" class="bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            </section>