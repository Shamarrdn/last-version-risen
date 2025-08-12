@extends(auth()->user()->hasRole('admin') ? 'layouts.admin' : 'layouts.customer')

@section('content')
<div class="profile-page-container overflow-y-auto">
    <!-- User Profile Card -->

    <!-- Profile Content Area -->
    <div class="profile-content">
        <!-- Profile Information Section -->
        <div class="content-section">
            <div class="section-header">
                <i class="fas fa-user section-icon"></i>
                <h2 class="section-title">Profile Information</h2>
            </div>
            <p class="section-description">Update your account's profile information and email address</p>

            @if (Laravel\Fortify\Features::canUpdateProfileInformation())
                <div class="livewire-form">
                    @livewire('profile.update-profile-information-form')
                </div>
            @endif
        </div>

        <!-- Password Update Section -->
        <div class="content-section">
            <div class="section-header">
                <i class="fas fa-lock section-icon"></i>
                <h2 class="section-title">Update Password</h2>
            </div>
            <p class="section-description">Ensure your account is using a long, random password to stay secure</p>

            @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::updatePasswords()))
                <div class="livewire-form">
                    @livewire('profile.update-password-form')
                </div>
            @endif
        </div>

        <!-- Two-Factor Authentication Section -->
        @if (Laravel\Fortify\Features::canManageTwoFactorAuthentication())
        <div class="content-section">
            <div class="section-header">
                <i class="fas fa-shield-alt section-icon"></i>
                <h2 class="section-title">Two Factor Authentication</h2>
            </div>
            <div class="livewire-form">
                @livewire('profile.two-factor-authentication-form')
            </div>
        </div>
        @endif

        <!-- Other Sections -->
        <div class="content-section">
            <div class="livewire-form">
                @livewire('profile.logout-other-browser-sessions-form')
            </div>
        </div>

        @if (Laravel\Jetstream\Jetstream::hasAccountDeletionFeatures())
        <div class="content-section danger-zone">
            <div class="section-header">
                <i class="fas fa-exclamation-triangle section-icon"></i>
                <h2 class="section-title">Delete Account</h2>
            </div>
            <div class="livewire-form">
                @livewire('profile.delete-user-form')
            </div>
        </div>
        @endif
    </div>
</div>

<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

:root {
    --primary-color: #000000;
    --primary-hover: #333333;
    --primary-light: rgba(0, 0, 0, 0.08);
    --border-color: #e0e0e0;
    --text-muted: #777;
    --gradient-primary: linear-gradient(135deg, #000000, #333333);
    --shadow-soft: 0 4px 20px rgba(0,0,0,0.08), 0 8px 32px rgba(0,0,0,0.1);
    --font-family: 'Poppins', sans-serif;
}

body {
    font-family: var(--font-family);
}

.profile-page-container {
    display: flex;
    gap: 2rem;
    padding: 2rem;
    max-width: 1200px;
    margin: 0 auto;
    min-height: calc(100vh - 160px);
    background: #f8f9fa;
}

/* Profile Card Styles */
.profile-card {
    width: 320px;
    background: white;
    border-radius: 16px;
    box-shadow: var(--shadow-soft);
    padding: 2rem;
    position: sticky;
    top: 120px;
    height: fit-content;
    transition: transform 0.3s ease;
}

.profile-card:hover {
    transform: translateY(-5px);
}

.profile-header {
    text-align: center;
    margin-bottom: 2rem;
}

.avatar {
    width: 120px;
    height: 120px;
    margin: 0 auto 1.5rem;
    border-radius: 50%;
    background: var(--gradient-primary);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 3.5rem;
    color: white;
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    transition: transform 0.3s ease;
}

.avatar:hover {
    transform: scale(1.05);
}

.user-name {
    font-size: 1.75rem;
    font-weight: 600;
    margin-bottom: 0.25rem;
    color: #2d3436;
    letter-spacing: -0.5px;
}

.user-email {
    color: var(--text-muted);
    font-size: 1rem;
    font-weight: 300;
}

.profile-details {
    margin: 2rem 0;
    border-top: 1px solid var(--border-color);
    border-bottom: 1px solid var(--border-color);
    padding: 1.5rem 0;
}

.detail-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1.25rem;
    transition: transform 0.2s ease;
}

.detail-item:hover {
    transform: translateX(5px);
}

.detail-icon {
    color: var(--primary-color);
    font-size: 1.25rem;
    width: 24px;
    text-align: center;
}

.detail-content {
    display: flex;
    flex-direction: column;
}

.detail-label {
    font-size: 0.85rem;
    color: var(--text-muted);
    margin-bottom: 0.25rem;
    font-weight: 400;
}

.detail-value {
    font-size: 1rem;
    color: #2d3436;
    font-weight: 500;
}

.profile-menu {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.menu-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem;
    border-radius: 12px;
    color: #2d3436;
    text-decoration: none;
    transition: all 0.3s ease;
    font-weight: 500;
}

.menu-item:hover {
    background: var(--primary-light);
    color: var(--primary-color);
    transform: translateX(5px);
}

.menu-item.active {
    background: var(--gradient-primary);
    color: white;
    font-weight: 600;
}

.menu-item i {
    width: 24px;
    text-align: center;
    font-size: 1.1rem;
}

/* Profile Content Styles */
.profile-content {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 2rem;
}

.content-section {
    background: white;
    border-radius: 16px;
    box-shadow: var(--shadow-soft);
    padding: 2.5rem;
    transition: transform 0.3s ease;
}

.content-section:hover {
    transform: translateY(-5px);
}

.section-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.section-icon {
    color: var(--primary-color);
    font-size: 1.75rem;
}

.section-title {
    font-size: 1.5rem;
    font-weight: 600;
    color: #2d3436;
    margin: 0;
    letter-spacing: -0.5px;
}

.section-description {
    color: var(--text-muted);
    margin-bottom: 2rem;
    font-size: 1rem;
    font-weight: 300;
    line-height: 1.6;
}

/* Form Input Styles */
.livewire-form input:not([type="checkbox"]):not([type="radio"]),
.livewire-form textarea,
.livewire-form select {
    width: 100%;
    padding: 1rem 1.25rem;
    border: 2px solid var(--border-color);
    border-radius: 12px;
    font-size: 1rem;
    font-family: var(--font-family);
    transition: all 0.3s ease;
    margin-bottom: 1.25rem;
}

.livewire-form input:focus,
.livewire-form textarea:focus,
.livewire-form select:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 4px var(--primary-light);
}

.livewire-form label {
    display: block;
    margin-bottom: 0.75rem;
    font-size: 0.95rem;
    font-weight: 500;
    color: #2d3436;
}

/* Button Styles */
.livewire-form button {
    background: var(--primary-color);
    color: white;
    border: none;
    padding: 1rem 2rem;
    border-radius: 12px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}

.livewire-form button:hover {
    background: var(--primary-hover);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.3);
}

/* Form Group Styling */
.form-group {
    margin-bottom: 2rem;
}

/* Error Message Styling */
.text-sm.text-red-600 {
    font-size: 0.9rem;
    color: #e74c3c;
    margin-top: -0.75rem;
    margin-bottom: 1.25rem;
    display: block;
    font-weight: 500;
}

.danger-zone {
    border-left: 4px solid #e74c3c;
    background: #fff5f5;
}

.danger-zone .section-icon {
    color: #e74c3c;
}

/* Responsive Design */
@media (max-width: 992px) {
    .profile-page-container {
        flex-direction: column;
    }

    .profile-card {
        width: 100%;
        position: static;
    }
}

@media (max-width: 576px) {
    .profile-page-container {
        padding: 1rem;
    }

    .profile-card,
    .content-section {
        padding: 1.5rem;
    }
}
</style>
@endsection