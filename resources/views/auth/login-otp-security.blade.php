@extends('layouts.app')

@section('title', 'OTP | Teacher Evaluation System')

@section('content')
    <div class="container-fluid welcome">
        <div class="row vh-100">
            <div class="col-lg-6 vh-100 w-first-col">
                <p>Welcome to</p>
                <h2>Teacher Evaluation System</h2>
                <p>Secure and Reliable Web-based Teacher Evaluation System <br /> with Multi-Factor Authentication and
                    Advance Encryption Standard</p>
            </div>
            <div class="col-lg-6 vh-100 w-second-col">
                <div class="w-login-form otp-container">
                    <div class="my-4 text-center">
                        <img src="{{ asset('/assets/images/logo.png') }}" alt="welcom-logo">
                    </div>
                    <form action="{{ route('verify.otp.security') }}" method="POST">
                        @csrf
                        <div class="form-group mb-4">
                            <label for="security_answer" class="form-label text-center">
                                An One-Time Password (OTP) has been dispatched to : {{ $recipientNumber }}. Kindly verify
                                the number for OTP receipt. Thank you.
                            </label>
                            <div class="input-field">
                                <input type="number" name="otp[]" />
                                <input type="number" name="otp[]" disabled />
                                <input type="number" name="otp[]" disabled />
                                <input type="number" name="otp[]" disabled />
                                <input type="number" name="otp[]" disabled />
                                <input type="number" name="otp[]" disabled />
                            </div>
                        </div>
                        <div class="form-goup">
                            <button type="submit" class="btn tes-btn w-100">Verify</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        const inputs = document.querySelectorAll("input"),
            button = document.querySelector("button");
        inputs.forEach((input, index1) => {
            input.addEventListener("keyup", (e) => {
                const currentInput = input,
                    nextInput = input.nextElementSibling,
                    prevInput = input.previousElementSibling;
                if (currentInput.value.length > 1) {
                    currentInput.value = "";
                    return;
                }
                if (nextInput && nextInput.hasAttribute("disabled") && currentInput.value !== "") {
                    nextInput.removeAttribute("disabled");
                    nextInput.focus();
                }
                if (e.key === "Backspace") {
                    inputs.forEach((input, index2) => {
                        if (index1 <= index2 && prevInput) {
                            input.setAttribute("disabled", true);
                            input.value = "";
                            prevInput.focus();
                        }
                    });
                }

                if (!inputs[3].disabled && inputs[3].value !== "") {
                    button.classList.add("active");
                    return;
                }
                button.classList.remove("active");
            });
        });
        window.addEventListener("load", () => inputs[0].focus());
    </script>
@endsection
