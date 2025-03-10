<?php require_once 'header.php'; ?>

<body class="bg-[#2C3E50] text-[#ECF0F1]">
<?php require_once 'nav.php'; ?>
  <div class="card w-96 bg-[#34495E] mx-auto mt-20 rounded-lg shadow-lg">
    <div class="card-body p-8">
      <h2 class="card-title text-center text-2xl font-bold mb-6">Update Article</h2>

      <!-- display flash messages -->
      <?php 
    $error = popSessionData('error');
    $success = popSessionData('success');
    if ($error): ?>
      <div class="mb-4 p-4 bg-red-500 text-white rounded">
        <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>
    <?php if ($success): ?>
      <div class="mb-4 p-4 bg-green-500 text-white rounded">
        <?= htmlspecialchars($success) ?>
      </div>
    <?php endif; ?>

      <!-- Article Update Form -->
      <form class="space-y-6" action="/articles/update" method="POST">
  <!-- Hidden field for the article ID -->
  <input type="hidden" name="id" value="<?= e($article->getId()) ?>">

  <div>
    <label for="title" class="block text-sm font-medium">Title</label>
    <input id="title" name="title" type="text" placeholder="Article Title" required
           value="<?= e($article->getTitle()) ?>"
           class="input input-bordered w-full bg-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-[#E67E22]">
  </div>
  <div>
    <label for="url" class="block text-sm font-medium">URL</label>
    <input id="url" name="url" type="url" placeholder="https://example.com" required
           value="<?= e($article->getUrl()) ?>"
           class="input input-bordered w-full bg-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-[#E67E22]">
  </div>
  <div>
    <button type="submit"
            class="w-full py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-[#E67E22] hover:bg-[#d66e1e] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#E67E22]">
      Update Article
    </button>
  </div>
</form>

    </div>
  </div>
</body>
