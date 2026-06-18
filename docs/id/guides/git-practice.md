# Git Practice — Pull Request Pertama Kamu

Panduan ini membantu kamu menjalankan **fork → clone → branch → commit → push → PR** untuk pertama kalinya.

## Prasyarat

- Akun [GitHub](https://github.com)
- Git sudah terinstal di laptop
  - Cek: buka terminal/cmd, ketik `git --version`
  - Kalau belum ada, download di [git-scm.com](https://git-scm.com)
- Text editor (VSCode, Sublime, Notepad++, dll)

---

## Langkah 1 — Fork Repository

1. Buka https://github.com/dxnz-id/siwayut-catering
2. Klik tombol **Fork** (pojok kanan atas)
3. Biarkan setting default, klik **Create fork**
4. Sekarang kamu punya salinan repo ini di akun GitHub kamu sendiri

## Langkah 2 — Clone ke Laptop

```bash
git clone https://github.com/<username-github-kamu>/siwayut-catering
cd siwayut-catering
```

Ganti `<username-github-kamu>` dengan username GitHub kamu.

## Langkah 3 — Tambah Remote Upstream

Supaya kamu bisa sinkron dengan repo utama kalau ada perubahan:

```bash
git remote add upstream https://github.com/dxnz-id/siwayut-catering
```

Cek: `git remote -v` — harus muncul `origin` (fork kamu) dan `upstream` (repo utama).

## Langkah 4 — Buat Branch Baru

```bash
git checkout -b feat/add-credits
```

Nama branch bebas, tapi usahakan deskriptif.

## Langkah 5 — Tambah Nama Kamu

Buka file **`docs/credits.md`**, tambahkan baris baru di bagian bawah:

```markdown
- **Nama Lengkap Kamu** (NIM)
```

Contoh:

```markdown
- **Ahmad Fauzi** (2201234567)
```

Simpan file-nya.

## Langkah 6 — Stage & Commit

```bash
git add docs/credits.md
git commit -m "feat: add credits — <nama kamu>"
```

Atau kalau ada banyak file berubah:

```bash
git add -A
git commit -m "feat: add credits — <nama kamu>"
```

## Langkah 7 — Push ke Fork Kamu

```bash
git push origin feat/add-credits
```

## Langkah 8 — Bikin Pull Request

1. Buka GitHub — repo hasil fork kamu
2. Bakal muncul banner kuning "feat/add-credits had recent pushes"
3. Klik **Compare & pull request**
4. **Title**: `feat: add credits — <nama kamu>`
5. **Description**: (opsional) bisa ditulis "Menambahkan nama ke credits"
6. Klik **Create pull request**

Selesai! PR kamu akan direview.

---

## Kalau Kena Conflict

Kalau tiba-tiba PR kamu tertulis **"This branch has conflicts"**, jangan panik. Itu artinya ada perubahan di repo utama yang bentrok dengan punya kamu.

Fix-nya:

```bash
# Pindah ke branch kamu
git checkout feat/add-credits

# Ambil perubahan terbaru dari repo utama
git pull upstream main

# Kalau ada conflict, buka file yang bentrok, perbaiki, lalu:
git add -A
git commit -m "fix: resolve merge conflict"
git push origin feat/add-credits
```

PR kamu akan otomatis ke-update.

---

## Ceklis Pembelajaran

Setelah selesai, kamu sudah belajar:

- [ ] Fork repository
- [ ] Clone ke lokal
- [ ] Tambah remote upstream
- [ ] Buat branch baru
- [ ] Edit file
- [ ] Stage & commit
- [ ] Push ke branch
- [ ] Bikin pull request
- [ ] Resolve conflict
