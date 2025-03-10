<?php require_once 'header.php' ?>
<body class="bg-[#2C3E50] text-[#ECF0F1] p-20">
  <div class="card w-96 bg-[#34495E] mx-auto mt-20 rounded-lg shadow-lg">
    <div class="card-body p-8">
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

      <h2 class="card-title text-white mx-auto text-2xl font-bold mb-6">Login</h2>
      
      <?php 
      $error = popSessionData('error');
      if ($error): 
      ?>
        <div class="mb-4 p-4 bg-red-500 text-white rounded">
          <?= htmlspecialchars($error) ?>
        </div>
      <?php endif; ?>
      
      <div class="py-4">
        <form class="space-y-6" action="/login" method="POST">
          <div>
            <label for="email" class="text-white">Email address</label>
            <div class="mt-1">
              <input 
                id="email" 
                name="email" 
                type="email" 
                placeholder="Your email" 
                autocomplete="email"
                class="input input-bordered w-full max-w-xs bg-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-[#E67E22]"
                value="<?= getSessionData('email') ?? '' ?>"
              >
            </div>
          </div>
          <div>
            <label for="password" class="text-white">Password</label>
            <div class="mt-1">
              <input 
                id="password" 
                name="password" 
                type="password" 
                placeholder="Your password"
                autocomplete="current-password"
                class="input input-bordered w-full max-w-xs bg-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-[#E67E22]">
            </div>
          </div>
          <div>
            <div class="flex items-center">
              <span class="text-sm">Don't have an account?&nbsp;&nbsp;</span>
              <a href="/register" class="font-medium text-[#E67E22] hover:text-[#d66e1e]">
                Register
              </a>
            </div>
            <div>
              <a href="/reset-password" class="text-sm font-medium text-[#E67E22] hover:underline">
                Forgot Password?
              </a>
            </div>
          </div>
          <div>
            <button type="submit"
                    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-[#E67E22] hover:bg-[#d66e1e] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#E67E22]">
              Login
            </button>
          </div>
        </form>
      </div>
      
      <!-- OR Divider -->
      <div class="mt-6 flex items-center">
        <div class="flex-grow border-t border-gray-600"></div>
        <span class="mx-4">or</span>
        <div class="flex-grow border-t border-gray-600"></div>
      </div>
      
      <!-- Login with GitHub Button -->
      <div class="mt-6">
        <a href="/auth/github" class="w-full inline-flex items-center justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gray-800 hover:bg-gray-700 transition-colors">
          <!-- GitHub Icon (Inline SVG) -->
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 .5C5.649.5.5 5.649.5 12c0 5.092 3.292 9.406 7.863 10.94.575.107.787-.25.787-.556 0-.274-.01-1.001-.015-1.966-3.202.696-3.877-1.544-3.877-1.544-.523-1.33-1.277-1.684-1.277-1.684-1.043-.713.08-.699.08-.699 1.153.081 1.758 1.186 1.758 1.186 1.026 1.758 2.693 1.251 3.35.957.104-.744.402-1.251.731-1.537-2.555-.291-5.243-1.277-5.243-5.687 0-1.256.449-2.283 1.184-3.089-.119-.292-.513-1.467.112-3.06 0 0 .967-.31 3.17 1.18.92-.256 1.91-.384 2.89-.388.98.004 1.97.132 2.89.388 2.2-1.49 3.165-1.18 3.165-1.18.627 1.593.233 2.768.114 3.06.737.806 1.182 1.833 1.182 3.089 0 4.42-2.691 5.392-5.256 5.675.414.356.783 1.059.783 2.137 0 1.544-.014 2.788-.014 3.166 0 .31.208.668.793.555C20.712 21.406 24 17.092 24 12 24 5.649 18.351.5 12 .5z"/>
          </svg>
          Login with GitHub
        </a>
      </div>
      
    </div>
  </div>
</body>
