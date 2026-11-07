<?php
// Style bersama untuk halaman admin (dipakai via include).
?>
<style>
  * { box-sizing: border-box; }
  body {
    font-family: -apple-system, system-ui, 'Segoe UI', Roboto, Arial, sans-serif;
    background: #f4f6f2;
    color: #1f2a22;
    margin: 0;
  }
  header.topbar {
    background: #2E5C3B;
    color: #fff;
    padding: 14px 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
  }
  header.topbar h1 { font-size: 18px; margin: 0; font-weight: 600; }
  header.topbar a { color: #e8f3ea; text-decoration: none; font-size: 14px; }
  header.topbar a:hover { text-decoration: underline; }
  .container { max-width: 1100px; margin: 0 auto; padding: 20px; }
  .tabs { display: flex; gap: 8px; margin-bottom: 16px; }
  .tabs a {
    padding: 8px 16px;
    border-radius: 999px;
    background: #fff;
    border: 1px solid #d8e2da;
    color: #2E5C3B;
    text-decoration: none;
    font-size: 14px;
    font-weight: 600;
  }
  .tabs a.active { background: #2E5C3B; color: #fff; border-color: #2E5C3B; }
  table {
    width: 100%;
    border-collapse: collapse;
    background: #fff;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0,0,0,0.06);
  }
  th, td {
    text-align: left;
    padding: 10px 12px;
    border-bottom: 1px solid #eef1ec;
    font-size: 13px;
    vertical-align: top;
  }
  th { background: #eaf2ec; color: #2E5C3B; font-weight: 600; }
  tr:last-child td { border-bottom: none; }
  .badge {
    display: inline-block;
    padding: 2px 8px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    background: #eee;
    color: #555;
  }
  .badge.pending { background: #fff3cd; color: #8a6d00; }
  .badge.confirmed, .badge.dikonfirmasi, .badge.lunas, .badge.selesai, .badge.diproses { background: #d7f0dd; color: #1f6b35; }
  .badge.cancelled, .badge.batal, .badge.dibatalkan { background: #fbdede; color: #a12424; }
  .muted { color: #8a948c; font-size: 12px; }
  .empty { padding: 30px; text-align: center; color: #8a948c; }
  .flash {
    padding: 10px 14px;
    border-radius: 8px;
    font-size: 13px;
    margin-bottom: 14px;
    font-weight: 600;
  }
  .flash-success { background: #d7f0dd; color: #1f6b35; }
  .flash-error { background: #fbdede; color: #a12424; }
  .aksi-form {
    display: flex;
    flex-direction: column;
    gap: 4px;
    min-width: 140px;
  }
  .aksi-form select {
    padding: 4px 6px;
    border: 1px solid #d8e2da;
    border-radius: 6px;
    font-size: 12px;
    background: #fff;
  }
  .aksi-form button {
    padding: 5px 8px;
    background: #2E5C3B;
    color: #fff;
    border: none;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
  }
  .aksi-form button:hover { background: #244a30; }
  .bukti-thumb {
    width: 48px;
    height: 48px;
    object-fit: cover;
    border-radius: 6px;
    border: 1px solid #d8e2da;
  }
  .login-wrap {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #2E5C3B;
  }
  .login-box {
    background: #fff;
    padding: 32px;
    border-radius: 14px;
    width: 100%;
    max-width: 320px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
  }
  .login-box h1 { font-size: 20px; margin: 0 0 20px; color: #2E5C3B; text-align: center; }
  .login-box input {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #d8e2da;
    border-radius: 8px;
    font-size: 14px;
    margin-bottom: 12px;
  }
  .login-box button {
    width: 100%;
    padding: 10px 12px;
    background: #2E5C3B;
    color: #fff;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
  }
  .login-box .error { color: #a12424; font-size: 13px; margin-bottom: 10px; }
</style>
