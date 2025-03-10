<?php require_once 'header.php'; ?>
<body class="bg-[#2C3E50] text-[#ECF0F1] pb-20">
  <?php require_once 'nav.php'; ?>

  <div class="container mx-auto px-4 py-8">
    <!-- Use flex container to display two boxes side-by-side on medium and larger screens -->
    <div class="flex flex-col md:flex-row gap-4 justify-center">
      
      <!-- Profile Settings Box -->
      <div class="bg-[#34495E] text-[#ECF0F1] p-8 rounded-lg shadow-lg w-full md:w-1/2">
        <h1 class="text-3xl font-bold text-center mb-6">Profile Settings</h1>
        
        <?php 
          $error = popSessionData('error');
          $success = popSessionData('success');
          if ($error): 
        ?>
          <div class="mb-4 p-4 bg-red-500 text-white rounded">
            <?= e($error) ?>
          </div>
        <?php endif; ?>
        <?php if ($success): ?>
          <div class="mb-4 p-4 bg-green-500 text-white rounded">
            <?= e($success) ?>
          </div>
        <?php endif; ?>

        <form action="/settings/update" method="POST" enctype="multipart/form-data" class="space-y-6">
          <!-- Username Field -->
          <div>
            <label for="username" class="block text-sm font-medium text-white">Username</label>
            <input type="text" name="username" id="username" required 
                   placeholder="Your new username"
                   class="w-full mt-1 px-3 py-2 rounded-md bg-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-[#E67E22]"
                   value="<?= e(getSessionData('username') ?? '') ?>">
          </div>
          
          <!-- Profile Picture Upload -->
          <div>
            <label for="profile_picture" class="block text-sm font-medium text-white">Profile Picture</label>
            <input type="file" name="profile_picture" id="profile_picture"
                   class="w-full mt-1 px-3 py-2 rounded-md bg-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-[#E67E22]">
          </div>
          
          <!-- Submit Button -->
          <div>
            <button type="submit" class="w-full py-2 px-4 bg-[#E67E22] rounded hover:bg-[#d66e1e] text-white font-medium transition-colors">
              Save Changes
            </button>
          </div>
        </form>
        
        <!-- Change Password Link -->
        <div class="mt-6 text-center">
          <a href="/reset-password" class="text-[#E67E22] hover:underline">
            Change Password
          </a>
        </div>
      </div>
      
      <!-- My Articles Box -->
      <div class="bg-[#34495E] text-[#ECF0F1] p-8 rounded-lg shadow-lg w-full md:w-1/2">
        <h2 class="text-2xl font-bold mb-4 text-center">My Articles</h2>
        <?php if (isset($myArticles) && !empty($myArticles)): ?>
          <ul class="list-disc pl-5 space-y-2">
            <?php foreach ($myArticles as $article): ?>
              <li>
                <a href="/article?id=<?= e($article->getId()) ?>" class="hover:underline">
                  <?= e($article->getTitle()) ?>
                </a>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php else: ?>
          <p class="text-center">No articles found.</p>
        <?php endif; ?>
      </div>
      
    </div>
  </div>
</body>
