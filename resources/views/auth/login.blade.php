<x-guest-layout> 
    <x-auth-session-status class="mb-4" :status="session('status')" /> 
 
    <form method="POST" action="{{ route('login') }}" class="w-full max-w-sm"> 
        @csrf 
        <h1 class="font-bold text-4xl font-montserrat mb-6 text-bpn-brown">Login Sistem</h1> 
 
        {{-- Kotak Pesan Error dari Server (Backend) --}} 
        @if ($errors->has('email') || $errors->has('password')) 
            <div class="mb-4 font-medium text-sm text-red-700 bg-red-100 p-3 rounded-md w-full text
center"> 
                Email atau password yang Anda masukkan tidak sesuai. 
            </div> 
        @endif 
 
        {{-- Input Email (tanpa value old) --}} 
        <div> 
            <input id="email" class="auth-input" type="email" name="email" required autofocus 
placeholder="Email" /> 
        </div> 
 
        {{-- Input Password --}} 
        <div class="mt-2"> 
            <input id="password" class="auth-input" type="password" name="password" required 
autocomplete="current-password" placeholder="Password"/> 
        </div> 
 
        <div class="flex justify-between items-center mt-4 w-full text-sm"> 
            <label for="remember_me" class="inline-flex items-center"> 
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-bpn-brown 
shadow-sm focus:ring-bpn-brown" name="remember"> 
                <span class="ms-2 text-gray-600">{{ __('Remember me') }}</span> 
            </label> 
 
            @if (Route::has('password.request')) 
                <a class="underline text-gray-600 hover:text-gray-900" href="{{ route('password.request') }}"> 
                    {{ __('Forgot password?') }} 
                </a> 
            @endif 
        </div> 
 
        <div class="flex items-center justify-center mt-6"> 
            <button type="submit" class="auth-button" style="font-size: 0.875rem; padding: 0.75rem 
2.5rem;"> 
                {{ __('Log In') }} 
            </button> 
        </div> 
    </form> 
</x-guest-layout> 