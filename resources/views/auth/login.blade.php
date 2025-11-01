@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card border-0 shadow-lg rounded-4">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <div class="auth-icon-wrapper mb-3">
                            <i class="bi bi-shield-lock-fill text-primary fs-1" id="authIcon"></i>
                        </div>
                        <h2 class="fw-bold mb-2" id="authTitle">Welcome</h2>
                        <p class="text-muted" id="authSubtitle">Enter your phone number to receive an OTP</p>
                        <div class="alert alert-info d-none mt-3" id="registerNotice">
                            <small><i class="bi bi-info-circle me-1"></i>New users will be automatically registered when verifying their phone number.</small>
                        </div>
                    </div>

                    <!-- Phone Input Step -->
                    <div id="phoneStep">
                        <form id="phoneForm">
                            <div class="mb-4">
                                <label for="phone" class="form-label fw-semibold">Phone Number</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="bi bi-telephone-fill text-primary"></i>
                                    </span>
                                    <input 
                                        type="tel" 
                                        class="form-control border-start-0" 
                                        id="phone" 
                                        name="phone" 
                                        placeholder="+1234567890" 
                                        required
                                        autocomplete="tel"
                                    >
                                </div>
                                <div class="invalid-feedback" id="phoneError"></div>
                                <small class="text-muted">We'll send you a 6-digit verification code</small>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100 py-2 rounded-pill fw-semibold" id="sendOtpBtn">
                                <span class="spinner-border spinner-border-sm d-none me-2" id="phoneSpinner"></span>
                                <i class="bi bi-send-fill me-2"></i><span id="sendOtpText">Send OTP</span>
                            </button>
                        </form>
                    </div>

                    <!-- OTP Verification Step -->
                    <div id="otpStep" class="d-none">
                        <div class="text-center mb-4">
                            <p class="text-muted mb-2">We sent a code to</p>
                            <p class="fw-semibold" id="phoneDisplay"></p>
                            <button type="button" class="btn btn-link text-decoration-none p-0" id="changePhoneBtn">
                                Change number
                            </button>
                        </div>

                        <form id="otpForm">
                            <div class="mb-4">
                                <label for="otp" class="form-label fw-semibold">Enter OTP</label>
                                <div class="otp-input-group d-flex gap-2 justify-content-center">
                                    <input type="text" class="form-control otp-input text-center fw-bold" maxlength="1" pattern="[0-9]" required>
                                    <input type="text" class="form-control otp-input text-center fw-bold" maxlength="1" pattern="[0-9]" required>
                                    <input type="text" class="form-control otp-input text-center fw-bold" maxlength="1" pattern="[0-9]" required>
                                    <input type="text" class="form-control otp-input text-center fw-bold" maxlength="1" pattern="[0-9]" required>
                                    <input type="text" class="form-control otp-input text-center fw-bold" maxlength="1" pattern="[0-9]" required>
                                    <input type="text" class="form-control otp-input text-center fw-bold" maxlength="1" pattern="[0-9]" required>
                                </div>
                                <input type="hidden" id="otp" name="otp">
                                <div class="invalid-feedback text-center mt-2" id="otpError"></div>
                            </div>

                            <div class="mb-4 text-center">
                                <p class="text-muted mb-2" id="resendTimer">
                                    Resend OTP in <span id="timerCount">60</span> seconds
                                </p>
                                <button type="button" class="btn btn-link text-decoration-none d-none" id="resendOtpBtn">
                                    Resend OTP
                                </button>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100 py-2 rounded-pill fw-semibold" id="verifyOtpBtn">
                                <span class="spinner-border spinner-border-sm d-none me-2" id="otpSpinner"></span>
                                <i class="bi bi-check-circle-fill me-2"></i><span id="verifyBtnText">Verify & Login</span>
                            </button>
                        </form>
                    </div>

                    <div class="text-center mt-4">
                        <small class="text-muted">
                            By continuing, you agree to our 
                            <a href="#" class="text-decoration-none">Terms of Service</a> 
                            and <a href="#" class="text-decoration-none">Privacy Policy</a>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/auth.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('js/auth.js') }}"></script>
@endpush

