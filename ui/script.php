<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

    <script>
        AOS.init({
            once: true,
            offset: 100
        });

        let currentPage = 'gambut';
        let timers = {}; 

        function isTimerRunning(val) {
            return val == 1 || val === '1' || val === true || val === 'true';
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
            updateDisplay(); 
        }

        function fetchTimers() {
            $.post('reservasi/api_timer.php', { action: 'load' }, function(data) {
                if (data && data.timers) {
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

            timersList.sort((a, b) => {
                return (parseInt(a.noMeja) || 0) - (parseInt(b.noMeja) || 0);
            });

            let countMeja = timersList.length;

            timersList.forEach(t => {
                let statusHtml = '';
                let cardClass = ''; 
                
                const endTimeInt = parseInt(t.endTime) || 0;
                const timeLeftInt = parseInt(t.timeLeft) || 0;
                const running = isTimerRunning(t.isRunning);

                if (running || timeLeftInt > 0) {
                    let endTimeStr = "-- : --";
                    let currentDisplayTime = 0;
                    
                    if (running) {
                        const end = new Date(endTimeInt);
                        const h = end.getHours().toString().padStart(2, '0');
                        const m = end.getMinutes().toString().padStart(2, '0');
                        endTimeStr = `${h} : ${m}`;
                        currentDisplayTime = Math.max(0, Math.round((endTimeInt - now) / 1000));
                    } else {
                        const end = new Date(now + (timeLeftInt * 1000));
                        const h = end.getHours().toString().padStart(2, '0');
                        const m = end.getMinutes().toString().padStart(2, '0');
                        endTimeStr = `${h} : ${m} (JEDA)`;
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
                        <div class="icon-status">
                            <i class="fas fa-check-circle icon-check"></i>
                        </div>
                        <div class="text-tersedia">TERSEDIA</div>
                        <div class="text-subtitle">${t.judulMeja}</div>
                    `;
                }

                htmlContent += `
                    <div class="card-display ${cardClass}">
                        ${statusHtml}
                    </div>
                `;
            });

            if (countMeja === 0) {
                htmlContent = `
                    <div style="grid-column: 1 / -1; text-align: center; margin-top: 50px;">
                        <i class="fas fa-box-open mb-3" style="font-size: 40px; color: #ccc;"></i>
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
        });

        $(document).ready(function() {
            var totalSlides = $('#arayaHeroCarousel .carousel-item').length;
            $('#arayaHeroCarousel').on('slide.bs.carousel', function (e) {
                var currentSlide = $(e.relatedTarget).index() + 1;
                $('#carouselCounter').text(currentSlide + ' / ' + totalSlides);
            });
        });

        const mobileMenuButton=document.getElementById('mobileMenuButton');
    const mobileMenuClose=document.getElementById('mobileMenuClose');
    const mobileMenuOverlay=document.getElementById('mobileMenuOverlay');
    const mobileSearchButton=document.getElementById('mobileSearchButton');
    const mobileSearchClose=document.getElementById('mobileSearchClose');
    const mobileSearchInput=document.getElementById('mobileSearchInput');
    function closeMobilePanels(){document.body.classList.remove('mobile-menu-open','mobile-search-open','mobile-notification-open');}
    if(mobileMenuButton)mobileMenuButton.addEventListener('click',function(){document.body.classList.remove('mobile-search-open','mobile-notification-open');document.body.classList.toggle('mobile-menu-open');});
    if(mobileMenuClose)mobileMenuClose.addEventListener('click',closeMobilePanels);
    if(mobileMenuOverlay)mobileMenuOverlay.addEventListener('click',closeMobilePanels);
    document.querySelectorAll('.mobile-menu-links a').forEach(function(link){link.addEventListener('click',closeMobilePanels);});
    if(mobileSearchButton)mobileSearchButton.addEventListener('click',function(){document.body.classList.remove('mobile-menu-open','mobile-notification-open');document.body.classList.add('mobile-search-open');setTimeout(function(){if(mobileSearchInput)mobileSearchInput.focus();},150);});
    if(mobileSearchClose)mobileSearchClose.addEventListener('click',closeMobilePanels);
    if(mobileSearchInput)mobileSearchInput.addEventListener('input',function(){const keyword=this.value.toLowerCase().trim();document.querySelectorAll('.mobile-search-results a').forEach(function(item){const text=(item.innerText+' '+item.dataset.search).toLowerCase();item.style.display=!keyword||text.includes(keyword)?'flex':'none';});});
    document.querySelectorAll('.mobile-search-results a').forEach(function(link){link.addEventListener('click',closeMobilePanels);});
    document.addEventListener('keydown',function(event){if(event.key==='Escape')closeMobilePanels();});

    function toggleWa() {
        const options = document.getElementById('wa-options');
        if (options.style.display === 'flex') {
            options.style.display = 'none';
        } else {
            options.style.display = 'flex';
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
            const toggleButtons = document.querySelectorAll('.btn-selengkapnya');
            
            toggleButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const card = this.closest('.card');
                    const expandableContent = card.querySelector('.expandable-pricelist');
                    const textSpan = this.querySelector('span');
                    
                    this.classList.toggle('active');
                    
                    if (expandableContent.classList.contains('expanded')) {
                        expandableContent.classList.remove('expanded');
                        textSpan.textContent = 'Selengkapnya';
                    } else {
                        expandableContent.classList.add('expanded');
                        textSpan.textContent = 'Lebih Sedikit';
                    }
                });
            });
        });
    </script>
</body>
</html>