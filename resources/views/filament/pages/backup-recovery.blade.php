@php($health = $this->backupHealth())

<x-filament-panels::page>
    <div class="space-y-6">
        <x-filament::section
            :icon="$health['level'] === 'success' ? 'heroicon-o-check-circle' : 'heroicon-o-exclamation-triangle'"
            :icon-color="$health['level']"
        >
            <x-slot name="heading">{{ $health['title'] }}</x-slot>
            <x-slot name="description">{{ $health['description'] }}</x-slot>

            <div class="grid gap-4 sm:grid-cols-3">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Backup terakhir</p>
                    <p class="mt-1 font-semibold text-gray-950 dark:text-white">
                        {{ $health['last']?->generated_at?->diffForHumans() ?? 'Belum ada' }}
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Target pemulihan data</p>
                    <p class="mt-1 font-semibold text-gray-950 dark:text-white">Maksimal 72 jam</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Cakupan</p>
                    <p class="mt-1 font-semibold text-gray-950 dark:text-white">Database saja</p>
                </div>
            </div>
        </x-filament::section>

        <x-filament::section icon="heroicon-o-shield-check" icon-color="info">
            <x-slot name="heading">Batas keamanan pemulihan</x-slot>
            <x-slot name="description">Dashboard tidak dapat memulihkan database secara langsung.</x-slot>

            <p class="text-sm leading-6 text-gray-600 dark:text-gray-300">
                Arsip memakai ZIP AES-256. Media tidak disertakan karena dilindungi oleh backup Cloudinary.
                Permintaan restore hanya menyimpan metadata aman dan wajib dilanjutkan melalui pengujian database terpisah.
            </p>
        </x-filament::section>

        <x-filament::section id="restore-request">
            <x-slot name="heading">Validasi dan ajukan restore</x-slot>
            <x-slot name="description">Arsip diperiksa lalu langsung dihapus dari server. SQL tidak dijalankan.</x-slot>

            @if (session('status'))
                <div class="mb-4 rounded-lg border border-success-200 bg-success-50 p-3 text-sm text-success-700 dark:border-success-500/30 dark:bg-success-500/10 dark:text-success-300">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.backup.restore-request') }}" enctype="multipart/form-data" class="grid gap-4 sm:grid-cols-2">
                @csrf

                <label class="block sm:col-span-2">
                    <span class="text-sm font-medium text-gray-950 dark:text-white">Arsip backup terenkripsi</span>
                    <input type="file" name="archive" accept=".zip,application/zip" required class="mt-2 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm dark:border-white/10 dark:bg-white/5">
                    @error('archive') <span class="mt-1 block text-sm text-danger-600">{{ $message }}</span> @enderror
                </label>

                <label class="block">
                    <span class="text-sm font-medium text-gray-950 dark:text-white">Password akun admin</span>
                    <input type="password" name="current_password" autocomplete="current-password" required class="mt-2 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm dark:border-white/10 dark:bg-white/5">
                    @error('current_password') <span class="mt-1 block text-sm text-danger-600">{{ $message }}</span> @enderror
                </label>

                <label class="block">
                    <span class="text-sm font-medium text-gray-950 dark:text-white">Passphrase arsip</span>
                    <input type="password" name="passphrase" autocomplete="off" minlength="16" required class="mt-2 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm dark:border-white/10 dark:bg-white/5">
                    @error('passphrase') <span class="mt-1 block text-sm text-danger-600">{{ $message }}</span> @enderror
                </label>

                <label class="block sm:col-span-2">
                    <span class="text-sm font-medium text-gray-950 dark:text-white">Alasan pengajuan restore</span>
                    <textarea name="reason" rows="3" minlength="10" maxlength="2000" required class="mt-2 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm dark:border-white/10 dark:bg-white/5">{{ old('reason') }}</textarea>
                    @error('reason') <span class="mt-1 block text-sm text-danger-600">{{ $message }}</span> @enderror
                </label>

                <div class="sm:col-span-2">
                    <button type="submit" class="inline-flex min-h-11 items-center justify-center rounded-lg bg-gray-900 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-700 focus-visible:outline-2 focus-visible:outline-offset-2 dark:bg-white dark:text-gray-950">
                        Validasi dan Ajukan Restore
                    </button>
                </div>
            </form>
        </x-filament::section>

        <x-filament::section>
            <x-slot name="heading">Riwayat backup database</x-slot>
            <x-slot name="description">Passphrase, kredensial database, dan isi SQL tidak pernah disimpan pada riwayat ini.</x-slot>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[760px] text-left text-sm">
                    <thead class="border-b border-gray-200 text-gray-500 dark:border-white/10 dark:text-gray-400">
                        <tr>
                            <th class="px-3 py-3 font-medium">Waktu</th>
                            <th class="px-3 py-3 font-medium">Admin</th>
                            <th class="px-3 py-3 font-medium">Status</th>
                            <th class="px-3 py-3 font-medium">Ukuran</th>
                            <th class="px-3 py-3 font-medium">Checksum</th>
                            <th class="px-3 py-3 font-medium">Diunduh</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                        @forelse ($this->backupRuns() as $run)
                            <tr>
                                <td class="px-3 py-3">{{ $run->created_at->format('d M Y H:i') }}</td>
                                <td class="px-3 py-3">{{ $run->requester?->name ?? 'Akun dihapus' }}</td>
                                <td class="px-3 py-3 font-medium">{{ $this->backupStatusLabel($run->status) }}</td>
                                <td class="px-3 py-3">{{ $run->size_bytes ? \Illuminate\Support\Number::fileSize($run->size_bytes) : '-' }}</td>
                                <td class="px-3 py-3 font-mono text-xs">{{ $run->checksum_sha256 ? \Illuminate\Support\Str::limit($run->checksum_sha256, 18) : '-' }}</td>
                                <td class="px-3 py-3">{{ $run->downloaded_at?->format('d M Y H:i') ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-3 py-6 text-center text-gray-500">Belum ada riwayat backup.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-filament::section>

        <x-filament::section>
            <x-slot name="heading">Permintaan restore</x-slot>
            <x-slot name="description">Status valid berarti arsip lolos pemeriksaan, bukan berarti database telah dipulihkan.</x-slot>

            <div class="space-y-3">
                @forelse ($this->restoreRequests() as $request)
                    <div class="flex flex-col gap-2 border-b border-gray-100 pb-3 last:border-0 last:pb-0 sm:flex-row sm:items-start sm:justify-between dark:border-white/5">
                        <div>
                            <p class="font-medium text-gray-950 dark:text-white">Permintaan #{{ $request->id }} - Backup #{{ $request->backup_run_id }}</p>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $request->reason }}</p>
                        </div>
                        <div class="text-sm sm:text-right">
                            <p class="font-medium">{{ $this->restoreStatusLabel($request->status) }}</p>
                            <p class="text-gray-500 dark:text-gray-400">{{ $request->created_at->format('d M Y H:i') }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada permintaan restore.</p>
                @endforelse
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
