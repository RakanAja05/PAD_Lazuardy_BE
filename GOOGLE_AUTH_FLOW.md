# 🔐 Google Authentication Flow - Laravel + Vue

## 📋 Overview Flow

```
1. User di Vue klik "Login with Google"
   ↓
2. Redirect ke: http://127.0.0.1:8000/api/auth/google
   ↓
3. User pilih akun Google
   ↓
4. Google redirect ke: http://127.0.0.1:8000/api/auth/google/callback
   ↓
5. Backend cek email sudah terdaftar atau belum:
   
   ✅ SUDAH TERDAFTAR:
      - Update google_id (jika belum ada)
      - Buat token
      - Redirect ke dashboard sesuai role (admin/tutor/student)
   
   ❌ BELUM TERDAFTAR:
      - Redirect ke /register dengan email & nama dari Google
      - User lengkapi form register (role, phone, dll)
      - Submit ke: POST /api/auth/google/complete
      - Backend buat user baru & return token
      - Redirect ke dashboard sesuai role
```

---

## 🔧 Backend Endpoints

### **1️⃣ Redirect ke Google**
```http
GET /auth/google
```
**Note:** Route ini di `web.php` karena butuh session untuk state verification

**Response:** Redirect ke halaman login Google

---

### **2️⃣ Callback dari Google**
```http
GET /auth/google/callback
```
**Note:** Route ini di `web.php` karena butuh session

**Response (Email Sudah Terdaftar → Login):**
```
Redirect ke dashboard sesuai role:
- Admin:   http://localhost:5173/admin/dashboard?token=xxx&user={...}
- Tutor:   http://localhost:5173/tutor/dashboard?token=xxx&user={...}
- Student: http://localhost:5173/student/dashboard?token=xxx&user={...}
```

**Response (Email Belum Terdaftar → Register):**
```
Redirect ke: http://localhost:5173/register?google_id=xxx&name=John&email=john@gmail.com&avatar=https://...&email_verified=true
```

**Response (Error):**
```
Redirect ke: http://localhost:5173/login?error=Gagal login dengan Google: xxx
```

---

### 3️⃣ **Complete Registration**
```http
POST /api/auth/google/complete
Content-Type: application/json
```

**Request Body:**
```json
{
  "google_id": "110505647819734587230",
  "name": "John Doe",
  "email": "john@gmail.com",
  "role": "student",
  "telephone_number": "081234567890",
  "date_of_birth": "2000-01-15",
  "gender": "male",
  "profile_photo_url": "https://lh3.googleusercontent.com/..."
}
```

**Response (Success):**
```json
{
  "message": "Registrasi berhasil",
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@gmail.com",
    "role": "student",
    "google_id": "110505647819734587230",
    ...
  },
  "token": "1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
}
```

---

## 🎨 Implementasi di Vue

### **1. Tombol Login Google (dengan Popup)**
```vue
<template>
  <button @click="loginWithGoogle">
    <img src="/google-icon.svg" />
    Login with Google
  </button>
</template>

<script setup>
import { onMounted } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()

// Setup listener untuk menerima data dari popup
onMounted(() => {
  window.addEventListener('message', handleGoogleCallback)
})

onUnmounted(() => {
  window.removeEventListener('message', handleGoogleCallback)
})

const loginWithGoogle = () => {
  // Buka popup window untuk Google login
  const width = 500
  const height = 600
  const left = window.screen.width / 2 - width / 2
  const top = window.screen.height / 2 - height / 2
  
  window.open(
    'http://127.0.0.1:8000/auth/google',
    'Google Login',
    `width=${width},height=${height},left=${left},top=${top}`
  )
}

const handleGoogleCallback = (event) => {
  // Validasi origin untuk keamanan
  if (event.origin !== 'http://127.0.0.1:8000') return
  
  const data = event.data
  
  if (data.status === 'error') {
    alert(data.message)
    return
  }
  
  // USER SUDAH TERDAFTAR - Login langsung
  if (data.type === 'login') {
    // Simpan token dan user
    localStorage.setItem('token', data.token)
    localStorage.setItem('user', JSON.stringify(data.user))
    
    // Redirect ke dashboard sesuai role
    const dashboardPath = `/${data.user.role}/dashboard`
    router.push(dashboardPath)
  }
  
  // USER BELUM TERDAFTAR - Ke halaman register
  if (data.type === 'register') {
    // Redirect ke register dengan data dari Google
    router.push({
      path: '/register',
      query: { googleData: JSON.stringify(data.google_data) }
    })
  }
}
</script>
```

---

### **2. Halaman Dashboard**

Tidak perlu handle apapun, karena token sudah disimpan oleh `handleGoogleCallback` di step 1.

Cukup tambahkan route guard:
```vue
// router/index.js
router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('token')
  
  if (to.meta.requiresAuth && !token) {
    next('/login')
  } else {
    next()
  }
})
```

---

### **3. Halaman Register (`/register`)**
```vue
<template>
  <form @submit.prevent="handleSubmit">
    <input v-model="form.name" placeholder="Nama Lengkap" required />
    <input v-model="form.email" type="email" placeholder="Email" readonly />
    
    <select v-model="form.role" required>
      <option value="student">Student</option>
      <option value="tutor">Tutor</option>
    </select>
    
    <input v-model="form.telephone_number" placeholder="No. HP (opsional)" />
    <input v-model="form.date_of_birth" type="date" placeholder="Tanggal Lahir" />
    
    <select v-model="form.gender">
      <option value="">Pilih Jenis Kelamin</option>
      <option value="male">Laki-laki</option>
      <option value="female">Perempuan</option>
    </select>
    
    <button type="submit" :disabled="loading">
      {{ loading ? 'Loading...' : 'Daftar' }}
    </button>
  </form>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import axios from 'axios'

const router = useRouter()
const route = useRoute()
const loading = ref(false)
const form = ref({
  google_id: '',
  name: '',
  email: '',
  role: '',
  telephone_number: '',
  date_of_birth: '',
  gender: '',
  profile_photo_url: ''
})

onMounted(() => {
  // Ambil data Google dari route query (dikirim via postMessage)
  if (route.query.googleData) {
    const googleData = JSON.parse(route.query.googleData)
    
    // Pre-fill form dengan data dari Google
    form.value.google_id = googleData.google_id
    form.value.name = googleData.name
    form.value.email = googleData.email
    form.value.profile_photo_url = googleData.avatar
  }
})

const handleSubmit = async () => {
  loading.value = true
  
  try {
    const response = await axios.post('http://127.0.0.1:8000/api/auth/google/complete', form.value)
    
    // Simpan token
    localStorage.setItem('token', response.data.token)
    localStorage.setItem('user', JSON.stringify(response.data.user))
    
    // Redirect ke dashboard
    router.push('/dashboard')
  } catch (error) {
    alert(error.response?.data?.message || 'Gagal registrasi')
  } finally {
    loading.value = false
  }
}
</script>
```

---

## ⚙️ Konfigurasi

### **1. Update Google Console**
Tambahkan Authorized redirect URIs:
```
http://127.0.0.1:8000/auth/google/callback
http://localhost:8000/auth/google/callback
```

### **2. `.env` Backend**
```env
FRONTEND_URL=http://localhost:5173
GOOGLE_CLIENT_ID=your-client-id
GOOGLE_CLIENT_SECRET=your-client-secret
GOOGLE_REDIRECT_URI=http://127.0.0.1:8000/auth/google/callback
```

---

## 🔑 Penggunaan Token di Vue

### **Axios Interceptor**
```javascript
// src/axios.js
import axios from 'axios'

const api = axios.create({
  baseURL: 'http://127.0.0.1:8000/api'
})

// Tambahkan token ke setiap request
api.interceptors.request.use(config => {
  const token = localStorage.getItem('token')
  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }
  return config
})

export default api
```

### **Protected Route di Vue**
```javascript
// router/index.js
router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('token')
  
  if (to.meta.requiresAuth && !token) {
    next('/login')
  } else {
    next()
  }
})
```

---

## ✅ Kelebihan Flow Ini

1. ✅ User dapat **melengkapi data** sebelum akun dibuat
2. ✅ Jika sudah terdaftar, langsung login tanpa form
3. ✅ Email sudah terverifikasi otomatis (dari Google)
4. ✅ Token-based auth cocok untuk SPA
5. ✅ CORS sudah dikonfigurasi dengan benar
6. ✅ Frontend punya kontrol penuh atas UI/UX

---

## 🐛 Troubleshooting

### Error: "CORS policy"
- Pastikan `config/cors.php` sudah include frontend URL
- Cek `FRONTEND_URL` di `.env`

### Error: "Invalid state"
- Gunakan `->stateless()` di Socialite (sudah ada)

### User tidak redirect
- Cek console browser untuk error
- Pastikan URL callback di Google Console benar

---

## 📝 Catatan

- Email dari Google sudah terverifikasi, tidak perlu OTP
- Password tidak dipakai untuk Google login
- Token bisa di-refresh dengan endpoint `/api/me`
