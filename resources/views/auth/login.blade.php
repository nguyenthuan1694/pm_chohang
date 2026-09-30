@extends('layouts.app')

@section('title', 'Đăng nhập | Tân Hòa Lợi - Phần Mềm Chở Hàng')

@section('content')
<div class="thl-login-wrapper">
    <div class="thl-login-backdrop">
        <div class="thl-backdrop-circle-1"></div>
        <div class="thl-backdrop-circle-2"></div>
        <div class="thl-backdrop-grid"></div>
    </div>

    <div class="container thl-login-container">
        <div class="thl-login-grid">
            <!-- Left Column: Hero & Brand Showcase -->
            <section class="thl-hero-panel">
                <div class="thl-brand-badge-wrap">
                    <div class="thl-logo-emblem">
                        <svg width="34" height="34" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect width="40" height="40" rx="10" fill="#193a77"/>
                            <path d="M10 14C10 12.8954 10.8954 12 12 12H23C24.1046 12 25 12.8954 25 14V26C25 27.1046 24.1046 28 23 28H12C10.8954 28 10 27.1046 10 26V14Z" fill="#ffffff" fill-opacity="0.2"/>
                            <path d="M12 23L18 15L24 23H12Z" fill="#ffffff"/>
                            <path d="M22 17H28C29.1046 17 30 17.8954 30 19V26C30 27.1046 29.1046 28 28 28H24V19C24 17.8954 23.1046 17 22 17Z" fill="#ffffff" fill-opacity="0.85"/>
                            <circle cx="16" cy="28" r="2.5" fill="#ffffff"/>
                            <circle cx="26" cy="28" r="2.5" fill="#ffffff"/>
                        </svg>
                    </div>
                    <div class="thl-brand-text-block">
                        <span class="thl-brand-name">TÂN HÒA LỢI</span>
                        <span class="thl-brand-desc">PHẦN MỀM CHỞ HÀNG</span>
                    </div>
                </div>

                <div class="thl-hero-content">
                    <span class="thl-kicker-tag">HỆ THỐNG ĐIỀU PHỐI VẬN CHUYỂN CHUYÊN NGHIỆP</span>
                    <h1 class="thl-hero-title">Quản lý giao nhận & cước phí chuẩn xác từng kilomet.</h1>
                    <p class="thl-hero-subtitle">
                        Giải pháp số hóa toàn diện quy trình giao hàng, kiểm soát cung đường và tối ưu cước chành xe cho các nhóm kinh doanh Tân Hòa Lợi.
                    </p>
                </div>

                <div class="thl-feature-list">
                    <div class="thl-feature-card">
                        <div class="thl-feature-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#193a77" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="1" y="3" width="15" height="13" rx="2"></rect>
                                <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon>
                                <circle cx="5.5" cy="18.5" r="2.5"></circle>
                                <circle cx="18.5" cy="18.5" r="2.5"></circle>
                            </svg>
                        </div>
                        <div>
                            <strong>Điều phối chuyến chở hàng</strong>
                            <span>Quản lý thông tin chuyến, khối lượng và tiến độ giao tức thời</span>
                        </div>
                    </div>

                    <div class="thl-feature-card">
                        <div class="thl-feature-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#193a77" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                        </div>
                        <div>
                            <strong>Định mức kilomet & Cước chành</strong>
                            <span>Tự động tính cước chành, xe ôm theo định mức quãng đường</span>
                        </div>
                    </div>

                    <div class="thl-feature-card">
                        <div class="thl-feature-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#193a77" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg>
                        </div>
                        <div>
                            <strong>Phân quyền theo nhóm độc lập</strong>
                            <span>Bảo mật dữ liệu riêng biệt cho 16+ nhóm kinh doanh và kho</span>
                        </div>
                    </div>
                </div>

                <div class="thl-hero-footer-stat">
                    <div class="thl-stat-item">
                        <span class="thl-stat-num">16+</span>
                        <span class="thl-stat-label">Nhóm kinh doanh</span>
                    </div>
                    <div class="thl-stat-divider"></div>
                    <div class="thl-stat-item">
                        <span class="thl-stat-num">100%</span>
                        <span class="thl-stat-label">Đồng bộ dữ liệu</span>
                    </div>
                    <div class="thl-stat-divider"></div>
                    <div class="thl-stat-item">
                        <span class="thl-stat-num">24/7</span>
                        <span class="thl-stat-label">Vận hành an toàn</span>
                    </div>
                </div>
            </section>

            <!-- Right Column: Login Card -->
            <section class="thl-login-card-wrap">
                <div class="thl-login-card">
                    <!-- Brand shown on mobile -->
                    <div class="thl-mobile-brand">
                        <div class="thl-mobile-logo-emblem">
                            <svg width="28" height="28" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect width="40" height="40" rx="8" fill="#193a77"/>
                                <path d="M12 23L18 15L24 23H12Z" fill="#ffffff"/>
                                <path d="M22 17H28C29.1046 17 30 17.8954 30 19V26C30 27.1046 29.1046 28 28 28H24V19C24 17.8954 23.1046 17 22 17Z" fill="#ffffff" fill-opacity="0.85"/>
                                <circle cx="16" cy="28" r="2.5" fill="#ffffff"/>
                                <circle cx="26" cy="28" r="2.5" fill="#ffffff"/>
                            </svg>
                        </div>
                        <div>
                            <span class="thl-mobile-brand-title">TÂN HÒA LỢI</span>
                            <span class="thl-mobile-brand-sub">PHẦN MỀM CHỞ HÀNG</span>
                        </div>
                    </div>

                    <div class="thl-card-header">
                        <span class="thl-portal-pill">HỆ THỐNG NỘI BỘ</span>
                        <h2 class="thl-card-title">ĐĂNG NHẬP</h2>
                        <p class="thl-card-desc">Nhập tài khoản của bạn để truy cập không gian làm việc.</p>
                    </div>

                    @if ($errors->any())
                        <div class="alert alert-danger thl-auth-alert" role="alert">
                            <div class="d-flex align-items-center gap-2">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                                <span>{{ $errors->first() }}</span>
                            </div>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}" class="thl-form">
                        @csrf

                        <div class="thl-form-group">
                            <label for="email" class="thl-label">
                                <span>Email đăng nhập</span>
                            </label>
                            <div class="thl-input-wrap">
                                <span class="thl-input-icon">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                        <polyline points="22,6 12,13 2,6"></polyline>
                                    </svg>
                                </span>
                                <input 
                                    id="email" 
                                    type="email" 
                                    class="form-control thl-input @error('email') is-invalid @enderror" 
                                    name="email" 
                                    value="{{ old('email') }}" 
                                    required 
                                    autocomplete="email" 
                                    autofocus 
                                    placeholder="ví dụ: example@gmail.com"
                                >
                            </div>
                            @error('email')
                                <span class="thl-field-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="thl-form-group">
                            <div class="thl-label-row">
                                <label for="password" class="thl-label">Mật khẩu</label>
                                @if (Route::has('password.request'))
                                    <a href="{{ route('password.request') }}" class="thl-forgot-link">Quên mật khẩu?</a>
                                @endif
                            </div>
                            <div class="thl-input-wrap">
                                <span class="thl-input-icon">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                    </svg>
                                </span>
                                <input 
                                    id="password" 
                                    type="password" 
                                    class="form-control thl-input @error('password') is-invalid @enderror" 
                                    name="password" 
                                    required 
                                    autocomplete="current-password" 
                                    placeholder="Nhập mật khẩu của bạn"
                                >
                                <button type="button" class="thl-pwd-toggle" id="togglePasswordBtn" aria-label="Hiện/ẩn mật khẩu">
                                    <svg id="eyeIcon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                </button>
                            </div>
                            @error('password')
                                <span class="thl-field-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="thl-form-actions-row">
                            <div class="form-check thl-checkbox-wrap">
                                <input class="form-check-input thl-checkbox" type="checkbox" name="remember" id="remember" {{ old('remember', true) ? 'checked' : '' }}>
                                <label class="form-check-label thl-checkbox-label" for="remember">
                                    Ghi nhớ đăng nhập
                                </label>
                            </div>
                        </div>

                        <button type="submit" class="btn thl-submit-btn">
                            <span>Đăng nhập hệ thống</span>
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                                <polyline points="12 5 19 12 12 19"></polyline>
                            </svg>
                        </button>
                    </form>

                    <div class="thl-card-footer">
                        <div class="thl-security-badge">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#193a77" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                            <span>Hệ thống bảo mật nội bộ Tân Hòa Lợi</span>
                        </div>
                    </div>
                </div>

                <div class="thl-page-bottom-credit">
                    <p>© {{ date('Y') }} <strong>Tân Hòa Lợi</strong> · Phần Mềm Chở Hàng. Tất cả các quyền được bảo lưu.</p>
                </div>
            </section>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var pwdInput = document.getElementById('password');
    var toggleBtn = document.getElementById('togglePasswordBtn');
    var eyeIcon = document.getElementById('eyeIcon');

    if (toggleBtn && pwdInput) {
        toggleBtn.addEventListener('click', function () {
            var isPassword = pwdInput.type === 'password';
            pwdInput.type = isPassword ? 'text' : 'password';
            
            if (isPassword) {
                // Eye off icon
                eyeIcon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>';
                toggleBtn.style.color = '#193a77';
            } else {
                // Normal Eye icon
                eyeIcon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>';
                toggleBtn.style.color = '#94a3b8';
            }
        });
    }
});
</script>

<style>
/* -------------------------------------------------------------
 * RESET & GLOBAL FONT APPLICATION
 * Chủ đạo: Trắng (#fff) và Xanh (#193a77)
 * Font chữ: "Roboto", sans-serif
 * ------------------------------------------------------------- */
@import url('https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap');

.app-navbar {
    display: none !important;
}

body,
.thl-login-wrapper,
.thl-login-wrapper * {
    font-family: "Roboto", -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif !important;
    box-sizing: border-box;
}

.thl-login-wrapper {
    position: relative;
    min-height: 100vh;
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f4f7fb;
    padding: 2.5rem 1rem;
    overflow-x: hidden;
}

/* Background Ambient Elements */
.thl-login-backdrop {
    position: absolute;
    inset: 0;
    pointer-events: none;
    overflow: hidden;
    z-index: 1;
}

.thl-backdrop-circle-1 {
    position: absolute;
    top: -12%;
    left: -8%;
    width: 650px;
    height: 650px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(25, 58, 119, 0.08) 0%, rgba(25, 58, 119, 0) 70%);
}

.thl-backdrop-circle-2 {
    position: absolute;
    bottom: -15%;
    right: -5%;
    width: 700px;
    height: 700px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(25, 58, 119, 0.06) 0%, rgba(25, 58, 119, 0) 70%);
}

.thl-backdrop-grid {
    position: absolute;
    inset: 0;
    background-image: 
        linear-gradient(to right, rgba(25, 58, 119, 0.025) 1px, transparent 1px),
        linear-gradient(to bottom, rgba(25, 58, 119, 0.025) 1px, transparent 1px);
    background-size: 36px 36px;
}

/* Container & Grid Layout */
.thl-login-container {
    position: relative;
    z-index: 2;
    max-width: 1200px;
    margin: 0 auto;
    width: 100%;
}

.thl-login-grid {
    display: grid;
    grid-template-columns: minmax(0, 1.15fr) minmax(380px, 460px);
    gap: clamp(2.5rem, 5vw, 6rem);
    align-items: center;
}

/* Left Column: Hero Showcase */
.thl-hero-panel {
    display: flex;
    flex-direction: column;
    gap: 2rem;
    padding-right: 1rem;
}

.thl-brand-badge-wrap {
    display: inline-flex;
    align-items: center;
    gap: 1rem;
}

.thl-logo-emblem {
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 8px 20px rgba(25, 58, 119, 0.18);
    border-radius: 10px;
}

.thl-brand-text-block {
    display: flex;
    flex-direction: column;
    text-align: left;
}

.thl-brand-name {
    font-size: 1.65rem;
    font-weight: 900;
    color: #193a77;
    letter-spacing: 0.04em;
    line-height: 1.1;
}

.thl-brand-desc {
    font-size: 0.8rem;
    font-weight: 700;
    color: #4b6694;
    letter-spacing: 0.12em;
    margin-top: 0.2rem;
}

.thl-kicker-tag {
    display: inline-block;
    padding: 0.35rem 0.85rem;
    background: #eaf1fc;
    color: #193a77;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 800;
    letter-spacing: 0.08em;
    margin-bottom: 0.85rem;
    border: 1px solid #d4e2f8;
}

.thl-hero-title {
    font-size: clamp(2rem, 3.2vw, 2.85rem);
    font-weight: 900;
    color: #193a77;
    line-height: 1.2;
    letter-spacing: -0.02em;
    margin-bottom: 1rem;
}

.thl-hero-subtitle {
    font-size: 1.05rem;
    color: #475569;
    line-height: 1.7;
    margin-bottom: 0;
    max-width: 580px;
}

/* Feature List */
.thl-feature-list {
    display: flex;
    flex-direction: column;
    gap: 0.9rem;
}

.thl-feature-card {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 0.85rem 1.1rem;
    background: #ffffff;
    border: 1px solid #e1eaf5;
    border-radius: 12px;
    box-shadow: 0 4px 14px rgba(25, 58, 119, 0.04);
    transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
}

.thl-feature-card:hover {
    transform: translateY(-2px);
    border-color: #cbdcf5;
    box-shadow: 0 8px 24px rgba(25, 58, 119, 0.08);
}

.thl-feature-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 42px;
    height: 42px;
    background: #edf3fa;
    border-radius: 10px;
    flex-shrink: 0;
}

.thl-feature-card strong {
    display: block;
    font-size: 0.92rem;
    font-weight: 700;
    color: #193a77;
    line-height: 1.3;
}

.thl-feature-card span {
    display: block;
    font-size: 0.8rem;
    color: #64748b;
    margin-top: 0.15rem;
}

/* Stats Row */
.thl-hero-footer-stat {
    display: flex;
    align-items: center;
    gap: 1.75rem;
    padding-top: 0.5rem;
}

.thl-stat-item {
    display: flex;
    flex-direction: column;
}

.thl-stat-num {
    font-size: 1.75rem;
    font-weight: 900;
    color: #193a77;
    line-height: 1;
}

.thl-stat-label {
    font-size: 0.78rem;
    font-weight: 600;
    color: #64748b;
    margin-top: 0.35rem;
}

.thl-stat-divider {
    width: 1px;
    height: 34px;
    background: #d5e2f3;
}

/* Right Column: Login Card */
.thl-login-card-wrap {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.thl-login-card {
    background: #ffffff;
    border: 1px solid #dbe6f5;
    border-radius: 18px;
    box-shadow: 0 24px 60px rgba(25, 58, 119, 0.09);
    padding: clamp(2rem, 4vw, 2.75rem);
    position: relative;
}

/* Mobile Brand Header */
.thl-mobile-brand {
    display: none;
    align-items: center;
    gap: 0.85rem;
    margin-bottom: 1.75rem;
    padding-bottom: 1.25rem;
    border-bottom: 1px solid #edf2fa;
}

.thl-mobile-logo-emblem {
    display: flex;
    align-items: center;
    justify-content: center;
}

.thl-mobile-brand-title {
    display: block;
    font-size: 1.35rem;
    font-weight: 900;
    color: #193a77;
    line-height: 1.1;
    letter-spacing: 0.03em;
}

.thl-mobile-brand-sub {
    display: block;
    font-size: 0.72rem;
    font-weight: 700;
    color: #5574a6;
    letter-spacing: 0.1em;
}

.thl-card-header {
    margin-bottom: 1.75rem;
}

.thl-portal-pill {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    background: #eaf1fc;
    color: #193a77;
    border-radius: 6px;
    font-size: 0.72rem;
    font-weight: 800;
    letter-spacing: 0.08em;
    margin-bottom: 0.65rem;
}

.thl-card-title {
    font-size: 1.85rem;
    font-weight: 800;
    color: #193a77;
    margin: 0 0 0.4rem;
    letter-spacing: -0.02em;
}

.thl-card-desc {
    font-size: 0.92rem;
    color: #64748b;
    margin: 0;
}

/* Form Fields */
.thl-form {
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
}

.thl-form-group {
    display: flex;
    flex-direction: column;
    gap: 0.45rem;
}

.thl-label-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.thl-label {
    font-size: 0.88rem;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 0;
}

.thl-forgot-link {
    font-size: 0.82rem;
    font-weight: 600;
    color: #193a77;
    text-decoration: none;
    transition: color 0.15s ease;
}

.thl-forgot-link:hover {
    color: #0f244c;
    text-decoration: underline;
}

.thl-input-wrap {
    position: relative;
    display: flex;
    align-items: center;
}

.thl-input-icon {
    position: absolute;
    left: 1rem;
    color: #94a3b8;
    pointer-events: none;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: color 0.2s ease;
}

.thl-input {
    width: 100%;
    padding: 0.85rem 1rem 0.85rem 2.85rem;
    font-size: 0.95rem;
    color: #0f172a;
    background: #ffffff;
    border: 1.5px solid #d5e2f3;
    border-radius: 10px;
    outline: none;
    transition: all 0.2s ease;
}

.thl-input::placeholder {
    color: #94a3b8;
    font-size: 0.88rem;
}

.thl-input:focus {
    border-color: #193a77;
    box-shadow: 0 0 0 4px rgba(25, 58, 119, 0.12);
    background: #ffffff;
}

.thl-input:focus + .thl-input-icon,
.thl-input-wrap:focus-within .thl-input-icon {
    color: #193a77;
}

.thl-pwd-toggle {
    position: absolute;
    right: 0.85rem;
    background: transparent;
    border: 0;
    padding: 0.4rem;
    color: #94a3b8;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: color 0.15s ease;
}

.thl-pwd-toggle:hover {
    color: #193a77;
}

.thl-field-error {
    font-size: 0.8rem;
    color: #dc2626;
    margin-top: 0.25rem;
}

/* Remember Me Checkbox */
.thl-form-actions-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: -0.25rem;
}

.thl-checkbox-wrap {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin: 0;
}

.thl-checkbox {
    width: 1.15rem;
    height: 1.15rem;
    border: 1.5px solid #cbd5e1;
    border-radius: 4px;
    cursor: pointer;
    margin: 0;
}

.thl-checkbox:checked {
    background-color: #193a77;
    border-color: #193a77;
}

.thl-checkbox-label {
    font-size: 0.88rem;
    font-weight: 500;
    color: #475569;
    cursor: pointer;
    user-select: none;
    margin: 0;
}

/* Submit Button */
.thl-submit-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.65rem;
    width: 100%;
    padding: 0.95rem 1.5rem;
    font-size: 1rem;
    font-weight: 700;
    color: #ffffff !important;
    background: #193a77;
    border: 1px solid #193a77;
    border-radius: 10px;
    cursor: pointer;
    box-shadow: 0 4px 16px rgba(25, 58, 119, 0.25);
    transition: all 0.2s ease;
    margin-top: 0.5rem;
}

.thl-submit-btn:hover {
    background: #102752;
    border-color: #102752;
    transform: translateY(-1px);
    box-shadow: 0 8px 24px rgba(25, 58, 119, 0.35);
}

.thl-submit-btn:active {
    transform: translateY(0);
}

/* Card Footer & Security */
.thl-card-footer {
    margin-top: 1.75rem;
    padding-top: 1.25rem;
    border-top: 1px solid #edf2fa;
    display: flex;
    justify-content: center;
}

.thl-security-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.45rem;
    font-size: 0.78rem;
    font-weight: 600;
    color: #193a77;
    background: #f4f7fb;
    padding: 0.4rem 0.85rem;
    border-radius: 6px;
    border: 1px solid #e1eaf5;
}

.thl-page-bottom-credit {
    text-align: center;
    font-size: 0.8rem;
    color: #94a3b8;
    margin-top: 0.5rem;
}

.thl-page-bottom-credit strong {
    color: #193a77;
    font-weight: 700;
}

.thl-auth-alert {
    padding: 0.85rem 1rem;
    font-size: 0.88rem;
    border-radius: 10px;
    margin-bottom: 1.25rem;
    background: #fef2f2;
    border: 1px solid #fee2e2;
    color: #991b1b;
}

/* Responsive Adjustments */
@media (max-width: 991.98px) {
    .thl-login-grid {
        grid-template-columns: 1fr;
        max-width: 500px;
        margin: 0 auto;
        gap: 2rem;
    }

    .thl-hero-panel {
        display: none;
    }

    .thl-mobile-brand {
        display: flex;
    }

    .thl-login-wrapper {
        padding: 2rem 1rem;
    }

    .thl-login-card {
        padding: 2rem 1.5rem;
    }
}
</style>
@endsection
