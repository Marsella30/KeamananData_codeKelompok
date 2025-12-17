@extends('Admin.dashboard')

@section('isi')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Rekap Log Sistem</h4>
            <small class="text-muted">Total {{ count($logs) }} entries</small>
        </div>
        <div class="d-flex gap-2 align-items-center">
            <span class="badge bg-primary">
                <i class="bi bi-clock"></i> {{ now()->format('d M Y H:i') }}
            </span>
            <button class="btn btn-sm btn-outline-secondary" onclick="refreshLogs()">
                <i class="bi bi-arrow-clockwise"></i> Refresh
            </button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-3">
        <div class="col-md-3">
            <div class="card border-success">
                <div class="card-body py-2 px-3">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-check-circle-fill text-success fs-4 me-2"></i>
                        <div>
                            <small class="text-muted d-block">Success</small>
                            <strong class="text-success" id="countSuccess">0</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-info">
                <div class="card-body py-2 px-3">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-info-circle-fill text-info fs-4 me-2"></i>
                        <div>
                            <small class="text-muted d-block">Info</small>
                            <strong class="text-info" id="countInfo">0</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-warning">
                <div class="card-body py-2 px-3">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-exclamation-triangle-fill text-warning fs-4 me-2"></i>
                        <div>
                            <small class="text-muted d-block">Warning</small>
                            <strong class="text-warning" id="countWarning">0</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-danger">
                <div class="card-body py-2 px-3">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-x-circle-fill text-danger fs-4 me-2"></i>
                        <div>
                            <small class="text-muted d-block">Error</small>
                            <strong class="text-danger" id="countError">0</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="card mb-3">
        <div class="card-body py-2">
            <div class="row g-2">
                <div class="col-md-3">
                    <select class="form-select form-select-sm" id="filterLevel">
                        <option value="">Semua Level</option>
                        <option value="SUCCESS">Success</option>
                        <option value="ERROR">Error</option>
                        <option value="WARNING">Warning</option>
                        <option value="INFO">Info</option>
                        <option value="DEBUG">Debug</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select form-select-sm" id="filterAction">
                        <option value="">Semua Aksi</option>
                        <option value="login">Login</option>
                        <option value="logout">Logout</option>
                        <option value="create">Create/Tambah</option>
                        <option value="update">Update/Edit</option>
                        <option value="delete">Delete/Hapus</option>
                        <option value="view">View/Lihat</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <input type="text" class="form-control form-select-sm" id="searchLog" 
                           placeholder="Cari log...">
                </div>
                <div class="col-md-2">
                    <button class="btn btn-sm btn-outline-danger w-100" onclick="clearFilters()">
                        <i class="bi bi-x-circle"></i> Clear
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Logs Card -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div id="logContainer" style="max-height: 600px; overflow-y: auto; background: #1e1e1e;">

                @forelse ($logs as $index => $log)
                    @php
                        // Parse log untuk mendapatkan informasi
                        $level = 'INFO';
                        $badge = 'secondary';
                        $icon = 'info-circle';
                        $actionType = '';
                        $actionBadge = '';
                        
                        // Deteksi level log
                        if (stripos($log, 'ERROR') !== false || stripos($log, 'CRITICAL') !== false || stripos($log, 'FAILED') !== false || stripos($log, 'GAGAL') !== false) {
                            $level = 'ERROR';
                            $badge = 'danger';
                            $icon = 'x-circle-fill';
                        } elseif (stripos($log, 'WARNING') !== false || stripos($log, 'WARN') !== false) {
                            $level = 'WARNING';
                            $badge = 'warning';
                            $icon = 'exclamation-triangle-fill';
                        } elseif (stripos($log, 'DEBUG') !== false) {
                            $level = 'DEBUG';
                            $badge = 'info';
                            $icon = 'bug-fill';
                        } elseif (stripos($log, 'SUCCESS') !== false || stripos($log, 'BERHASIL') !== false) {
                            $level = 'SUCCESS';
                            $badge = 'success';
                            $icon = 'check-circle-fill';
                        }
                        
                        // Deteksi tipe aksi
                        if (stripos($log, 'LOGIN') !== false || stripos($log, 'MASUK') !== false) {
                            $actionType = 'login';
                            $actionBadge = '<span class="badge bg-primary bg-opacity-25 text-primary ms-2"><i class="bi bi-box-arrow-in-right"></i> Login</span>';
                        } elseif (stripos($log, 'LOGOUT') !== false || stripos($log, 'KELUAR') !== false) {
                            $actionType = 'logout';
                            $actionBadge = '<span class="badge bg-secondary bg-opacity-25 text-secondary ms-2"><i class="bi bi-box-arrow-right"></i> Logout</span>';
                        } elseif (stripos($log, 'CREATE') !== false || stripos($log, 'TAMBAH') !== false || stripos($log, 'INSERT') !== false) {
                            $actionType = 'create';
                            $actionBadge = '<span class="badge bg-success bg-opacity-25 text-success ms-2"><i class="bi bi-plus-circle"></i> Create</span>';
                        } elseif (stripos($log, 'UPDATE') !== false || stripos($log, 'EDIT') !== false || stripos($log, 'UBAH') !== false) {
                            $actionType = 'update';
                            $actionBadge = '<span class="badge bg-warning bg-opacity-25 text-warning ms-2"><i class="bi bi-pencil-square"></i> Update</span>';
                        } elseif (stripos($log, 'DELETE') !== false || stripos($log, 'HAPUS') !== false) {
                            $actionType = 'delete';
                            $actionBadge = '<span class="badge bg-danger bg-opacity-25 text-danger ms-2"><i class="bi bi-trash"></i> Delete</span>';
                        } elseif (stripos($log, 'VIEW') !== false || stripos($log, 'LIHAT') !== false || stripos($log, 'READ') !== false) {
                            $actionType = 'view';
                            $actionBadge = '<span class="badge bg-info bg-opacity-25 text-info ms-2"><i class="bi bi-eye"></i> View</span>';
                        }

                        // Extract timestamp
                        preg_match('/\[(.*?)\]/', $log, $matches);
                        $timestamp = $matches[1] ?? '';
                        
                        // Extract user jika ada
                        preg_match('/user[:\s]+([^\s,]+)/i', $log, $userMatches);
                        $user = $userMatches[1] ?? '';
                        
                        $message = $log;
                    @endphp

                    <div class="log-entry border-bottom" data-level="{{ $level }}" data-action="{{ $actionType }}" data-index="{{ $index }}">
                        <div class="d-flex align-items-start p-3 hover-highlight">
                            <div class="me-3 text-center" style="min-width: 50px;">
                                <small class="text-muted d-block" style="font-size: 11px;">#{{ str_pad($index + 1, 3, '0', STR_PAD_LEFT) }}</small>
                            </div>
                            
                            <div class="me-3">
                                <span class="badge bg-{{ $badge }}">
                                    <i class="bi bi-{{ $icon }}"></i> {{ $level }}
                                </span>
                            </div>
                            
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center mb-1">
                                    {!! $actionBadge !!}
                                    @if($user)
                                        <span class="badge bg-dark bg-opacity-50 text-light ms-2">
                                            <i class="bi bi-person-fill"></i> {{ $user }}
                                        </span>
                                    @endif
                                </div>
                                
                                <div class="log-message" style="font-family: 'Courier New', monospace; font-size: 13px; color: #e0e0e0; word-break: break-word; line-height: 1.6;">
                                    {{ $message }}
                                </div>
                                
                                @if($timestamp)
                                    <small class="text-muted d-block mt-2">
                                        <i class="bi bi-clock"></i> {{ $timestamp }}
                                    </small>
                                @endif
                            </div>
                            
                            <div class="ms-3">
                                <button class="btn btn-sm btn-outline-light btn-copy" onclick="copyLog({{ $index }})" title="Copy log">
                                    <i class="bi bi-clipboard"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5">
                        <i class="bi bi-inbox" style="font-size: 48px; color: #6c757d;"></i>
                        <p class="text-muted mt-3 mb-0">Tidak ada log yang tersedia</p>
                    </div>
                @endforelse

            </div>
        </div>
    </div>

</div>

<style>
    .hover-highlight:hover {
        background-color: #2d2d2d !important;
        cursor: pointer;
        transition: background-color 0.2s;
    }
    
    .log-entry {
        border-color: #333 !important;
    }
    
    #logContainer {
        scrollbar-width: thin;
        scrollbar-color: #555 #1e1e1e;
    }
    
    #logContainer::-webkit-scrollbar {
        width: 8px;
    }
    
    #logContainer::-webkit-scrollbar-track {
        background: #1e1e1e;
    }
    
    #logContainer::-webkit-scrollbar-thumb {
        background: #555;
        border-radius: 4px;
    }
    
    #logContainer::-webkit-scrollbar-thumb:hover {
        background: #777;
    }

    .badge {
        font-weight: 500;
        padding: 0.35rem 0.65rem;
        font-size: 11px;
    }
    
    .btn-copy {
        opacity: 0.6;
        transition: opacity 0.2s;
    }
    
    .btn-copy:hover {
        opacity: 1;
    }
    
    .bg-opacity-25 {
        --bs-bg-opacity: 0.25;
    }
</style>

<script>
    const logs = @json($logs);
    
    // Count statistics
    document.addEventListener('DOMContentLoaded', function() {
        updateStatistics();
    });
    
    function updateStatistics() {
        const entries = document.querySelectorAll('.log-entry');
        let success = 0, info = 0, warning = 0, error = 0;
        
        entries.forEach(entry => {
            const level = entry.dataset.level;
            if (level === 'SUCCESS') success++;
            else if (level === 'INFO' || level === 'DEBUG') info++;
            else if (level === 'WARNING') warning++;
            else if (level === 'ERROR') error++;
        });
        
        document.getElementById('countSuccess').textContent = success;
        document.getElementById('countInfo').textContent = info;
        document.getElementById('countWarning').textContent = warning;
        document.getElementById('countError').textContent = error;
    }
    
    // Filter by level
    document.getElementById('filterLevel').addEventListener('change', function() {
        filterLogs();
    });
    
    // Filter by action
    document.getElementById('filterAction').addEventListener('change', function() {
        filterLogs();
    });
    
    // Search logs
    document.getElementById('searchLog').addEventListener('input', function() {
        filterLogs();
    });
    
    function filterLogs() {
        const level = document.getElementById('filterLevel').value;
        const action = document.getElementById('filterAction').value;
        const search = document.getElementById('searchLog').value.toLowerCase();
        const entries = document.querySelectorAll('.log-entry');
        
        entries.forEach(entry => {
            const entryLevel = entry.dataset.level;
            const entryAction = entry.dataset.action;
            const entryText = entry.textContent.toLowerCase();
            
            const levelMatch = !level || entryLevel === level;
            const actionMatch = !action || entryAction === action;
            const searchMatch = !search || entryText.includes(search);
            
            entry.style.display = (levelMatch && actionMatch && searchMatch) ? 'block' : 'none';
        });
    }
    
    function clearFilters() {
        document.getElementById('filterLevel').value = '';
        document.getElementById('filterAction').value = '';
        document.getElementById('searchLog').value = '';
        filterLogs();
    }
    
    function copyLog(index) {
        const logText = logs[index];
        navigator.clipboard.writeText(logText).then(() => {
            // Toast notification
            const toast = document.createElement('div');
            toast.className = 'position-fixed top-0 end-0 p-3';
            toast.style.zIndex = '9999';
            toast.innerHTML = `
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>Log berhasil disalin!
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 3000);
        });
    }
    
    function refreshLogs() {
        location.reload();
    }
    
    // Auto scroll to bottom
    const container = document.getElementById('logContainer');
    if (container) {
        container.scrollTop = container.scrollHeight;
    }
</script>

@endsection