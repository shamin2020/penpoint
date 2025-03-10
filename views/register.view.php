<?php require_once 'header.php' ?>

<body class="bg-[#2C3E50] text-[#ECF0F1] min-h-screen flex items-center justify-center">
    
  <div class="bg-[#34495E] p-8 rounded-lg shadow-md w-full max-w-md">
    <h1 class="text-2xl font-bold mb-6 text-center">Register for PenPoint</h1>

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
    
    <!-- Registration Form -->
    <form action="/register" method="POST" class="space-y-4">
      <div>
        <label for="name" class="block text-sm font-medium">Name</label>
        <input type="text" name="name" id="name" required 
               class="w-full mt-1 px-3 py-2 rounded-md bg-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-[#E67E22]">
      </div>
      <div>
        <label for="email" class="block text-sm font-medium">Email</label>
        <input type="email" name="email" id="email" required 
               class="w-full mt-1 px-3 py-2 rounded-md bg-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-[#E67E22]">
      </div>
      <div>
        <label for="password" class="block text-sm font-medium">Password</label>
        <input type="password" name="password" id="password" required 
               class="w-full mt-1 px-3 py-2 rounded-md bg-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-[#E67E22]">
      </div>
      <div>
        <button type="submit" 
                class="w-full bg-[#E67E22] hover:bg-[#d66e1e] transition-colors text-white py-2 rounded-md font-medium">
          Register
        </button>
      </div>
    </form>
    
    <!-- Divider -->
    <div class="mt-6 flex items-center justify-center">
      <span class="text-sm">or register with</span>
    </div>
    
    <!-- Login with GitHub Button -->
<div class="mt-6">
  <a href="/auth/github" class="w-full inline-flex items-center justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gray-800 hover:bg-gray-700 transition-colors">
    <!-- GitHub Icon (Inline SVG) -->
    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" viewBox="0 0 24 24" fill="currentColor">
      <path d="M12 .5C5.65.5.5 5.65.5 12c0 5.09 3.29 9.41 7.86 10.94.57.11.78-.25.78-.56 0-.27-.01-1.01-.02-1.98-3.2.69-3.88-1.54-3.88-1.54-.52-1.33-1.28-1.69-1.28-1.69-1.04-.71.08-.7.08-.7 1.15.08 1.76 1.18 1.76 1.18 1.02 1.76 2.69 1.25 3.35.95.11-.74.4-1.25.73-1.54-2.56-.29-5.25-1.28-5.25-5.68 0-1.26.45-2.28 1.18-3.09-.12-.29-.51-1.46.11-3.05 0 0 .97-.31 3.18 1.18 1-.28 2.08-.42 3.15-.43 1.07.01 2.16.15 3.16.43 2.2-1.49 3.17-1.18 3.17-1.18.62 1.59.23 2.76.11 3.05.74.81 1.18 1.83 1.18 3.09 0 4.41-2.69 5.39-5.26 5.68.41.35.78 1.05.78 2.12 0 1.53-.01 2.77-.01 3.15 0 .31.21.67.79.56C20.71 21.41 24 17.09 24 12 24 5.65 18.35.5 12 .5z"/>
    </svg>
    Login with GitHub
  </a>
</div>
    
    <!-- Link to Login Page -->
    <div class="mt-4 text-center">
      <span class="text-sm">Already have an account?</span>
      <a href="/login" class="text-[#E67E22] hover:underline">Login</a>
    </div>
  </div>
</body>
