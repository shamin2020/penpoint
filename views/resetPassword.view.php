<?php require_once 'header.php'; ?>
<body class="bg-[#2C3E50] text-[#ECF0F1] pb-10">
  <?php require_once 'nav.php'; ?>

  <div class="card w-96 bg-[#34495E] mx-auto rounded-lg shadow-lg my-20">
    <div class="card-body p-8">
               <!-- Display flash messages -->
  <?php 
    $error = popSessionData('error');
    $success = popSessionData('success');
    if ($error): ?>
      <div class="mb-4 p-4 bg-red-500 text-white rounded">
        <?= e($error) ?>
      </div>
  <?php endif; ?>
  <?php if ($success): ?>
  <div class="mb-4 p-4 bg-green-500 text-white rounded">
    <?= $success ?>
  </div>
  <?php endif; ?>
  
      <?php if (isset($token)): ?>
        <h2 class="card-title text-white mx-auto text-2xl font-bold mb-6">Reset Password</h2>
        <form class="space-y-6" action="/reset-password" method="POST">
          <!-- Pass the token as hidden input -->
          <input type="hidden" name="token" value="<?= e($token) ?>">

          <!-- New Password Field with Eye Toggle -->
          <div class="relative">
            <label for="new_password" class="block text-white text-sm font-medium mb-2">New Password</label>
            <input id="new_password" name="password" type="password" placeholder="New password" autocomplete="new-password"
                   class="input input-bordered w-full bg-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-[#E67E22]">
            <span class="absolute inset-y-0 right-0 pr-3 pt-5 flex items-center cursor-pointer" onclick="togglePassword('new_password', 'toggleIconNew')">
              <svg id="toggleIconNew" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
              </svg>
            </span>
          </div>

          <!-- Confirm New Password Field with Eye Toggle -->
          <div class="relative">
            <label for="confirm_password" class="block text-white text-sm font-medium mb-2">Confirm New Password</label>
            <input id="confirm_password" name="confirm_password" type="password" placeholder="Confirm new password" autocomplete="new-password"
                   class="input input-bordered w-full bg-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-[#E67E22]">
            <span class="absolute inset-y-0 right-0 pr-3 pt-5 flex items-center cursor-pointer" onclick="togglePassword('confirm_password', 'toggleIconConfirm')">
              <svg id="toggleIconConfirm" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
              </svg>
            </span>
          </div>

          <div>
            <button type="submit"
                    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-[#E67E22] hover:bg-[#d66e1e] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#E67E22]">
              Reset Password
            </button>
          </div>
        </form>
      <?php else: ?>
        <h2 class="card-title text-white mx-auto text-2xl font-bold mb-6">Request Password Reset</h2>
        <form class="space-y-6" action="/reset-password" method="POST">
          <div>
            <label for="email" class="text-white">Email Address</label>
            <div class="mt-1">
              <input 
                id="email" 
                name="email" 
                type="email" 
                placeholder="Your email address" 
                autocomplete="email"
                class="input input-bordered w-full max-w-xs bg-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-[#E67E22]">
            </div>
          </div>
          <div>
            <button type="submit"
                    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-[#E67E22] hover:bg-[#d66e1e] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#E67E22]">
              Request Reset Link
            </button>
          </div>
        </form>
      <?php endif; ?>
    </div>
  </div>


  <script>
    function togglePassword(inputId, iconId) {
      var input = document.getElementById(inputId);
      var icon = document.getElementById(iconId);
      if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('text-gray-300');
        icon.classList.add('text-[#E67E22]');
      } else {
        input.type = 'password';
        icon.classList.remove('text-[#E67E22]');
        icon.classList.add('text-gray-300');
      }
    }
  </script>
</body>
