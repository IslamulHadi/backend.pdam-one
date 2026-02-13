<x-mail::message>
# Halo, {{ $contactMessage->name }}

Terima kasih telah menghubungi **{{ config('app.name') }}**. Kami telah merespon pesan Anda.

---

**Pesan Anda:**

> {{ $contactMessage->message }}

---

**Balasan Kami:**

{{ $contactMessage->response }}

---

Jika Anda memiliki pertanyaan lebih lanjut, silakan hubungi kami kembali melalui halaman kontak.

Salam,<br>
{{ config('app.name') }}
</x-mail::message>
