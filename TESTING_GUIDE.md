# 🧪 Testing Guide - Google OAuth untuk Backend Developer

## 📋 3 Cara Mengecek Data yang Terkirim

---

## 🔍 **Cara 1: Lihat di Browser (Visual)**

### **Step by Step:**

1. **Buka browser** dan akses:
   ```
   http://127.0.0.1:8000/auth/google
   ```

2. **Pilih akun Google** Anda

3. **Lihat halaman callback** yang muncul
   - Ada tampilan **formatted JSON**
   - Ada **countdown 3 detik** sebelum popup close
   - Anda bisa **screenshot** atau **copy** datanya

### **Tampilan yang Akan Muncul:**

```
🔐 Google OAuth Callback
✅ Data berhasil diterima dari Google!

📤 Data yang akan dikirim ke Frontend:
{
    "status": "success",
    "type": "login",
    "token": "1|xxxxxxxxxxxxxxx",
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "john@gmail.com",
        "role": "student"
    }
}

⏳ Mengirim data ke parent window dan menutup popup dalam 2 detik...
```

### **Tips:**
- Countdown 3 detik memberi waktu untuk membaca data
- Bisa **pause** dengan membuka DevTools sebelum countdown habis
- Data akan tetap terlihat sebelum window close

---

## 📝 **Cara 2: Lihat di Laravel Logs**

### **Lokasi File Log:**
```
storage/logs/laravel.log
```

### **Cara Membuka:**

**Option A - Via VS Code:**
1. Buka file: `storage/logs/laravel.log`
2. Scroll ke paling bawah
3. Cari log dengan tag: `=== GOOGLE OAUTH CALLBACK ===`

**Option B - Via Terminal:**
```powershell
# Lihat log real-time
Get-Content storage\logs\laravel.log -Wait -Tail 50

# Atau cari log Google OAuth saja
Select-String -Path storage\logs\laravel.log -Pattern "GOOGLE OAUTH" -Context 0,10
```

### **Format Log yang Muncul:**

```
[2025-10-30 10:30:45] local.INFO: === GOOGLE OAUTH CALLBACK ===  
{
    "data": {
        "status": "success",
        "type": "login",
        "token": "1|xxxxxxx",
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "john@gmail.com",
            "role": "student"
        }
    },
    "frontend_url": "http://localhost:5173",
    "timestamp": "2025-10-30 10:30:45"
}
```

### **Kelebihan Cara Ini:**
- ✅ Data tersimpan permanent
- ✅ Bisa review kapan saja
- ✅ Lengkap dengan timestamp
- ✅ Tidak perlu buka browser

---

## 🌐 **Cara 3: Test Endpoint (Tanpa Login Google)**

### **Endpoint Test:**
```
GET http://127.0.0.1:8000/api/auth/google/test-data
```

### **Cara Mengakses:**

**Option A - Browser:**
Buka URL di browser:
```
http://127.0.0.1:8000/api/auth/google/test-data
```

**Option B - Postman:**
1. Buat request baru
2. Method: `GET`
3. URL: `http://127.0.0.1:8000/api/auth/google/test-data`
4. Klik **Send**

**Option C - cURL (PowerShell):**
```powershell
curl http://127.0.0.1:8000/api/auth/google/test-data | ConvertFrom-Json | ConvertTo-Json -Depth 10
```

**Option D - HTTPie (jika terinstall):**
```bash
http GET http://127.0.0.1:8000/api/auth/google/test-data
```

### **Response yang Didapat:**

```json
{
    "message": "Ini adalah contoh data yang akan dikirim ke frontend",
    "scenarios": {
        "user_sudah_terdaftar": {
            "status": "success",
            "type": "login",
            "token": "1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx",
            "user": {
                "id": 1,
                "name": "John Doe",
                "email": "john@example.com",
                "role": "student"
            }
        },
        "user_belum_terdaftar": {
            "status": "success",
            "type": "register",
            "google_data": {
                "google_id": "110505647819734587230",
                "name": "Jane Doe",
                "email": "jane@gmail.com",
                "avatar": "https://lh3.googleusercontent.com/a/xxxxxxx"
            }
        },
        "error": {
            "status": "error",
            "message": "Gagal login dengan Google: Connection timeout"
        }
    },
    "note": "Data ini dikirim via postMessage, bukan JSON response"
}
```

### **Kelebihan Cara Ini:**
- ✅ Tidak perlu login Google
- ✅ Bisa test kapan saja
- ✅ Lihat semua skenario sekaligus
- ✅ Bisa share ke frontend developer

---

## 🔧 **Cara 4: Browser DevTools Console**

### **Step by Step:**

1. **Buka DevTools** (F12) di browser
2. Pergi ke tab **Console**
3. **Akses** `http://127.0.0.1:8000/auth/google`
4. **Pilih akun** Google
5. **Lihat console log** di DevTools

### **Log yang Muncul:**

```javascript
=== GOOGLE OAUTH CALLBACK ===
Data yang dikirim: {
    status: "success",
    type: "login",
    token: "1|xxxxxxx",
    user: {
        id: 1,
        name: "John Doe",
        email: "john@gmail.com",
        role: "student"
    }
}
Target origin: http://localhost:5173
Window opener exists: true
Sending postMessage to parent window...
```

### **Tips DevTools:**
- Centang **"Preserve log"** agar log tidak hilang saat redirect
- Bisa **copy** log sebagai Object atau String
- Gunakan **Network tab** untuk lihat request/response

---

## 📊 **Comparison Table**

| Cara | Kemudahan | Perlu Google Login | Data Lengkap | Real Data | Recommended |
|------|-----------|-------------------|--------------|-----------|-------------|
| **Browser Visual** | ⭐⭐⭐⭐⭐ | Ya | ⭐⭐⭐⭐ | Ya | ✅ Pemula |
| **Laravel Logs** | ⭐⭐⭐⭐ | Ya | ⭐⭐⭐⭐⭐ | Ya | ✅ Production |
| **Test Endpoint** | ⭐⭐⭐⭐⭐ | Tidak | ⭐⭐⭐ | Tidak | ✅ Development |
| **DevTools Console** | ⭐⭐⭐ | Ya | ⭐⭐⭐⭐ | Ya | ✅ Advanced |

---

## 🎯 **Rekomendasi untuk Backend Developer:**

### **Saat Development:**
```
1. Gunakan Test Endpoint untuk quick check
   → http://127.0.0.1:8000/api/auth/google/test-data

2. Lihat Browser Visual untuk validasi flow
   → http://127.0.0.1:8000/auth/google

3. Cek Laravel Logs untuk debugging
   → storage/logs/laravel.log
```

### **Saat Testing dengan Frontend:**
```
1. Monitor Laravel Logs real-time:
   Get-Content storage\logs\laravel.log -Wait -Tail 50

2. Minta frontend developer buka DevTools Console
   untuk validasi data yang diterima

3. Cross-check data di log backend vs frontend
```

### **Saat Production:**
```
1. Monitoring via Laravel Logs
2. Set up log aggregation (Sentry, Logstash, dll)
3. Disable test endpoint untuk security
```

---

## 🚨 **Security Notes:**

### **⚠️ JANGAN di Production:**

1. **Hapus test endpoint** di production:
   ```php
   // routes/api.php
   // Route::get('/auth/google/test-data', ...) // ← Comment atau hapus
   ```

2. **Kurangi log detail** di production:
   ```php
   // Hanya log error, bukan semua data
   if (config('app.env') === 'local') {
       Log::info('GOOGLE OAUTH', $data);
   }
   ```

3. **Hapus console.log** di view production:
   ```blade
   @if(config('app.env') === 'local')
       <script>console.log('Debug:', data);</script>
   @endif
   ```

---

## 🐛 **Troubleshooting:**

### **Tidak Ada Log di laravel.log:**
```powershell
# Cek permission
icacls storage\logs

# Atau buat file baru
New-Item storage\logs\laravel.log -Force
```

### **Countdown Terlalu Cepat:**
Edit di view, ubah dari 3 jadi 10 detik:
```javascript
let countdown = 10; // ← dari 3 jadi 10
```

### **Ingin Lihat Raw Response:**
Akses langsung callback URL (akan error tapi lihat response):
```
http://127.0.0.1:8000/auth/google/callback
```

---

## 📚 **Dokumentasi Tambahan:**

- **View File:** `resources/views/auth/google-callback.blade.php`
- **Controller:** `app/Http/Controllers/Auth/GoogleController.php`
- **Log Location:** `storage/logs/laravel.log`
- **Test Endpoint:** `http://127.0.0.1:8000/api/auth/google/test-data`

---

Dengan 4 cara ini, Anda sebagai backend developer bisa **fully validate** data yang dikirim tanpa perlu bantuan frontend! 🎉
