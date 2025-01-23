<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Una moderna aplicación de cámara web con múltiples funciones">
    <title>📸 Modern Camera App</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --theme-color: #198754;
            --theme-hover: #157347;
        }

        body {
            margin: 0;
            padding: 0;
            height: 100vh;
            background-color: #000;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .camera-container {
            position: relative;
            height: 100vh;
            width: 100vw;
        }

        #camera--view,
        #camera--sensor,
        #camera--output {
            position: fixed;
            height: 100%;
            width: 100%;
            object-fit: cover;
            transition: filter 0.3s ease;
        }

        #camera--view.mirror {
            transform: scaleX(-1);
        }

        .controls-panel {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 20px;
            background: linear-gradient(to top, rgba(0, 0, 0, 0.9), transparent);
            transition: transform 0.3s ease;
        }

        .settings-panel {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            padding: 20px;
            background: linear-gradient(to bottom, rgba(0, 0, 0, 0.9), transparent);
            z-index: 100;
            display: none;
        }

        .settings-panel.show {
            display: block;
        }

        .capture-btn {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background-color: var(--theme-color);
            border: 3px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.3);
        }

        .capture-btn:hover {
            transform: scale(1.1);
            background-color: var(--theme-hover);
            border-color: rgba(255, 255, 255, 0.4);
        }

        .timer-btn {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid rgba(255, 255, 255, 0.2);
            color: white;
            transition: all 0.3s ease;
            margin-right: 10px;
        }

        .timer-btn:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: scale(1.1);
        }

        .timer-btn.active {
            background-color: var(--theme-color);
            border-color: white;
        }

        .gallery-item {
            width: 70px;
            height: 70px;
            object-fit: cover;
            cursor: pointer;
            transition: all 0.3s ease;
            border-radius: 10px;
            border: 2px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
        }

        .gallery-item:hover {
            transform: scale(1.1);
            border-color: var(--theme-color);
        }

        #timer-display {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 8rem;
            color: white;
            text-shadow: 0 0 20px rgba(0, 0, 0, 0.5);
            z-index: 1000;
        }

        #gallery {
            position: fixed;
            top: 20px;
            right: 20px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            z-index: 1000;
            padding: 10px;
            background: rgba(0, 0, 0, 0.3);
            border-radius: 15px;
            backdrop-filter: blur(5px);
        }

        .control-btn {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid rgba(255, 255, 255, 0.2);
            color: white;
            transition: all 0.3s ease;
        }

        .control-btn:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: scale(1.1);
        }

        .form-select {
            background-color: rgba(0, 0, 0, 0.5) !important;
            border: 1px solid rgba(255, 255, 255, 0.2) !important;
            color: white !important;
            backdrop-filter: blur(5px);
        }

        .form-select option {
            background-color: #1a1a1a;
        }

        .quality-slider {
            width: 100%;
            height: 5px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 5px;
            outline: none;
            -webkit-appearance: none;
        }

        .quality-slider::-webkit-slider-thumb {
            -webkit-appearance: none;
            width: 15px;
            height: 15px;
            background: var(--theme-color);
            border-radius: 50%;
            cursor: pointer;
        }

        .flash-effect {
            position: fixed;
            inset: 0;
            background: white;
            opacity: 0;
            pointer-events: none;
            z-index: 9999;
            animation: flash 0.3s ease-out;
        }

        .gallery-modal {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.9);
            z-index: 2000;
            display: none;
            justify-content: center;
            align-items: center;
        }

        .gallery-modal.show {
            display: flex;
        }

        .gallery-modal img {
            max-width: 95%;
            max-height: 95vh;
            object-fit: contain;
        }

        .gallery-modal .close-btn {
            position: absolute;
            top: 20px;
            right: 20px;
            color: white;
            font-size: 2rem;
            cursor: pointer;
            background: none;
            border: none;
        }

        @keyframes flash {
            0% { opacity: 0.8; }
            100% { opacity: 0; }
        }

        .toast-notification {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(25, 135, 84, 0.9);
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            z-index: 9999;
            backdrop-filter: blur(5px);
            animation: toast 0.3s ease-out;
        }

        @keyframes toast {
            from {
                transform: translateX(-50%) translateY(-20px);
                opacity: 0;
            }
            to {
                transform: translateX(-50%) translateY(0);
                opacity: 1;
            }
        }
    </style>
</head>
<body>
    <div id="settings-panel" class="settings-panel">
        <div class="container">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="text-white mb-2">Resolución</label>
                    <select id="resolution-select" class="form-select">
                        <option value="hd">HD (1280x720)</option>
                        <option value="fhd" selected>Full HD (1920x1080)</option>
                        <option value="4k">4K (3840x2160)</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="text-white mb-2">Formato</label>
                    <select id="format-select" class="form-select">
                        <option value="jpeg" selected>JPEG</option>
                        <option value="png">PNG</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="text-white mb-2">Calidad <span id="quality-value">90%</span></label>
                    <input type="range" class="quality-slider" id="quality-slider" min="1" max="100" value="90">
                </div>
            </div>
        </div>
    </div>

    <main id="camera" class="camera-container">
        <canvas id="camera--sensor"></canvas>
        <video id="camera--view" autoplay playsinline></video>
        <img src="//:0" alt="" id="camera--output">

        <div id="timer-display" class="d-none"></div>

        <div class="controls-panel">
            <div class="container">
                <div class="row justify-content-center align-items-center g-3">
                    <div class="col-4">
                        <select id="filter-select" class="form-select">
                            <option value="none">Normal</option>
                            <option value="grayscale(1)">Blanco y Negro</option>
                            <option value="sepia(1)">Sepia</option>
                            <option value="brightness(1.2) contrast(1.2)">Brillo</option>
                            <option value="contrast(1.4)">Alto Contraste</option>
                            <option value="hue-rotate(90deg)">Color Frío</option>
                            <option value="hue-rotate(180deg)">Color Cálido</option>
                            <option value="saturate(2)">Saturado</option>
                            <option value="blur(2px)">Suave</option>
                            <option value="brightness(1.1) contrast(1.1) saturate(1.2)">Vibrante</option>
                        </select>
                    </div>

                    <div class="col-4 text-center">
                        <div class="d-flex justify-content-center align-items-center mb-2">
                            <button id="timer-mode" class="timer-btn" title="Temporizador 3s">
                                <i class="fas fa-clock"></i>
                            </button>
                            <button id="burst-mode" class="timer-btn" title="Modo Ráfaga">
                                <i class="fas fa-bolt"></i>
                            </button>
                        </div>
                        <button id="camera--trigger" class="capture-btn">
                            <i class="fas fa-camera text-white fs-4"></i>
                        </button>
                    </div>

                    <div class="col-4 text-end">
                        <button id="camera-switch" class="control-btn me-2">
                            <i class="fas fa-sync"></i>
                        </button>
                        <button id="settings-toggle" class="control-btn">
                            <i class="fas fa-cog"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div id="gallery"></div>
        
        <!-- Modal for gallery images -->
        <div class="gallery-modal" id="gallery-modal">
            <button class="close-btn"><i class="fas fa-times"></i></button>
            <img src="" alt="Preview" />
        </div>
    </main>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>

    <script>
        window.addEventListener('load', function() {
            const TIMER_SECONDS = 3;
            let currentStream = null;
            let facingMode = "user";
            let burstMode = false;
            let timerMode = false;

            // DOM Elements
            const cameraView = document.querySelector("#camera--view");
            const cameraSensor = document.querySelector("#camera--sensor");
            const cameraTrigger = document.querySelector("#camera--trigger");
            const cameraSwitch = document.querySelector("#camera-switch");
            const gallery = document.querySelector("#gallery");
            const filterSelect = document.querySelector("#filter-select");
            const settingsToggle = document.querySelector("#settings-toggle");
            const settingsPanel = document.querySelector("#settings-panel");
            const qualitySlider = document.querySelector("#quality-slider");
            const qualityValue = document.querySelector("#quality-value");
            const formatSelect = document.querySelector("#format-select");
            const resolutionSelect = document.querySelector("#resolution-select");
            const timerDisplay = document.querySelector("#timer-display");
            const burstButton = document.querySelector("#burst-mode");
            const timerButton = document.querySelector("#timer-mode");

            // Mode buttons functionality
            burstButton.addEventListener('click', () => {
                burstMode = !burstMode;
                timerMode = false;
                burstButton.classList.toggle('active');
                timerButton.classList.remove('active');
            });

            timerButton.addEventListener('click', () => {
                timerMode = !timerMode;
                burstMode = false;
                timerButton.classList.toggle('active');
                burstButton.classList.remove('active');
            });

            // Settings toggle
            settingsToggle.addEventListener('click', () => {
                settingsPanel.classList.toggle('show');
                settingsToggle.querySelector('i').classList.toggle('fa-spin');
            });

            // Quality slider
            qualitySlider.addEventListener('input', () => {
                qualityValue.textContent = `${qualitySlider.value}%`;
            });

            // Resolution constraints
            function getResolutionConstraints() {
                const resolution = resolutionSelect.value;
                switch (resolution) {
                    case '4k':
                        return { width: { ideal: 3840 }, height: { ideal: 2160 } };
                    case 'fhd':
                        return { width: { ideal: 1920 }, height: { ideal: 1080 } };
                    case 'hd':
                        return { width: { ideal: 1280 }, height: { ideal: 720 } };
                    default:
                        return { width: { ideal: 1920 }, height: { ideal: 1080 } };
                }
            }

            // Start camera
            async function startCamera() {
                const constraints = {
                    video: {
                        facingMode: facingMode,
                        ...getResolutionConstraints()
                    },
                    audio: false
                };

                try {
                    if (currentStream) {
                        currentStream.getTracks().forEach(track => track.stop());
                    }

                    const stream = await navigator.mediaDevices.getUserMedia(constraints);
                    currentStream = stream;
                    cameraView.srcObject = stream;
                    cameraView.classList.toggle('mirror', facingMode === "user");
                } catch (err) {
                    showToast('Error al acceder a la cámara: ' + err.message, 'error');
                }
            }

            // Toast notifications
            function showToast(message, type = 'success') {
                const toast = document.createElement('div');
                toast.className = `toast-notification ${type}`;
                toast.textContent = message;
                document.body.appendChild(toast);
                setTimeout(() => toast.remove(), 3000);
            }

            // Capture photo
            async function capturePhoto() {
                cameraSensor.width = cameraView.videoWidth;
                cameraSensor.height = cameraView.videoHeight;
                const context = cameraSensor.getContext('2d');

                if (facingMode === "user") {
                    context.translate(cameraSensor.width, 0);
                    context.scale(-1, 1);
                }

                context.filter = filterSelect.value === 'none' ? '' : filterSelect.value;
                context.drawImage(cameraView, 0, 0);
                context.setTransform(1, 0, 0, 1, 0, 0);

                const format = formatSelect.value;
                if (format === 'jpeg') {
                    const quality = qualitySlider.value / 100;
                    return cameraSensor.toDataURL('image/jpeg', quality);
                } else {
                    return cameraSensor.toDataURL('image/png');
                }
            }

            // Add photo to gallery
            function addToGallery(imageData) {
                const container = document.createElement('div');
                container.className = 'position-relative d-inline-block';

                const img = document.createElement('img');
                img.src = imageData;
                img.className = 'gallery-item';

                const deleteBtn = document.createElement('button');
                deleteBtn.className = 'position-absolute top-0 end-0 m-1 border-0 bg-transparent';
                deleteBtn.innerHTML = '<i class="fas fa-trash text-danger" style="font-size: 14px;"></i>';
                deleteBtn.onclick = (e) => {
                    e.stopPropagation();
                    container.remove();
                };

                const downloadBtn = document.createElement('button');
                downloadBtn.className = 'position-absolute top-0 start-0 m-1 border-0 bg-transparent';
                downloadBtn.innerHTML = '<i class="fas fa-download text-white" style="font-size: 14px;"></i>';
                downloadBtn.onclick = (e) => {
                    e.stopPropagation();
                    handleDownload(imageData);
                };

                const modal = document.querySelector('.gallery-modal');
                const modalImg = modal.querySelector('img');
                const modalCloseBtn = modal.querySelector('.close-btn');

                modalCloseBtn.onclick = () => modal.classList.remove('show');
                modal.onclick = (e) => {
                    if (e.target === modal) modal.classList.remove('show');
                };

                img.onclick = () => {
                    modalImg.src = imageData;
                    modal.classList.add('show');
                };

                container.appendChild(img);
                container.appendChild(deleteBtn);
                container.appendChild(downloadBtn);
                gallery.insertBefore(container, gallery.firstChild);

                while (gallery.children.length > 5) {
                    gallery.removeChild(gallery.lastChild);
                }

                const flash = document.createElement('div');
                flash.className = 'flash-effect';
                document.body.appendChild(flash);
                setTimeout(() => flash.remove(), 300);
            }

            // Handle download
            function handleDownload(imageData) {
                const format = formatSelect.value;
                const byteString = atob(imageData.split(',')[1]);
                const mimeString = `image/${format}`;
                const ab = new ArrayBuffer(byteString.length);
                const ia = new Uint8Array(ab);
                for (let i = 0; i < byteString.length; i++) {
                    ia[i] = byteString.charCodeAt(i);
                }
                const blob = new Blob([ab], { type: mimeString });
                const blobUrl = URL.createObjectURL(blob);

                const link = document.createElement('a');
                const fileName = `photo_${new Date().toISOString().replace(/[:.]/g, '-')}.${format}`;
                link.download = fileName;
                link.href = blobUrl;
                link.click();
                URL.revokeObjectURL(blobUrl);
                showToast(`Imagen guardada como: ${fileName}`);
            }

            // Burst mode photos
            async function takeBurstPhotos() {
                for (let i = 0; i < 4; i++) {
                    try {
                        const imageData = await capturePhoto();
                        if (imageData) {
                            addToGallery(imageData);
                            await new Promise(resolve => setTimeout(resolve, 200));
                        }
                    } catch (err) {
                        showToast('Error en modo ráfaga: ' + err.message, 'error');
                        break;
                    }
                }
            }

            // Handle capture process
            async function handleCapture() {
                const takePhoto = async () => {
                    try {
                        const imageData = await capturePhoto();
                        if (imageData) {
                            addToGallery(imageData);
                        }
                    } catch (err) {
                        showToast('Error al capturar foto: ' + err.message, 'error');
                    }
                };

                if (timerMode) {
                    timerDisplay.classList.remove('d-none');
                    let countdown = TIMER_SECONDS;

                    const timerInterval = setInterval(() => {
                        countdown--;
                        timerDisplay.textContent = countdown;

                        if (countdown <= 0) {
                            clearInterval(timerInterval);
                            timerDisplay.classList.add('d-none');
                            if (burstMode) {
                                takeBurstPhotos();
                            } else {
                                takePhoto();
                            }
                        }
                    }, 1000);

                    timerDisplay.textContent = TIMER_SECONDS;
                } else {
                    if (burstMode) {
                        takeBurstPhotos();
                    } else {
                        await takePhoto();
                    }
                }
            }

            // Keyboard controls
            document.addEventListener('keydown', (e) => {
                if (e.code === 'Space' && !e.repeat) {
                    e.preventDefault();
                    handleCapture();
                } else if (e.code === 'Escape') {
                    const modal = document.querySelector('.gallery-modal');
                    if (modal.classList.contains('show')) {
                        modal.classList.remove('show');
                    }
                }
            });

            // Event Listeners
            cameraTrigger.onclick = handleCapture;
            cameraSwitch.onclick = () => {
                facingMode = facingMode === "user" ? "environment" : "user";
                startCamera();
            };
            filterSelect.onchange = () => {
                cameraView.style.filter = filterSelect.value === 'none' ? '' : filterSelect.value;
            };
            resolutionSelect.onchange = startCamera;

            // Initialize camera
            if ('mediaDevices' in navigator && 'getUserMedia' in navigator.mediaDevices) {
                startCamera();
            } else {
                showToast('Tu navegador no soporta el acceso a la cámara', 'error');
            }
        });
    </script>
</body>
</html>