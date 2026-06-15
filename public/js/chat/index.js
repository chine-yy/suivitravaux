(function () {
    const chatForm = document.getElementById('chatForm');
    const messageInput = document.getElementById('messageInput');
    const imageInput = document.getElementById('imageInput');
    const audioInput = document.getElementById('audioInput');
    const messagesList = document.getElementById('messagesList');
    const messageContainer = document.getElementById('messageContainer');
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    const currentUserId = messagesList?.dataset.currentUserId || '';
    const currentUserType = messagesList?.dataset.currentUserType || '';

    let mediaRecorder;
    let audioChunks = [];
    let startTime;
    let timerInterval;
    let streamReference = null;
    let isSendingAudio = false;
    let pollingTimer = null;

    const startBtn = document.getElementById('startRecording');
    const stopBtn = document.getElementById('stopAndSend');
    const cancelBtn = document.getElementById('cancelRecording');
    const recordUI = document.getElementById('recordingUI');
    const timerDisplay = document.getElementById('recordingTimer');

    function escapeHtml(value) {
        const div = document.createElement('div');
        div.textContent = value ?? '';
        return div.innerHTML;
    }

    function getLastMessageId() {
        if (!messagesList) return 0;
        const last = messagesList.querySelector('[data-id]:last-of-type');
        return last ? parseInt(last.getAttribute('data-id'), 10) || 0 : 0;
    }

    function scrollToBottom() {
        if (messageContainer) {
            messageContainer.scrollTop = messageContainer.scrollHeight;
        }
    }

    function formatTime(dateValue) {
        const date = dateValue ? new Date(dateValue) : new Date();
        return date.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
    }

    function buildMessageHtml(msg) {
        const isMe = String(msg.sender_id) === String(currentUserId) && String(msg.sender_type) === String(currentUserType);

        const imageHtml = msg.image_path
            ? `<div class="mb-2"><img src="/storage/${escapeHtml(msg.image_path)}" class="img-fluid rounded-3 message-img" alt="Image" style="max-height: 300px; cursor: pointer;" onclick="window.open(this.src)"></div>`
            : '';

        const audioHtml = msg.audio_path
            ? `<div class="mb-2"><audio controls class="mw-100 message-audio" style="height: 35px;"><source src="/storage/${escapeHtml(msg.audio_path)}" type="audio/mpeg">Votre navigateur ne supporte pas l'audio.</audio></div>`
            : '';

        const textHtml = msg.message ? `<p class="mb-1">${escapeHtml(msg.message)}</p>` : '';
        const checksHtml = isMe
            ? `<i class="bi ${msg.is_read ? 'bi-check2-all text-info' : 'bi-check2'} ms-1"></i>`
            : '';

        return `
            <div class="d-flex mb-3 ${isMe ? 'justify-content-end' : 'justify-content-start'}" data-id="${msg.id}">
                <div class="message-bubble ${isMe ? 'bubble-me' : 'bubble-them'} shadow-sm">
                    ${imageHtml}
                    ${audioHtml}
                    ${textHtml}
                    <div class="text-end message-time">${formatTime(msg.created_at)} ${checksHtml}</div>
                </div>
            </div>
        `;
    }

    function appendMessage(msg) {
        if (!messagesList || !msg || !msg.id) return;
        if (messagesList.querySelector(`[data-id="${msg.id}"]`)) return;

        messagesList.insertAdjacentHTML('beforeend', buildMessageHtml(msg));
        scrollToBottom();
    }

    async function sendMessageAjax() {
        if (!chatForm) return;

        const hasMsg = messageInput && messageInput.value && messageInput.value.trim() !== '';
        const hasImg = imageInput && imageInput.files && imageInput.files.length > 0;
        const hasAudio = audioInput && audioInput.files && audioInput.files.length > 0;

        if (!hasMsg && !hasImg && !hasAudio) {
            return;
        }

        const submitBtn = chatForm.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
        }

        try {
            const formData = new FormData(chatForm);

            const response = await fetch(chatForm.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: formData
            });

            const payload = await response.json();
            if (!response.ok || payload.error) {
                throw new Error(payload.error || payload.message || 'Erreur lors de l\'envoi du message');
            }

            if (payload.message) {
                appendMessage(payload.message);
            }

            if (messageInput) messageInput.value = '';
            clearFiles();
        } catch (err) {
            console.error(err);
            alert(err.message || 'Impossible d\'envoyer le message.');
        } finally {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="bi bi-send-fill fs-5"></i>';
            }
        }
    }

    async function pollNewMessages() {
        if (!messagesList) return;

        const lastId = getLastMessageId();
        const separator = window.location.search ? '&' : '?';
        const url = `${window.location.pathname}${window.location.search}${separator}last_id=${lastId}`;

        try {
            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) return;
            const payload = await response.json();
            if (!payload || !Array.isArray(payload.messages)) return;

            payload.messages.forEach(appendMessage);
        } catch (err) {
            console.error('Polling chat error:', err);
        }
    }

    if (chatForm) {
        chatForm.addEventListener('submit', function (e) {
            e.preventDefault();
            sendMessageAjax();
        });
    }

    if (messagesList) {
        scrollToBottom();
        pollingTimer = setInterval(pollNewMessages, 3000);
    }

    if (startBtn) {
        startBtn.addEventListener('click', async () => {
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                alert('Votre navigateur ne supporte pas l\'enregistrement audio.');
                return;
            }

            try {
                const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                streamReference = stream;

                mediaRecorder = new MediaRecorder(stream);
                audioChunks = [];

                mediaRecorder.ondataavailable = (event) => {
                    if (event.data.size > 0) {
                        audioChunks.push(event.data);
                    }
                };

                mediaRecorder.onstop = async () => {
                    if (streamReference) {
                        streamReference.getTracks().forEach(track => track.stop());
                    }

                    if (isSendingAudio && audioChunks.length > 0) {
                        const blobType = audioChunks[0].type || 'audio/webm';
                        const audioBlob = new Blob(audioChunks, { type: blobType });
                        const ext = blobType.includes('wav') ? 'wav' : (blobType.includes('ogg') ? 'ogg' : 'webm');
                        const file = new File([audioBlob], `vocal.${ext}`, { type: blobType });

                        const dataTransfer = new DataTransfer();
                        dataTransfer.items.add(file);
                        audioInput.files = dataTransfer.files;

                        sendMessageAjax();
                    }

                    resetRecordingUI();
                };

                isSendingAudio = false;
                mediaRecorder.start();
                recordUI.classList.remove('d-none');
                recordUI.classList.add('d-flex');

                startTime = Date.now();
                updateTimer();
                timerInterval = setInterval(updateTimer, 1000);
            } catch (err) {
                console.error('Microphone error:', err);
                alert('Impossible d\'accéder au microphone. Vérifiez les permissions.');
            }
        });
    }

    if (cancelBtn) {
        cancelBtn.addEventListener('click', () => {
            if (mediaRecorder && mediaRecorder.state !== 'inactive') {
                isSendingAudio = false;
                mediaRecorder.stop();
            }
        });
    }

    if (stopBtn) {
        stopBtn.addEventListener('click', () => {
            if (mediaRecorder && mediaRecorder.state !== 'inactive') {
                isSendingAudio = true;
                mediaRecorder.stop();
            }
        });
    }

    function updateTimer() {
        const elapsed = Math.floor((Date.now() - startTime) / 1000);
        const mins = String(Math.floor(elapsed / 60)).padStart(2, '0');
        const secs = String(elapsed % 60).padStart(2, '0');
        timerDisplay.textContent = `${mins}:${secs}`;
    }

    function resetRecordingUI() {
        if (!recordUI || !timerDisplay) return;
        recordUI.classList.add('d-none');
        recordUI.classList.remove('d-flex');
        clearInterval(timerInterval);
        timerDisplay.textContent = '00:00';
    }

    function handleFileSelect(input, type) {
        const file = input.files[0];
        const preview = document.getElementById('filePreview');
        const fileName = document.getElementById('fileName');
        const imgPreview = document.getElementById('imagePreview');
        const imgContainer = document.getElementById('imagePreviewContainer');

        if (!preview || !fileName || !imgPreview || !imgContainer) return;

        if (file) {
            fileName.textContent = file.name;
            preview.classList.remove('d-none');
            preview.classList.add('d-flex');

            if (type === 'image' && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    imgPreview.src = e.target.result;
                    imgContainer.classList.remove('d-none');
                };
                reader.readAsDataURL(file);
            } else {
                imgContainer.classList.add('d-none');
            }
        }
    }

    function clearFiles() {
        if (imageInput) imageInput.value = '';
        if (audioInput) audioInput.value = '';

        const preview = document.getElementById('filePreview');
        const imgContainer = document.getElementById('imagePreviewContainer');
        const imagePreview = document.getElementById('imagePreview');

        if (preview) {
            preview.classList.add('d-none');
            preview.classList.remove('d-flex');
        }

        if (imgContainer) imgContainer.classList.add('d-none');
        if (imagePreview) imagePreview.src = '';
    }

    window.handleFileSelect = handleFileSelect;
    window.clearFiles = clearFiles;

    window.addEventListener('beforeunload', () => {
        if (pollingTimer) clearInterval(pollingTimer);
    });
})();
