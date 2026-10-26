# Standar Tombol Aksi Sistem (Pill-Rounded Buttons)

## Aturan Utama
1. **Semua Tombol Aksi WAJIB Berbentuk Pill Rounded (`border-radius: var(--radius-full)` / `9999px`)**:
   - Seluruh tombol aksi pada sistem (Primary, Secondary, Danger/Hapus, Warning, Success, Submit Form, Cancel/Batal, Modal Konfirmasi/Batal, Floating Bulk Action Bar) **WAJIB berbentuk pill / kapsul simetris**.
   - **DILARANG KERAS** membuat tombol aksi berbentuk **KOTAK** dengan sudut tajam atau sudut lengkung kecil kaku (seperti `border-radius: 4px`, `6px`, `8px`, `10px`, `12px`, `var(--radius-sm)`, atau `var(--radius-md)`).
   - Dilarang menampilkan tombol pill kapsul (seperti *Tandai Semua Dibaca*) berdampingan dengan tombol kotak (seperti *Hapus Sudah Dibaca*).

## Klasifikasi Kelas Tombol Standar
- **Primary**: `.btn-sima-primary`, `.btn-primary`, `.btn-submit`
  - Background: `var(--color-primary)`
  - Border: `1px solid var(--color-primary)`
  - Color: `var(--color-text-on-primary)` (#fff / #f8faf7)
  - Radius: `border-radius: var(--radius-full) !important;`
- **Secondary**: `.btn-sima-secondary`, `.btn-secondary`, `.btn-cancel`
  - Background: `transparent` (atau `var(--color-surface)`)
  - Border: `1px solid var(--color-border)`
  - Color: `var(--color-text)`
  - Radius: `border-radius: var(--radius-full) !important;`
- **Danger / Destructive**: `.btn-sima-danger`, `.btn-danger`, `.bulk-btn-delete`
  - Background: `var(--color-danger-soft)` (#FBE8E6)
  - Border: `1px solid var(--color-danger-soft)`
  - Color: `var(--color-danger)`
  - Radius: `border-radius: var(--radius-full) !important;`
  - Hover: background `#ebd0c8`
- **Modal Buttons**: `.modal-btn`, `.modal-btn-primary`, `.modal-btn-secondary`, `.modal-btn-danger`, `.modal-btn-warning`
  - Radius: `border-radius: var(--radius-full) !important;`
- **Bulk Floating Action**: `.bulk-btn-delete`, `.bulk-btn-restore`, `.bulk-btn-cancel`
  - Radius: `border-radius: var(--radius-full) !important;`
