<?php function drawChat() { ?>
    <?php 
        $session = Session::getInstance();
        $user = $session->getUser();
        if (!$user) {
            return;
        }
        $userId = $user->id;
    ?>
    
    <div id="chat-container">
        <button id="chat-toggle-btn">
            <i class="fa-solid fa-message"></i>
            <span id="chat-badge" class="chat-badge hidden"></span>
        </button>

        <div id="chat-modal" class="hidden">
            <div class="chat-wrapper">

                <?php
                    drawChatSidebar($userId); 
                
                    drawMainChat();

                    drawCloseButton();

                    drawCustomOrder($userId);
                ?>

            </div>
        </div>
    </div>

    <script type="module" src="/js/chat.js"></script>
<?php } ?>

<?php function drawChatSidebar($userId) { ?>
    <div class="chat-sidebar">
        <h4>Chats</h4>                   
        
        <?php
            $conversations = Chat::getUserConversations($userId);

            if (empty($conversations)) {
                echo "<p>No open chats</p>";
            } else {
                echo "<div class='chat-scrollable'>";
                    foreach ($conversations as $conversation) {
                        $service = Service::getById((int)$conversation['service_id']);
                        $serviceTitle = htmlspecialchars($service->title);
                        $conversationId = $conversation['id'];
                        $serviceId = $conversation['service_id'];

                        echo "
                            <div class='chat-item'
                                data-conversation-id='$conversationId'
                                data-service-id='$serviceId'
                                data-user-id='$userId'>
                        
                                <div class='chat-item-title-wrapper'>
                                    <p class='chat-item-title'>$serviceTitle</p>
                                    <span class='chat-item-badge hidden' id='unread-badge-$conversationId-$serviceId'></span>
                                </div>
                            </div>
                        ";
                    }
                echo "</div>";
            }
        ?>
    </div>
<?php } ?>

<?php function drawMainChat() { ?>
    <div class="chat-main hidden" id="chat-main">
        <div class="chat-header">
            <strong><span id="chat-username">[username]</span></strong><br>
            <small id="chat-service-title">[service title]</small>
        </div>

        <div class="chat-body" id="chat-body">
            <p><em>Select a conversation to start chatting...</em></p>
        </div>

        <div class="chat-footer">
            <form id="chat-form" onsubmit="sendMessage(event)" enctype="multipart/form-data">
                <input type="hidden" id="csrf_token" name="csrf_token" value="<?= htmlspecialchars(Session::getInstance()->getCSRFToken()) ?>">
                <div id="chat-input-wrapper">
                    <label for="chat-file" id="chat-file-label">
                        <i class="fa-solid fa-paperclip"></i>
                    </label>
                    <input type="file" id="chat-file" accept="image/*,application/pdf,.doc,.docx" />
                    <input type="text" id="chat-input" placeholder="Type a message..." autocomplete="off" />
                </div>
                
                <button type="submit" id="chat-send-btn">
                    <i class="fa-solid fa-paper-plane"></i>
                </button>
            </form>

        </div>
        
        <div id="file-name-display" class="file-name-display"></div>
    </div>
<?php } ?>

<?php function drawCloseButton() { ?>
    <div class="close-button">
        <button id="chat-custom-order"><i class="fa-solid fa-ellipsis-vertical"></i></button>
        <button id="chat-close-btn"><i class="fa-solid fa-xmark"></i></button>
    </div>
<?php } ?>

<?php function drawCustomOrder($userId) { ?>
    <div id="custom-order-modal" class="custom-order-popup hidden">
        <div class="custom-order-content">
            <h3>Offer a custom service on</h3>
            <div id="hirings-list">
                <p>Loading...</p>
            </div>
            <button id="custom-order-close-btn">Close</button>
        </div>
    </div>
<?php } ?>
