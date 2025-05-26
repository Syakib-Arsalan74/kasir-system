<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EasyCashier - Manajemen Pengguna</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/alpinejs/3.10.2/cdn.min.js" defer></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        html,
        body {
            font-family: 'Inter', sans-serif;
            height: 100%;
            margin: 0;
            padding: 0;
            overflow: hidden;
        }

        .glass-effect {
            backdrop-filter: blur(8px);
            background: rgba(255, 255, 255, 0.85);
        }

        .animate-float {
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-20px);
            }

            100% {
                transform: translateY(0px);
            }
        }

        .gradient-border {
            position: relative;
            border-radius: 0.75rem;
        }

        .gradient-border::before {
            content: "";
            position: absolute;
            top: -2px;
            left: -2px;
            right: -2px;
            bottom: -2px;
            background: linear-gradient(120deg, #6366f1, #8b5cf6, #ec4899);
            border-radius: 0.85rem;
            z-index: -1;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .input-field:focus-within .gradient-border::before {
            opacity: 1;
        }

        .input-field:hover .gradient-border::before {
            opacity: 0.5;
        }

        .sidebar-link {
            transition: all 0.3s ease;
        }

        .sidebar-link:hover {
            background-color: rgba(99, 102, 241, 0.1);
        }

        .sidebar-link.active {
            background-color: rgba(99, 102, 241, 0.15);
            border-left: 3px solid #6366f1;
        }

        .card-stats {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card-stats:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px -5px rgba(99, 102, 241, 0.25);
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: rgba(243, 244, 246, 0.1);
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background-color: rgba(107, 114, 128, 0.3);
            border-radius: 20px;
        }

        main.content-area {
            max-height: calc(100vh - 138px);
            overflow-y: auto;
        }

        .blob {
            position: absolute;
            width: 500px;
            height: 500px;
            border-radius: 50%;
            filter: blur(80px);
            z-index: 0;
            pointer-events: none;
            opacity: 0.3;
        }

        .badge-active {
            background-color: rgba(16, 185, 129, 0.1);
            color: #10B981;
        }

        .badge-inactive {
            background-color: rgba(239, 68, 68, 0.1);
            color: #EF4444;
        }

        .badge-pending {
            background-color: rgba(245, 158, 11, 0.1);
            color: #F59E0B;
        }

        .modal-backdrop {
            background-color: rgba(17, 24, 39, 0.7);
            backdrop-filter: blur(3px);
        }
    </style>
</head>

<body class="bg-gray-50" x-data="{
    sidebarOpen: true,
    activeTab: 'users',
    notifications: 5,
    showDropdown: false,
    showAddUserModal: false,
    showEditUserModal: false,
    showDeleteConfirmModal: false,
    showResetPasswordModal: false,
    currentUser: null,
    searchQuery: '',
    filterRole: 'all',
    filterStatus: 'all',
    users: [
        { id: 1, name: 'Admin EasyCashier', email: 'admin@easycashier.com', role: 'admin', status: 'active', lastLogin: '08 Mei 2025, 08:42', image: null },
        { id: 2, name: 'Budi Santoso', email: 'budi@easycashier.com', role: 'kasir', status: 'active', lastLogin: '08 Mei 2025, 07:15', image: null },
        { id: 3, name: 'Dewi Anggraini', email: 'dewi@easycashier.com', role: 'kasir', status: 'active', lastLogin: '07 Mei 2025, 16:30', image: null },
        { id: 4, name: 'Hendra Wijaya', email: 'hendra@easycashier.com', role: 'kasir', status: 'inactive', lastLogin: '01 Mei 2025, 09:20', image: null },
        { id: 5, name: 'Siti Nurhaliza', email: 'siti@easycashier.com', role: 'kasir', status: 'active', lastLogin: '06 Mei 2025, 14:05', image: null },
        { id: 6, name: 'Bambang Hermawan', email: 'bambang@easycashier.com', role: 'kasir', status: 'pending', lastLogin: 'Belum pernah', image: null },
        { id: 7, name: 'Ratna Dewi', email: 'ratna@easycashier.com', role: 'admin', status: 'active', lastLogin: '05 Mei 2025, 11:52', image: null },
        { id: 8, name: 'Agus Supriyanto', email: 'agus@easycashier.com', role: 'kasir', status: 'inactive', lastLogin: '27 Apr 2025, 15:30', image: null }
    ],
    roles: ['admin', 'kasir'],
    statuses: ['active', 'inactive', 'pending'],

    // Methods
    editUser(user) {
        this.currentUser = { ...user };
        this.showEditUserModal = true;
    },

    prepareDelete(user) {
        this.currentUser = user;
        this.showDeleteConfirmModal = true;
    },

    resetPassword(user) {
        this.currentUser = user;
        this.showResetPasswordModal = true;
    },

    toggleUserStatus(user) {
        const index = this.users.findIndex(u => u.id === user.id);
        if (index !== -1) {
            this.users[index].status = user.status === 'active' ? 'inactive' : 'active';
        }
    },

    addNewUser() {
        this.currentUser = { id: this.users.length + 1, name: '', email: '', role: 'kasir', status: 'active', lastLogin: 'Belum pernah', image: null };
        this.showAddUserModal = true;
    },

    saveNewUser() {
        this.users.push({ ...this.currentUser });
        this.showAddUserModal = false;
    },

    saveEditUser() {
        const index = this.users.findIndex(u => u.id === this.currentUser.id);
        if (index !== -1) {
            this.users[index] = { ...this.currentUser };
        }
        this.showEditUserModal = false;
    },

    deleteUser() {
        this.users = this.users.filter(u => u.id !== this.currentUser.id);
        this.showDeleteConfirmModal = false;
    },

    confirmResetPassword() {
        // Simulasi reset password
        this.showResetPasswordModal = false;
    },

    getInitials(name) {
        if (!name) return '';
        return name.split(' ').map(n => n[0]).join('').toUpperCase();
    },

    filteredUsers() {
        return this.users.filter(user => {
            // Filter by search query
            const matchesSearch = this.searchQuery === '' ||
                user.name.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                user.email.toLowerCase().includes(this.searchQuery.toLowerCase());

            // Filter by role
            const matchesRole = this.filterRole === 'all' || user.role === this.filterRole;

            // Filter by status
            const matchesStatus = this.filterStatus === 'all' || user.status === this.filterStatus;

            return matchesSearch && matchesRole && matchesStatus;
        });
    }
}">
    <!-- Background blobs -->
    <div class="blob bg-blue-300/40 -top-96 -left-32 fixed"></div>
    <div class="blob bg-indigo-300/40 bottom-0 right-0 fixed"></div>
    <div class="blob bg-purple-300/30 top-1/2 left-1/3 fixed"></div>

    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <div :class="{ 'w-64': sidebarOpen, 'w-20': !sidebarOpen }"
            class="bg-white shadow-lg relative z-10 transition-all duration-300 ease-in-out flex flex-col">
            <!-- Sidebar Header -->
            <div class="flex items-center justify-between px-4 py-5 border-b border-gray-100">
                <div class="flex items-center space-x-3" :class="{ 'justify-center w-full': !sidebarOpen }">
                    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-2 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h1 class="text-xl font-bold text-gray-800 transition-opacity duration-300"
                        :class="{ 'opacity-0 w-0 h-0 overflow-hidden': !sidebarOpen }">EasyCashier</h1>
                </div>
                <button @click="sidebarOpen = !sidebarOpen"
                    class="text-gray-500 hover:text-indigo-600 focus:outline-none transition-opacity duration-300"
                    :class="{ 'opacity-0 w-0 h-0 overflow-hidden': !sidebarOpen }">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                    </svg>
                </button>
            </div>

            <!-- Sidebar Links -->
            <div class="flex-1 overflow-y-auto custom-scrollbar py-4">
                <nav>
                    <!-- Dashboard -->
                    <a href="#" @click.prevent="activeTab = 'dashboard'"
                        class="sidebar-link flex items-center px-4 py-3 text-gray-700 hover:text-indigo-600"
                        :class="{ 'active': activeTab === 'dashboard', 'justify-center': !sidebarOpen }">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" :class="{ 'mr-3': sidebarOpen }"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        <span class="font-medium transition-opacity duration-300"
                            :class="{ 'opacity-0 w-0 overflow-hidden': !sidebarOpen }">Dashboard</span>
                    </a>

                    <!-- Transaksi -->
                    <a href="#" @click.prevent="activeTab = 'transactions'"
                        class="sidebar-link flex items-center px-4 py-3 text-gray-700 hover:text-indigo-600"
                        :class="{ 'active': activeTab === 'transactions', 'justify-center': !sidebarOpen }">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" :class="{ 'mr-3': sidebarOpen }"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="font-medium transition-opacity duration-300"
                            :class="{ 'opacity-0 w-0 overflow-hidden': !sidebarOpen }">Transaksi</span>
                    </a>

                    <!-- Produk -->
                    <a href="#" @click.prevent="activeTab = 'products'"
                        class="sidebar-link flex items-center px-4 py-3 text-gray-700 hover:text-indigo-600"
                        :class="{ 'active': activeTab === 'products', 'justify-center': !sidebarOpen }">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" :class="{ 'mr-3': sidebarOpen }"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        <span class="font-medium transition-opacity duration-300"
                            :class="{ 'opacity-0 w-0 overflow-hidden': !sidebarOpen }">Produk</span>
                    </a>

                    <!-- Kategori -->
                    <a href="#" @click.prevent="activeTab = 'categories'"
                        class="sidebar-link flex items-center px-4 py-3 text-gray-700 hover:text-indigo-600"
                        :class="{ 'active': activeTab === 'categories', 'justify-center': !sidebarOpen }">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" :class="{ 'mr-3': sidebarOpen }"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        <span class="font-medium transition-opacity duration-300"
                            :class="{ 'opacity-0 w-0 overflow-hidden': !sidebarOpen }">Kategori</span>
                    </a>

                    <!-- Pelanggan -->
                    <a href="#" @click.prevent="activeTab = 'customers'"
                        class="sidebar-link flex items-center px-4 py-3 text-gray-700 hover:text-indigo-600"
                        :class="{ 'active': activeTab === 'customers', 'justify-center': !sidebarOpen }">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" :class="{ 'mr-3': sidebarOpen }"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <span class="font-medium transition-opacity duration-300"
                            :class="{ 'opacity-0 w-0 overflow-hidden': !sidebarOpen }">Pelanggan</span>
                    </a>

                    <!-- Divider -->
                    <div class="py-2 px-4" :class="{ 'px-0': !sidebarOpen }">
                        <div class="border-t border-gray-200"></div>
                    </div>

                    <!-- Pengaturan -->
                    <a href="#" @click.prevent="activeTab = 'settings'"
                        class="sidebar-link flex items-center px-4 py-3 text-gray-700 hover:text-indigo-600"
                        :class="{ 'active': activeTab === 'settings', 'justify-center': !sidebarOpen }">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" :class="{ 'mr-3': sidebarOpen }"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span class="font-medium transition-opacity duration-300"
                            :class="{ 'opacity-0 w-0 overflow-hidden': !sidebarOpen }">Pengaturan</span>
                    </a>

                    <!-- Pengguna - Active Link -->
                    <a href="#" @click.prevent="activeTab = 'users'"
                        class="sidebar-link flex items-center px-4 py-3 text-indigo-600 bg-indigo-50 active"
                        :class="{ 'active': activeTab === 'users', 'justify-center': !sidebarOpen }">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" :class="{ 'mr-3': sidebarOpen }"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <span class="font-medium transition-opacity duration-300"
                            :class="{ 'opacity-0 w-0 overflow-hidden': !sidebarOpen }">Pengguna</span>
                    </a>
                </nav>
            </div>

            <!-- Sidebar Footer -->
            <div class="border-t border-gray-200 p-4">
                <a href="#" class="sidebar-link flex items-center text-gray-700 hover:text-red-600"
                    :class="{ 'justify-center': !sidebarOpen }">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" :class="{ 'mr-3': sidebarOpen }"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    <span class="font-medium transition-opacity duration-300"
                        :class="{ 'opacity-0 w-0 overflow-hidden': !sidebarOpen }">Keluar</span>
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Header -->
            <header class="bg-white shadow-sm z-10">
                <div class="px-6 py-4 flex items-center justify-between">
                    <!-- Left Side - Toggle and Page Title -->
                    <div class="flex items-center space-x-4">
                        <button @click="sidebarOpen = !sidebarOpen"
                            class="text-gray-500 hover:text-indigo-600 focus:outline-none"
                            :class="{ 'rotate-180': !sidebarOpen }">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h7" />
                            </svg>
                        </button>
                        <h2 class="text-xl font-semibold text-gray-800">Manajemen Pengguna</h2>
                    </div>

                    <!-- Right Side - Search and User Profile -->
                    <div class="flex items-center space-x-4">
                        <!-- Search Bar -->
                        <div class="input-field relative w-64 hidden md:block">
                            <div class="gradient-border">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                                <input type="text" x-model="searchQuery" placeholder="Cari pengguna..."
                                    class="bg-gray-100 border-transparent focus:border-transparent focus:ring-0 focus:outline-none block w-full pl-10 pr-3 py-2 rounded-lg text-sm">
                            </div>
                        </div>

                        <!-- Notifications -->
                        <div class="relative">
                            <button class="text-gray-500 hover:text-indigo-600 focus:outline-none relative">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                                <span
                                    class="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center"
                                    x-show="notifications > 0" x-text="notifications">5</span>
                            </button>
                        </div>

                        <!-- User Profile -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center space-x-3 focus:outline-none">
                                <div
                                    class="w-8 h-8 rounded-full bg-gradient-to-r from-indigo-600 to-purple-600 flex items-center justify-center text-white font-semibold">
                                    A
                                </div>
                                <div class="hidden md:block">
                                    <div class="font-medium text-sm text-gray-700">Admin EasyCashier</div>
                                    <div class="text-xs text-gray-500">admin@easycashier.com</div>
                                </div>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400"
                                    viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>

                            <!-- Dropdown Menu -->
                            <div x-show="open" @click.away="open = false"
                                class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-2 z-20">
                                <a href="#"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-600">
                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 text-gray-400"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        Profil Saya
                                    </div>
                                </a>
                                <a href="#"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-600">
                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 text-gray-400"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        Pengaturan
                                    </div>
                                </a>
                                <div class="border-t border-gray-100 my-1"></div>
                                <a href="#" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 text-red-500"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                        </svg>
                                        Keluar
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </header>
            <!-- Main Content Area -->
            <main class="flex-1 overflow-y-auto bg-gray-50 p-6 custom-scrollbar content-area">
                <!-- User Management Content -->
                <div class="space-y-6">
                    <!-- Action Buttons & Filters -->
                    <div class="flex flex-col md:flex-row md:justify-between md:items-center space-y-4 md:space-y-0">
                        <div>
                            <button @click="addNewUser()"
                                class="px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white rounded-lg shadow-md shadow-indigo-200 flex items-center transition duration-300">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                Tambah Pengguna
                            </button>
                        </div>
                        <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-2">
                            <div class="relative">
                                <select x-model="filterRole"
                                    class="bg-white border border-gray-200 text-gray-700 py-2 pl-3 pr-10 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 appearance-none text-sm">
                                    <option value="all">Semua Role</option>
                                    <template x-for="role in roles" :key="role">
                                        <option x-text="role === 'admin' ? 'Admin' : 'Kasir'" :value="role">
                                        </option>
                                    </template>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="relative">
                                <select x-model="filterStatus"
                                    class="bg-white border border-gray-200 text-gray-700 py-2 pl-3 pr-10 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 appearance-none text-sm">
                                    <option value="all">Semua Status</option>
                                    <option value="active">Aktif</option>
                                    <option value="inactive">Tidak Aktif</option>
                                    <option value="pending">Menunggu</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Users Table Card -->
                    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                        <div class="p-6 border-b border-gray-100">
                            <div class="flex items-center justify-between">
                                <h3 class="font-semibold text-gray-800">Daftar Pengguna</h3>
                                <span class="text-sm text-gray-500"
                                    x-text="'Total: ' + filteredUsers().length + ' pengguna'">Total: 8 pengguna</span>
                            </div>
                        </div>

                        <!-- Table -->
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="bg-gray-50">
                                        <th
                                            class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Full Name</th>
                                        <th
                                            class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Username</th>
                                        <th
                                            class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Role</th>
                                        <th
                                            class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status</th>
                                        <th
                                            class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach ($users as $data)
                                        <tr class="hover:bg-gray-50 transition duration-150">
                                            <td class="py-4 px-6">
                                                <div class="flex items-center">
                                                    <div class="w-10 h-10 rounded-full bg-gradient-to-r from-indigo-600 to-purple-600 flex items-center justify-center text-white font-semibold"
                                                        x-text="getInitials($data->nama)">
                                                        {{ collect(explode(' ', $data->nama))->map(fn($word) => strtoupper(mb_substr($word, 0, 1)))->implode('') }}
                                                    </div>
                                                    <div class="ml-4">
                                                        <div class="font-medium text-gray-800">
                                                            {{ $data->nama }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="py-4 px-6 text-sm text-gray-600">
                                                {{ $data->username }}
                                            </td>
                                            <td class="py-4 px-6">
                                                <span
                                                    class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                                    {{ $data->role == 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                                    {{ $data->role }}
                                                </span>
                                            </td>
                                            <td class="py-4 px-6">
                                                <span
                                                    class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                                        {{ $data->isActive == 1 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                    @if ($data->isActive == 1)
                                                        Aktif
                                                    @else
                                                        Non Aktif
                                                    @endif
                                                </span>
                                            </td>
                                            <td class="py-4 px-6">
                                                <div class="flex space-x-2">
                                                    <button @click="editUser({{ json_encode($data) }})"
                                                        class="text-indigo-600 hover:text-indigo-900" title="Edit">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                        </svg>
                                                    </button>

                                                    <button @click="toggleUserStatus(user)"
                                                        class="text-yellow-600 hover:text-yellow-900"
                                                        title="Toggle Status">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z" />
                                                        </svg>
                                                    </button>
                                                    <button @click="resetPassword(user)"
                                                        class="text-green-600 hover:text-green-900"
                                                        title="Reset Password">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                                        </svg>
                                                    </button>
                                                    <button @click="prepareDelete({{ $data->id }})"
                                                        class="text-red-600 hover:text-red-900" title="Hapus">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>

                                                    <!-- Delete Confirmation Modal -->
                                                    <div x-show="showDeleteConfirmModal"
                                                        class="fixed inset-0 flex items-center justify-center z-50 modal-backdrop"
                                                        x-transition:enter="transition ease-out duration-300"
                                                        x-transition:enter-start="opacity-0"
                                                        x-transition:enter-end="opacity-100"
                                                        x-transition:leave="transition ease-in duration-200"
                                                        x-transition:leave-start="opacity-100"
                                                        x-transition:leave-end="opacity-0">
                                                        <div @click.away="showDeleteConfirmModal = false"
                                                            class="bg-white rounded-xl shadow-xl max-w-md w-full p-6 mx-4"
                                                            x-transition:enter="transition ease-out duration-300"
                                                            x-transition:enter-start="transform scale-95 opacity-0"
                                                            x-transition:enter-end="transform scale-100 opacity-100"
                                                            x-transition:leave="transition ease-in duration-200"
                                                            x-transition:leave-start="transform scale-100 opacity-100"
                                                            x-transition:leave-end="transform scale-95 opacity-0">
                                                            <div class="text-center">
                                                                <div
                                                                    class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-6">
                                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                                        class="h-8 w-8 text-red-600" fill="none"
                                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                                        <path stroke-linecap="round"
                                                                            stroke-linejoin="round" stroke-width="2"
                                                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                                    </svg>
                                                                </div>
                                                                <h3 class="text-lg font-semibold text-gray-800 mb-2">
                                                                    Konfirmasi Hapus Pengguna</h3>
                                                                <p class="text-gray-600 mb-6">
                                                                    Apakah Anda yakin ingin menghapus pengguna <span
                                                                        class="font-semibold"
                                                                        x-text="currentUser ? currentUser.name : ''"></span>?
                                                                    Tindakan ini tidak dapat dibatalkan.
                                                                </p>
                                                                <div class="flex space-x-3">
                                                                    <button
                                                                        class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition duration-200">
                                                                        Batal
                                                                    </button>
                                                                    <form class="flex-1"
                                                                        action="{{ route('user.destroy', $data->id) }}"
                                                                        method="POST">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button
                                                                            class="bg-red-600 w-full py-2 text-white rounded-lg hover:bg-red-700 transition duration-200">
                                                                            Hapus
                                                                        </button>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Empty Table State -->
                        <div class="p-8 text-center" x-show="filteredUsers().length === 0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <p class="mt-2 text-sm text-gray-500">Tidak ada pengguna yang sesuai dengan filter Anda.
                            </p>
                            <button @click="searchQuery = ''; filterRole = 'all'; filterStatus = 'all'"
                                class="mt-4 px-4 py-2 text-sm text-indigo-700 bg-indigo-50 hover:bg-indigo-100 rounded-md transition duration-200">
                                Reset Filter
                            </button>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <div class="flex items-center justify-between">
                        <p class="text-sm text-gray-500">Menampilkan 1-8 dari 8 pengguna</p>
                        <div class="flex space-x-1">
                            <button
                                class="px-3 py-1 text-sm bg-white text-gray-600 border border-gray-200 rounded-md disabled:opacity-50"
                                disabled>
                                &laquo; Sebelumnya
                            </button>
                            <button class="px-3 py-1 text-sm bg-indigo-600 text-white rounded-md">1</button>
                            <button
                                class="px-3 py-1 text-sm bg-white text-gray-600 border border-gray-200 rounded-md disabled:opacity-50"
                                disabled>
                                Selanjutnya &raquo;
                            </button>
                        </div>
                    </div>
                </div>
            </main>

            <!-- Footer -->
            <footer class="bg-white border-t border-gray-200 p-4 text-center text-gray-500 text-xs">
                &copy; 2025 EasyCashier. Hak cipta dilindungi. Versi 1.0.0
            </footer>
        </div>
    </div>

    <!-- Add User Modal -->
    <div x-show="showAddUserModal" class="fixed inset-0 flex items-center justify-center z-50 modal-backdrop"
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div @click.away="showAddUserModal = false" class="bg-white rounded-xl shadow-xl max-w-md w-full p-6 mx-4"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="transform scale-95 opacity-0"
            x-transition:enter-end="transform scale-100 opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="transform scale-100 opacity-100"
            x-transition:leave-end="transform scale-95 opacity-0">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-semibold text-gray-800">Tambah Pengguna Baru</h3>
                <button @click="showAddUserModal = false" class="text-gray-500 hover:text-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form action="{{ route('user.store') }}" method="POST">
                @csrf
                <d class="space-y-4">
                    <!-- Name Field -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama
                            Lengkap</label>
                        <input x-model="currentUser.name" type="text" id="nama" name="nama"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                            placeholder="Masukkan nama lengkap" required>
                    </div>

                    <!-- Username Field -->
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                        <input x-model="currentUser.username" type="username" id="username" name="username"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                            placeholder="Masukkan Username" required>
                    </div>

                    <!-- Password Field -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <input type="password" id="password" name="password"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                            placeholder="Masukkan password" required>
                    </div>

                    <!-- Role Field -->
                    <div>
                        <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                        <div class="relative">
                            <select x-model="currentUser.role" id="role" name="role"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent appearance-none bg-white"
                                required>
                                <option value="" disabled>Pilih role</option>
                                <option value="admin">Admin</option>
                                <option value="kasir">Kasir</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Status Field -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <div class="relative">
                            <select x-model="currentUser.status" id="status" name="isActive"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent appearance-none bg-white"
                                required>
                                <option value="" disabled>Pilih status</option>
                                <option value="1">Aktif</option>
                                <option value="0">Tidak Aktif</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Last Login Info (Read-only) -->
                    {{-- <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Login Terakhir</label>
                        <div class="w-full border border-gray-200 bg-gray-50 rounded-lg px-4 py-2 text-gray-500 text-sm"
                            x-text="currentUser.lastLogin">-</div>
                    </div> --}}

                    <div class="flex space-x-3 mt-6">
                        <button type="button" @click="showAddUserModal = false"
                            class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition duration-200">
                            Batal
                        </button>
                        <button type="submit"
                            class="flex-1 px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-lg hover:from-indigo-700 hover:to-purple-700 transition duration-200">
                            Simpan
                        </button>
                    </div>
            </form>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div x-show="showEditUserModal" class="fixed inset-0 flex items-center justify-center z-50 modal-backdrop"
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div @click.away="showEditUserModal = false" class="bg-white rounded-xl shadow-xl max-w-md w-full p-6 mx-4"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="transform scale-95 opacity-0"
            x-transition:enter-end="transform scale-100 opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="transform scale-100 opacity-100"
            x-transition:leave-end="transform scale-95 opacity-0">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-semibold text-gray-800">Edit
                    Pengguna</h3>
                <button @click="showEditUserModal = false" class="text-gray-500 hover:text-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form :action="`{{ route('user.update', ':userId') }}`.replace(':userId', user.id)" method="POST">
                @csrf
                @method('put')
                <div class="space-y-4">
                    <!-- Name Field -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama
                            Lengkap</label>
                        <input x-model="currentUser.nama" type="text" id="nama" name="nama"
                            :value="user.nama"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                            placeholder="Masukkan nama lengkap" required>
                    </div>

                    <!-- Username Field -->
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                        <input x-model="currentUser.username" type="username" id="username" name="username"
                            :value="user.username"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                            placeholder="Masukkan Username" required>
                    </div>

                    <!-- Password Field -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <input type="password" id="password" name="password"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                            placeholder="Masukkan password">
                    </div>

                    <!-- Role Field -->
                    <div>
                        <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                        <div class="relative">
                            <select x-model="currentUser.role" name="role" id="role"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent appearance-none bg-white"
                                required>
                                <template x-for="role in roles" :key="role">
                                    <option :value="role" x-text="role"
                                        :selected="role === currentUser.role"></option>
                                </template>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Status Field -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <div class="relative">
                            <select x-model="currentUser.isActive" name="isActive" id="isActive"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent appearance-none bg-white"
                                required>
                                <option value="1" :selected="currentUser.isActive == 1">Aktif</option>
                                <option value="0" :selected="currentUser.isActive == 0">Tidak Aktif</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="flex space-x-3 mt-6">
                        <button type="button" @click="showAddUserModal = false"
                            class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition duration-200">
                            Batal
                        </button>
                        <button type="submit"
                            class="flex-1 px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-lg hover:from-indigo-700 hover:to-purple-700 transition duration-200">
                            Simpan Perubahan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>


    <!-- Reset Password Modal -->
    <div x-show="showResetPasswordModal" class="fixed inset-0 flex items-center justify-center z-50 modal-backdrop"
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div @click.away="showResetPasswordModal = false"
            class="bg-white rounded-xl shadow-xl max-w-md w-full p-6 mx-4"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="transform scale-95 opacity-0"
            x-transition:enter-end="transform scale-100 opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="transform scale-100 opacity-100"
            x-transition:leave-end="transform scale-95 opacity-0">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-semibold text-gray-800">Reset Password</h3>
                <button @click="showResetPasswordModal = false" class="text-gray-500 hover:text-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form @submit.prevent="confirmResetPassword()">
                <div class="space-y-4">
                    <!-- User Info -->
                    <div class="flex items-center p-4 bg-gray-50 rounded-lg">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-r from-indigo-600 to-purple-600 flex items-center justify-center text-white font-semibold mr-3"
                            x-text="currentUser ? getInitials(currentUser.name) : ''">AS</div>
                        <div>
                            <p class="font-medium text-gray-800" x-text="currentUser ? currentUser.name : ''">User
                                Name</p>
                            <p class="text-sm text-gray-500" x-text="currentUser ? currentUser.email : ''">
                                user@email.com</p>
                        </div>
                    </div>

                    <!-- New Password Field -->
                    <div>
                        <label for="new-password" class="block text-sm font-medium text-gray-700 mb-1">Password
                            Baru</label>
                        <input type="password" id="new-password"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                            placeholder="Masukkan password baru" required>
                    </div>

                    <!-- Confirm Password Field -->
                    <div>
                        <label for="confirm-password" class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi
                            Password</label>
                        <input type="password" id="confirm-password"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                            placeholder="Konfirmasi password baru" required>
                    </div>

                    <!-- Send Email Notification Option -->
                    <div class="flex items-center">
                        <input type="checkbox" id="send-notification"
                            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                        <label for="send-notification" class="ml-2 block text-sm text-gray-700">
                            Kirim notifikasi ke email pengguna
                        </label>
                    </div>
                </div>

                <div class="flex space-x-3 mt-6">
                    <button type="button" @click="showResetPasswordModal = false"
                        class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition duration-200">
                        Batal
                    </button>
                    <button type="submit"
                        class="flex-1 px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-lg hover:from-indigo-700 hover:to-purple-700 transition duration-200">
                        Reset Password
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Help Guide Tooltip - Hidden by default -->
    <div class="fixed bottom-6 right-6 z-30">
        <button
            class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-full w-12 h-12 flex items-center justify-center shadow-lg hover:from-indigo-700 hover:to-purple-700 transition duration-300">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </button>
    </div>

    <!-- Quick Access Floating Action Button -->
    <div class="fixed bottom-6 left-6 z-30" x-data="{ showQuickActions: false }">
        <div x-show="showQuickActions" @click.away="showQuickActions = false"
            class="absolute bottom-16 left-0 mb-2 bg-white rounded-lg shadow-xl p-3 w-48">
            <div class="space-y-2">
                <button @click="addNewUser(); showQuickActions = false"
                    class="w-full flex items-center p-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 rounded-lg transition duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-gray-500" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                    Tambah Pengguna
                </button>
                <button
                    class="w-full flex items-center p-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 rounded-lg transition duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-gray-500" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Export ke Excel
                </button>
                <button
                    class="w-full flex items-center p-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 rounded-lg transition duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-gray-500" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2" />
                    </svg>
                    Print Daftar
                </button>
            </div>
        </div>
        <button @click="showQuickActions = !showQuickActions"
            class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-full w-12 h-12 flex items-center justify-center shadow-lg hover:from-indigo-700 hover:to-purple-700 transition duration-300">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
        </button>
    </div>
</body>

</html>
