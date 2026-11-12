<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Display Status Meja</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;700;800&family=Orbitron:wght@700;900&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            background-color: #050505;
            color: white;
            font-family: 'Montserrat', sans-serif; 
            overflow-x: hidden;
            min-height: 100vh;
        }
        
        .main-container {
            width: 90%;
            max-width: 1300px;
            margin: 0 auto;
            padding: 30px 0;
        }
        
        .pagination-container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 25px;
            gap: 15px;
        }
        
        .page-btn {
            background-color: transparent;
            color: #6c757d;
            border: 2px solid #1f2229;
            padding: 6px 20px; 
            font-size: 12px; 
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.4s ease;
            outline: none !important;
            font-family: 'Montserrat', sans-serif;
        }

        .page-btn:hover {
            color: #ffffff;
            border-color: #00ffcc;
        }

        .page-btn.active {
            background-color: #00ffcc;
            color: #000000;
            border-color: #00ffcc;
            box-shadow: 0 0 15px rgba(0, 255, 204, 0.4);
            transform: scale(1.05);
        }

        .grid-container {
            display: grid;
            grid-template-columns: repeat(5, 1fr); 
            gap: 15px;
            width: 100%;
        }

        .fade-slide-in {
            animation: fadeSlideIn 0.5s ease-out forwards;
        }

        @keyframes fadeSlideIn {
            0% { opacity: 0; transform: translateY(20px); }
            100% { opacity: 1; transform: translateY(0); }
        }

        .card-display {
            background-color: #000000;
            border: 3px solid #ffffff; 
            border-radius: 12px; 
            padding: 20px 8px; 
            text-align: center;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            transition: box-shadow 0.3s ease, border-color 0.3s ease;
            position: relative; 
        }

        .card-display.active-card {
            border-color: #00ffcc;
            box-shadow: 0 0 15px rgba(0, 255, 204, 0.15) inset, 0 0 20px rgba(0, 255, 204, 0.2);
        }

        /* Style khusus jika meja nonaktif / maintenance */
        .card-display.inactive-card {
            border-color: #555555;
            background-color: #111111;
            opacity: 0.6;
        }

        .badge-meja {
            position: absolute;
            top: 5px;
            right: 15px;
            font-size: 25px;
            font-weight: 800;
            color: #ffffff;
            font-family: 'Montserrat', sans-serif;
        }

        .icon-status {
            font-size: 32px; 
            margin-bottom: 12px;
            margin-top: 10px; 
        }
        
        .icon-check { color: #ffffff; }
        .icon-active { color: #dc3545; animation: pulse-danger 1.5s infinite; }
        .icon-inactive { color: #888888; }

        @keyframes pulse-danger {
            0% { transform: scale(1); text-shadow: 0 0 0 rgba(220, 53, 69, 0.7); }
            50% { transform: scale(1.15); text-shadow: 0 0 15px rgba(220, 53, 69, 0.8); }
            100% { transform: scale(1); text-shadow: 0 0 0 rgba(220, 53, 69, 0); }
        }

        .text-countdown {
            font-family: 'Orbitron', sans-serif; 
            font-size: 20px; 
            font-weight: 700;
            color: #ffffff;
            letter-spacing: 2px;
            margin-bottom: 8px;
        }
        .text-selesai {
            font-size: 11px; 
            font-weight: 700;
            margin-bottom: 12px;
            color: #4DF046;
            letter-spacing: 1.5px;
            text-transform: uppercase;
        }
        .text-tersedia {
            font-size: 11px;
            font-weight: 700;
            color: #4DF046; 
            letter-spacing: 1.5px;
            text-transform: uppercase;
            margin-bottom: 12px;
        }
        .text-nonaktif {
            font-size: 11px;
            font-weight: 700;
            color: #ff4d4d; 
            letter-spacing: 1.5px;
            text-transform: uppercase;
            margin-bottom: 12px;
        }
        .text-subtitle {
            color: #ffffff; 
            font-size: 14px; 
            font-weight: 600;
            text-transform: uppercase;
            margin-top: 4px;
        }

        @media (max-width: 1200px) {
            .grid-container { grid-template-columns: repeat(4, 1fr); }
        }

        @media (max-width: 992px) {
            .main-container { width: 95%; }
            .grid-container { grid-template-columns: repeat(3, 1fr); gap: 12px; }
            .card-display { padding: 15px 5px; border-width: 2.5px; }
            .text-countdown { font-size: 18px; }
            .badge-meja { font-size: 18px; top: 8px; right: 12px; }
            .text-subtitle { font-size: 12px; }
        }

        @media (max-width: 768px) {
            .grid-container { grid-template-columns: repeat(2, 1fr); gap: 10px; }
        }
    </style>
</head>
<body>
    <div class="main-container">
        <div class="pagination-container">
            <button class="page-btn active" id="btn-beruntung" onclick="switchPage('beruntung')">Beruntung</button>
            <button class="page-btn" id="btn-gambut" onclick="switchPage('gambut')">Gambut</button>
        </div>
        <div class="grid-container fade-slide-in" id="display-area"></div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script>
        let currentPage = 'beruntung';
        let timers = {}; 
        let warnedTables = {}; 
        
        window.speechUtterance = null; 

        function isTimerRunning(val) {
            return val == 1 || val === '1' || val === true || val === 'true';
        }

        function showAddedTimeAlert(noMeja, judulMeja, addedSeconds) {
            let minutes = Math.round(addedSeconds / 60);
            let timeText = minutes >= 60 ? `${Math.floor(minutes / 60)} Jam ${minutes % 60} Menit` : `${minutes} Menit`;

            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'info',
                title: 'Waktu Ditambahkan!',
                html: `<b>${noMeja} | ${judulMeja}</b> menambah waktu <b>${timeText}</b>`,
                showConfirmButton: false,
                timer: 6000,
                timerProgressBar: true,
                background: '#1a1a1a',
                color: '#ffffff',
                iconColor: '#00ffcc'
            });

            if ('speechSynthesis' in window) {
                window.speechSynthesis.cancel();
                let utterance = new SpeechSynthesisUtterance(`Waktu untuk nomor ${noMeja}, ${judulMeja}, telah ditambah ${timeText}.`);
                utterance.lang = 'id-ID';
                utterance.rate = 1.0;
                window.speechSynthesis.speak(utterance);
            }
        }

        function speakWarning(noMeja, judulMeja, cabang) {
            Swal.fire({
                title: 'Peringatan Waktu!',
                html: `Waktu TV <b>${noMeja}</b> <b>${judulMeja}</b> Cabang <b>${cabang}</b> sudah mendekati habis.`,
                icon: 'warning',
                showConfirmButton: false,
                allowOutsideClick: false,
                background: '#1a1a1a',
                color: '#ffffff',
                iconColor: '#dc3545'
            });

            if ('speechSynthesis' in window) {
                window.speechSynthesis.cancel(); 
                let textToSpeak = `Waktu tv nomor ${noMeja}, ${judulMeja}, cabang ${cabang}, sudah mendekati habis. Mohon segera diselesaikan. Terima kasih.`;
                window.speechUtterance = new SpeechSynthesisUtterance(textToSpeak);
                window.speechUtterance.lang = 'id-ID'; 
                window.speechUtterance.rate = 0.9; 
                
                let isAlertClosed = false;
                const closeAlert = () => {
                    if (!isAlertClosed) {
                        isAlertClosed = true;
                        setTimeout(() => { Swal.close(); }, 3000); 
                    }
                };

                window.speechUtterance.onend = () => closeAlert();
                setTimeout(() => { if (!isAlertClosed) Swal.close(); }, 12000);
                window.speechSynthesis.speak(window.speechUtterance);
            } else {
                setTimeout(() => { Swal.close(); }, 5000);
            }
        }

        function formatTime(seconds) {
            const h = Math.floor(seconds / 3600).toString().padStart(2, '0');
            const m = Math.floor((seconds % 3600) / 60).toString().padStart(2, '0');
            const s = (seconds % 60).toString().padStart(2, '0');
            return `${h}:${m}:${s}`;
        }

        function switchPage(cabang) {
            if (currentPage === cabang) return; 
            currentPage = cabang;

            $('.page-btn').removeClass('active');
            $(`#btn-${cabang}`).addClass('active');

            const displayArea = document.getElementById('display-area');
            displayArea.classList.remove('fade-slide-in');
            void displayArea.offsetWidth; 
            displayArea.classList.add('fade-slide-in');

            updateDisplay(); 
        }

        function fetchTimers() {
            $.post('beruntung/api_timer.php', { action: 'load' }, function(data) {
                if (data && data.timers) {
                    for (const id in data.timers) {
                        const tNew = data.timers[id];
                        const tOld = timers[id];

                        if (tOld && tNew.cabang === currentPage) {
                            let timeAdded = 0;
                            const isNewRunning = isTimerRunning(tNew.isRunning);
                            const isOldRunning = isTimerRunning(tOld.isRunning);
                            
                            if (isNewRunning && isOldRunning) {
                                timeAdded = Math.round((parseInt(tNew.endTime) - parseInt(tOld.endTime)) / 1000);
                            } else if (!isNewRunning && !isOldRunning) {
                                timeAdded = Math.round(parseInt(tNew.timeLeft) - parseInt(tOld.timeLeft));
                            }

                            if (timeAdded >= 60) {
                                showAddedTimeAlert(tNew.noMeja, tNew.judulMeja, timeAdded);
                                warnedTables[id] = 0;
                            }
                        }
                    }

                    timers = data.timers;
                    updateDisplay();
                }
            }, 'json');
        }

        function updateDisplay() {
            let htmlContent = '';
            const now = Date.now();
            let timersList = [];

            for (const id in timers) {
                if (timers[id].cabang === currentPage) {
                    let tData = Object.assign({}, timers[id]);
                    tData.originalId = id; 
                    timersList.push(tData);
                }
            }

            timersList.sort((a, b) => (parseInt(a.noMeja) || 0) - (parseInt(b.noMeja) || 0));
            let countMeja = timersList.length;

            timersList.forEach(t => {
                const id = t.originalId; 
                let statusHtml = '';
                let cardClass = ''; 
                
                const endTimeInt = parseInt(t.endTime) || 0;
                const timeLeftInt = parseInt(t.timeLeft) || 0;
                const running = isTimerRunning(t.isRunning);
                const isActive = t.isActive !== false; // Cek status aktif/nonaktif dari database

                // Logika Tampilan Berdasarkan Status Aktif & Waktu
                if (!isActive) {
                    cardClass = 'inactive-card';
                    statusHtml = `
                        <div class="badge-meja">${t.noMeja}</div>
                        <div class="icon-status">
                            <i class="fas fa-ban icon-inactive"></i>
                        </div>
                        <div class="text-nonaktif">NONAKTIF</div>
                        <div class="text-subtitle">${t.judulMeja}</div>
                    `;
                } else if (running || timeLeftInt > 0) {
                    let endTimeStr = "-- : --";
                    let currentDisplayTime = 0;
                    
                    if (running) {
                        const end = new Date(endTimeInt);
                        endTimeStr = `${end.getHours().toString().padStart(2, '0')} : ${end.getMinutes().toString().padStart(2, '0')}`;
                        currentDisplayTime = Math.max(0, Math.round((endTimeInt - now) / 1000));

                        if (currentDisplayTime <= 120 && currentDisplayTime > 115) {
                            if (warnedTables[id] !== endTimeInt) {
                                speakWarning(t.noMeja, t.judulMeja, t.cabang);
                                warnedTables[id] = endTimeInt; 
                            }
                        }
                    } else {
                        const end = new Date(now + (timeLeftInt * 1000));
                        endTimeStr = `${end.getHours().toString().padStart(2, '0')} : ${end.getMinutes().toString().padStart(2, '0')} (JEDA)`;
                        currentDisplayTime = timeLeftInt;
                    }

                    cardClass = 'active-card';
                    statusHtml = `
                        <div class="badge-meja">${t.noMeja}</div>
                        <div class="icon-status">
                            <i class="fab fa-playstation icon-active"></i>
                        </div>
                        <div class="text-countdown">${formatTime(currentDisplayTime)}</div>
                        <div class="text-selesai">SELESAI PUKUL ${endTimeStr}</div>
                        <div class="text-subtitle">${t.judulMeja}</div>
                    `;
                } else {
                    cardClass = ''; 
                    statusHtml = `
                        <div class="badge-meja">${t.noMeja}</div>
                        <div class="icon-status" style="margin-bottom: 8px;">
                            <i class="fas fa-check icon-check"></i>
                        </div>
                        <div class="text-tersedia">TERSEDIA</div>
                        <div class="text-subtitle">${t.judulMeja}</div>
                    `;
                }

                htmlContent += `<div class="card-display ${cardClass}">${statusHtml}</div>`;
            });

            if (countMeja === 0) {
                htmlContent = `
                    <div style="grid-column: 1 / -1; text-align: center; margin-top: 50px;">
                        <i class="fas fa-box-open mb-3" style="font-size: 40px; color: #6c757d;"></i>
                        <h5 class="text-secondary" style="font-family: 'Montserrat', sans-serif;">Belum ada perangkat ditambahkan pada cabang ini.</h5>
                    </div>
                `;
            }

            $('#display-area').html(htmlContent);
        }

        $(document).ready(function() {
            fetchTimers(); 
            setInterval(fetchTimers, 3000); 
            setInterval(updateDisplay, 1000); 

            $(document).on('click', function() {
                if (window.speechSynthesis && window.speechSynthesis.getVoices().length > 0) {
                    let unlockUtterance = new SpeechSynthesisUtterance('');
                    window.speechSynthesis.speak(unlockUtterance);
                    $(document).off('click'); 
                }
            });
        });
    </script>
</body>
</html>