@php
    $health = $this->backupHealth();
    $lastBackup = $health['last'];
    $backupRuns = $this->backupRuns();
    $restoreRequests = $this->restoreRequests();
    $nextBackup = $lastBackup?->generated_at?->copy()->addHours(72);
@endphp

<x-filament-panels::page>
    <div class="backup-page" data-backup-page>
        <section class="backup-status backup-status-{{ $health['level'] }}" aria-labelledby="backup-status-title">
            <div class="backup-status-main">
                <span class="backup-status-icon" aria-hidden="true">
                    <x-filament::icon :icon="$health['level'] === 'success' ? 'heroicon-o-check-circle' : 'heroicon-o-exclamation-triangle'" />
                </span>

                <div class="backup-status-copy">
                    <p class="backup-eyebrow">Status backup</p>
                    <h2 id="backup-status-title">{{ $health['title'] }}</h2>
                    <p>{{ $health['description'] }}</p>
                </div>

                <div class="backup-status-actions">
                    <x-filament::button
                        type="button"
                        icon="heroicon-o-arrow-down-tray"
                        wire:click="mountAction('createBackup')"
                    >
                        Buat Backup Baru
                    </x-filament::button>

                    <a href="#restore-request" class="backup-text-link" x-on:click="document.getElementById('restore-request').open = true">Perlu memulihkan data?</a>
                </div>
            </div>

            <dl class="backup-metrics">
                <div>
                    <dt>Backup terakhir</dt>
                    <dd>{{ $lastBackup?->generated_at?->translatedFormat('d M Y, H:i') ?? 'Belum tersedia' }}</dd>
                </div>
                <div>
                    <dt>Backup berikutnya</dt>
                    <dd>
                        @if ($nextBackup)
                            {{ $nextBackup->isPast() ? 'Sudah melewati jadwal' : $nextBackup->diffForHumans() }}
                        @else
                            Buat hari ini
                        @endif
                    </dd>
                </div>
                <div>
                    <dt>Isi backup</dt>
                    <dd>Data aplikasi, tanpa foto</dd>
                </div>
            </dl>
        </section>

        <div class="backup-layout">
            <div class="backup-main-column">
                <x-filament::section>
                    <x-slot name="heading">Riwayat backup</x-slot>
                    <x-slot name="description">Sepuluh aktivitas backup terbaru.</x-slot>

                    @if ($backupRuns->isEmpty())
                        <div class="backup-empty-state">
                            <span aria-hidden="true"><x-filament::icon icon="heroicon-o-archive-box" /></span>
                            <p>Belum ada riwayat backup.</p>
                            <small>Gunakan tombol "Buat Backup Baru" untuk membuat salinan pertama.</small>
                        </div>
                    @else
                        <div class="backup-history-desktop">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Waktu</th>
                                        <th>Dibuat oleh</th>
                                        <th>Status</th>
                                        <th>Ukuran</th>
                                        <th>Unduhan</th>
                                        <th><span class="sr-only">Detail</span></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($backupRuns as $run)
                                        <tr>
                                            <td>{{ $run->created_at->translatedFormat('d M Y, H:i') }}</td>
                                            <td>{{ $run->requester?->name ?? 'Akun tidak tersedia' }}</td>
                                            <td><span class="backup-pill backup-pill-{{ $run->status }}">{{ $this->backupStatusLabel($run->status) }}</span></td>
                                            <td>{{ $run->size_bytes ? \Illuminate\Support\Number::fileSize($run->size_bytes) : '-' }}</td>
                                            <td>{{ $run->downloaded_at ? 'Dimulai' : '-' }}</td>
                                            <td>
                                                <details class="backup-row-detail">
                                                    <summary aria-label="Lihat detail backup #{{ $run->id }}">Detail</summary>
                                                    <div>
                                                        <span>ID backup: #{{ $run->id }}</span>
                                                        <span>Checksum: {{ $run->checksum_sha256 ?? '-' }}</span>
                                                        @if ($run->failure_code)<span>Kode gagal: {{ $run->failure_code }}</span>@endif
                                                    </div>
                                                </details>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="backup-history-mobile" aria-label="Riwayat backup">
                            @foreach ($backupRuns as $run)
                                <article class="backup-history-card">
                                    <div class="backup-history-card-top">
                                        <div>
                                            <strong>{{ $run->created_at->translatedFormat('d M Y') }}</strong>
                                            <span>{{ $run->created_at->format('H:i') }} oleh {{ $run->requester?->name ?? 'Akun tidak tersedia' }}</span>
                                        </div>
                                        <span class="backup-pill backup-pill-{{ $run->status }}">{{ $this->backupStatusLabel($run->status) }}</span>
                                    </div>
                                    <div class="backup-history-card-meta">
                                        <span>{{ $run->size_bytes ? \Illuminate\Support\Number::fileSize($run->size_bytes) : 'Ukuran tidak tersedia' }}</span>
                                        <span>{{ $run->downloaded_at ? 'Unduhan dimulai' : 'Belum diunduh' }}</span>
                                    </div>
                                    <details class="backup-row-detail">
                                        <summary>Detail backup</summary>
                                        <div>
                                            <span>ID backup: #{{ $run->id }}</span>
                                            <span>Checksum: {{ $run->checksum_sha256 ?? '-' }}</span>
                                            @if ($run->failure_code)<span>Kode gagal: {{ $run->failure_code }}</span>@endif
                                        </div>
                                    </details>
                                </article>
                            @endforeach
                        </div>
                    @endif
                </x-filament::section>

                @if ($restoreRequests->isNotEmpty())
                    <x-filament::section>
                        <x-slot name="heading">Riwayat permintaan pemulihan</x-slot>
                        <x-slot name="description">Permintaan yang sudah diperiksa oleh sistem.</x-slot>

                        <div class="restore-request-list">
                            @foreach ($restoreRequests as $request)
                                <article>
                                    <div>
                                        <strong>Permintaan #{{ $request->id }}</strong>
                                        <span>{{ $request->created_at->translatedFormat('d M Y, H:i') }} oleh {{ $request->requester?->name ?? 'Akun tidak tersedia' }}</span>
                                        <p>{{ $request->reason }}</p>
                                    </div>
                                    <span class="backup-pill backup-pill-validated">{{ $this->restoreStatusLabel($request->status) }}</span>
                                </article>
                            @endforeach
                        </div>
                    </x-filament::section>
                @endif
            </div>

            <aside class="backup-side-column" aria-label="Panduan backup dan pemulihan">
                <x-filament::section>
                    <x-slot name="heading">Cara menyimpan backup</x-slot>

                    <ol class="backup-steps">
                        <li>
                            <span>1</span>
                            <div><strong>Buat backup</strong><p>Masukkan password admin dan password khusus untuk file backup.</p></div>
                        </li>
                        <li>
                            <span>2</span>
                            <div><strong>Simpan file dengan aman</strong><p>Pindahkan file dari folder Unduhan ke penyimpanan yang terlindungi.</p></div>
                        </li>
                        <li>
                            <span>3</span>
                            <div><strong>Pisahkan password</strong><p>Jangan simpan password backup di folder yang sama dengan file.</p></div>
                        </li>
                    </ol>

                    <div class="backup-note">
                        <x-filament::icon icon="heroicon-o-photo" aria-hidden="true" />
                        <p><strong>Foto tidak ikut dalam file ini.</strong> Foto usaha dan produk tetap berada di penyimpanan media yang digunakan aplikasi.</p>
                    </div>
                </x-filament::section>

                <details
                    id="restore-request"
                    class="restore-panel"
                    @if ($errors->any() || session('status')) open @endif
                >
                    <summary>
                        <span class="restore-summary-icon"><x-filament::icon icon="heroicon-o-arrow-path" /></span>
                        <span><strong>Periksa file pemulihan</strong><small>Gunakan hanya saat data perlu dipulihkan.</small></span>
                        <x-filament::icon icon="heroicon-o-chevron-down" class="restore-chevron" />
                    </summary>

                    <div class="restore-panel-body">
                        <div class="restore-safety-note">
                            <x-filament::icon icon="heroicon-o-shield-check" />
                            <p>Langkah ini hanya memeriksa file dan mencatat permintaan. Data website tidak akan berubah.</p>
                        </div>

                        @if (session('status'))
                            <div class="restore-success" role="status">{{ session('status') }}</div>
                        @endif

                        <form method="POST" action="{{ route('admin.backup.restore-request') }}" enctype="multipart/form-data" class="restore-form">
                            @csrf

                            <label class="restore-field restore-field-full">
                                <span>File backup</span>
                                <input type="file" name="archive" accept=".zip,application/zip" required>
                                <small>Pilih file ZIP yang sebelumnya dibuat dari halaman ini.</small>
                                @error('archive') <em>{{ $message }}</em> @enderror
                            </label>

                            <label class="restore-field">
                                <span>Password admin</span>
                                <input type="password" name="current_password" autocomplete="current-password" required>
                                @error('current_password') <em>{{ $message }}</em> @enderror
                            </label>

                            <label class="restore-field">
                                <span>Password backup</span>
                                <input type="password" name="passphrase" autocomplete="off" minlength="16" required>
                                @error('passphrase') <em>{{ $message }}</em> @enderror
                            </label>

                            <label class="restore-field restore-field-full">
                                <span>Alasan pemulihan</span>
                                <textarea name="reason" rows="3" minlength="10" maxlength="2000" placeholder="Contoh: Memeriksa backup setelah gangguan data" required>{{ old('reason') }}</textarea>
                                @error('reason') <em>{{ $message }}</em> @enderror
                            </label>

                            <div class="restore-field-full">
                                <button type="submit" class="restore-submit">
                                    <x-filament::icon icon="heroicon-o-document-magnifying-glass" />
                                    Periksa dan Catat Permintaan
                                </button>
                            </div>
                        </form>
                    </div>
                </details>
            </aside>
        </div>
    </div>

    <style>
        .backup-page { display: grid; gap: 1.25rem; }
        .backup-status { overflow: hidden; border: 1px solid color-mix(in oklab, var(--gray-500) 18%, transparent); border-radius: .5rem; background: white; }
        .dark .backup-status { background: color-mix(in oklab, var(--gray-900) 94%, black); }
        .backup-status-main { display: grid; grid-template-columns: auto minmax(0, 1fr) auto; align-items: center; gap: 1rem; padding: 1.25rem; }
        .backup-status-icon { display: inline-flex; width: 3rem; height: 3rem; align-items: center; justify-content: center; border-radius: .5rem; }
        .backup-status-icon .fi-icon { width: 1.5rem; height: 1.5rem; }
        .backup-status-success .backup-status-icon { color: var(--success-700); background: var(--success-50); }
        .backup-status-warning .backup-status-icon { color: var(--warning-700); background: var(--warning-50); }
        .backup-status-danger .backup-status-icon { color: var(--danger-700); background: var(--danger-50); }
        .backup-eyebrow { margin: 0 0 .2rem; color: var(--gray-500); font-size: .72rem; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; }
        .backup-status-copy h2 { margin: 0; color: var(--gray-950); font-size: 1.15rem; font-weight: 700; line-height: 1.35; }
        .dark .backup-status-copy h2 { color: white; }
        .backup-status-copy > p:last-child { margin: .25rem 0 0; color: var(--gray-500); font-size: .875rem; line-height: 1.5; }
        .backup-status-actions { display: grid; justify-items: stretch; gap: .45rem; min-width: 11.5rem; text-align: center; }
        .backup-text-link { color: var(--gray-600); font-size: .75rem; font-weight: 600; text-decoration: underline; text-underline-offset: 3px; }
        .dark .backup-text-link { color: var(--gray-300); }
        .backup-metrics { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); margin: 0; border-top: 1px solid color-mix(in oklab, var(--gray-500) 14%, transparent); background: color-mix(in oklab, var(--gray-50) 72%, transparent); }
        .dark .backup-metrics { background: color-mix(in oklab, var(--gray-800) 45%, transparent); }
        .backup-metrics > div { min-width: 0; padding: .85rem 1.25rem; border-right: 1px solid color-mix(in oklab, var(--gray-500) 14%, transparent); }
        .backup-metrics > div:last-child { border-right: 0; }
        .backup-metrics dt { color: var(--gray-500); font-size: .72rem; }
        .backup-metrics dd { overflow-wrap: anywhere; margin: .2rem 0 0; color: var(--gray-950); font-size: .85rem; font-weight: 650; }
        .dark .backup-metrics dd { color: white; }
        .backup-layout { display: grid; grid-template-columns: minmax(0, 1.65fr) minmax(18rem, .75fr); align-items: start; gap: 1.25rem; }
        .backup-main-column, .backup-side-column { display: grid; min-width: 0; gap: 1.25rem; }
        .backup-history-desktop { overflow-x: auto; }
        .backup-history-desktop table { width: 100%; border-collapse: collapse; font-size: .8rem; }
        .backup-history-desktop th { padding: .7rem .65rem; border-bottom: 1px solid color-mix(in oklab, var(--gray-500) 18%, transparent); color: var(--gray-500); font-size: .7rem; font-weight: 650; text-align: left; }
        .backup-history-desktop td { padding: .8rem .65rem; border-bottom: 1px solid color-mix(in oklab, var(--gray-500) 10%, transparent); color: var(--gray-700); vertical-align: top; }
        .dark .backup-history-desktop td { color: var(--gray-300); }
        .backup-history-desktop tbody tr:last-child td { border-bottom: 0; }
        .backup-pill { display: inline-flex; align-items: center; min-height: 1.55rem; border-radius: 999px; padding: .15rem .55rem; background: var(--gray-100); color: var(--gray-700); font-size: .7rem; font-weight: 650; white-space: nowrap; }
        .backup-pill-completed, .backup-pill-expired, .backup-pill-validated { background: var(--success-50); color: var(--success-700); }
        .backup-pill-processing { background: var(--warning-50); color: var(--warning-700); }
        .backup-pill-failed, .backup-pill-rejected { background: var(--danger-50); color: var(--danger-700); }
        .backup-row-detail summary { cursor: pointer; color: var(--primary-600); font-size: .75rem; font-weight: 650; }
        .backup-row-detail > div { display: grid; gap: .3rem; margin-top: .5rem; color: var(--gray-500); font-family: ui-monospace, monospace; font-size: .67rem; overflow-wrap: anywhere; }
        .backup-history-mobile { display: none; }
        .backup-empty-state { display: grid; justify-items: center; gap: .35rem; padding: 2rem 1rem; text-align: center; }
        .backup-empty-state > span { display: inline-flex; width: 2.75rem; height: 2.75rem; align-items: center; justify-content: center; border-radius: .5rem; background: var(--gray-100); color: var(--gray-500); }
        .backup-empty-state .fi-icon { width: 1.35rem; height: 1.35rem; }
        .backup-empty-state p { margin: .35rem 0 0; color: var(--gray-950); font-weight: 650; }
        .backup-empty-state small { color: var(--gray-500); }
        .backup-steps { display: grid; gap: 1rem; margin: 0; padding: 0; list-style: none; }
        .backup-steps li { display: grid; grid-template-columns: 1.75rem minmax(0, 1fr); gap: .7rem; }
        .backup-steps li > span { display: inline-flex; width: 1.75rem; height: 1.75rem; align-items: center; justify-content: center; border-radius: .5rem; background: var(--primary-50); color: var(--primary-700); font-size: .75rem; font-weight: 750; }
        .backup-steps strong { display: block; color: var(--gray-950); font-size: .82rem; }
        .dark .backup-steps strong { color: white; }
        .backup-steps p { margin: .2rem 0 0; color: var(--gray-500); font-size: .75rem; line-height: 1.45; }
        .backup-note { display: grid; grid-template-columns: 1.25rem minmax(0, 1fr); gap: .65rem; margin-top: 1.1rem; padding-top: 1rem; border-top: 1px solid color-mix(in oklab, var(--gray-500) 14%, transparent); color: var(--gray-500); }
        .backup-note .fi-icon { width: 1.15rem; height: 1.15rem; }
        .backup-note p { margin: 0; font-size: .75rem; line-height: 1.5; }
        .backup-note strong { color: var(--gray-700); }
        .dark .backup-note strong { color: var(--gray-200); }
        .restore-panel { overflow: hidden; border: 1px solid color-mix(in oklab, var(--gray-500) 18%, transparent); border-radius: .5rem; background: white; }
        .dark .restore-panel { background: color-mix(in oklab, var(--gray-900) 94%, black); }
        .restore-panel > summary { display: grid; grid-template-columns: 2.25rem minmax(0, 1fr) 1.1rem; align-items: center; gap: .75rem; min-height: 4.5rem; padding: .8rem 1rem; cursor: pointer; list-style: none; }
        .restore-panel > summary::-webkit-details-marker { display: none; }
        .restore-summary-icon { display: inline-flex; width: 2.25rem; height: 2.25rem; align-items: center; justify-content: center; border-radius: .5rem; background: var(--gray-100); color: var(--gray-600); }
        .restore-summary-icon .fi-icon, .restore-chevron { width: 1.1rem; height: 1.1rem; }
        .restore-panel summary strong { display: block; color: var(--gray-950); font-size: .85rem; }
        .dark .restore-panel summary strong { color: white; }
        .restore-panel summary small { display: block; margin-top: .15rem; color: var(--gray-500); font-size: .72rem; }
        .restore-chevron { color: var(--gray-400); transition: transform 150ms ease; }
        .restore-panel[open] .restore-chevron { transform: rotate(180deg); }
        .restore-panel-body { padding: 1rem; border-top: 1px solid color-mix(in oklab, var(--gray-500) 14%, transparent); }
        .restore-safety-note { display: grid; grid-template-columns: 1.15rem minmax(0, 1fr); gap: .55rem; margin-bottom: 1rem; padding: .75rem; border-radius: .5rem; background: var(--info-50); color: var(--info-800); }
        .restore-safety-note .fi-icon { width: 1.05rem; height: 1.05rem; }
        .restore-safety-note p { margin: 0; font-size: .75rem; line-height: 1.45; }
        .restore-success { margin-bottom: 1rem; padding: .75rem; border-radius: .5rem; background: var(--success-50); color: var(--success-700); font-size: .78rem; }
        .restore-form { display: grid; grid-template-columns: minmax(0, 1fr); gap: .9rem; }
        .restore-field { display: grid; min-width: 0; gap: .35rem; }
        .restore-field-full { grid-column: auto; }
        .restore-field > span { color: var(--gray-950); font-size: .76rem; font-weight: 650; }
        .dark .restore-field > span { color: white; }
        .restore-field input, .restore-field textarea { width: 100%; min-height: 2.75rem; border: 1px solid color-mix(in oklab, var(--gray-500) 28%, transparent); border-radius: .5rem; padding: .6rem .7rem; background: white; color: var(--gray-950); font-size: .8rem; }
        .restore-field input[type="file"] { padding: .35rem; }
        .restore-field input[type="file"]::file-selector-button { min-height: 2rem; margin-right: .65rem; border: 0; border-radius: .4rem; padding: .35rem .65rem; background: var(--gray-100); color: var(--gray-700); cursor: pointer; font-size: .75rem; font-weight: 650; }
        .dark .restore-field input, .dark .restore-field textarea { background: color-mix(in oklab, var(--gray-800) 75%, transparent); color: white; }
        .restore-field input:focus, .restore-field textarea:focus { border-color: var(--primary-500); outline: 2px solid color-mix(in oklab, var(--primary-500) 25%, transparent); outline-offset: 1px; }
        .restore-field small { color: var(--gray-500); font-size: .68rem; line-height: 1.4; }
        .restore-field em { color: var(--danger-600); font-size: .7rem; font-style: normal; }
        .restore-submit { display: inline-flex; width: 100%; min-height: 2.75rem; align-items: center; justify-content: center; gap: .5rem; border: 0; border-radius: .5rem; padding: .65rem .85rem; background: var(--gray-900); color: white; cursor: pointer; font-size: .78rem; font-weight: 700; }
        .restore-submit .fi-icon { width: 1rem; height: 1rem; }
        .restore-submit:hover { background: var(--gray-700); }
        .restore-submit:focus-visible { outline: 2px solid var(--primary-600); outline-offset: 2px; }
        .restore-request-list { display: grid; gap: .75rem; }
        .restore-request-list article { display: flex; align-items: flex-start; justify-content: space-between; gap: 1rem; padding-bottom: .75rem; border-bottom: 1px solid color-mix(in oklab, var(--gray-500) 12%, transparent); }
        .restore-request-list article:last-child { padding-bottom: 0; border-bottom: 0; }
        .restore-request-list strong { display: block; color: var(--gray-950); font-size: .82rem; }
        .dark .restore-request-list strong { color: white; }
        .restore-request-list span:not(.backup-pill), .restore-request-list p { color: var(--gray-500); font-size: .72rem; }
        .restore-request-list p { margin: .3rem 0 0; line-height: 1.45; }

        @media (max-width: 1023px) {
            .backup-layout { grid-template-columns: minmax(0, 1fr); }
            .backup-side-column { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }

        @media (max-width: 639px) {
            .backup-page { gap: 1rem; }
            .backup-status-main { grid-template-columns: 2.5rem minmax(0, 1fr); gap: .75rem; padding: 1rem; }
            .backup-status-icon { width: 2.5rem; height: 2.5rem; }
            .backup-status-copy h2 { font-size: 1rem; }
            .backup-status-actions { grid-column: 1 / -1; width: 100%; min-width: 0; margin-top: .25rem; }
            .backup-status-actions .fi-btn { width: 100%; min-height: 2.75rem; }
            .backup-metrics { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .backup-metrics > div { padding: .75rem 1rem; }
            .backup-metrics > div:nth-child(2) { border-right: 0; }
            .backup-metrics > div:last-child { grid-column: 1 / -1; border-top: 1px solid color-mix(in oklab, var(--gray-500) 14%, transparent); }
            .backup-layout { gap: 1rem; }
            .backup-main-column, .backup-side-column { gap: 1rem; }
            .backup-side-column { grid-template-columns: minmax(0, 1fr); }
            .backup-history-desktop { display: none; }
            .backup-history-mobile { display: grid; gap: .75rem; }
            .backup-history-card { padding: .8rem; border: 1px solid color-mix(in oklab, var(--gray-500) 16%, transparent); border-radius: .5rem; }
            .backup-history-card-top { display: flex; align-items: flex-start; justify-content: space-between; gap: .75rem; }
            .backup-history-card-top strong { display: block; color: var(--gray-950); font-size: .8rem; }
            .dark .backup-history-card-top strong { color: white; }
            .backup-history-card-top div > span { display: block; margin-top: .15rem; color: var(--gray-500); font-size: .68rem; }
            .backup-history-card-meta { display: flex; flex-wrap: wrap; gap: .4rem .8rem; margin: .7rem 0; color: var(--gray-500); font-size: .7rem; }
            .restore-request-list article { display: grid; }
        }
    </style>
</x-filament-panels::page>
