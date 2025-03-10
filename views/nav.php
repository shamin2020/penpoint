<?php
$authenticatedUser = null;
if (isset($_SESSION['user_id'])) {
    $userRepository = new \src\Repositories\UserRepository();
    $authenticatedUser = $userRepository->getById($_SESSION['user_id']);
}
?>
<nav class="bg-[#2C3E50] text-[#ECF0F1] shadow-lg">
  <div class="container mx-auto flex items-center justify-between">
    <!-- Logo and Site Name -->
    <a href="/" class="flex items-center"> 
      <img src="<?= image('pen-logo.jpg') ?>" alt="Logo" class="h-20 w-20 mr-2">
      <span class="font-bold text-xl text-[#E67E22]">PenPoint</span>
    </a>

    <!-- Navigation Links -->
    <div class="flex items-center space-x-4">
      <?php if ($authenticatedUser): ?>
        <?php
          $profilePic = $authenticatedUser->getProfilePicture();
          if (empty($profilePic)) {
              $profilePic = image('default.jpg', 'profiles');
          }
        ?>
        <!-- Authenticated user: Display profile info and logout link -->
        <div class="flex items-center space-x-2 mr-5">
          <a href="/articles/create" class="px-3 py-2 rounded-md text-sm font-medium border border-[#E67E22] hover:bg-[#E67E22] transition-colors">
            New Article
          </a>
          <a href="/logout" class="flex items-center px-3 py-2 rounded-md text-sm font-medium transition-transform duration-200 hover:-translate-y-1 hover:scale-105 hover:shadow-2xl">
            <img src="<?= image('exit.png') ?>" alt="Logout" class="h-8 w-8 mr-2">
          </a>
          <img src="<?= e($profilePic) ?>" alt="Profile" class="h-8 w-8 rounded-full">
          <a href="/settings">
            <span>Welcome, <?= e($authenticatedUser->getName()) ?>!</span>
          </a>
        </div>
      <?php else: ?>
        <!-- Guest: Login and Register links -->
        <a href="/login" class="px-3 py-2 rounded-md text-sm font-medium hover:bg-[#E67E22] transition-colors">
          Login
        </a>
        <a href="/register" class="px-3 py-2 rounded-md text-sm font-medium hover:bg-[#E67E22] transition-colors">
          Register
        </a>
      <?php endif; ?>
    </div>
  </div>
</nav>
