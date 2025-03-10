<?php require_once 'header.php'; ?>
<body class="bg-[#2C3E50] text-[#ECF0F1]">
  <?php require_once 'nav.php'; ?>
  <div class="container mx-auto px-4 py-8">
    
    <!-- Search Form -->
    <form action="/" method="GET" class="mb-6 flex justify-center">
      <input type="text" name="search" placeholder="Search articles..." 
             value="<?= htmlspecialchars($search ?? '') ?>" 
             class="p-2 rounded-l-md w-64 bg-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-[#E67E22]">
      <button type="submit" 
              class="px-4 py-2 bg-[#E67E22] rounded-r-md text-white hover:bg-[#d66e1e] transition-colors">
        Search
      </button>
    </form>

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


    <!-- Articles Listing -->
    <?php if (empty($articles)): ?>
      <p class="text-center">No articles found.</p>
    <?php else: ?>
      <?php foreach ($articles as $article): ?>
        <div class="card bg-[#34495E] rounded-lg p-2 mb-4 shadow-lg">
          
          <!-- Title & Voting Container -->
          <div class="flex justify-between items-center">
            <!-- Article Title Container (truncated if too long) -->
            <div class="flex-grow overflow-hidden">
              <h2 class="text-xl font-bold mb-1 whitespace-nowrap overflow-hidden overflow-ellipsis">
              <a href="<?= e($article->getUrl()) ?>" 
                   class="hover:underline" 
                   target="_blank" 
                   title="<?= e($article->getUrl()) ?>">
                  <?= e($article->getTitle()) ?>
                </a>
              </h2>
            </div>
            <!-- Edit & Delete Icons (only for the author) -->
            <div class="flex-none ml-2">
            <?php if (isset($authenticatedUser) && $authenticatedUser->getId() == $article->getAuthorId()): ?>
              <div class="flex items-center space-x-4">
                <!-- Edit Icon -->
                <a href="/articles/edit?id=<?= e($article->getId()) ?>" class="inline-block">
                  <img src="<?= image('edit2.png') ?>" alt="Edit" class="h-5 w-5 hover:opacity-80">
                </a>
                <!-- Delete Icon -->
                <form action="/articles/delete" method="POST" onsubmit="return confirm('Are you sure you want to delete this article?');" class="inline-block">
                  <input type="hidden" name="id" value="<?= e($article->getId()) ?>">
                  <button type="submit">
                    <img src="<?= image('delete1.png') ?>" alt="Delete" class="h-5 w-5 hover:opacity-80">
                  </button>
                </form>
              </div>
            <?php endif; ?>
            </div>
          </div>
          
          <!-- Posted By & Date -->
          <div class="text-sm text-gray-400 mb-2">
            <?php 
              $user = $articleRepository->getArticleAuthor($article->getId());
            ?>
            <p>
              Posted by <span class="font-medium text-white">
                <?= $user ? e($user->getName()) : 'Unknown' ?>
              </span><img src="<?= e($user->getProfilePicture()) ?>" alt="Profile" class="h-5 w-5 rounded-full inline">
              ,<?= date('l jS \o\f F Y, h:i A', strtotime($article->getCreatedAt())) ?>
            </p>
            <?php if ($article->getUpdatedAt()) {
                echo '<p class="text-sm text-gray-400">Updated on: ' . e($article->getUpdatedAtFmt()) . '</p>';
            } ?>
          </div>

          <div class="flex justify-between items-center mt-2 mb-2">
            <!-- Votes Section -->
            <div class="flex items-center space-x-4">
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
              
              
              <!-- Comments Count -->   
              <a href="/article?id=<?= e($article->getId()) ?>" class="flex items-center px-3 py-1 rounded shadow-lg text-white text-sm transition-transform duration-200 hover:-translate-y-1 hover:scale-105 hover:shadow-2xl shrink-0">
                <img src="<?= image('comment1.png') ?>" alt="Comment Count" class="h-5 w-5 mr-2">
                <span><?= function_exists('getArticleComments') ? e(count(getArticleComments($article->getId()))) : 0 ?></span>
              </a>
            </div>


          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>

    <!-- Pagination -->
    <div class="flex justify-center items-center mt-8 space-x-4">
      <?php if ($currentPage > 1): ?>
        <a href="/?page=<?= $currentPage - 1 ?>&search=<?= urlencode($search) ?>" 
           class="px-4 py-2 bg-[#E67E22] text-white rounded hover:bg-[#d66e1e]">
          Previous
        </a>
      <?php endif; ?>
      <span class="text-white">Page <?= $currentPage ?> of <?= $totalPages ?></span>
      <?php if ($currentPage < $totalPages): ?>
        <a href="/?page=<?= $currentPage + 1 ?>&search=<?= urlencode($search) ?>" 
           class="px-4 py-2 bg-[#E67E22] text-white rounded hover:bg-[#d66e1e]">
          Next
        </a>
      <?php endif; ?>
    </div>
  </div>
</body>
