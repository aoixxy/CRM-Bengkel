<?php

$current_page = $current_page ?? '';

$menu = [
    'dashboard' => [
        'title' => 'Dashboard',
        'icon'  => '🏠',
        'link'  => 'dashboard.php',
        'color' => 'blue'
    ],

    'pelanggan' => [
        'title' => 'Pelanggan',
        'icon'  => '👥',
        'link'  => 'pelanggan.php',
        'color' => 'cyan'
    ],

    'inventory' => [
        'title' => 'Inventory',
        'icon'  => '📦',
        'link'  => 'inventory.php',
        'color' => 'orange'
    ],

    'transaksi' => [
        'title' => 'Transaksi',
        'icon'  => '💳',
        'link'  => 'transaksi.php',
        'color' => 'green'
    ],

    'laporan' => [
        'title' => 'Laporan',
        'icon'  => '📊',
        'link'  => 'laporan.php',
        'color' => 'purple'
    ]
];

$activeGradient = [
    'blue'   => 'from-blue-600 to-cyan-500',
    'cyan'   => 'from-cyan-500 to-sky-500',
    'orange' => 'from-orange-500 to-yellow-400',
    'green'  => 'from-green-600 to-emerald-500',
    'purple' => 'from-purple-600 to-pink-500'
];
?>

<div class="w-72 bg-white/80 backdrop-blur-xl border-r border-white/30 shadow-2xl p-6 flex flex-col justify-between min-h-screen">

    <div>

        <!-- LOGO -->
        <div class="mb-10">

            <div class="flex items-center gap-4">

                <div class="w-14 h-14 rounded-2xl
                bg-gradient-to-br from-blue-600 to-cyan-400
                flex items-center justify-center
                text-white text-2xl shadow-lg">

                    🏍️

                </div>

                <div>

                    <h1 class="text-xl font-black text-gray-800">
                        SITANGGANG
                    </h1>

                    <p class="text-sm text-gray-500">
                        CRM Bengkel
                    </p>

                </div>

            </div>

        </div>

        <!-- MENU -->
        <ul class="space-y-3">

            <?php foreach($menu as $key => $item): ?>

                <?php if($current_page == $key): ?>

                    <li>

                        <a href="<?= $item['link']; ?>"
                        class="group flex items-center justify-between
                        bg-gradient-to-r <?= $activeGradient[$item['color']]; ?>
                        text-white px-4 py-4 rounded-2xl shadow-lg">

                            <div class="flex items-center gap-3">

                                <span class="text-xl">
                                    <?= $item['icon']; ?>
                                </span>

                                <span class="font-semibold">
                                    <?= $item['title']; ?>
                                </span>

                            </div>

                            <span class="opacity-70">
                                →
                            </span>

                        </a>

                    </li>

                <?php else: ?>

                    <li>

                        <a href="<?= $item['link']; ?>"
                        class="group flex items-center justify-between
                        hover:bg-gray-50 px-4 py-4 rounded-2xl transition">

                            <div class="flex items-center gap-3">

                                <span class="text-xl">
                                    <?= $item['icon']; ?>
                                </span>

                                <span class="font-medium text-gray-700">
                                    <?= $item['title']; ?>
                                </span>

                            </div>

                            <span class="opacity-0 group-hover:opacity-100">
                                →
                            </span>

                        </a>

                    </li>

                <?php endif; ?>

            <?php endforeach; ?>

        </ul>

    </div>

    <!-- LOGOUT -->
    <div>

        <div class="bg-gradient-to-r from-red-500 to-pink-500 p-[1px] rounded-2xl">

            <a href="../auth/logout.php"
            class="w-full bg-white hover:bg-red-50
            text-red-600 px-4 py-4 rounded-2xl
            flex items-center justify-between transition">

                <div class="flex items-center gap-3">

                    <span class="text-xl">🚪</span>

                    <span class="font-semibold">
                        Logout
                    </span>

                </div>

                <span>→</span>

            </a>

        </div>

    </div>

</div>