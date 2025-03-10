# PenPoint

**PenPoint** is a web-based news aggregation application that allows users to submit, view, and interact with articles. It features user authentication, CRUD operations for articles, nested comments (similar to Reddit/Hacker News), voting on articles, and password management including reset functionality.

## Features

- **User Authentication:**
  - User registration with email, name, and password (password is hashed using Bcrypt).
  - Login and logout functionality.
  - Integration with GitHub SSO.
- **Article Management:**

  - Create, read, update, and delete (CRUD) articles.
  - Each article is associated with the user who submitted it.
  - Pagination on the home page with search functionality.
  - Voting on articles (upvote only, with one vote per user).

- **Comments:**

  - Nested comments allowing users to reply to articles and to other comments.
  - Authorization checks ensure users can only delete their own comments.
  - Display of comment counts.

- **Password Reset:**
  - Request a password reset link via email (for demo, a link is shown).
  - Reset password with token validation.
- **Additional Security:**

  - Use of parameterized queries to prevent SQL injection.
  - Output escaping to prevent XSS attacks.
  - Secure session management with HttpOnly cookies and session regeneration after login.

- **Caching:**
  - Redis caching (using Predis) for reducing database load on home page queries.

## Technologies Used

- **Backend:** PHP (using a custom MVC structure)
- **Database:** MySQL
- **Caching:** Redis (with Predis)
- **Authentication:** PHP sessions and password hashing (Bcrypt)
- **Frontend:** HTML, Tailwind CSS, JavaScript
- **Version Control:** Git and GitHub
- **Optional Services:** GitHub SSO integration
