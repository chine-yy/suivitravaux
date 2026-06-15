<?php $__env->startSection('title', 'IA Chat Box - Assistant'); ?>

<?php $__env->startSection('content'); ?>
<style>
.ia-chat-container {
    height: calc(100vh - 150px);
    display: flex;
    flex-direction: row;
    overflow: hidden;
}
.ia-chat-sidebar {
    width: 300px;
    height: 100%;
    border-right: 1px solid #e9ecef;
    background: #f8f9fa;
    display: flex;
    flex-direction: column;
}
.ia-chat-main {
    flex: 1;
    display: flex;
    flex-direction: column;
    height: 100%;
    overflow: hidden;
}
.ia-chat-messages {
    flex: 1;
    overflow-y: auto;
    padding: 1.5rem;
    background: linear-gradient(135deg, #009A44 0%, #007a35 100%);
    scroll-behavior: smooth;
}
.message-bubble {
    max-width: 80%;
    padding: 0.85rem 1.25rem;
    border-radius: 1.25rem;
    margin-bottom: 1rem;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}
.message-user {
    background: #ffffff;
    color: #333;
    margin-left: auto;
    border-bottom-right-radius: 0.25rem;
}
.message-assistant {
    background: #fdf2f2;
    color: #333;
    margin-right: auto;
    border-bottom-left-radius: 0.25rem;
}
/* ... rest of your styles ... */
.typing-indicator span {
    display: inline-block;
    width: 8px;
    height: 8px;
    background: #999;
    border-radius: 50%;
    margin: 0 2px;
    animation: typing 1.4s infinite;
}
.typing-indicator span:nth-child(2) { animation-delay: 0.2s; }
.typing-indicator span:nth-child(3) { animation-delay: 0.4s; }
@keyframes typing {
    0%, 60%, 100% { transform: translateY(0); }
    30% { transform: translateY(-5px); }
}
.new-chat-btn {
    background: linear-gradient(135deg, #009A44 0%, #007a35 100%);
    color: white;
    border: none;
    padding: 0.75rem 1rem;
    border-radius: 0.75rem;
    cursor: pointer;
    width: 100%;
    font-weight: 600;
    transition: all 0.3s;
}
.new-chat-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 154, 68, 0.3);
}
.conversation-item {
    padding: 1rem;
    cursor: pointer;
    border-bottom: 1px solid #eee;
    transition: background 0.2s;
}
.conversation-item:hover {
    background: #f0f0f0;
}
.conversation-item.active {
    background: #fff;
    border-left: 4px solid #009A44;
    box-shadow: inset 0 0 10px rgba(0,0,0,0.05);
}
.send-btn {
    background: linear-gradient(135deg, #009A44 0%, #007a35 100%);
    border: none;
    color: white;
    padding: 0.5rem 1.5rem;
    border-radius: 0.75rem;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.3s;
}
.send-btn:hover {
    transform: scale(1.05);
}
.input-area {
    padding: 1.25rem;
    background: white;
    border-top: 1px solid #eee;
}
.db-query-section {
    background: #1e1e1e;
    color: #d4d4d4;
    padding: 1rem;
    border-radius: 0.5rem;
    margin-top: 0.5rem;
    font-family: monospace;
    font-size: 0.85rem;
}
.query-result {
    background: #2d2d2d;
    padding: 0.5rem;
    border-radius: 0.25rem;
    margin-top: 0.5rem;
    overflow-x: auto;
}
</style>

<div class="container-fluid py-3">
    <div class="row ia-chat-container bg-white rounded-4 shadow-lg overflow-hidden">
        <!-- Sidebar -->
        <div class="ia-chat-sidebar">
            <div class="p-3 border-bottom">
                <button class="new-chat-btn" onclick="createNewConversation()">
                    <i class="bi bi-plus-lg me-2"></i>Nouvelle conversation
                </button>
            </div>
            <div class="flex-grow-1 overflow-auto">
                <?php $__empty_1 = true; $__currentLoopData = $conversations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $conversation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="conversation-item <?php echo e($selectedConversation == (is_object($conversation) ? $conversation->id : $conversation['id']) ? 'active' : ''); ?>" 
                         onclick="selectConversation(<?php echo e(is_object($conversation) ? $conversation->id : $conversation['id']); ?>)">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1 overflow-hidden">
                                <div class="fw-bold small text-truncate"><?php echo e(is_object($conversation) ? $conversation->preview : ($conversation['preview'] ?? 'Nouvelle conversation')); ?></div>
                                <small class="text-muted"><?php echo e(\Carbon\Carbon::parse(is_object($conversation) ? $conversation->updated_at : $conversation['updated_at'])->diffForHumans()); ?></small>
                            </div>
                            <button class="btn btn-sm text-danger p-0 ms-2" onclick="event.stopPropagation(); deleteConversation(<?php echo e(is_object($conversation) ? $conversation->id : $conversation['id']); ?>)" title="Supprimer">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="text-center text-muted p-3">
                        <i class="bi bi-chat-square-quote display-4 opacity-25"></i>
                        <p class="mt-2">Aucune conversation</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Main Chat Area -->
        <div class="ia-chat-main">
            <?php if($selectedConversation): ?>
                <div class="ia-chat-messages" id="messagesContainer">
                    <?php $__empty_1 = true; $__currentLoopData = $messages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $message): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="message-bubble <?php echo e($message->role === 'user' ? 'message-user' : 'message-assistant'); ?>">
                            <?php if($message->image_path): ?>
                                <img src="<?php echo e(asset('storage/' . $message->image_path)); ?>" class="img-fluid rounded mb-2" style="max-width: 200px;" alt="Image">
                            <?php endif; ?>
                            <p class="mb-0"><?php echo nl2br(e($message->content)); ?></p>
                            <small class="text-muted"><?php echo e(\Carbon\Carbon::parse($message->created_at)->format('H:i')); ?></small>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="text-center text-white">
                            <i class="bi bi-robot display-1 opacity-50"></i>
                            <h4 class="mt-3">Comment puis-je vous aider?</h4>
                            <p class="opacity-75">Je peux interroger la base de données, répondre à vos questions, et plus encore.</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Input Area -->
                <div class="input-area">
                    <form id="iaChatForm" enctype="multipart/form-data">
                        <?php echo csrf_field(); ?>
                        <div class="d-flex gap-2 align-items-end">
                            <div class="flex-grow-1">
                                <input type="file" name="image" id="imageInput" class="d-none" accept="image/*" onchange="previewImage(this)">
                                <textarea name="message" id="messageInput" class="form-control" rows="2" placeholder="Tapez votre message..."></textarea>
                                <div id="imagePreview" class="d-none mt-2">
                                    <img src="" alt="Preview" class="img-thumbnail" style="max-height: 80px;">
                                    <button type="button" class="btn btn-sm btn-danger" onclick="removeImage()">×</button>
                                </div>
                            </div>
                            <button type="button" class="btn btn-outline-secondary" onclick="document.getElementById('imageInput').click()">
                                <i class="bi bi-image"></i>
                            </button>
                            <button type="submit" class="send-btn">
                                <i class="bi bi-send"></i> Envoyer
                            </button>
                        </div>
                    </form>
                </div>
            <?php else: ?>
                <div class="d-flex align-items-center justify-content-center h-100 flex-column">
                    <i class="bi bi-robot display-1 text-primary opacity-25"></i>
                    <h3 class="mt-3 text-muted">IA Chat Box</h3>
                    <p class="text-muted">Sélectionnez une conversation ou créez-en une nouvelle</p>
                    <button class="btn btn-primary" onclick="createNewConversation()">
                        <i class="bi bi-plus-lg me-2"></i>Nouvelle conversation
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
let currentConversationId = <?php echo e($selectedConversation ? $selectedConversation : 'null'); ?>;

function createNewConversation() {
    fetch('<?php echo e(route($baseRoute . '.create')); ?>', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = '<?php echo e(route($baseRoute . '.index')); ?>?conversation_id=' + data.conversation_id;
        }
    });
}

function selectConversation(id) {
    window.location.href = '<?php echo e(route($baseRoute . '.index')); ?>?conversation_id=' + id;
}

function deleteConversation(id) {
    if (!confirm('Voulez-vous vraiment supprimer cette conversation ?')) {
        return;
    }
    fetch('<?php echo e(route($baseRoute . '.destroy', ['id' => '__ID__'])); ?>'.replace('__ID__', id), {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = '<?php echo e(route($baseRoute . '.index')); ?>';
        }
    });
}

function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('imagePreview').querySelector('img').src = e.target.result;
            document.getElementById('imagePreview').classList.remove('d-none');
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function removeImage() {
    document.getElementById('imageInput').value = '';
    document.getElementById('imagePreview').classList.add('d-none');
}

document.getElementById('iaChatForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const messageInput = document.getElementById('messageInput');
    const imageInput = document.getElementById('imageInput');
    const message = messageInput.value.trim();
    const hasImage = imageInput.files.length > 0;
    
    if (!message && !hasImage) {
        return;
    }

    const formData = new FormData();
    formData.append('message', message);
    if (hasImage) {
        formData.append('file', imageInput.files[0]);
    }
    if (currentConversationId) {
        formData.append('conversation_id', currentConversationId);
    }

    const submitBtn = this.querySelector('.send-btn');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Envoi...';

    fetch('<?php echo e(route($baseRoute . '.store')); ?>', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = '<?php echo e(route($baseRoute . '.index')); ?>?conversation_id=' + data.conversation_id;
        }
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="bi bi-send"></i> Envoyer';
    });
});

<?php if($selectedConversation): ?>
    // Scroll to bottom
    const container = document.getElementById('messagesContainer');
    container.scrollTop = container.scrollHeight;
<?php endif; ?>
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make($layout, array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/dydy/Documents/base/laravel/suivitravaux CNRST/resources/views/ia-chat/index.blade.php ENDPATH**/ ?>