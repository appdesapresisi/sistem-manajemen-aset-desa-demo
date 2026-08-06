<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Core schema snapshot — satu-satunya migration yang membuat semua tabel inti.
     *
     * Semua kolom sudah lengkap (termasuk yang sebelumnya ditambahkan via ALTER).
     * Tidak ada migration ALTER terpisah yang dibutuhkan.
     *
     * Catatan:
     * - Ditujukan sebagai alternatif deploy via migration saat SQL dump tidak dipakai.
     * - Guard `if (!Schema::hasTable(...))` memastikan aman dijalankan di DB yang sudah ada.
     */
    public function up(): void
    {
        $driver = DB::getDriverName();
        if ($driver !== 'mysql') {
            throw new RuntimeException('Core schema snapshot migration saat ini hanya mendukung MySQL.');
        }

        $this->createOrAlignRoles();
        $this->createOrAlignBidangs();
        $this->createOrAlignDinas();
        $this->createOrAlignPejabats();
        $this->createOrAlignDropdowns();
        $this->createOrAlignDropdownItems();
        $this->createOrAlignJenisInventaris();
        $this->createOrAlignJenisInventarisData();
        $this->createOrAlignJenisInventarisPejabats();
        $this->createOrAlignUsers();
        $this->createOrAlignInventaris();
        $this->createOrAlignInventarisFotos();
        $this->createOrAlignInventarisValues();
        $this->createOrAlignExportLogs();
        $this->createOrAlignNotifications();
        $this->createOrAlignActivityLogs();
    }

    public function down(): void
    {
        // no-op — tidak melakukan drop untuk mencegah kehilangan data
    }

    // =========================================================================
    // ROLES
    // =========================================================================
    private function createOrAlignRoles(): void
    {
        if (!Schema::hasTable('roles')) {
            DB::unprepared(<<<'SQL'
CREATE TABLE `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tugas` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deskripsi` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }
    }

    // =========================================================================
    // BIDANGS
    // =========================================================================
    private function createOrAlignBidangs(): void
    {
        if (!Schema::hasTable('bidangs')) {
            DB::unprepared(<<<'SQL'
CREATE TABLE `bidangs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama` varchar(150) NOT NULL,
  `deskripsi` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nama` (`nama`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }
    }

    // =========================================================================
    // DINAS
    // =========================================================================
    private function createOrAlignDinas(): void
    {
        if (!Schema::hasTable('dinas')) {
            DB::unprepared(<<<'SQL'
CREATE TABLE `dinas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nama Dinas',
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }
    }

    // =========================================================================
    // PEJABATS (sudah termasuk: alamat, is_petugas_barang)
    // =========================================================================
    private function createOrAlignPejabats(): void
    {
        if (!Schema::hasTable('pejabats')) {
            DB::unprepared(<<<'SQL'
CREATE TABLE `pejabats` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jabatan` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tugas` text COLLATE utf8mb4_unicode_ci,
  `alamat` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ttd_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ttd_type` enum('gambar','elektronik') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'gambar',
  `is_aktif` tinyint(1) DEFAULT '1',
  `is_petugas_barang` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }
    }

    // =========================================================================
    // DROPDOWNS
    // =========================================================================
    private function createOrAlignDropdowns(): void
    {
        if (!Schema::hasTable('dropdowns')) {
            DB::unprepared(<<<'SQL'
CREATE TABLE `dropdowns` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deskripsi` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }
    }

    // =========================================================================
    // DROPDOWN ITEMS
    // =========================================================================
    private function createOrAlignDropdownItems(): void
    {
        if (!Schema::hasTable('dropdown_items')) {
            DB::unprepared(<<<'SQL'
CREATE TABLE `dropdown_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `dropdown_id` bigint unsigned NOT NULL,
  `label` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_di_dropdown` (`dropdown_id`),
  CONSTRAINT `fk_di_dropdown` FOREIGN KEY (`dropdown_id`) REFERENCES `dropdowns` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }
    }

    // =========================================================================
    // JENIS INVENTARIS (sudah termasuk: icon_mode, custom_icon, custom_color, requires_ruangan, kib_type)
    // =========================================================================
    private function createOrAlignJenisInventaris(): void
    {
        if (!Schema::hasTable('jenis_inventaris')) {
            DB::unprepared(<<<'SQL'
CREATE TABLE `jenis_inventaris` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deskripsi` text COLLATE utf8mb4_unicode_ci,
  `icon_mode` enum('auto','manual') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'auto',
  `custom_icon` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `custom_color` varchar(7) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `requires_ruangan` tinyint(1) NOT NULL DEFAULT '0',
  `kib_type` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }
    }

    // =========================================================================
    // JENIS INVENTARIS DATA (sudah termasuk: dropdown_id, include_in_export)
    // =========================================================================
    private function createOrAlignJenisInventarisData(): void
    {
        if (!Schema::hasTable('jenis_inventaris_data')) {
            DB::unprepared(<<<'SQL'
CREATE TABLE `jenis_inventaris_data` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `jenis_inventaris_id` bigint unsigned NOT NULL,
  `nama_field` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `label_field` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `field_type` enum('fixed','custom') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fixed_field_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tipe` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'string',
  `dropdown_id` bigint unsigned DEFAULT NULL,
  `is_required` tinyint(1) DEFAULT '0',
  `include_in_export` tinyint(1) NOT NULL DEFAULT '1',
  `order` int DEFAULT '1',
  `is_name_field` tinyint(1) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_jid_jenis` (`jenis_inventaris_id`),
  KEY `fk_jid_dropdown` (`dropdown_id`),
  CONSTRAINT `fk_jid_jenis` FOREIGN KEY (`jenis_inventaris_id`) REFERENCES `jenis_inventaris` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_jid_dropdown` FOREIGN KEY (`dropdown_id`) REFERENCES `dropdowns` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }
    }

    // =========================================================================
    // JENIS INVENTARIS PEJABATS
    // =========================================================================
    private function createOrAlignJenisInventarisPejabats(): void
    {
        if (!Schema::hasTable('jenis_inventaris_pejabats')) {
            DB::unprepared(<<<'SQL'
CREATE TABLE `jenis_inventaris_pejabats` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `jenis_inventaris_id` bigint unsigned NOT NULL,
  `pejabat_id` bigint unsigned NOT NULL,
  `order` int DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_jip_jenis` (`jenis_inventaris_id`),
  KEY `fk_jip_pejabat` (`pejabat_id`),
  CONSTRAINT `fk_jip_jenis` FOREIGN KEY (`jenis_inventaris_id`) REFERENCES `jenis_inventaris` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_jip_pejabat` FOREIGN KEY (`pejabat_id`) REFERENCES `pejabats` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }
    }

    // =========================================================================
    // USERS (sudah termasuk: dinas_id, alamat, unique username)
    // =========================================================================
    private function createOrAlignUsers(): void
    {
        if (!Schema::hasTable('users')) {
            DB::unprepared(<<<'SQL'
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  `bidang_id` bigint unsigned DEFAULT NULL,
  `dinas_id` bigint unsigned DEFAULT NULL,
  `pejabat_id` bigint unsigned DEFAULT NULL,
  `alamat` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_aktif` tinyint(1) DEFAULT '1',
  `last_login_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `users_username_unique` (`username`),
  KEY `fk_users_role` (`role_id`),
  KEY `fk_users_pejabat` (`pejabat_id`),
  KEY `fk_users_bidang_id` (`bidang_id`),
  KEY `fk_users_dinas_id` (`dinas_id`),
  CONSTRAINT `fk_users_bidang_id` FOREIGN KEY (`bidang_id`) REFERENCES `bidangs` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_users_dinas_id` FOREIGN KEY (`dinas_id`) REFERENCES `dinas` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_users_pejabat` FOREIGN KEY (`pejabat_id`) REFERENCES `pejabats` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_users_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }
    }

    // =========================================================================
    // INVENTARIS (sudah termasuk: satuan, ruangan_id)
    // =========================================================================
    private function createOrAlignInventaris(): void
    {
        if (!Schema::hasTable('inventaris')) {
            DB::unprepared(<<<'SQL'
CREATE TABLE `inventaris` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `jenis_inventaris_id` bigint unsigned NOT NULL,
  `nama_barang` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jenis_barang` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `satuan` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kode_barang` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nup` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tahun_perolehan` year DEFAULT NULL,
  `nilai_perolehan` decimal(15,2) DEFAULT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `asal_usul` enum('apbdes','perolehan_lain','kekayaan_asli') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pengguna` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ruangan_id` bigint unsigned DEFAULT NULL,
  `kondisi` enum('baik','rusak_ringan','rusak_berat') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('draft','pending','disetujui','ditolak') COLLATE utf8mb4_unicode_ci DEFAULT 'draft',
  `barcode` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` bigint unsigned NOT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_inv_jenis` (`jenis_inventaris_id`),
  KEY `fk_inv_created_by` (`created_by`),
  KEY `fk_inv_updated_by` (`updated_by`),
  KEY `fk_inv_ruangan` (`ruangan_id`),
  CONSTRAINT `fk_inv_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_inv_jenis` FOREIGN KEY (`jenis_inventaris_id`) REFERENCES `jenis_inventaris` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_inv_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_inv_ruangan` FOREIGN KEY (`ruangan_id`) REFERENCES `ruangans` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }
    }

    // =========================================================================
    // INVENTARIS FOTOS
    // =========================================================================
    private function createOrAlignInventarisFotos(): void
    {
        if (!Schema::hasTable('inventaris_fotos')) {
            DB::unprepared(<<<'SQL'
CREATE TABLE `inventaris_fotos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `inventaris_id` bigint unsigned NOT NULL,
  `path` varchar(255) NOT NULL,
  `urutan` tinyint DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `inventaris_id` (`inventaris_id`),
  CONSTRAINT `inventaris_fotos_ibfk_1` FOREIGN KEY (`inventaris_id`) REFERENCES `inventaris` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }
    }

    // =========================================================================
    // INVENTARIS VALUES
    // =========================================================================
    private function createOrAlignInventarisValues(): void
    {
        if (!Schema::hasTable('inventaris_values')) {
            DB::unprepared(<<<'SQL'
CREATE TABLE `inventaris_values` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `inventaris_id` bigint unsigned NOT NULL,
  `jenis_inventaris_data_id` bigint unsigned NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_iv_inventaris` (`inventaris_id`),
  KEY `fk_iv_field` (`jenis_inventaris_data_id`),
  CONSTRAINT `fk_iv_field` FOREIGN KEY (`jenis_inventaris_data_id`) REFERENCES `jenis_inventaris_data` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_iv_inventaris` FOREIGN KEY (`inventaris_id`) REFERENCES `inventaris` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }
    }

    // =========================================================================
    // EXPORT LOGS
    // =========================================================================
    private function createOrAlignExportLogs(): void
    {
        if (!Schema::hasTable('export_logs')) {
            DB::unprepared(<<<'SQL'
CREATE TABLE `export_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `inventaris_id` bigint unsigned DEFAULT NULL,
  `jenis_inventaris_id` bigint unsigned DEFAULT NULL,
  `pejabat_id` bigint unsigned DEFAULT NULL,
  `tipe_export` enum('pdf','excel') COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_file` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pejabat_override` json DEFAULT NULL,
  `filters` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_el_user` (`user_id`),
  KEY `fk_el_inventaris` (`inventaris_id`),
  KEY `fk_el_jenis` (`jenis_inventaris_id`),
  KEY `fk_el_pejabat` (`pejabat_id`),
  CONSTRAINT `fk_el_inventaris` FOREIGN KEY (`inventaris_id`) REFERENCES `inventaris` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_el_jenis` FOREIGN KEY (`jenis_inventaris_id`) REFERENCES `jenis_inventaris` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_el_pejabat` FOREIGN KEY (`pejabat_id`) REFERENCES `pejabats` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_el_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }
    }

    // =========================================================================
    // NOTIFICATIONS
    // =========================================================================
    private function createOrAlignNotifications(): void
    {
        if (!Schema::hasTable('notifications')) {
            DB::unprepared(<<<'SQL'
CREATE TABLE `notifications` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `type` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `body` text COLLATE utf8mb4_unicode_ci,
  `data` json DEFAULT NULL,
  `url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_user_id_index` (`user_id`),
  KEY `notifications_is_read_index` (`is_read`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }
    }

    // =========================================================================
    // ACTIVITY LOGS
    // =========================================================================
    private function createOrAlignActivityLogs(): void
    {
        if (!Schema::hasTable('activity_logs')) {
            DB::unprepared(<<<'SQL'
CREATE TABLE `activity_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `module` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `action` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `detail` text COLLATE utf8mb4_unicode_ci,
  `subject_id` bigint unsigned DEFAULT NULL,
  `subject_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        }
    }
};
