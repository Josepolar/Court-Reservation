<?php
/**
 * Authentication Controller
 */
use League\OAuth2\Client\Provider\GenericProvider;

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
            $log->log('logout', 'User logged out', 'user', currentUser()['id'] ?? null);
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
        if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            flash('error', 'Please enter a valid email address.');
            $this->redirect('forgotpassword');
        }

        $user = $this->user->findByEmail($email);
        
        if ($user) {
            // Generate reset token
            $token = bin2hex(random_bytes(32));
            $this->db->query(
                "UPDATE users SET remember_token = ?, updated_at = NOW() WHERE id = ?",
                [$token, $user['id']]
            );
            
            $resetLink = url('resetpassword?token=' . urlencode($token));
            $subject = APP_NAME . ' Password Reset Request';
            $textBody = "Hello {$user['name']},\n\n"
                . "We received a request to reset your password.\n"
                . "Use this link to set a new password:\n{$resetLink}\n\n"
                . "If you did not request this, you can ignore this email.\n\n"
                . APP_NAME;

            $htmlBody = '<p>Hello ' . e($user['name']) . ',</p>'
                . '<p>We received a request to reset your password.</p>'
                . '<p><a href="' . e($resetLink) . '">Click here to reset your password</a></p>'
                . '<p>If you did not request this, you can ignore this email.</p>'
                . '<p>' . e(APP_NAME) . '</p>';

            $sent = sendEmail($user['email'], $subject, $textBody, $user['name'], $htmlBody);
            if (!$sent) {
                logError('Forgot password email was not sent', [
                    'user_id' => $user['id'],
                    'email' => $user['email'],
                    'reset_link' => $resetLink,
                ]);
            }
        }
        
        // Always show success to prevent email enumeration
        flash('success', 'If an account exists with that email, you will receive a password reset link.');
        $this->redirect('login');
    }

    public function showResetPassword() {
        $this->requireGuest();

        $token = sanitize($_GET['token'] ?? '');
        if (!$token) {
            flash('error', 'Invalid or missing reset token.');
            $this->redirect('forgotpassword');
        }

        $user = $this->db->fetch("SELECT id FROM users WHERE remember_token = ?", [$token]);
        if (!$user) {
            flash('error', 'This password reset link is invalid or expired.');
            $this->redirect('forgotpassword');
        }

        $this->renderWithLayout('auth.resetpassword', [
            'title' => 'Reset Password - ' . APP_NAME,
            'token' => $token,
        ]);
    }

    public function resetPassword() {
        $this->requireGuest();

        $token = sanitize($_POST['token'] ?? '');
        $password = $_POST['password'] ?? '';
        $passwordConfirmation = $_POST['password_confirmation'] ?? '';

        if (!$token) {
            flash('error', 'Invalid reset token.');
            $this->redirect('forgotpassword');
        }

        $errors = $this->validate([
            'password' => $password,
            'password_confirmation' => $passwordConfirmation,
        ], [
            'password' => 'required|min:6|confirmed',
        ]);

        if ($errors) {
            flash('error', implode(' ', $errors));
            $this->redirect('resetpassword?token=' . urlencode($token));
        }

        $user = $this->db->fetch("SELECT id FROM users WHERE remember_token = ?", [$token]);
        if (!$user) {
            flash('error', 'This password reset link is invalid or expired.');
            $this->redirect('forgotpassword');
        }

        $this->db->query(
            "UPDATE users SET password = ?, remember_token = NULL, updated_at = NOW() WHERE id = ?",
            [password_hash($password, PASSWORD_DEFAULT), $user['id']]
        );

        flash('success', 'Your password has been reset. Please login with your new password.');
        $this->redirect('login');
    }
    
    public function profile(    ) {
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
        $this->requireGuest();

        $missing = [];
        if (!config('oauth.google.client_id')) {
            $missing[] = 'GOOGLE_OAUTH_CLIENT_ID';
        }
        if (!config('oauth.google.client_secret')) {
            $missing[] = 'GOOGLE_OAUTH_CLIENT_SECRET';
        }
        if (!config('oauth.google.redirect_uri')) {
            $missing[] = 'GOOGLE_OAUTH_REDIRECT_URI';
        }

        if (!empty($missing)) {
            flash('error', 'Google sign-in is not configured. Missing: ' . implode(', ', $missing));
            $this->redirect('login');
        }

        if (!config('oauth.google.enabled', false)) {
            flash('error', 'Google sign-in is disabled. Set GOOGLE_OAUTH_ENABLED=true in .env.');
            $this->redirect('login');
        }

        try {
            $provider = $this->googleProvider();
            $authorizationUrl = $provider->getAuthorizationUrl([
                'scope' => ['openid', 'email', 'profile'],
                'access_type' => 'offline',
                'prompt' => 'select_account',
            ]);
            $_SESSION['google_oauth2_state'] = $provider->getState();
            header('Location: ' . $authorizationUrl);
            exit;
        } catch (Throwable $e) {
            logError('Google OAuth redirect failed', ['message' => $e->getMessage()]);
            flash('error', 'Unable to start Google sign-in. Please try again.');
            $this->redirect('login');
        }
    }
    
    public function handleGoogleCallback() {
        $this->requireGuest();

        $state = $_GET['state'] ?? '';
        $code = $_GET['code'] ?? '';
        $sessionState = $_SESSION['google_oauth2_state'] ?? '';
        unset($_SESSION['google_oauth2_state']);

        if (!$state || !$sessionState || !hash_equals($sessionState, $state)) {
            flash('error', 'Invalid Google sign-in state. Please try again.');
            $this->redirect('login');
        }

        if (!$code) {
            flash('error', 'Google sign-in was cancelled or failed.');
            $this->redirect('login');
        }

        try {
            $provider = $this->googleProvider();
            $token = $provider->getAccessToken('authorization_code', [
                'code' => $code,
            ]);
            $owner = $provider->getResourceOwner($token);
            $googleUser = $owner->toArray();

            $user = $this->findOrCreateGoogleUser($googleUser);
            if (!$user) {
                flash('error', 'Unable to sign in with Google.');
                $this->redirect('login');
            }

            if (!empty($user['is_blacklisted'])) {
                flash('error', 'Your account has been suspended. Reason: ' . ($user['blacklist_reason'] ?? 'Contact admin.'));
                $this->redirect('login');
            }

            $this->startSession($user);

            $log = new ActivityLog();
            $log->log('google_login', 'User logged in with Google', 'user', $user['id']);

            flash('success', 'Signed in with Google successfully.');
            $this->redirect('/');
        } catch (Throwable $e) {
            logError('Google OAuth callback failed', [
                'message' => $e->getMessage(),
                'query' => $_GET,
            ]);
            flash('error', 'Google sign-in failed. Please try again.');
            $this->redirect('login');
        }
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
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_role'] = $user['role'];
        
        // Regenerate session ID for security
        session_regenerate_id(true);
    }

    private function googleProvider() {
        return new GenericProvider([
            'clientId' => config('oauth.google.client_id'),
            'clientSecret' => config('oauth.google.client_secret'),
            'redirectUri' => config('oauth.google.redirect_uri'),
            'urlAuthorize' => config('oauth.google.auth_url'),
            'urlAccessToken' => config('oauth.google.token_url'),
            'urlResourceOwnerDetails' => config('oauth.google.user_info_url'),
        ]);
    }

    private function findOrCreateGoogleUser($googleUser) {
        $providerId = $googleUser['sub'] ?? null;
        $email = $googleUser['email'] ?? null;
        $name = trim((string)($googleUser['name'] ?? 'Google User'));

        if (!$providerId || !$email) {
            return null;
        }

        $existingByProvider = $this->user->findByProvider('google', $providerId);
        if ($existingByProvider) {
            return $existingByProvider;
        }

        $existingByEmail = $this->user->findByEmail($email);
        if ($existingByEmail) {
            $this->user->update($existingByEmail['id'], [
                'provider' => 'google',
                'provider_id' => $providerId,
                'email_verified_at' => $existingByEmail['email_verified_at'] ?? date('Y-m-d H:i:s'),
            ]);
            return $this->user->find($existingByEmail['id']);
        }

        $userId = $this->user->createUser([
            'name' => $name ?: 'Google User',
            'email' => $email,
            'phone' => null,
            'password' => bin2hex(random_bytes(16)),
            'role' => 'user',
            'provider' => 'google',
            'provider_id' => $providerId,
            'email_verified_at' => date('Y-m-d H:i:s'),
        ]);

        return $this->user->find($userId);
    }
}
