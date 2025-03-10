<?php require_once 'header.php'; ?>


<body class="bg-[#2C3E50] text-[#ECF0F1] pb-20">
<?php require_once 'nav.php'; ?>

  <div class="card w-96 bg-[#34495E] mx-auto mt-20 mb-20 rounded-lg shadow-lg ">
    <div class="card-body p-8 ">
      <h2 class="card-title text-center text-2xl font-bold mb-6">New Article</h2>

      <!-- Flash Error Message -->
      <?php if (isset($_SESSION['error'])): ?>
        <div class="mb-4 p-4 bg-red-500 text-white rounded">
          <?= htmlspecialchars($_SESSION['error']) ?>
          <?php unset($_SESSION['error']); ?>
        </div>
      <?php endif; ?>

      <!-- Article Creation Form -->
      <form class="space-y-6" action="/articles" method="POST">
        <div>
          <label for="title" class="block text-sm font-medium mb-3">Title</label>
          <input id="title" name="title" type="text" placeholder="Article Title" required
                 class="input input-bordered w-full bg-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-[#E67E22]">
        </div>
        <div>
          <label for="url" class="block text-sm font-medium mb-3">URL</label>
          <input id="url" name="url" type="url" placeholder="https://example.com" required
                 class="input input-bordered w-full bg-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-[#E67E22]">
        </div>
        <div>
          <button type="submit"
                  class="w-full py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-[#E67E22] hover:bg-[#d66e1e] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#E67E22]">
            Create Article
          </button>
        </div>
      </form>
    </div>
  </div>
</body>