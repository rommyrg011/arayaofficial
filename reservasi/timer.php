<?php 
require_once __DIR__ . '/../function.php'; 

include 'template/head.php'; 
include 'template/sidebar.php'; 
include 'template/topbar.php'; 
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Timer Billing</h1>
        <div>
            <button class="btn btn-sm btn-danger mr-2 shadow-sm" id="btnDeleteSelected" disabled onclick="deleteSelectedTimers()">
                <i class="fas fa-trash"></i> Hapus Terpilih (<span id="selectedCount">0</span>)
            </button>
            <button class="btn btn-sm btn-secondary shadow-sm" data-toggle="modal" data-target="#addTimerModal">
                <i class="fas fa-plus"></i> Tambah
            </button>
        </div>
    </div>

    <div class="d-flex justify-content-center mb-4">
        <div class="btn-group shadow-sm" role="group" style="border-radius: 50px; overflow: hidden;">
            <button type="button" class="btn btn-sm btn-primary px-4 py-2 font-weight-bold" id="tab-beruntung" onclick="switchAdminTab('beruntung')">Beruntung</button>
            <button type="button" class="btn btn-sm btn-light text-secondary px-4 py-2 font-weight-bold border" id="tab-gambut" onclick="switchAdminTab('gambut')">Gambut</button>
        </div>
    </div>

    <div id="section-beruntung" class="admin-tab-content fade-in">
        <div class="row mx-n1" id="container-beruntung"></div>
    </div>

    <div id="section-gambut" class="admin-tab-content fade-in" style="display: none;">
        <div class="row mx-n1" id="container-gambut"></div>
    </div>

    <div class="card shadow mb-4 mt-5">
        <div class="card-header py-3 text-center">
            <h6 class="m-0 font-weight-bold text-primary" style="font-size:20px;">Riwayat Berakhir</h6>
            <button class="btn btn-sm btn-danger mt-2" onclick="clearHistory()">Bersihkan Riwayat</button>
        </div>
        <div class="card-body">
            <ul class="list-group" id="historyContainer"></ul>
        </div>
    </div>
</div>

<div class="modal fade" id="addTimerModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold">Tambah Multi Timer Baru</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Cabang</label>
                    <select class="form-control" id="inputCabang">
                        <option value="beruntung">Beruntung</option>
                        <option value="gambut">Gambut</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Nomor</label>
                    <input type="number" class="form-control" id="inputNoMeja" required autocomplete="off">
                </div>
                <div class="form-group">
                    <label>Judul</label>
                    <input type="text" class="form-control" id="inputJudulMeja" required autocomplete="off">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btnSaveTimer">Tambah Timer</button>
            </div>
        </div>
    </div>
</div>

<style>
    .fade-in { animation: fadeIn 0.4s ease-in-out; }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .text-truncate-custom { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block; width: 100%; }
    .selected-card { transform: scale(0.97); opacity: 0.8; border: 2px solid #e74a3b !important; transition: all 0.2s ease; }
    
    .card-timer-hitam { background-color: #4a4f5c !important; color: #ffffff !important; }
    .card-timer-hitam .timer-title { color: #66b0ff !important; }
    .card-timer-hitam .display-time { color: #ffffff !important; }
    
    .card-timer-putih { background-color: #BCD6C0 !important; color: #333333 !important; }
    .card-timer-putih .timer-title, .card-timer-putih .display-time { color: #333333 !important; }
    
    .card-timer-kuning { background-color: #f6c23e !important; color: #333333 !important; }
    .card-timer-kuning .timer-title, .card-timer-kuning .display-time { color: #333333 !important; }
    
    .card-timer-merah { background-color: #e74a3b !important; color: #ffffff !important; }
    .card-timer-merah .timer-title, .card-timer-merah .display-time { color: #ffffff !important; }
</style>

<?php 
include 'template/footer.php'; 
include 'template/script.php'; 
?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
let timers = {};
let historyData = [];
let selectedTimers = new Set(); 
let pendingUpdates = new Set();
let isSyncing = false;

const alarmSound = new Audio('https://actions.google.com/sounds/v1/alarms/medium_bell_ringing_near.ogg');
alarmSound.loop = true;
const API_URL = 'api_timer.php'; 

function formatTime(seconds) {
    const h = Math.floor(seconds / 3600).toString().padStart(2, '0');
    const m = Math.floor((seconds % 3600) / 60).toString().padStart(2, '0');
    const s = (seconds % 60).toString().padStart(2, '0');
    return `${h}:${m}:${s}`;
}

function switchAdminTab(cabang) {
    $('#tab-beruntung, #tab-gambut').removeClass('btn-primary text-white').addClass('btn-light text-secondary border');
    $(`#tab-${cabang}`).removeClass('btn-light text-secondary border').addClass('btn-primary text-white');
    
    $('.admin-tab-content').hide();
    $(`#section-${cabang}`).show();
}

function sortCardsByNumber(cabang) {
    const container = $(`#container-${cabang}`);
    const cards = container.children().get();

    cards.sort(function(a, b) {
        const idA = a.id.replace('card-', '');
        const idB = b.id.replace('card-', '');
        if (!timers[idA] || !timers[idB]) return 0;
        return (parseInt(timers[idA].noMeja) || 0) - (parseInt(timers[idB].noMeja) || 0); 
    });

    $.each(cards, function(index, item) {
        container.append(item);
    });
}

function renderCard(id) {
    const t = timers[id];
    if ($(`#card-${id}`).length > 0) return; 

    const isSelected = selectedTimers.has(id);

    const cardHtml = `
        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 col-12 mb-3 px-2" id="card-${id}">
            <div class="card shadow-sm h-100 ${isSelected ? 'selected-card' : ''}" style="transition: all 0.2s;">
                <div class="card-body p-3 position-relative">
                    <div class="position-absolute" style="top: 10px; right: 12px; z-index: 10;">
                        <input type="checkbox" style="transform: scale(1.5); cursor: pointer;" id="check-${id}" ${isSelected ? 'checked' : ''} onchange="toggleSelectTimer('${id}')">
                    </div>
                    <div class="text-center mt-2 mb-2">
                        <div class="font-weight-bold text-uppercase mb-0 text-truncate-custom timer-title" style="font-size: 13px; padding-right: 25px;">
                            ${t.noMeja} | ${t.judulMeja}
                        </div>
                        <div class="font-weight-bold display-time" id="display-${id}" style="font-size: 28px; letter-spacing: 1px; margin: 8px 0;">
                            ${formatTime(t.timeLeft)}
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <select class="form-control text-dark" style="height: 30px; padding: 0 8px; font-size: 12px; border-radius: 4px;" id="paket-${id}">
                            <option value="180">3 Menit</option>
                            <option value="600">10 Menit</option>
                            <option value="900">15 Menit</option>
                            <option value="1200">20 Menit</option>
                            <option value="1800">30 Menit</option>
                            <option value="3900">1 Jam</option>
                            <option value="5600">1.5 Jam</option>
                            <option value="7500">2 Jam</option>
                            <option value="11100">3 Jam</option>
                            <option value="14700">4 Jam</option>
                            <option value="18300">5 Jam</option>
                            <option value="21900">6 Jam</option>
                            <option value="36300">10 Jam</option>
                        </select>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <button class="btn btn-success flex-fill mr-1 font-weight-bold" style="padding: 6px 0; font-size: 12px; border-radius: 4px;" id="start-${id}" onclick="startTimer('${id}')">Mulai</button>
                        <button class="btn flex-fill ml-1 text-white font-weight-bold" style="padding: 6px 0; font-size: 12px; background-color: #d4a34b; border-color: #d4a34b; border-radius: 4px;" id="pause-${id}" onclick="pauseTimer('${id}')">Jeda</button>
                    </div>
                    <div class="d-flex justify-content-between">
                        <button class="btn btn-info flex-fill mr-1 font-weight-bold text-white" style="padding: 6px 0; font-size: 12px; border-radius: 4px;" id="add-time-${id}" onclick="addTimePrompt('${id}')"><i class="fas fa-plus"></i> Waktu</button>
                        <button class="btn btn-danger flex-fill ml-1 font-weight-bold" style="padding: 6px 0; font-size: 12px; border-radius: 4px;" onclick="resetTimer('${id}')">Reset</button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    $(`#container-${t.cabang}`).append(cardHtml);
}

window.toggleSelectTimer = function(id) {
    const isChecked = $(`#check-${id}`).is(':checked');
    if (isChecked) {
        selectedTimers.add(id);
        $(`#card-${id} .card`).addClass('selected-card');
    } else {
        selectedTimers.delete(id);
        $(`#card-${id} .card`).removeClass('selected-card');
    }
    
    $('#selectedCount').text(selectedTimers.size);
    $('#btnDeleteSelected').prop('disabled', selectedTimers.size === 0);
};

window.deleteSelectedTimers = function() {
    if (selectedTimers.size === 0) return;

    Swal.fire({
        title: "Hapus Timer?",
        text: `Anda yakin ingin menghapus ${selectedTimers.size} timer yang dipilih?`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#858796",
        confirmButtonText: "Ya, Hapus!",
        cancelButtonText: "Batal"
    }).then((result) => {
        if (result.isConfirmed) {
            selectedTimers.forEach(id => removeTimer(id));
            selectedTimers.clear(); 
            $('#selectedCount').text(0);
            $('#btnDeleteSelected').prop('disabled', true); 
        }
    });
};

function renderHistory() {
    $('#historyContainer').empty();
    historyData.forEach(item => {
        const branchBadge = item.cabang === 'beruntung' ? 'badge-primary' : 'badge-success';
        const branchName = item.cabang === 'beruntung' ? 'Beruntung' : 'Gambut';
        $('#historyContainer').append(`
            <li class="list-group-item d-flex justify-content-between align-items-center px-3 py-2">
                <span class="font-weight-bold" style="font-size: 13px;">
                    <span class="badge ${branchBadge} mr-2">${branchName}</span> 
                    ${item.noMeja} - ${item.judulMeja}
                </span>
                <span class="badge badge-danger badge-pill px-2 py-1" style="font-size: 11px;">Berakhir: ${item.endTime}</span>
            </li>
        `);
    });
}

function updateCardColor(id) {
    const t = timers[id];
    if(!t) return;
    const card = $(`#card-${id} .card`);
    card.removeClass('card-timer-hitam card-timer-putih card-timer-kuning card-timer-merah border-left-primary border-left-success border-left-warning border-left-danger');
    
    if (t.isFinished) {
        card.addClass('card-timer-merah border-left-danger');
    } else if (t.isRunning) {
        card.addClass('card-timer-putih border-left-success');
    } else if (t.timeLeft > 0 && !t.isRunning) {
        card.addClass('card-timer-kuning border-left-warning');
    } else {
        card.addClass('card-timer-hitam border-left-primary');
    }
}

function updateUI(id) {
    const t = timers[id];
    if (!t) return;

    $(`#display-${id}`).text(formatTime(t.timeLeft));
    $(`#paket-${id}`).val(t.paket);

    if (t.isRunning) {
        $(`#paket-${id}`).prop('disabled', true);
        $(`#start-${id}`).prop('disabled', true);
        $(`#pause-${id}`).prop('disabled', false);
        $(`#add-time-${id}`).prop('disabled', false);
    } else {
        $(`#paket-${id}`).prop('disabled', t.timeLeft > 0);
        $(`#start-${id}`).prop('disabled', false).text(t.timeLeft > 0 ? 'Lanjut' : 'Mulai');
        $(`#pause-${id}`).prop('disabled', true);
        $(`#add-time-${id}`).prop('disabled', !(t.timeLeft > 0));
    }
    
    updateCardColor(id);
}

function syncData() {
    if (isSyncing) return;
    isSyncing = true;

    $.post(API_URL, { action: 'load' }, function(data) {
        const dbTimers = data.timers;
        historyData = data.history;
        renderHistory();
        let hasStructureChanged = false;

        for (const id in dbTimers) {
            const dbT = dbTimers[id];
            const isRunning = (dbT.isRunning == 1 || dbT.isRunning === true || dbT.isRunning === 'true');

            if (!timers[id]) {
                dbT.paket = parseInt(dbT.paket);
                dbT.endTime = parseInt(dbT.endTime);
                dbT.timeLeft = parseInt(dbT.timeLeft);
                dbT.isRunning = isRunning; 
                dbT.isFinished = false;

                timers[id] = dbT;
                renderCard(id);
                updateUI(id);
                hasStructureChanged = true; 
            } else {
                if (pendingUpdates.has(id)) continue;
                
                timers[id].paket = parseInt(dbT.paket);
                timers[id].endTime = parseInt(dbT.endTime);
                
                if (timers[id].isRunning != isRunning || (timers[id].timeLeft != parseInt(dbT.timeLeft) && !isRunning)) {
                    timers[id].isRunning = isRunning;
                    timers[id].timeLeft = parseInt(dbT.timeLeft);
                    updateUI(id);
                }
            }
        }

        for (const id in timers) {
            if (!dbTimers[id] && !pendingUpdates.has(id)) {
                delete timers[id];
                $(`#card-${id}`).remove();
                hasStructureChanged = true; 
                
                if (selectedTimers.has(id)) {
                    selectedTimers.delete(id);
                    $('#selectedCount').text(selectedTimers.size);
                    if (selectedTimers.size === 0) $('#btnDeleteSelected').prop('disabled', true);
                }
            }
        }

        if (hasStructureChanged) {
            sortCardsByNumber('beruntung');
            sortCardsByNumber('gambut');
        }
        isSyncing = false;
    }, 'json').fail(function() {
        isSyncing = false;
    });
}

function saveTimerToDB(id) {
    const t = timers[id];
    pendingUpdates.add(id);
    $.post(API_URL, {
        action: 'update',
        id_timer: id,
        paket: t.paket,
        time_left: t.timeLeft,
        end_time: t.endTime,
        is_running: t.isRunning ? 1 : 0
    }).always(function() {
        pendingUpdates.delete(id);
    });
}

function handleTimerFinish(id) {
    const t = timers[id];
    if(!t || t.isFinished) return; 
    
    t.isFinished = true;
    t.isRunning = false;
    t.timeLeft = 0;
    t.endTime = 0;
    
    const now = new Date();
    const strTime = now.toLocaleDateString('id-ID') + ' ' + now.toLocaleTimeString('id-ID');
    
    pendingUpdates.add(id);
    
    $.post(API_URL, {
        action: 'finish',
        id_timer: id,
        end_time_str: strTime
    }, function() {
        syncData(); 
    }).always(function() {
        pendingUpdates.delete(id);
    });

    updateUI(id);

    alarmSound.play().catch(e => console.log(e));
    
    const branchName = t.cabang === 'beruntung' ? 'Beruntung' : 'Gambut';
    Swal.fire({
        title: "Waktu Habis!",
        text: `Waktu untuk ${t.noMeja} (${t.judulMeja}) di Cabang ${branchName} telah selesai.`,
        icon: "warning",
        confirmButtonText: "Tutup & Matikan Alarm",
        allowOutsideClick: false
    }).then((result) => {
        if (result.isConfirmed) {
            alarmSound.pause();
            alarmSound.currentTime = 0;
        }
    });
}

setInterval(() => {
    const now = Date.now();
    for (const id in timers) {
        const t = timers[id];
        if (t.isRunning && !pendingUpdates.has(id)) {
            t.timeLeft = Math.max(0, Math.round((parseInt(t.endTime) - now) / 1000));
            $(`#display-${id}`).text(formatTime(t.timeLeft));
            
            if (t.timeLeft <= 0) {
                handleTimerFinish(id);
            }
        }
    }
}, 1000);

function init() {
    syncData();
    setInterval(syncData, 3000); 
}

$('#btnSaveTimer').click(function() {
    const cabang = $('#inputCabang').val();
    const noMeja = $('#inputNoMeja').val();
    const judulMeja = $('#inputJudulMeja').val();
    
    if(!noMeja || !judulMeja) return;

    const id = "T" + Date.now().toString();
    
    $.post(API_URL, {
        action: 'add',
        id_timer: id,
        cabang: cabang,
        no_meja: noMeja,
        judul_meja: judulMeja
    }, function() {
        syncData(); 
        $('#inputNoMeja').val('');
        $('#inputJudulMeja').val('');
        $('#addTimerModal').modal('hide');
        switchAdminTab(cabang);
    });
});

window.startTimer = function(id) {
    const t = timers[id];
    if(t.isRunning) return;
    
    t.isFinished = false;
    
    if(t.timeLeft === 0) {
        t.paket = parseInt($(`#paket-${id}`).val());
        t.timeLeft = t.paket;
    }

    t.endTime = Date.now() + (parseInt(t.timeLeft) * 1000);
    t.isRunning = true;
    
    updateUI(id);
    saveTimerToDB(id); 
};

window.pauseTimer = function(id) {
    const t = timers[id];
    if(!t.isRunning) return;
    
    t.isRunning = false;
    const now = Date.now();
    t.timeLeft = Math.max(0, Math.round((parseInt(t.endTime) - now) / 1000));
    
    updateUI(id);
    saveTimerToDB(id); 
};

window.resetTimer = function(id) {
    const t = timers[id];
    if (!t) return;

    Swal.fire({
        title: "Reset Timer?",
        html: `Anda yakin ingin mereset waktu untuk <b>${t.noMeja} (${t.judulMeja})</b>?`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#858796",
        confirmButtonText: "Ya, Reset!",
        cancelButtonText: "Batal"
    }).then((result) => {
        if (result.isConfirmed) {
            t.isFinished = false;
            t.isRunning = false;
            t.timeLeft = 0;
            t.endTime = 0;
            
            updateUI(id);
            saveTimerToDB(id); 
            
            Swal.fire({
                icon: 'success',
                title: 'Direset!',
                text: 'Waktu berhasil dikembalikan ke awal.',
                timer: 1500,
                showConfirmButton: false
            });
        }
    });
};

window.removeTimer = function(id) {
    delete timers[id];
    pendingUpdates.delete(id);
    
    $(`#card-${id}`).fadeOut(300, function() {
        $(this).remove();
        sortCardsByNumber('beruntung');
        sortCardsByNumber('gambut');
    });
    
    $.post(API_URL, {
        action: 'delete',
        id_timer: id
    });
};

window.clearHistory = function() {
    $.post(API_URL, { action: 'clear_history' }, function() {
        historyData = [];
        renderHistory();
    });
};

window.addTimePrompt = function(id) {
    const t = timers[id];
    Swal.fire({
        title: 'Tambah Waktu',
        html: `Berapa waktu yang ingin ditambahkan untuk <b>${t.noMeja} (${t.judulMeja})</b>?`,
        input: 'select',
        inputOptions: {
            '300': '5 Menit',
            '600': '10 Menit',
            '900': '15 Menit',
            '1200': '20 Menit',
            '1800': '30 Menit',
            '3600': '1 Jam',
            '7200': '2 Jam',
            '10800': '3 Jam',
            '14400': '4 Jam',
            '18000': '5 Jam'
        },
        inputPlaceholder: '-- Pilih Tambahan Waktu --',
        showCancelButton: true,
        confirmButtonText: 'Tambahkan',
        cancelButtonText: 'Batal',
        inputValidator: (value) => {
            return new Promise((resolve) => {
                if (value) resolve();
                else resolve('Anda harus memilih durasi waktu terlebih dahulu!');
            });
        }
    }).then((result) => {
        if (result.isConfirmed) {
            addExtraTime(id, parseInt(result.value));
        }
    });
};

window.addExtraTime = function(id, extraSeconds) {
    const t = timers[id];
    t.isFinished = false;
    t.timeLeft = parseInt(t.timeLeft) + parseInt(extraSeconds);
    
    if (t.isRunning) {
        t.endTime = parseInt(t.endTime) + (parseInt(extraSeconds) * 1000);
    }
    
    updateUI(id);
    saveTimerToDB(id);
    
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: 'Waktu berhasil ditambahkan ke Perangkat.',
        timer: 1500,
        showConfirmButton: false
    });
};

$(document).ready(function() {
    init();
});
</script>
</body>
</html>