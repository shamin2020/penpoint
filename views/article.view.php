<?php require_once 'header.php'; ?>
<body class="bg-[#2C3E50] text-[#ECF0F1]">
  <?php require_once 'nav.php'; ?>
  <div class="container mx-auto px-4 py-8">

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

    <!-- Article Details -->
    <div class="bg-[#34495E] p-6 rounded-lg shadow-lg mb-6">
      <!-- Title & Voting (placed in a flex container) -->
      <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold">  
            <?= htmlspecialchars($article->getTitle()) ?>
        </h1>
        <!-- Voting Section -->
        <div>
        <?php if (isset($authenticatedUser)): ?>
        <?php $userVote = getUserVoteForArticle($article->getId(), $authenticatedUser->getId()); ?>
          <form action="/vote" method="POST" class="inline">
            <input type="hidden" name="article_id" value="<?= e($article->getId()) ?>">
            <input type="hidden" name="value" value="1">
            <?php if (!$userVote): ?>
            <!-- Upvote button -->
            <button type="submit" class="flex items-center px-3 py-1 rounded shadow-lg text-white text-sm transition-transform duration-200 hover:-translate-y-1 hover:scale-105 hover:shadow-2xl shrink-0">
              <img src="<?= image('vote.png') ?>" alt="Vote" class="h-6 w-6 mr-2">
              <span><?= function_exists('getArticleVotes') ? e(getArticleVotes($article->getId())) : 0 ?></span>
            </button>
            <?php else: ?>
            <!-- Already upvoted -->
            <button type="submit" class="flex items-center px-3 py-1 rounded shadow-lg text-white text-sm transition-transform duration-200 hover:-translate-y-1 hover:scale-105 hover:shadow-2xl shrink-0">
              <img src="<?= image('voted.png') ?>" alt="Voted" class="h-6 w-6 mr-2">
              <span><?= function_exists('getArticleVotes') ? e(getArticleVotes($article->getId())) : 0 ?></span>
            </button>
            <?php endif; ?>
          </form>
        <?php else: ?> 
          <button type="submit" class="flex items-center px-3 py-1 rounded shadow-lg text-white text-sm transition-transform duration-200 hover:-translate-y-1 hover:scale-105 hover:shadow-2xl shrink-0" disabled title="Login to vote!">
            <img src="<?= image('vote.png') ?>" alt="Vote" class="h-6 w-6 mr-2">
            <span><?= function_exists('getArticleVotes') ? e(getArticleVotes($article->getId())) : 0 ?></span>
          </button>
        <?php endif; ?>
        </div>
      </div>
      <!-- Article URL -->
      <p class="mt-4 text-gray-300">
        URL: <a href="<?= htmlspecialchars($article->getUrl()) ?>" class="text-[#E67E22] hover:underline" target="_blank">
          <?= htmlspecialchars($article->getUrl()) ?>
        </a>
      </p>
    
      <!-- Edit & Delete Icons (only for the author) -->
      <?php if (isset($authenticatedUser) && $authenticatedUser->getId() == $article->getAuthorId()): ?>
      <div class="flex justify-end space-x-4">

        <!-- Edit Icon -->
        <a href="/articles/edit?id=<?= e($article->getId()) ?>" class="inline-block">
          <img src="<?= image('edit.png') ?>" alt="Edit" class="h-5 w-5 hover:opacity-80">
        </a>

        <!-- Delete Icon -->
        <form action="/articles/delete" 
              method="POST" 
              onsubmit="return confirm('Are you sure you want to delete this article?');" 
              class="inline-block">
          <input type="hidden" name="id" value="<?= e($article->getId()) ?>">
          <button type="submit">
            <img src="<?= image('delete.png') ?>" alt="Delete" class="h-5 w-5 hover:opacity-80">
          </button>
        </form>
      </div>
      <?php endif; ?>
    </div>
    
    <!-- Comments Section -->
    <div class="bg-[#34495E] p-6 rounded-lg shadow-lg mb-6">
          <h2 class="text-2xl font-bold mb-4">Comments</h2>
          <?php $userRepository = new \src\Repositories\UserRepository();
            if (!empty($comments)) 
            {
              renderComments($comments, $authenticatedUser, $userRepository, $article->getId());
            } 
            else { echo '<p>No comments yet.</p>'; }
          ?>
    </div>
    
    <!-- Comment Submission Form -->
    <div class="bg-[#34495E] p-6 rounded-lg shadow-lg">
      <h2 class="text-2xl font-bold mb-4">Leave a Comment</h2>
      <?php if (isset($authenticatedUser)): ?>
        <form action="/comments/create" method="POST" class="space-y-4">
          <input type="hidden" name="article_id" value="<?= e($article->getId()) ?>">
          <!-- Optionally, you might want to include a hidden input for parent_id if replying -->
          <textarea name="description" rows="4" placeholder="Your comment" class="w-full p-2 bg-gray-700 text-white rounded focus:outline-none focus:ring-2 focus:ring-[#E67E22]"></textarea>
          <button type="submit" class="px-4 py-2 bg-[#E67E22] rounded hover:bg-[#d66e1e] text-white">
            Submit
          </button>
        </form>
      <?php else: ?>
        <p>Please <a href="/login" class="text-[#E67E22] hover:underline">login</a> to leave a comment.</p>
      <?php endif; ?>
    </div>
   
  </div>

  <script>
function toggleReplyForm(commentId) {
    var form = document.getElementById('reply-form-' + commentId);
    if (form.classList.contains('hidden')) {
        form.classList.remove('hidden');
    } else {
        form.classList.add('hidden');
    }
}
</script>

</body>
