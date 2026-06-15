<?php
    use Illuminate\Support\Str;
    
    $layoutSelect = 'layouts.super-admin';
    $baseRoute = 'super-admin.chat';

    $routeName = request()->route()->getName();
    if (Str::startsWith($routeName, 'admin.')) {
        $layoutSelect = 'layouts.admin';
        $baseRoute = 'admin.chat';
    } elseif ($routeName === 'partenaire.chat.index') {
        $layoutSelect = 'layouts.partenaire';
        $baseRoute = 'partenaire.chat';
    } elseif (Str::startsWith($routeName, 'role-dynamique.')) {
        $layoutSelect = 'layouts.role-dynamique';
        $baseRoute = 'role-dynamique.chat';
    } elseif (isset($currentUser['type']) && $currentUser['type'] === 'Partenaire') {
        $layoutSelect = 'layouts.partenaire';
        $baseRoute = 'partenaire.chat';
    } elseif (isset($currentUser['type']) && $currentUser['type'] === 'Admin') {
        $layoutSelect = 'layouts.admin';
        $baseRoute = 'admin.chat';
    } elseif (isset($currentUser['type']) && $currentUser['type'] === 'User') {
        $layoutSelect = 'layouts.role-dynamique';
        $baseRoute = 'role-dynamique.chat';
    }
?>



<?php $__env->startSection('title', 'Messsagerie'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $chatMobileClass = $selectedContact ? 'chat-mobile-open' : 'chat-mobile-list';
?>
<div class="container-fluid py-0 py-md-4">
    <div class="row g-0 rounded-md-4 shadow-lg overflow-hidden bg-white chat-main-container <?php echo e($chatMobileClass); ?>">
        <!-- Liste des contacts (Sidebar WhatsApp style) -->
        <div class="col-md-4 border-end d-flex flex-column bg-light chat-contact-pane">
            <div class="p-3 bg-white border-bottom">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0 fw-bold text-dark">Discussions</h5>
                    <div class="dropdown">
                        <button class="btn btn-link text-muted p-0" data-bs-toggle="dropdown">
                            <i class="bi bi-three-dots-vertical fs-5"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                            <li><a class="dropdown-item py-2" href="#"><i class="bi bi-person-circle me-2"></i>Mon Profil</a></li>
                            <li><a class="dropdown-item py-2" href="#"><i class="bi bi-gear me-2"></i>Paramètres</a></li>
                        </ul>
                    </div>
                </div>
                <div class="position-relative">
                    <span class="position-absolute top-50 start-0 translate-middle-y ps-3 text-muted">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" id="contactSearch" class="form-control rounded-pill bg-light border-0 ps-5" placeholder="Rechercher ou démarrer une discussion">
                </div>
            </div>

            <div class="flex-grow-1 overflow-auto bg-white custom-scrollbar" id="contactList">
                <div class="list-group list-group-flush">
                    <?php $__empty_1 = true; $__currentLoopData = $contacts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $isActive = ($selectedContact && $selectedContact->id == $item['model']->id && request('type') == $item['type']);
                        ?>
                        <a href="<?php echo e(route($baseRoute . '.index', ['id' => $item['model']->id, 'type' => $item['type']])); ?>"
                           class="list-group-item list-group-item-action border-0 py-3 px-3 d-flex align-items-center contact-item <?php echo e($isActive ? 'active-contact' : ''); ?>">
                            <div class="position-relative me-3">
                                <div class="avatar bg-gradient-green text-white rounded-circle d-flex align-items-center justify-content-center fw-bold shadow-sm" style="width: 54px; height: 54px; font-size: 1.3rem;">
                                    <?php echo e(strtoupper(substr($item['fullname'], 0, 1))); ?>

                                </div>
                                <?php if($isActive): ?>
                                    <span class="position-absolute bottom-0 end-0 p-1 bg-success border border-2 border-white rounded-circle"></span>
                                <?php endif; ?>
                            </div>
                            <div class="flex-grow-1 overflow-hidden">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <h6 class="mb-0 fw-bold text-truncate <?php echo e($item['unread_count'] > 0 ? 'text-dark' : 'text-secondary'); ?>"><?php echo e($item['fullname']); ?></h6>
                                    <?php if($item['unread_count'] > 0): ?>
                                        <span class="badge bg-success rounded-pill px-2"><?php echo e($item['unread_count']); ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <small class="text-muted text-truncate w-75"><?php echo e($item['label']); ?></small>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="text-center p-5 text-muted">
                            <i class="bi bi-person-x display-4 mb-3 opacity-25"></i>
                            <p>Aucun contact disponible</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Zone de messagerie -->
        <div class="col-md-8 d-flex flex-column bg-white chat-conversation-pane">
            <?php if($selectedContact): ?>
                <!-- Header -->
                <div class="p-3 border-bottom d-flex align-items-center bg-white shadow-sm z-1">
                    <a href="<?php echo e(route($baseRoute . '.index')); ?>" class="chat-back-btn d-md-none me-2" title="Retour aux discussions">
                        <i class="bi bi-arrow-left"></i>
                    </a>
                    <div class="avatar bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-3 shadow-sm" style="width: 45px; height: 45px; font-weight: bold;">
                        <?php echo e(strtoupper(substr(($selectedContactMeta['fullname'] ?? ($selectedContact->prenom ?? $selectedContact->name ?? '?')), 0, 1))); ?>

                    </div>
                    <div class="flex-grow-1">
                        <h6 class="mb-0 fw-bold text-dark"><?php echo e($selectedContactMeta['fullname'] ?? (($selectedContact->prenom ?? '') . ' ' . ($selectedContact->nom ?? '') ?: ($selectedContact->name ?? 'Inconnu'))); ?></h6>
                        <small class="text-success"><i class="bi bi-circle-fill me-1" style="font-size: 0.5rem;"></i> <?php echo e($selectedContactMeta['label'] ?? request('type')); ?></small>
                    </div>
                </div>

                <!-- Messages -->
                <div class="flex-grow-1 p-4 overflow-auto d-flex flex-column custom-scrollbar" style="background: #e5ddd5 url('https://user-images.githubusercontent.com/15075759/28719144-86dc0f70-73b1-11e7-911d-60d70fcded21.png') repeat;" id="messageContainer">
                    <div id="messagesList" class="d-flex flex-column w-100"
                         data-current-user-id="<?php echo e($currentUser['id']); ?>"
                         data-current-user-type="<?php echo e($currentUser['type']); ?>">
                        <?php $__empty_1 = true; $__currentLoopData = $messages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $msg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php
                                $isMe = ($msg->sender_id == $currentUser['id'] && $msg->sender_type == $currentUser['type']);
                            ?>
                            <div class="d-flex mb-3 <?php echo e($isMe ? 'justify-content-end' : 'justify-content-start'); ?>" data-id="<?php echo e($msg->id); ?>">
                                <div class="message-bubble <?php echo e($isMe ? 'bubble-me' : 'bubble-them'); ?> shadow-sm">
                                    <?php if($msg->image_path): ?>
                                        <div class="mb-2">
                                            <img src="<?php echo e(asset('storage/' . $msg->image_path)); ?>" class="img-fluid rounded-3 message-img" alt="Image" style="max-height: 300px; cursor: pointer;" onclick="window.open(this.src)">
                                        </div>
                                    <?php endif; ?>

                                    <?php if($msg->audio_path): ?>
                                        <div class="mb-2">
                                            <audio controls class="mw-100 message-audio" style="height: 35px;">
                                                <source src="<?php echo e(asset('storage/' . $msg->audio_path)); ?>" type="audio/mpeg">
                                                Votre navigateur ne supporte pas l'audio.
                                            </audio>
                                        </div>
                                    <?php endif; ?>

                                    <?php if($msg->message): ?>
                                        <p class="mb-1"><?php echo e($msg->message); ?></p>
                                    <?php endif; ?>

                                    <div class="text-end message-time">
                                        <?php echo e($msg->created_at->format('H:i')); ?>

                                        <?php if($isMe): ?>
                                            <i class="bi <?php echo e($msg->is_read ? 'bi-check2-all text-info' : 'bi-check2'); ?> ms-1"></i>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <div class="m-auto text-center bg-white p-4 rounded-4 shadow-sm empty-state" style="max-width: 300px;">
                                <i class="bi bi-lock-fill text-muted mb-2 fs-4"></i>
                                <p class="text-muted small mb-0">Les messages sont chiffrés. Seules les personnes concernées peuvent les lire.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Footer / Input -->
                <div class="p-3 bg-light border-top chat-footer">
                    <!-- Preview zone for files before sending -->
                    <div id="filePreview" class="d-none p-2 mb-2 bg-white rounded-3 shadow-sm align-items-center gap-3">
                        <div id="imagePreviewContainer" class="d-none">
                            <img id="imagePreview" src="" alt="Aperçu" class="rounded-2" style="max-height: 100px; max-width: 100px; object-fit: cover;">
                        </div>
                        <div class="d-flex align-items-center flex-grow-1 overflow-hidden">
                            <i class="bi bi-file-earmark-check fs-4 me-2 text-success"></i>
                            <span id="fileName" class="small text-truncate flex-grow-1"></span>
                        </div>
                        <button type="button" class="btn-close btn-sm" onclick="clearFiles()"></button>
                    </div>

                    <form action="<?php echo e(route($baseRoute . '.store')); ?>" method="POST" enctype="multipart/form-data" id="chatForm">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="receiver_id" value="<?php echo e($selectedContact->id); ?>">
                        <input type="hidden" name="receiver_type" value="<?php echo e(request('type')); ?>">

                        <!-- Hidden File Inputs -->
                        <input type="file" name="image" id="imageInput" class="d-none" accept="image/*" onchange="handleFileSelect(this, 'image')">
                        <input type="file" name="audio" id="audioInput" class="d-none" accept="audio/*" onchange="handleFileSelect(this, 'audio')">

                        <div class="d-flex align-items-center gap-2">
                            <div class="d-flex">
                                <button type="button" class="btn btn-link text-muted p-2" onclick="document.getElementById('imageInput').click()" title="Envoyer une image">
                                    <i class="bi bi-image fs-4"></i>
                                </button>
                                <button type="button" class="btn btn-link text-muted p-2" id="startRecording" title="Enregistrer un message vocal">
                                    <i class="bi bi-mic fs-4"></i>
                                </button>
                            </div>

                            <div class="flex-grow-1 position-relative">
                                <input type="text" name="message" id="messageInput" class="form-control rounded-pill border-0 py-2 shadow-sm" placeholder="Tapez un message..." autocomplete="off">
                                
                                <!-- Recording UI Overlay -->
                                <div id="recordingUI" class="d-none position-absolute top-0 start-0 w-100 h-100 bg-light rounded-pill align-items-center px-3 z-3">
                                    <div class="recording-dot me-2"></div>
                                    <span class="small fw-bold text-danger flex-grow-1" id="recordingTimer">00:00</span>
                                    <button type="button" class="btn btn-sm btn-outline-danger border-0 rounded-circle me-1" id="cancelRecording">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-success border-0 rounded-circle" id="stopAndSend">
                                        <i class="bi bi-check-lg"></i>
                                    </button>
                                </div>
                            </div>

                            <button type="submit" class="btn bg-gradient-green text-white rounded-circle shadow-sm p-0 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; min-width: 45px;">
                                <i class="bi bi-send-fill fs-5"></i>
                            </button>
                        </div>
                    </form>
                </div>
            <?php else: ?>
                <!-- Empty State -->
                <div class="m-auto text-center p-5">
                    <div class="bg-light rounded-circle d-inline-flex p-5 mb-4">
                        <i class="bi bi-chat-left-dots-fill display-1 text-success opacity-25"></i>
                    </div>
                    <h3 class="fw-bold text-dark">Garder la connexion</h3>
                    <p class="text-muted px-5">Sélectionnez une conversation pour envoyer un message ou partagez une image ou un audio avec votre équipe.</p>
                    <div class="mt-4 border-top pt-4 text-muted small">
                        <i class="bi bi-laptop me-2"></i> Version bureau disponible
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<link rel="stylesheet" href="<?php echo e(asset('css/chat/index.css')); ?>">

<script src="<?php echo e(asset('js/chat/index.js')); ?>"></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make($layoutSelect, array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/dydy/Documents/base/laravel/suivitravaux CNRST/resources/views/chat/index.blade.php ENDPATH**/ ?>