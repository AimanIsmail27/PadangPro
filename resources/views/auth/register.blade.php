<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - PadangPro</title>
    @vite('resources/css/app.css')
</head>
<body class="min-h-screen flex items-center justify-center bg-gray-100">

    <div class="flex w-full max-w-5xl bg-white shadow-lg rounded-lg overflow-hidden">
        <!-- Left side (same as login) -->
        <div class="hidden md:flex flex-col justify-center items-center w-1/2 bg-gradient-to-br from-yellow-400 to-yellow-600 text-white p-10">
            <h1 class="text-4xl font-bold mb-4">Welcome to PadangPro</h1>
            <p class="text-lg">Book your football pitch with ease and connect with players around you.</p>
        </div>

        <!-- Right side (Registration Form) -->
        <div class="w-full md:w-1/2 p-8">
            <h2 class="text-2xl font-bold mb-6 text-gray-800">Create Your Account</h2>

            <!-- Validation Errors -->
            @if ($errors->any())
                <div class="bg-red-100 text-red-700 p-4 rounded mb-4">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Success/Error messages -->
            @if (session('success'))
                <div class="bg-green-100 text-green-700 p-4 rounded mb-4">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="bg-red-100 text-red-700 p-4 rounded mb-4">{{ session('error') }}</div>
            @endif

            <form action="{{ route('register.submit') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label for="user_Email" class="block text-gray-700">Email</label>
                    <input type="email" name="user_Email" id="user_Email" value="{{ old('user_Email') }}" class="w-full p-2 border rounded" required>
                </div>

                <div>
                    <label for="user_Password" class="block text-gray-700">Password</label>
                    <input type="password" name="user_Password" id="user_Password" class="w-full p-2 border rounded" required>
                </div>

                <div>
                    <label for="user_Password_confirmation" class="block text-gray-700">Confirm Password</label>
                    <input type="password" name="user_Password_confirmation" id="user_Password_confirmation" class="w-full p-2 border rounded" required>
                </div>

                <div>
                    <label for="customer_FullName" class="block text-gray-700">Full Name</label>
                    <input type="text" name="customer_FullName" id="customer_FullName" value="{{ old('customer_FullName') }}" class="w-full p-2 border rounded" required>
                </div>

                <div>
                    <label for="customer_Age" class="block text-gray-700">Age</label>
                    <input type="number" name="customer_Age" id="customer_Age" value="{{ old('customer_Age') }}" class="w-full p-2 border rounded" required>
                </div>

                <div>
                    <label for="customer_PhoneNumber" class="block text-gray-700">Phone Number</label>
                    <input type="text" name="customer_PhoneNumber" id="customer_PhoneNumber" value="{{ old('customer_PhoneNumber') }}" class="w-full p-2 border rounded" required>
                </div>

                <div>
                    <label for="customer_Address" class="block text-gray-700">Address</label>
                    <input type="text" name="customer_Address" id="customer_Address" value="{{ old('customer_Address') }}" class="w-full p-2 border rounded" required>
                </div>

                <div>
                    <label for="customer_Position" class="block text-gray-700">Position</label>
                    <input type="text" name="customer_Position" id="customer_Position" value="{{ old('customer_Position') }}" class="w-full p-2 border rounded" required>
                </div>

                <button type="submit" class="w-full bg-yellow-500 text-white py-2 rounded hover:bg-yellow-600 transition">
                    Register
                </button>
            </form>

            <p class="mt-4 text-center text-sm text-gray-600">
                Already have an account?
                <a href="{{ route('login') }}" class="text-yellow-600 hover:underline">Login here</a>
            </p>
        </div>
    </div>

</body>
</html>
