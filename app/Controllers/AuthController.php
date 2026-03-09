<?php
/**
 * Authentication Controller
 */
class AuthController extends Controller {
    private $user;
    
    public function __construct() {
        parent::__construct();
        $this->user = new User();
    }
    
    public function showLogin() {
        $this->requireGuest();
        $this->renderWithLayout('auth.login', [
            'title' => 'Login - ' . APP_NAME
        ]);
    }
    
    public function login() {
        $this->requireGuest();
        
        $errors = $this->validate($_POST, [
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);
        
        if ($errors) {
            $_SESSION['old'] = $_POST;
            flash('error', implode(' ', $errors));
            $this->redirect('login');
        }
        
        $user = $this->user->authenticate($_POST['email'], $_POST['password']);
        
        if (!$user) {
            flash('error', 'Invalid email or password.');
            $this->redirect('login');
        }
        
        if ($user['is_blacklisted']) {
            flash('error', 'Your account has been suspended. Reason: ' . ($user['blacklist_reason'] ?? 'Contact admin.'));
            $this->redirect('login');
        }
        
        $this->startSession($user);
        
        // Log activity
        $log = new ActivityLog();
        $log->log('login', 'User logged in', 'user', $user['id']);
        
        // Redirect to intended URL or dashboard
        $intended = $_SESSION['intended_url'] ?? ($user['role'] === 'admin' ? 'admin' : '/');
        unset($_SESSION['intended_url']);
        
        flash('success', 'Welcome back, ' . $user['name'] . '!');
        $this->redirect($intended);
    }
    
    public function showRegister() {
        $this->requireGuest();
        $this->renderWithLayout('auth.register', [
            'title' => 'Register - ' . APP_NAME
        ]);
    }
    
    public function register() {
        $this->requireGuest();
        
        $errors = $this->validate($_POST, [
            'name' => 'required|min:2|max:100',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|phone|unique:users,phone',
            'password' => 'required|min:6|confirmed',
        ]);
        
        if ($errors) {
            $_SESSION['old'] = $_POST;
            flash('error', implode(' ', $errors));
            $this->redirect('register');
        }
        
        $userId = $this->user->createUser([
            'name' => sanitize($_POST['name']),
            'email' => sanitize($_POST['email']),
            'phone' => sanitize($_POST['phone']),
            'password' => $_POST['password'],
            'role' => 'user',
        ]);
        
        $user = $this->user->find($userId);
        $this->startSession($user);
        
        // Log activity
        $log = new ActivityLog();
        $log->log('register', 'New user registered', 'user', $userId);
        
        // Send welcome notification
        $notification = new Notification();
        $notification->createNotification(
            $userId,
            'welcome',
            'Welcome to ' . APP_NAME,
            'Thank you for joining! Start booking your favorite courts now.',
            [],
            'web'
        );
        
        flash('success', 'Welcome to ' . APP_NAME . '! Your account has been created.');
        $this->redirect('/');
    }
    
    public function logout() {
        if (isLoggedIn()) {
            $log = new ActivityLog();
            $log->log('logout', 'User logged out', 'user', $_SESSION['user_id']);
        }
        
        session_destroy();
        session_start();
        
        flash('success', 'You have been logged out.');
        $this->redirect('/');
    }
    
    public function showforgotpassword() {
        $this->requireGuest();
        $this->renderWithLayout('auth.forgotpassword', [
            'title' => 'Forgot Password - ' . APP_NAME
        ]);
    }

    public function forgotpassword() {
        $this->requireGuest();
        
        $email = sanitize($_POST['email'] ?? '');
        $user = $this->user->findByEmail($email);
        
        if ($user) {
            // Generate reset token
            $token = bin2hex(random_bytes(32));
            $this->db->query(
                "UPDATE users SET remember_token = ?, updated_at = NOW() WHERE id = ?",
                [$token, $user['id']]
            );
            
            // TODO: Send email with reset link
            // For now, just show a message
        }
        
        // Always show success to prevent email enumeration
        flash('success', 'If an account exists with that email, you will receive a password reset link.');
        $this->redirect('login');
    }
    
    public function profile() {
        $this->requireAuth();
        
        $user = currentUser();
        $stats = $this->user->getStats($user['id']);
        $bookings = $this->user->getBookings($user['id']);
        
        $this->renderWithLayout('auth.profile', [
            'title' => 'My Profile - ' . APP_NAME,
            'user' => $user,
            'stats' => $stats,
            'bookings' => array_slice($bookings, 0, 5),
        ]);
    }
    
    public function updateProfile() {
        $this->requireAuth();
        
        $user = currentUser();
        
        $errors = $this->validate($_POST, [
            'name' => 'required|min:2|max:100',
            'email' => 'required|email|unique:users,email,' . $user['id'],
            'phone' => 'required|phone|unique:users,phone,' . $user['id'],
        ]);
        
        if ($errors) {
            flash('error', implode(' ', $errors));
            $this->redirect('profile');
        }
        
        $this->user->update($user['id'], [
            'name' => sanitize($_POST['name']),
            'email' => sanitize($_POST['email']),
            'phone' => sanitize($_POST['phone']),
        ]);
        
        // Update session
        $_SESSION['user_name'] = sanitize($_POST['name']);
        
        flash('success', 'Profile updated successfully.');
        $this->redirect('profile');
    }
    
    public function changePassword() {
        $this->requireAuth();
        
        $user = currentUser();
        
        // Verify current password
        if (!password_verify($_POST['current_password'], $user['password'])) {
            flash('error', 'Current password is incorrect.');
            $this->redirect('profile');
        }
        
        $errors = $this->validate($_POST, [
            'password' => 'required|min:6|confirmed',
        ]);
        
        if ($errors) {
            flash('error', implode(' ', $errors));
            $this->redirect('profile');
        }
        
        $this->user->updatePassword($user['id'], $_POST['password']);
        
        flash('success', 'Password changed successfully.');
        $this->redirect('profile');
    }
    
    // Social Login Handlers (Facebook/Google)
    public function redirectToFacebook() {
        // Implement Facebook OAuth redirect
        // For production, use Facebook SDK or OAuth library
        flash('info', 'Facebook login coming soon!');
        $this->redirect('login');
    }
    
    public function handleFacebookCallback() {
        // Handle Facebook OAuth callback
        // For production implementation
    }
    
    public function redirectToGoogle() {
        // Implement Google OAuth redirect
        flash('info', 'Google login coming soon!');
        $this->redirect('login');
    }
    
    public function handleGoogleCallback() {
        // Handle Google OAuth callback
    }
    
    private function startSession($user) {
        $_SESSION['user'] = [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role'],
            'phone' => $user['phone'] ?? null,
            'profile_image' => $user['profile_image'] ?? null,
        ];
        
        // Regenerate session ID for security
        session_regenerate_id(true);
    }
}
