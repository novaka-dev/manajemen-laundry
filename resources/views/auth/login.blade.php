<!DOCTYPE html>
<html lang="id" class="h-full bg-blue-950">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login — LaundryKu</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased flex items-center justify-center p-4">

<div class="w-full max-w-sm">
    {{-- Logo & Judul --}}
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-14 h-14 bg-blue-500 rounded-2xl mb-4 shadow-lg">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>
            </svg>
        </div>
        <h1 class="text-2xl font-bold text-white">LaundryKu</h1>
        <p class="text-blue-300 text-sm mt-1">Sistem Manajemen Laundry</p>
    </div>

    {{-- Card Login --}}
    <div class="bg-white rounded-2xl shadow-2xl p-8">
        <h2 class="text-lg font-semibold text-slate-800 mb-6">Masuk ke akun Anda</h2>

        <form method="POST" action="{{ route('login.post') }}" class="space-y-5">
            @csrf

            {{-- Username --}}
            <div>
                <label for="username" class="block text-sm font-medium text-slate-700 mb-1.5">
                    Username
                </label>
                <input
                    type="text"
                    id="username"
                    name="username"
                    value="{{ old('username') }}"
                    autocomplete="username"
                    autofocus
                    required
                    class="w-full px-3.5 py-2.5 text-sm border rounded-lg outline-none transition-colors
                        {{ $errors->has('username') ? 'border-red-400 bg-red-50 text-red-900 focus:border-red-500 focus:ring-2 focus:ring-red-200' : 'border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-100' }}"
                    placeholder="Masukkan username"
                >
                @error('username')
                    <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Password --}}
            <div>
                <label for="password" class="block text-sm font-medium text-slate-700 mb-1.5">
                    Password
                </label>
                <div class="relative">
                    <input
                        type="password"
                        id="password"
                        name="password"
                        autocomplete="current-password"
                        required
                        class="w-full px-3.5 py-2.5 text-sm border rounded-lg outline-none transition-colors pr-10
                            {{ $errors->has('password') ? 'border-red-400 bg-red-50 focus:border-red-500 focus:ring-2 focus:ring-red-200' : 'border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-100' }}"
                        placeholder="Masukkan password"
                    >
                    {{-- Toggle show/hide password --}}
                    <button type="button"
                        onclick="togglePassword()"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                        <svg id="eye-icon" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                        </svg>
                    </button>
                </div>
                @error('password')
                    <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Submit Button --}}
            <button type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-semibold text-sm py-2.5 rounded-lg transition-colors mt-2">
                Masuk
            </button>
        </form>
    </div>

    <p class="text-center text-xs text-blue-400 mt-6">
        &copy; {{ date('Y') }} LaundryKu. Sistem Informasi Manajemen Laundry.
    </p>
</div>

<script>
function togglePassword() {
    const input = document.getElementById('password');
    input.type = input.type === 'password' ? 'text' : 'password';
}
</script>

</body>
</html>

